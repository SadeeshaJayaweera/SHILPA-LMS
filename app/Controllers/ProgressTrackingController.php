<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\Course;
use App\Models\Lesson;
use App\Services\ProgressTrackingService;

class ProgressTrackingController extends Controller {
    public function toggle(Request $request, $courseId, $lessonId): Response {
        if (!session()->has('user')) {
            if ($request->isAjax()) {
                return json(['success' => false, 'error' => 'Authentication required.'], 401);
            }
            session()->flash('error', 'Please sign in to track learning progress.');
            return $this->redirect('/login');
        }

        $userId = (int)session()->get('user_id', 0);
        $cId = (int)$courseId;
        $lId = (int)$lessonId;

        $timeSpent = (int)$request->input('time_spent', 0);
        $result = ProgressTrackingService::toggleLessonCompletion($userId, $cId, $lId, $timeSpent);

        if ($request->isAjax()) {
            return json([
                'success' => true,
                'is_completed' => $result['is_completed'],
                'progress_percent' => $result['progress_percent'],
                'completed_lessons' => $result['completed_lessons'],
                'total_lessons' => $result['total_lessons'],
                'is_course_completed' => $result['is_course_completed'],
            ]);
        }

        $msg = $result['is_completed'] ? 'Lesson marked as completed.' : 'Lesson marked as incomplete.';
        session()->flash('success', $msg);
        return $this->redirect("/courses/{$cId}/learn/{$lId}");
    }

    public function progress(Request $request, $courseId): Response {
        $userId = (int)session()->get('user_id', 0);
        if ($userId <= 0) {
            return json(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        $progress = ProgressTrackingService::getCourseProgress($userId, (int)$courseId);
        return json(['success' => true, 'progress' => $progress]);
    }
}
