<?php
declare(strict_types=1);

namespace App\Testing;

use App\Core\Application;
use App\Core\Request;
use App\Database\Connection;
use App\Database\MigrationRunner;
use App\Models\User;
use PDO;

abstract class TestCase {
    protected Application $app;
    protected PDO $pdo;
    protected ?User $authenticatedUser = null;

    public function setUp(): void {
        if (!defined('SHILPA_TEST_MODE')) {
            define('SHILPA_TEST_MODE', true);
        }

        $this->app = new Application(dirname(__DIR__, 2));
        $this->app->bootstrap();

        $this->pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $this->pdo->exec('PRAGMA foreign_keys = ON;');
        Connection::set('mysql', $this->pdo);

        $runner = new MigrationRunner($this->pdo, dirname(__DIR__, 2) . '/database/migrations');
        $runner->run();
    }

    public function tearDown(): void {
        Connection::reset();
        $this->authenticatedUser = null;
    }

    public function actingAs(User $user): self {
        $this->authenticatedUser = $user;
        $_SESSION['user'] = 'yes';
        $_SESSION['email'] = $user->Email;
        $_SESSION['user_id'] = $user->UserId;
        $_SESSION['user_role'] = $user->user_type;
        return $this;
    }

    public function get(string $uri, array $headers = []): TestResponse {
        $request = new Request('GET', $uri, [], [], [], [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => $uri,
        ]);
        $response = $this->app->handle($request);
        return new TestResponse($response);
    }

    public function post(string $uri, array $data = [], array $headers = []): TestResponse {
        $request = new Request('POST', $uri, [], $data, [], [
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => $uri,
        ]);
        $response = $this->app->handle($request);
        return new TestResponse($response);
    }

    public function assertTrue(bool $condition, string $message = ''): void {
        assert($condition === true, $message ?: 'Expected true');
    }

    public function assertFalse(bool $condition, string $message = ''): void {
        assert($condition === false, $message ?: 'Expected false');
    }

    public function assertSame(mixed $expected, mixed $actual, string $message = ''): void {
        assert($expected === $actual, $message ?: "Expected $expected but got $actual");
    }

    public function assertNotNull(mixed $actual, string $message = ''): void {
        assert($actual !== null, $message ?: 'Expected not null');
    }

    public function assertFileExists(string $path, string $message = ''): void {
        assert(file_exists($path), $message ?: "File does not exist: $path");
    }

    public function assertStringContainsString(string $needle, string $haystack, string $message = ''): void {
        assert(str_contains($haystack, $needle), $message ?: "Expected string to contain '$needle'");
    }

    public function assertMatchesRegularExpression(string $pattern, string $string, string $message = ''): void {
        assert(preg_match($pattern, $string) === 1, $message ?: "Expected string to match pattern '$pattern'");
    }
}
