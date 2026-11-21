<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseResource;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\CourseManagementService;
use App\Services\EnrollmentService;
use App\Services\ResourceManagementService;
use App\Testing\TestCase;

class SecureLearningResourcesTest extends TestCase {
    public function testResourceUploadValidationAndSecurityConstraints(): void {
        $course = CourseManagementService::createCourse([
            'title' => 'CS502 Cloud Security Architecture',
            'status' => 'published',
        ]);
        $cId = (int)$course->C_ID;

        // Create temporary test files
        $tempDir = sys_get_temp_dir();
        $safeFile = $tempDir . '/shilpa_lecture_01.pdf';
        file_put_contents($safeFile, '%PDF-1.4 sample academic syllabus content');

        $maliciousFile = $tempDir . '/exploit.php';
        file_put_contents($maliciousFile, '<?php phpinfo(); ?>');

        $forbiddenExtFile = $tempDir . '/payload.exe';
        file_put_contents($forbiddenExtFile, 'MZ binary content');

        // 1. Forbidden script/executable rejection
        $maliciousThrew = false;
        try {
            ResourceManagementService::storeUploadedFile(
                $cId,
                null,
                1,
                ['name' => 'exploit.php', 'tmp_name' => $maliciousFile, 'size' => 20, 'error' => UPLOAD_ERR_OK],
                'Exploit Test'
            );
        } catch (\Throwable $e) {
            $maliciousThrew = true;
            assert(str_contains($e->getMessage(), 'strictly prohibited'), "Must reject executable/script files");
        }
        assert($maliciousThrew, "Attempting to upload executable/php script must throw exception");

        // 2. Disallowed extension rejection
        $forbiddenThrew = false;
        try {
            ResourceManagementService::storeUploadedFile(
                $cId,
                null,
                1,
                ['name' => 'payload.exe', 'tmp_name' => $forbiddenExtFile, 'size' => 20, 'error' => UPLOAD_ERR_OK],
                'Payload Test'
            );
        } catch (\Throwable $e) {
            $forbiddenThrew = true;
            assert(str_contains($e->getMessage(), 'not permitted') || str_contains($e->getMessage(), 'prohibited'));
        }
        assert($forbiddenThrew, "Disallowed file extension must throw exception");

        // 3. Valid file upload and randomized storage
        $res = ResourceManagementService::storeUploadedFile(
            $cId,
            null,
            1,
            ['name' => 'shilpa_lecture_01.pdf', 'tmp_name' => $safeFile, 'size' => 38, 'error' => UPLOAD_ERR_OK],
            'Lecture 01 Syllabus & Guide',
            false
        );

        assert($res->title === 'Lecture 01 Syllabus & Guide');
        assert($res->original_filename === 'shilpa_lecture_01.pdf');
        assert(!empty($res->storage_path), "Resource must have non-empty storage path");
        assert(!str_contains($res->storage_path, 'shilpa_lecture_01.pdf'), "Filename in storage must be randomized/hashed");

        $storedFullPath = ResourceManagementService::getStorageBaseDirectory() . '/' . $res->storage_path;
        assert(file_exists($storedFullPath), "Stored file must exist on disk");
        assert(file_get_contents($storedFullPath) === '%PDF-1.4 sample academic syllabus content');

        // Cleanup temp files
        @unlink($safeFile);
        @unlink($maliciousFile);
        @unlink($forbiddenExtFile);
    }

    public function testCourseResourceAccessControlAndDownloads(): void {
        $course = CourseManagementService::createCourse([
            'title' => 'CS505 High Performance Distributed Databases',
            'status' => 'published',
        ]);
        $cId = (int)$course->C_ID;

        $tempDir = sys_get_temp_dir();
        $privateFile = $tempDir . '/private_lab_guide.pdf';
        file_put_contents($privateFile, 'Private lab materials for enrolled learners only.');

        $publicFile = $tempDir . '/syllabus_overview.pdf';
        file_put_contents($publicFile, 'Public course syllabus open to everyone.');

        $privateRes = ResourceManagementService::storeUploadedFile(
            $cId,
            null,
            1,
            ['name' => 'private_lab_guide.pdf', 'tmp_name' => $privateFile, 'size' => 48, 'error' => UPLOAD_ERR_OK],
            'Lab Guide 01',
            false
        );

        $publicRes = ResourceManagementService::storeUploadedFile(
            $cId,
            null,
            1,
            ['name' => 'syllabus_overview.pdf', 'tmp_name' => $publicFile, 'size' => 42, 'error' => UPLOAD_ERR_OK],
            'Syllabus Overview',
            true
        );

        // 1. Create student and verify unenrolled student cannot access private resource
        $student = User::create([
            'Full_Name' => 'Bob Learner',
            'Email' => 'bob.learner.' . time() . '@example.com',
            'Password' => password_hash('SecurePass123!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);
        $studentId = (int)$student->UserId;

        session()->set('user', ['id' => $studentId, 'name' => 'Bob Learner']);
        session()->set('user_id', $studentId);
        session()->set('user_role', 'student');

        $downloadUnenrolled = $this->get("/courses/{$cId}/resources/{$privateRes->id}/download");
        $downloadUnenrolled->assertRedirect("/courses/{$cId}/workspace");

        // 2. Unenrolled student CAN access public preview resource
        $downloadPublic = $this->get("/courses/{$cId}/resources/{$publicRes->id}/download");
        $downloadPublic->assertOk();
        assert($downloadPublic->getHeader('Content-Disposition') !== null, "Must include download attachment header");
        assert(str_contains($downloadPublic->getHeader('Content-Disposition'), 'syllabus_overview.pdf'));

        // 3. Enrolled student CAN download private resource
        $enrollResult = EnrollmentService::enroll($studentId, $cId);
        assert($enrollResult['success'] === true, "Enrollment must succeed: " . ($enrollResult['error'] ?? ''));

        $downloadEnrolled = $this->get("/courses/{$cId}/resources/{$privateRes->id}/download");
        $downloadEnrolled->assertOk();
        assert(str_contains($downloadEnrolled->getHeader('Content-Disposition'), 'private_lab_guide.pdf'));
        assert($downloadEnrolled->getHeader('X-Content-Type-Options') === 'nosniff');

        // Check download count incremented
        $refreshedRes = CourseResource::find((int)$privateRes->id);
        assert((int)$refreshedRes->download_count === 1, "Download count must increment");

        // 4. Faculty and Admin management view
        session()->set('user_role', 'lecturer');
        $facultyMgmt = $this->get("/faculty/courses/{$cId}/resources");
        $facultyMgmt->assertOk();
        $facultyMgmt->assertSee('Lab Guide 01');
        $facultyMgmt->assertSee('Syllabus Overview');

        // 5. Deletion cleans up disk
        $fullPath = ResourceManagementService::getStorageBaseDirectory() . '/' . $privateRes->storage_path;
        assert(file_exists($fullPath), "Physical file must exist before deletion");

        $deleteResponse = $this->post("/faculty/courses/{$cId}/resources/{$privateRes->id}/delete");
        $deleteResponse->assertRedirect("/faculty/courses/{$cId}/resources");

        assert(!file_exists($fullPath), "Physical file must be unlinked after resource deletion");
        assert(CourseResource::find((int)$privateRes->id) === null, "Resource record must be deleted");

        // Cleanup
        @unlink($privateFile);
        @unlink($publicFile);
    }
}
