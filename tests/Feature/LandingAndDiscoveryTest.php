<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Course;
use App\Testing\TestCase;

class LandingAndDiscoveryTest extends TestCase {
    public function testLandingPageRendersHeroAndNavigation(): void {
        Course::create([
            'C_NAME' => 'Fullstack Modern PHP 8.2',
            'C_CARD_DESCRIPTION' => 'Architecture and engineering course',
            'C_DESCRIPTION' => 'In-depth full curriculum for enterprise web systems.',
            'C_CARD_IMAGE' => 'Images/allCourses/kiddo.png',
            'C_DONE_BY' => 'Sadeesha Jayaweera',
            'C_LINK' => 'https://shilpalms.com/php',
            'TOP_COURSE' => 'Yes',
            'C_ADD_DATE' => date('Y-m-d H:i:s'),
        ]);

        $response = $this->get('/');
        $response->assertOk()
                 ->assertSee('Master Modern Computing')
                 ->assertSee('Top Rated Courses')
                 ->assertSee('Explore Course Categories')
                 ->assertSee('Fullstack Modern PHP 8.2')
                 ->assertSee('Sadeesha Jayaweera');
    }

    public function testCourseDiscoverySearchAndFilter(): void {
        Course::create([
            'C_NAME' => 'Advanced Algorithms',
            'C_CARD_DESCRIPTION' => 'Algorithms & computational systems',
            'C_DESCRIPTION' => 'Graph algorithms and dynamic programming.',
            'C_CARD_IMAGE' => '',
            'C_DONE_BY' => 'Faculty of Computing',
            'C_LINK' => '',
            'TOP_COURSE' => 'No',
            'C_ADD_DATE' => date('Y-m-d H:i:s'),
        ]);

        $response = $this->get('/courses?q=Algorithms');
        $response->assertOk()
                 ->assertSee('Course Catalog & Discovery')
                 ->assertSee('Advanced Algorithms');
    }

    public function testCourseDetailView(): void {
        $course = Course::create([
            'C_NAME' => 'Cloud Security Fundamentals',
            'C_CARD_DESCRIPTION' => 'Identity, RBAC, and secure systems',
            'C_DESCRIPTION' => 'Detailed syllabus explaining security principles.',
            'C_CARD_IMAGE' => '',
            'C_DONE_BY' => 'Security Team',
            'C_LINK' => 'https://example.com/sec',
            'TOP_COURSE' => 'Yes',
            'C_ADD_DATE' => date('Y-m-d H:i:s'),
        ]);

        $response = $this->get('/courses/' . $course->C_ID);
        $response->assertOk()
                 ->assertSee('Cloud Security Fundamentals')
                 ->assertSee('Security Team')
                 ->assertSee('About This Course & Syllabus Overview');
    }

    public function testNonExistentCourseReturns404(): void {
        $response = $this->get('/courses/99999');
        $response->assertNotFound()
                 ->assertSee('404');
    }
}
