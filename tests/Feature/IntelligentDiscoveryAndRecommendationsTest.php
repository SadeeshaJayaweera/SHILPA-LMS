<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseBookmark;
use App\Models\Enrollment;
use App\Models\LearningPathway;
use App\Models\User;
use App\Services\CourseManagementService;
use App\Services\RecommendationService;
use App\Testing\TestCase;

class IntelligentDiscoveryAndRecommendationsTest extends TestCase {
    public function testRecommendationEngineBlendsAffinities(): void {
        $studentA = User::create([
            'Full_Name' => 'Ada Lovelace',
            'Email' => 'ada.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('Secret123!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);
        $studentB = User::create([
            'Full_Name' => 'Charles Babbage',
            'Email' => 'charles.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('Secret123!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);

        $course1 = CourseManagementService::createCourse([
            'title' => 'Analytical Engine Architecture ' . uniqid(),
            'status' => 'published',
        ]);
        $course2 = CourseManagementService::createCourse([
            'title' => 'Difference Engine Mechanics ' . uniqid(),
            'status' => 'published',
        ]);
        $course3 = CourseManagementService::createCourse([
            'title' => 'Algorithmic Notation ' . uniqid(),
            'status' => 'published',
        ]);

        $cId1 = (int)$course1->C_ID;
        $cId2 = (int)$course2->C_ID;
        $cId3 = (int)$course3->C_ID;

        // Student A is enrolled in Course 1
        Enrollment::create([
            'user_id' => (int)$studentA->id,
            'course_id' => $cId1,
            'status' => 'active',
            'role' => 'student',
            'enrolled_at' => date('Y-m-d H:i:s'),
        ]);

        // Student B is enrolled in Course 1 and Course 2
        Enrollment::create([
            'user_id' => (int)$studentB->id,
            'course_id' => $cId1,
            'status' => 'active',
            'role' => 'student',
            'enrolled_at' => date('Y-m-d H:i:s'),
        ]);
        Enrollment::create([
            'user_id' => (int)$studentB->id,
            'course_id' => $cId2,
            'status' => 'active',
            'role' => 'student',
            'enrolled_at' => date('Y-m-d H:i:s'),
        ]);

        // Get recommendations for Student A
        $recommendations = RecommendationService::getRecommendedCoursesForUser((int)$studentA->id, 5);
        assert(!empty($recommendations), 'Recommendations should not be empty');

        // Course 1 should NOT be in recommendations because Student A is already enrolled
        $recCourseIds = array_map(fn($r) => (int)$r['course']->C_ID, $recommendations);
        assert(!in_array($cId1, $recCourseIds, true), 'Enrolled course must be excluded from recommendations');

        // Course 2 should be recommended due to collaborative filtering from peer Student B
        assert(in_array($cId2, $recCourseIds, true), 'Peer enrolled course should be recommended');
    }

    public function testCourseBookmarkToggleAndRetrieval(): void {
        $student = User::create([
            'Full_Name' => 'Grace Hopper',
            'Email' => 'grace.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('Compiler1952!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);
        $course = CourseManagementService::createCourse([
            'title' => 'COBOL Compilers ' . uniqid(),
            'status' => 'published',
        ]);

        $uid = (int)$student->id;
        $cid = (int)$course->C_ID;

        assert(!RecommendationService::isBookmarked($uid, $cid), 'Should not be bookmarked initially');

        // 1. Toggle ON
        $added = RecommendationService::toggleBookmark($uid, $cid);
        assert($added === true, 'Toggle ON should return true');
        assert(RecommendationService::isBookmarked($uid, $cid), 'Should now be bookmarked');

        // 2. Retrieve bookmarks list
        $bookmarks = RecommendationService::getUserBookmarks($uid);
        assert(count($bookmarks) === 1, 'Should have 1 bookmark');
        assert((int)$bookmarks[0]['course']->C_ID === $cid, 'Bookmarked course must match');

        // 3. Toggle OFF
        $removed = RecommendationService::toggleBookmark($uid, $cid);
        assert($removed === false, 'Toggle OFF should return false');
        assert(!RecommendationService::isBookmarked($uid, $cid), 'Should no longer be bookmarked');

        $emptyBookmarks = RecommendationService::getUserBookmarks($uid);
        assert(empty($emptyBookmarks), 'Bookmarks should be empty after removal');
    }

    public function testCuratedLearningPathways(): void {
        $pathways = RecommendationService::getPathways();
        assert(count($pathways) >= 3, 'Default learning pathways must be seeded');

        $slug = 'full-stack-web-architect';
        $track = RecommendationService::getPathway($slug);
        assert($track !== null, 'Full-stack pathway must be resolvable by slug');
        assert($track->target_role === 'Full-Stack Software Engineer');

        $courses = $track->courses();
        assert(is_array($courses));
    }

    public function testDiscoveryAndRecommendationEndpoints(): void {
        // Recommendations index
        $res = $this->get('/courses/recommended');
        assert($res->getStatusCode() === 200, 'Recommendations page should return 200');
        assert(str_contains($res->getContent(), 'Personalized Recommendations'));

        // Pathways list
        $resPw = $this->get('/pathways');
        assert($resPw->getStatusCode() === 200, 'Pathways page should return 200');
        assert(str_contains($resPw->getContent(), 'Curated Learning Pathways'));

        // Pathway detail
        $resDetail = $this->get('/pathways/full-stack-web-architect');
        assert($resDetail->getStatusCode() === 200, 'Pathway detail should return 200');
        assert(str_contains($resDetail->getContent(), 'Full-Stack Modern Web Engineering'));

        // API Recommendations
        $resApi = $this->get('/api/courses/recommendations?limit=4');
        assert($resApi->getStatusCode() === 200, 'API recommendations should return 200');
        $json = json_decode($resApi->getContent(), true);
        assert(is_array($json));
        assert($json['status'] === 'success');
        assert(isset($json['recommendations']));
    }
}
