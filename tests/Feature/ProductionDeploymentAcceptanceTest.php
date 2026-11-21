<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Testing\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\CourseGrade;
use App\Security\SecurityAuditLog;
use App\Services\GradingAndResultsService;

class ProductionDeploymentAcceptanceTest extends TestCase {

    /**
     * Test Docker container configurations exist and contain production directives.
     */
    public function testDockerAndContainerConfigurationsExistAndAreValid(): void {
        $root = dirname(__DIR__, 2);

        $dockerfile = $root . '/Dockerfile';
        $this->assertFileExists($dockerfile);
        $dockerContent = file_get_contents($dockerfile);
        $this->assertStringContainsString('php:8.2-fpm-alpine', $dockerContent);
        $this->assertStringContainsString('pdo_mysql', $dockerContent);
        $this->assertStringContainsString('opcache', $dockerContent);
        $this->assertStringContainsString('EXPOSE 9000', $dockerContent);

        $compose = $root . '/docker-compose.yml';
        $this->assertFileExists($compose);
        $composeContent = file_get_contents($compose);
        $this->assertStringContainsString('shilpa_app', $composeContent);
        $this->assertStringContainsString('shilpa_web', $composeContent);
        $this->assertStringContainsString('shilpa_db', $composeContent);
        $this->assertStringContainsString('shilpa_redis', $composeContent);
        $this->assertStringContainsString('shilpa_queue_worker', $composeContent);

        $nginxConf = $root . '/docker/nginx.conf';
        $this->assertFileExists($nginxConf);
        $nginxContent = file_get_contents($nginxConf);
        $this->assertStringContainsString('fastcgi_pass app:9000', $nginxContent);
        $this->assertStringContainsString('X-Frame-Options', $nginxContent);
        $this->assertStringContainsString('gzip on', $nginxContent);

        $phpIni = $root . '/docker/php.ini';
        $this->assertFileExists($phpIni);
        $phpIniContent = file_get_contents($phpIni);
        $this->assertStringContainsString('expose_php = Off', $phpIniContent);
        $this->assertStringContainsString('opcache.enable = 1', $phpIniContent);

        $entrypoint = $root . '/docker/entrypoint.sh';
        $this->assertFileExists($entrypoint);
        $this->assertTrue(is_executable($entrypoint) || file_exists($entrypoint));
    }

    /**
     * Test production environment template configuration and security constraints.
     */
    public function testProductionEnvironmentTemplateConfigured(): void {
        $envProdPath = file_exists(dirname(__DIR__, 2) . '/.env.production.example')
            ? dirname(__DIR__, 2) . '/.env.production.example'
            : dirname(__DIR__, 2) . '/.env.production';
        $this->assertFileExists($envProdPath);

        $content = file_get_contents($envProdPath);
        $this->assertStringContainsString('APP_ENV=production', $content);
        $this->assertStringContainsString('APP_DEBUG=false', $content);
        $this->assertStringContainsString('SESSION_SECURE_COOKIE=true', $content);
        $this->assertStringContainsString('QUEUE_CONNECTION=database', $content);
        $this->assertStringContainsString('DB_CONNECTION=mysql', $content);

        // Ensure no actual leaked passwords or keys in template
        $this->assertMatchesRegularExpression('/APP_KEY=\s*$/m', $content);
        $this->assertMatchesRegularExpression('/DB_PASSWORD=\s*$/m', $content);
    }

    /**
     * Test deployment verification script execution and readiness output.
     */
    public function testDeploymentVerifierScriptExecution(): void {
        $root = dirname(__DIR__, 2);
        $verifyScript = $root . '/bin/deploy-verify';
        $this->assertFileExists($verifyScript);

        $output = [];
        $exitCode = 0;
        exec("php " . escapeshellarg($verifyScript) . " 2>&1", $output, $exitCode);

        $outputStr = implode("\n", $output);
        $this->assertSame(0, $exitCode, "deploy-verify failed with exit code $exitCode:\n" . $outputStr);
        $this->assertStringContainsString('DEPLOYMENT VERIFICATION PASSED', $outputStr);
    }

    /**
     * Test production system health and API ping endpoints.
     */
    public function testProductionSystemHealthAndApiPing(): void {
        $healthResponse = $this->get('/health');
        $healthResponse->assertOk()
                       ->assertJson(['status' => 'healthy', 'app' => 'SHILPA LMS']);

        $pingResponse = $this->get('/api/v1/ping');
        $pingResponse->assertOk()
                     ->assertJson(['success' => true]);
    }

    /**
     * Test complete end-to-end critical user path from registration to grading and audit.
     */
    public function testFullLifecycleEndToEndAcceptanceFlow(): void {
        // 1. User Registration & Provisioning
        $lecturer = User::create([
            'Full_Name' => 'Prof. Alan Turing',
            'Fname' => 'Prof. Alan',
            'Lname' => 'Turing',
            'Email' => 'alan.turing@shilpalms.com',
            'Password' => password_hash('Pass#123456', PASSWORD_BCRYPT),
            'user_type' => 'lecturer',
            'Gender' => 'Male',
        ]);
        $this->assertNotNull($lecturer->UserId);

        $student = User::create([
            'Full_Name' => 'Ada Lovelace',
            'Fname' => 'Ada',
            'Lname' => 'Lovelace',
            'Email' => 'ada.lovelace@shilpalms.com',
            'Password' => password_hash('Pass#123456', PASSWORD_BCRYPT),
            'user_type' => 'student',
            'Gender' => 'Female',
        ]);
        $this->assertNotNull($student->UserId);

        // 2. Course Creation & Publishing
        $course = Course::create([
            'C_NAME' => 'Foundations of Computer Architecture',
            'C_CARD_DESCRIPTION' => 'Theoretical and practical foundations of computing.',
            'C_DESCRIPTION' => 'Deep dive into computational architecture and algorithms.',
            'C_DONE_BY' => 'Prof. Alan Turing',
            'TOP_COURSE' => 'Yes',
        ]);
        $this->assertNotNull($course->C_ID);

        // 3. Module & Lesson Curriculum Structure
        $module = CourseModule::create([
            'course_id' => $course->C_ID,
            'title' => 'Module 1: Machine Organization',
            'description' => 'Processor register architecture',
            'order_index' => 1,
            'is_published' => 1,
        ]);
        $this->assertNotNull($module->id);

        $lesson = Lesson::create([
            'module_id' => $module->id,
            'title' => 'Von Neumann Architecture',
            'slug' => 'von-neumann-architecture',
            'content_type' => 'article',
            'content_body' => '<p>Instruction execution cycles.</p>',
            'duration_minutes' => 30,
            'order_index' => 1,
            'is_free_preview' => 0,
        ]);
        $this->assertNotNull($lesson->id);

        // 4. Student Enrollment
        $enrollment = Enrollment::create([
            'user_id' => $student->UserId,
            'course_id' => $course->C_ID,
            'status' => 'active',
            'progress_percent' => 50,
            'enrolled_at' => date('Y-m-d H:i:s'),
        ]);
        $this->assertNotNull($enrollment->id);

        // 5. Assignment Creation & Student Submission
        $assignment = Assignment::create([
            'course_id' => $course->C_ID,
            'created_by' => $lecturer->UserId,
            'title' => 'Analytical Engine Micro-Operations',
            'description' => 'Provide timing diagram analysis.',
            'max_score' => 100,
            'due_at' => date('Y-m-d H:i:s', strtotime('+7 days')),
            'is_published' => 1,
        ]);
        $this->assertNotNull($assignment->id);

        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $student->UserId,
            'submission_text' => 'Micro-operation sequence verified with clock cycles.',
            'status' => 'submitted',
            'submitted_at' => date('Y-m-d H:i:s'),
        ]);
        $this->assertNotNull($submission->id);

        // 6. Faculty Grading and Feedback
        $graded = GradingAndResultsService::gradeSubmission(
            (int)$submission->id,
            (int)$lecturer->UserId,
            98,
            'Exceptional rigor and historical accuracy.'
        );
        $this->assertTrue($graded->isGraded());
        $this->assertSame(98, (int)$graded->score);

        // 7. Course Gradebook & Transcript Calculation
        $courseGrade = CourseGrade::query()
            ->where('course_id', (int)$course->C_ID)
            ->where('student_id', (int)$student->UserId)
            ->first();
        $this->assertNotNull($courseGrade);
        $this->assertSame(98, (int)$courseGrade['percentage']);
        $this->assertSame('A+', $courseGrade['letter_grade']);

        // 8. Security Audit Trail Logged
        $auditLog = SecurityAuditLog::log(
            'production.final_acceptance.completed',
            'info',
            (int)$student->UserId,
            $student->Email,
            ['course_id' => $course->C_ID, 'grade' => 'A+']
        );
        $this->assertNotNull($auditLog->id);
        $verification = SecurityAuditLog::verifyChainIntegrity();
        $this->assertTrue($verification['intact']);
    }
}
