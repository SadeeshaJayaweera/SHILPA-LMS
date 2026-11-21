<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use App\Services\NavigationService;
use App\Testing\TestCase;

class NavigationAndLayoutTest extends TestCase {
    public function testWorkspaceLayoutRendersHeaderSidebarAndSearch(): void {
        Course::create([
            'C_NAME' => 'Modern Software Architecture',
            'C_CARD_DESCRIPTION' => 'Enterprise architectural patterns',
            'C_DESCRIPTION' => 'Full architectural principles',
            'C_CARD_IMAGE' => 'Images/allCourses/kiddo.png',
            'C_DONE_BY' => 'Faculty of IT',
            'C_LINK' => 'https://shilpalms.com',
            'TOP_COURSE' => 'Yes',
        ]);

        $response = $this->get('/workspace');
        $response->assertOk()
            ->assertSee('workspace-sidebar')
            ->assertSee('workspace-header')
            ->assertSee('global-search-trigger')
            ->assertSee('breadcrumbs-bar')
            ->assertSee('mobile-bottom-nav')
            ->assertSee('Learning Workspace');
    }

    public function testStudentNavigationStructure(): void {
        $student = new User([
            'Full_Name' => 'Kasun Silva',
            'Email' => 'kasun@shilpa.lk',
            'user_type' => 'student',
        ]);

        $nav = NavigationService::getNavigationForUser($student);
        assert(isset($nav['Learning Hub']), "Student nav must contain Learning Hub");
        assert(isset($nav['Assessments & Progress']), "Student nav must contain Assessments & Progress");
        assert(isset($nav['Collaboration']), "Student nav must contain Collaboration");

        $hubLabels = array_column($nav['Learning Hub'], 'label');
        assert(in_array('Student Overview', $hubLabels, true));
        assert(in_array('Course Catalog', $hubLabels, true));
        assert(in_array('Enrolled Courses', $hubLabels, true));
    }

    public function testLecturerNavigationStructure(): void {
        $lecturer = new User([
            'Full_Name' => 'Dr. Jayaweera',
            'Email' => 'lecturer@shilpa.lk',
            'user_type' => 'lecturer',
        ]);

        $nav = NavigationService::getNavigationForUser($lecturer);
        assert(isset($nav['Faculty Workspace']), "Lecturer nav must contain Faculty Workspace");
        assert(isset($nav['Instruction & Grading']), "Lecturer nav must contain Instruction & Grading");

        $facultyLabels = array_column($nav['Faculty Workspace'], 'label');
        assert(in_array('Lecturer Overview', $facultyLabels, true));
        assert(in_array('Module Builder', $facultyLabels, true));
    }

    public function testAdminNavigationStructure(): void {
        $admin = new User([
            'Full_Name' => 'System Admin',
            'Email' => 'admin@shilpa.lk',
            'user_type' => 'admin',
        ]);

        $nav = NavigationService::getNavigationForUser($admin);
        assert(isset($nav['System Administration']), "Admin nav must contain System Administration");
        assert(isset($nav['Platform & Health']), "Admin nav must contain Platform & Health");

        $adminLabels = array_column($nav['System Administration'], 'label');
        assert(in_array('Admin Console', $adminLabels, true));
        assert(in_array('User Management', $adminLabels, true));
    }

    public function testBreadcrumbGeneration(): void {
        $crumbs = NavigationService::getBreadcrumbs('/courses/advanced-php');
        assert(count($crumbs) === 3, "Expected 3 breadcrumbs for /courses/advanced-php");
        assert($crumbs[0]['label'] === 'Home' && $crumbs[0]['url'] === '/');
        assert($crumbs[1]['label'] === 'Courses' && $crumbs[1]['url'] === '/courses');
        assert($crumbs[2]['label'] === 'Advanced Php' && $crumbs[2]['url'] === '/courses/advanced-php');
    }

    public function testQuickNotificationsHelper(): void {
        $notifications = NavigationService::getQuickNotifications();
        assert(!empty($notifications), "Quick notifications should not be empty");
        assert(isset($notifications[0]['title']), "Notification must have title");
        assert(isset($notifications[0]['unread']), "Notification must have unread flag");
    }

    public function testStaticNavigationAssetsExistAndContainBreakpoints(): void {
        $cssPath = dirname(__DIR__, 2) . '/public/css/navigation.css';
        $jsPath = dirname(__DIR__, 2) . '/public/js/navigation.js';

        assert(file_exists($cssPath), "navigation.css must exist");
        assert(file_exists($jsPath), "navigation.js must exist");

        $css = file_get_contents($cssPath);
        assert(str_contains($css, '--sidebar-width'), "CSS must define --sidebar-width");
        assert(str_contains($css, '@media (max-width: 1024px)'), "CSS must support tablet breakpoint");
        assert(str_contains($css, '@media (max-width: 768px)'), "CSS must support mobile breakpoint");
        assert(str_contains($css, '.mobile-bottom-nav'), "CSS must define mobile bottom bar");

        $js = file_get_contents($jsPath);
        assert(str_contains($js, 'initSidebar'), "JS must initialize sidebar");
        assert(str_contains($js, 'initMobileDrawer'), "JS must initialize mobile drawer");
        assert(str_contains($js, 'initDropdowns'), "JS must initialize dropdowns");
    }
}
