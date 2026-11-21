<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseMetadata;
use App\Models\User;
use App\Services\CourseManagementService;
use App\Testing\TestCase;

class AdvancedCourseManagementTest extends TestCase {
    public function testCourseCategorySeeding(): void {
        $categories = CourseCategory::allOrdered();
        assert(!empty($categories), "Academic categories should be seeded");
        assert(count($categories) >= 5, "At least 5 default categories must exist");

        $seCat = CourseCategory::findBySlug('software-engineering');
        assert($seCat !== null, "Software Engineering category must exist by slug");
        assert($seCat->name === 'Software Engineering', "Category name must match");
    }

    public function testCourseCreationWithMetadata(): void {
        $course = CourseManagementService::createCourse([
            'title' => 'SE402 Distributed Systems Engineering',
            'short_description' => 'Scalable consensus and replication algorithms',
            'description' => 'Comprehensive master-level course on distributed architectures and raft consensus.',
            'category_id' => 1,
            'level' => 'advanced',
            'status' => 'draft',
            'instructor_name' => 'Prof. Wickramasinghe',
            'tags' => ['Distributed', 'Consensus', 'Raft', 'Scalability'],
            'prerequisites' => ["CS301 Operating Systems\nCS202 Algorithms"],
            'featured' => 1,
        ]);

        assert($course instanceof Course, "Created course must be instance of Course");
        assert($course->C_NAME === 'SE402 Distributed Systems Engineering', "Course title must match");
        assert($course->isDraft() === true, "Course must initially be in draft status");
        assert($course->isPublished() === false, "Draft course must not be published");

        $meta = $course->metadata();
        assert($meta instanceof CourseMetadata, "Metadata relationship must resolve");
        assert($meta->level === 'advanced', "Course level must be advanced");
        assert($meta->featured === 1, "Course must be flagged as featured");
    }

    public function testCoursePublishToggle(): void {
        $course = CourseManagementService::createCourse([
            'title' => 'SE201 Object Oriented Design Patterns',
            'status' => 'draft',
        ]);

        assert($course->isPublished() === false, "Initial status must be draft");

        // Toggle to publish
        $newStatus = CourseManagementService::togglePublish((int)$course->C_ID);
        assert($newStatus === true, "Toggle must return true when transitioning to published");

        $refreshed = Course::find((int)$course->C_ID);
        assert($refreshed->isPublished() === true, "Course must now be published");

        // Toggle back to draft
        $newStatus2 = CourseManagementService::togglePublish((int)$course->C_ID);
        assert($newStatus2 === false, "Toggle must return false when transitioning to draft");

        $refreshed2 = Course::find((int)$course->C_ID);
        assert($refreshed2->isDraft() === true, "Course must now be draft");
    }

    public function testCourseUpdateWithCategoryAndTags(): void {
        $course = CourseManagementService::createCourse([
            'title' => 'AI101 Introduction to Neural Networks',
            'category_id' => 1,
            'status' => 'draft',
        ]);

        CourseManagementService::updateCourse((int)$course->C_ID, [
            'title' => 'AI101 Modern Deep Learning Architectures',
            'category_id' => 2,
            'level' => 'intermediate',
            'status' => 'published',
            'tags' => ['DeepLearning', 'PyTorch', 'Transformers'],
        ]);

        $updated = Course::find((int)$course->C_ID);
        assert($updated->C_NAME === 'AI101 Modern Deep Learning Architectures', "Title must be updated");
        assert($updated->isPublished() === true, "Status must be updated to published");

        $meta = $updated->metadata();
        assert((int)$meta->category_id === 2, "Category ID must be updated");
        assert($meta->level === 'intermediate', "Level must be updated");
    }

    public function testCourseDeletionAndMetadataCleanup(): void {
        $course = CourseManagementService::createCourse([
            'title' => 'Temporary Course to Delete',
            'status' => 'draft',
        ]);
        $id = (int)$course->C_ID;

        assert(Course::find($id) !== null, "Course must exist prior to deletion");
        assert(CourseMetadata::query()->where('course_id', $id)->first() !== null, "Metadata must exist");

        $deleted = CourseManagementService::deleteCourse($id);
        assert($deleted === true, "deleteCourse must return true");
        assert(Course::find($id) === null, "Course must be removed from database");
        assert(CourseMetadata::query()->where('course_id', $id)->first() === null, "Metadata must be cleaned up");
    }

    public function testFacultyCourseManagementAccessControl(): void {
        session()->clear();

        // 1. Unauthenticated request redirects
        $unauth = $this->get('/faculty/courses');
        assert($unauth->getStatusCode() === 302, "Unauthenticated access should redirect");

        // 2. Student access is blocked/redirected
        session()->set('user', 'Student Learner');
        session()->set('user_role', 'student');
        $studentAccess = $this->get('/faculty/courses');
        assert($studentAccess->getStatusCode() === 302, "Student must be redirected away from faculty courses");

        // 3. Lecturer access is authorized
        session()->set('user', 'Dr. Perera');
        session()->set('user_role', 'lecturer');
        $lecturerAccess = $this->get('/faculty/courses');
        assert($lecturerAccess->getStatusCode() === 200, "Lecturer must access curriculum management with 200 OK");
        $lecturerAccess->assertSee('Curriculum &amp; Course Management');
        $lecturerAccess->assertSee('Author New Course');
    }
}
