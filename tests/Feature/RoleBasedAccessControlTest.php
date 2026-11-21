<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Core\Request;
use App\Core\Response;
use App\Middleware\RequirePermission;
use App\Models\User;
use App\Security\AuthorizationException;
use App\Security\Gate;
use App\Testing\TestCase;

class RoleBasedAccessControlTest extends TestCase {
    public function testGateMatrixForStudent(): void {
        $student = new User([
            'id' => 101,
            'Full_Name' => 'Kasun Student',
            'Email' => 'kasun.student@shilpa.lk',
            'user_type' => 'student',
        ]);

        assert(Gate::allows('course.view', $student) === true, "Student should be allowed to view courses");
        assert(Gate::allows('course.enroll', $student) === true, "Student should be allowed to enroll in courses");
        assert(Gate::allows('assignment.submit', $student) === true, "Student should be allowed to submit assignments");
        assert(Gate::allows('course.create', $student) === false, "Student must not be allowed to create courses");
        assert(Gate::allows('course.delete', $student) === false, "Student must not be allowed to delete courses");
        assert(Gate::denies('course.create', $student) === true, "Gate::denies should return true for course.create");
    }

    public function testGateMatrixForLecturer(): void {
        $lecturer = new User([
            'id' => 102,
            'Full_Name' => 'Dr. Perera',
            'Email' => 'dr.perera@shilpa.lk',
            'user_type' => 'lecturer',
        ]);

        assert(Gate::allows('course.create', $lecturer) === true, "Lecturer should be allowed to create courses");
        assert(Gate::allows('course.edit', $lecturer) === true, "Lecturer should be allowed to edit courses");
        assert(Gate::allows('grade.submit', $lecturer) === true, "Lecturer should be allowed to submit grades");
        assert($lecturer->can('course.create') === true, "User::can should return true for lecturer creating courses");
        assert($lecturer->cannot('system.configure') === true, "Lecturer cannot configure system");
    }

    public function testAdminUniversalBypass(): void {
        $admin = new User([
            'id' => 103,
            'Full_Name' => 'Admin Operator',
            'Email' => 'admin.operator@shilpa.lk',
            'user_type' => 'a',
        ]);

        assert(Gate::allows('anything.arbitrary', $admin) === true, "Admin should have universal access bypass");
        assert($admin->can('course.delete') === true, "Admin can delete courses");
        assert($admin->can('users.manage') === true, "Admin can manage users");
    }

    public function testGateAuthorizeThrowsException(): void {
        $student = new User([
            'id' => 104,
            'Full_Name' => 'Nimal Student',
            'Email' => 'nimal@shilpa.lk',
            'user_type' => 'student',
        ]);

        $exceptionCaught = false;
        try {
            Gate::authorize('course.create', $student);
        } catch (AuthorizationException $e) {
            $exceptionCaught = true;
            assert(str_contains($e->getMessage(), 'course.create'), "Exception must mention required permission");
        }

        assert($exceptionCaught === true, "Gate::authorize must throw AuthorizationException when denied");
    }

    public function testUserRoleChecking(): void {
        $student = new User(['id' => 105, 'user_type' => 'student']);
        $lecturer = new User(['id' => 106, 'user_type' => 'l']);
        $admin = new User(['id' => 107, 'user_type' => 'a']);

        assert($student->hasRole('student') === true, "Student should have role student");
        assert($student->hasRole('admin') === false, "Student should not have role admin");

        assert($lecturer->hasRole('lecturer') === true, "Lecturer should have role lecturer");
        assert($admin->hasRole('admin') === true, "Admin should have role admin");
        assert($admin->hasRole(['admin', 'superadmin']) === true, "Admin should match array of roles");
    }

    public function testCustomGateDefinition(): void {
        Gate::define('course.publish', function (User $user, $courseOwnerId) {
            return $user->id === $courseOwnerId;
        });

        $lecturer1 = new User(['id' => 201, 'user_type' => 'lecturer']);
        $lecturer2 = new User(['id' => 202, 'user_type' => 'lecturer']);

        assert(Gate::allows('course.publish', $lecturer1, 201) === true, "Owner lecturer should be allowed to publish");
        assert(Gate::allows('course.publish', $lecturer2, 201) === false, "Non-owner lecturer should be denied");
    }

    public function testRequirePermissionMiddleware(): void {
        session()->clear();

        // 1. Unauthenticated -> redirects to login
        $middleware = new RequirePermission('course.create');
        $request = Request::create('GET', '/courses/create');
        $response = $middleware->handle($request, fn($req) => new Response('OK'));

        assert($response->getStatusCode() === 302, "Unauthenticated request should redirect");

        // 2. Authenticated as student lacking permission -> returns 403
        $student = User::create([
            'Full_Name' => 'Perm Test Student',
            'Email' => 'permtest@shilpa.lk',
            'Password' => 'Hash1234',
            'user_type' => 'student',
        ]);
        session()->set('user', $student->Full_Name);
        session()->set('user_id', $student->id);
        session()->set('email', $student->Email);
        session()->set('user_type', 'student');

        $response403 = $middleware->handle($request, fn($req) => new Response('OK'));
        assert($response403->getStatusCode() === 403, "Student should receive 403 Forbidden");
        assert(str_contains($response403->getContent(), 'Access Denied'), "View should contain Access Denied");

        // 3. Authenticated as lecturer with permission -> passes through
        $lecturer = User::create([
            'Full_Name' => 'Perm Test Lecturer',
            'Email' => 'permlec@shilpa.lk',
            'Password' => 'Hash1234',
            'user_type' => 'lecturer',
        ]);
        session()->set('user', $lecturer->Full_Name);
        session()->set('user_id', $lecturer->id);
        session()->set('email', $lecturer->Email);
        session()->set('user_type', 'lecturer');

        $responsePassed = $middleware->handle($request, fn($req) => new Response('Allowed OK', 200));
        assert($responsePassed->getStatusCode() === 200, "Lecturer should pass through with 200");
        assert($responsePassed->getContent() === 'Allowed OK', "Should execute next callable");
    }
}
