<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Course;
use App\Testing\TestCase;

class CourseTest extends TestCase {
    public function testCourseCreationAndSearch(): void {
        Course::create([
            'C_NAME' => 'Object Oriented PHP 8.2',
            'C_CARD_DESCRIPTION' => 'OOP principles in modern PHP',
            'C_DESCRIPTION' => 'Deep dive into classes, interfaces, and design patterns',
            'C_CARD_IMAGE' => 'Images/allCourses/kiddo.png',
            'C_DONE_BY' => 'Isuru Bandara',
            'C_LINK' => 'https://shilpalms.com/oop-php',
            'TOP_COURSE' => 'Yes',
            'C_ADD_DATE' => date('Y-m-d H:i:s'),
        ]);

        $results = Course::search('Object Oriented');
        assert(count($results) === 1, "Should find 1 matching course");
        assert($results[0]->C_NAME === 'Object Oriented PHP 8.2');

        $top = Course::getTopCourses(1);
        assert(count($top) === 1, "Should return top course");
    }

    public function testCourseUpdateAndDelete(): void {
        $course = Course::create([
            'C_NAME' => 'Temporary Course',
            'C_CARD_DESCRIPTION' => 'To be deleted',
            'C_DESCRIPTION' => 'Desc',
            'C_CARD_IMAGE' => '',
            'C_DONE_BY' => 'Tester',
            'C_LINK' => '',
            'TOP_COURSE' => 'No',
            'C_ADD_DATE' => date('Y-m-d H:i:s'),
        ]);

        $courseId = $course->C_ID;
        $course->C_NAME = 'Updated Course Name';
        $course->save();

        $refreshed = Course::find($courseId);
        assert($refreshed->C_NAME === 'Updated Course Name', "Name should be updated");

        $refreshed->delete();
        assert(Course::find($courseId) === null, "Course should be deleted");
    }
}
