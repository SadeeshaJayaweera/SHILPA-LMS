<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\ActiveSession;
use App\Models\User;
use App\Services\ProfileService;

class ProfileController extends Controller {
    public function index(Request $request): Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to access your account profile.');
            return $this->redirect('/login');
        }

        $userId = $this->resolveUserId();
        $sessionId = session_id() ?: 'sess_' . md5((string)$userId);
        ActiveSession::record($userId, $sessionId, $request);

        $data = ProfileService::getProfileData($userId, $sessionId);
        $data['title'] = 'Profile & Account Settings — ' . config('app.name', 'SHILPA LMS');
        $data['currentSessionId'] = $sessionId;

        return $this->render('profile/index', $data);
    }

    public function update(Request $request): Response {
        if (!session()->has('user')) {
            return $this->unauthorizedResponse($request);
        }

        $userId = $this->resolveUserId();
        $file = $_FILES['avatar'] ?? null;
        $result = ProfileService::updateGeneralProfile($userId, $request->all(), $file);

        if (!$result['success']) {
            if ($request->isAjax()) {
                return json(['success' => false, 'error' => $result['error']], 422);
            }
            session()->flash('error', $result['error']);
            return $this->redirect('/profile');
        }

        // Update session user name
        session()->set('user', $result['user']->Full_Name);
        session()->set('user_name', $result['user']->Full_Name);

        if ($request->isAjax()) {
            return json([
                'success' => true,
                'message' => $result['message'],
                'user' => [
                    'name' => $result['user']->Full_Name,
                    'headline' => $result['profile']->headline,
                ],
            ]);
        }

        session()->flash('success', $result['message']);
        return $this->redirect('/profile');
    }

    public function updatePreferences(Request $request): Response {
        if (!session()->has('user')) {
            return $this->unauthorizedResponse($request);
        }

        $userId = $this->resolveUserId();
        $result = ProfileService::updatePreferences($userId, $request->all());

        if ($request->isAjax()) {
            return json($result);
        }

        session()->flash('success', $result['message']);
        return $this->redirect('/profile#preferences');
    }

    public function updatePassword(Request $request): Response {
        if (!session()->has('user')) {
            return $this->unauthorizedResponse($request);
        }

        $userId = $this->resolveUserId();
        $sessionId = session_id() ?: 'sess_' . md5((string)$userId);

        $current = (string)$request->input('current_password', '');
        $new = (string)$request->input('new_password', '');
        $confirm = (string)$request->input('confirm_password', '');

        $result = ProfileService::updatePassword($userId, $current, $new, $confirm, $sessionId);

        if (!$result['success']) {
            if ($request->isAjax()) {
                return json(['success' => false, 'error' => $result['error']], 422);
            }
            session()->flash('error', $result['error']);
            return $this->redirect('/profile#security');
        }

        if ($request->isAjax()) {
            return json(['success' => true, 'message' => $result['message']]);
        }

        session()->flash('success', $result['message']);
        return $this->redirect('/profile#security');
    }

    public function terminateSession(Request $request): Response {
        if (!session()->has('user')) {
            return $this->unauthorizedResponse($request);
        }

        $userId = $this->resolveUserId();
        $targetSessionId = (string)$request->input('session_id', '');

        $success = ProfileService::terminateSession($userId, $targetSessionId);

        if ($request->isAjax()) {
            return json(['success' => $success]);
        }

        session()->flash('success', 'Active session terminated.');
        return $this->redirect('/profile#security');
    }

    public function terminateOtherSessions(Request $request): Response {
        if (!session()->has('user')) {
            return $this->unauthorizedResponse($request);
        }

        $userId = $this->resolveUserId();
        $currentSessionId = session_id() ?: 'sess_' . md5((string)$userId);

        $count = ProfileService::terminateOtherSessions($userId, $currentSessionId);

        if ($request->isAjax()) {
            return json(['success' => true, 'terminated_count' => $count]);
        }

        session()->flash('success', "Signed out {$count} other device sessions.");
        return $this->redirect('/profile#security');
    }

    private function resolveUserId(): int {
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

    private function unauthorizedResponse(Request $request): Response {
        if ($request->isAjax()) {
            return json(['error' => 'Unauthorized'], 401);
        }
        session()->flash('error', 'Session expired. Please sign in.');
        return $this->redirect('/login');
    }
}
