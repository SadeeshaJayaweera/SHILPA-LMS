<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\Course;
use App\Models\User;
use App\Services\RecommendationService;

class DiscoveryAndRecommendationController extends Controller {
    /**
     * Helper to resolve current authenticated user ID
     */
    protected function getUserId(): int {
        $userId = (int)session()->get('user_id', 0);
        if ($userId <= 0) {
            $email = (string)session()->get('email', '');
            if ($email !== '') {
                $user = User::findByEmail($email);
                if ($user) {
                    $userId = (int)$user->UserId;
                    session()->set('user_id', $userId);
                }
            }
        }
        return $userId;
    }

    /**
     * Show personalized recommendations page
     */
    public function recommendations(Request $request): Response {
        $userId = $this->getUserId();
        $recommendations = RecommendationService::getRecommendedCoursesForUser($userId, 9);
        $pathways = RecommendationService::getPathways();

        $data = [
            'recommendations' => $recommendations,
            'pathways' => array_slice($pathways, 0, 3),
            'userId' => $userId,
            'title' => 'Personalized Course Recommendations — SHILPA LMS',
        ];

        return $this->render('courses/discovery/recommendations', array_merge($data, ['data' => $data]));
    }

    /**
     * Show all curated learning pathways
     */
    public function pathways(Request $request): Response {
        $pathways = RecommendationService::getPathways();

        $data = [
            'pathways' => $pathways,
            'title' => 'Curated Career Learning Pathways — SHILPA LMS',
        ];

        return $this->render('courses/discovery/pathways', array_merge($data, ['data' => $data]));
    }

    /**
     * Show single pathway detail
     */
    public function pathwayDetail(Request $request, $slug): Response {
        $pathway = RecommendationService::getPathway((string)$slug);
        if (!$pathway) {
            session()->flash('error', 'Learning Pathway not found.');
            return $this->redirect('/pathways');
        }

        $userId = $this->getUserId();
        $courses = $pathway->courses();

        $data = [
            'pathway' => $pathway,
            'courses' => $courses,
            'userId' => $userId,
            'title' => $pathway->title . ' — Learning Pathway',
        ];

        return $this->render('courses/discovery/pathway_detail', array_merge($data, ['data' => $data]));
    }

    /**
     * Toggle course bookmark
     */
    public function toggleBookmark(Request $request, $id): Response {
        if (!session()->has('user')) {
            if ($request->header('Accept') === 'application/json' || $request->isJson()) {
                return $this->json(['error' => 'Authentication required'], 401);
            }
            session()->flash('error', 'Please log in to bookmark courses.');
            return $this->redirect('/login');
        }

        $userId = $this->getUserId();
        $courseId = (int)$id;

        $course = Course::find($courseId);
        if (!$course) {
            if ($request->header('Accept') === 'application/json' || $request->isJson()) {
                return $this->json(['error' => 'Course not found'], 404);
            }
            session()->flash('error', 'Course not found.');
            return $this->redirect('/courses');
        }

        $isBookmarked = RecommendationService::toggleBookmark($userId, $courseId);

        if ($request->header('Accept') === 'application/json' || $request->isJson()) {
            return $this->json([
                'success' => true,
                'course_id' => $courseId,
                'bookmarked' => $isBookmarked,
                'message' => $isBookmarked ? 'Course added to bookmarks' : 'Course removed from bookmarks',
            ]);
        }

        session()->flash('success', $isBookmarked ? 'Added to your bookmarked courses.' : 'Removed from bookmarks.');
        return $this->redirect('/courses/' . $courseId);
    }

    /**
     * List user bookmarks
     */
    public function bookmarks(Request $request): Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please log in to view your bookmarks.');
            return $this->redirect('/login');
        }

        $userId = $this->getUserId();
        $bookmarks = RecommendationService::getUserBookmarks($userId);

        $data = [
            'bookmarks' => $bookmarks,
            'title' => 'My Bookmarked Courses — SHILPA LMS',
        ];

        return $this->render('courses/discovery/bookmarks', array_merge($data, ['data' => $data]));
    }

    /**
     * JSON API recommendations endpoint
     */
    public function apiRecommendations(Request $request): Response {
        $userId = $this->getUserId();
        $limit = max(1, min(20, (int)($request->query('limit') ?? 6)));
        $recs = RecommendationService::getRecommendedCoursesForUser($userId, $limit);

        $formatted = array_map(function ($item) {
            /** @var Course $course */
            $course = $item['course'];
            return [
                'id' => (int)$course->C_ID,
                'name' => (string)$course->C_NAME,
                'description' => (string)$course->C_CARD_DESCRIPTION,
                'instructor' => (string)$course->C_DONE_BY,
                'image' => (string)$course->C_CARD_IMAGE,
                'reason' => (string)$item['reason'],
                'score' => (float)$item['score'],
                'is_bookmarked' => (bool)$item['is_bookmarked'],
            ];
        }, $recs);

        return $this->json([
            'status' => 'success',
            'count' => count($formatted),
            'recommendations' => $formatted,
        ]);
    }
}
