<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Services\CourseWorkspaceService;

class CourseWorkspaceController extends Controller {
    public function show(Request $request, $id): Response {
        $userId = $this->resolveUserId();
        $courseId = (int)$id;

        try {
            $data = CourseWorkspaceService::getWorkspaceOverview($courseId, $userId);
        } catch (\Throwable $e) {
            session()->flash('error', 'Course not found.');
            return $this->redirect('/courses');
        }

        $data['title'] = "{$data['course']->C_NAME} — Learning Workspace";
        return $this->render('courses/workspace/show', $data);
    }

    public function learn(Request $request, $courseId, $lessonId): Response {
        $userId = $this->resolveUserId();
        $cId = (int)$courseId;
        $lId = (int)$lessonId;

        try {
            $data = CourseWorkspaceService::getLearningTheater($cId, $lId, $userId);
        } catch (\Throwable $e) {
            session()->flash('error', 'Lesson not found.');
            return $this->redirect("/courses/{$cId}/workspace");
        }

        // Access check: If not free preview and not enrolled, prompt to enroll
        if (!$data['lesson']->isFreePreview() && !$data['isEnrolled']) {
            session()->flash('error', 'This lecture requires active course enrollment.');
            return $this->redirect("/courses/{$cId}/workspace");
        }

        $data['title'] = "{$data['lesson']->title} — {$data['course']->C_NAME}";
        return $this->render('courses/workspace/learn', $data);
    }

    private function resolveUserId(): ?int {
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
        return $userId > 0 ? $userId : null;
    }
}
