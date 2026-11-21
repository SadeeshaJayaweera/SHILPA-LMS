<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Models\User;
use App\Security\Gate;

class RequirePermission {
    private string $permission;

    public function __construct(string $permission) {
        $this->permission = $permission;
    }

    public function handle(Request $request, callable $next): Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to proceed.');
            return Response::redirect('/login');
        }

        $userId = (int)session()->get('user_id', 0);
        $user = $userId > 0 ? User::find($userId) : User::findByEmail((string)session()->get('email', ''));

        if (!$user || !Gate::allows($this->permission, $user)) {
            if ($request->isAjax()) {
                return Response::json([
                    'error' => 'Forbidden',
                    'message' => "Insufficient permissions for '{$this->permission}'.",
                ], 403);
            }

            return new Response(
                view('errors/403', [
                    'title' => '403 Forbidden — Access Denied',
                    'permission' => $this->permission,
                ])->getContent(),
                403
            );
        }

        return $next($request);
    }
}
