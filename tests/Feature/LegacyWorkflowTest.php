<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use App\Testing\TestCase;

class LegacyWorkflowTest extends TestCase {
    public function testLegacyCourseCreationWorkflow(): void {
        $course = Course::create([
            'C_NAME' => 'Fullstack Web Development',
            'C_CARD_DESCRIPTION' => 'Modern Web stack course',
            'C_DESCRIPTION' => 'Full curriculum including modern frameworks',
            'C_CARD_IMAGE' => 'Images/allCourses/kiddo.png',
            'C_DONE_BY' => 'Faculty of IT',
            'C_LINK' => 'https://shilpalms.com/fullstack',
            'TOP_COURSE' => 'Yes',
            'C_ADD_DATE' => date('Y-m-d H:i:s'),
        ]);

        assert($course->C_ID > 0, "Course creation should succeed");

        // Verify retrieval via BusinessLogic helper functions
        require_once dirname(__DIR__, 2) . '/BusinessLogic.php';
        ob_start();
        getLatestCourse();
        $output = ob_get_clean();

        assert(str_contains($output, 'Fullstack Web Development'), "getLatestCourse() should render course name");
        assert(str_contains($output, 'Faculty of IT'), "getLatestCourse() should render author name");
    }

    public function testUserAuthenticationFlow(): void {
        $user = User::create([
            'Full_Name' => 'Pasindu Kumara',
            'Email' => 'pasindu@shilpa.lk',
            'Password' => password_hash('SecureStudentPass2026!', PASSWORD_DEFAULT),
            'user_type' => 'student',
        ]);

        $found = User::findByEmail('pasindu@shilpa.lk');
        assert($found !== null, "User should be found by email");
        assert($found->verifyPassword('SecureStudentPass2026!') === true, "Password must authenticate");
    }
}
