<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Application;
use App\Database\Connection;
use App\Database\MigrationRunner;
use App\Database\QueryBuilder;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;

echo "Running Database Architecture & Access Tests...\n";

// Force test environment with in-memory SQLite
define('SHILPA_TEST_MODE', true);

$testPdo = new PDO('sqlite::memory:', null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
$testPdo->exec('PRAGMA foreign_keys = ON;');
Connection::set('mysql', $testPdo);

// 1. Run Migrations
$runner = new MigrationRunner($testPdo, dirname(__DIR__) . '/database/migrations');
$executed = $runner->run();
assert(count($executed) === 3, "Expected 3 migrations executed, got " . count($executed));
echo "✓ Database migrations executed: " . implode(', ', $executed) . "\n";

// 2. Test User Model CRUD & Password Security
$user = User::create([
    'Full_Name' => 'Sadeesha Jayaweera',
    'Email' => 'sadeesha@shilpa.lk',
    'Password' => password_hash('SecurePass123!', PASSWORD_DEFAULT),
    'user_type' => 'a',
]);
assert($user->UserId > 0, "User ID should be generated");
assert($user->isAdmin() === true, "User should have admin role");
assert($user->isStudent() === false, "Admin user should not be student");
assert($user->verifyPassword('SecurePass123!') === true, "Password should verify correctly");
assert($user->verifyPassword('WrongPass') === false, "Incorrect password should fail verification");
echo "✓ User model CRUD and authentication verification passed\n";

// 3. Test Course Model CRUD & Query Scopes
$course1 = Course::create([
    'C_NAME' => 'Web Development with PHP',
    'C_CARD_DESCRIPTION' => 'Learn modern web engineering',
    'C_DESCRIPTION' => 'Comprehensive full-stack course with PHP and MySQL',
    'C_CARD_IMAGE' => 'Images/allCourses/kiddo.png',
    'C_DONE_BY' => 'Sadeesha Jayaweera',
    'C_LINK' => 'https://shilpalms.com/web-dev',
    'TOP_COURSE' => 'Yes',
    'C_ADD_DATE' => date('Y-m-d H:i:s'),
]);
assert($course1->C_ID > 0, "Course ID should be generated");

$course2 = Course::create([
    'C_NAME' => 'Data Structures and Algorithms',
    'C_CARD_DESCRIPTION' => 'Core computing fundamentals',
    'C_DESCRIPTION' => 'Master algorithmic efficiency and problem solving',
    'C_CARD_IMAGE' => 'Images/allCourses/kiddo.png',
    'C_DONE_BY' => 'Faculty of Computing',
    'C_LINK' => 'https://shilpalms.com/dsa',
    'TOP_COURSE' => 'No',
    'C_ADD_DATE' => date('Y-m-d H:i:s'),
]);

$topCourses = Course::getTopCourses(2);
assert(count($topCourses) >= 1, "Top courses query should return results");

$searchResults = Course::search('Algorithms');
assert(count($searchResults) === 1, "Search for 'Algorithms' should return 1 course");
assert($searchResults[0]->C_NAME === 'Data Structures and Algorithms', "Search result name mismatch");
echo "✓ Course model CRUD, scopes, and search passed\n";

// 4. Test Enrollment Model
$enrollment = Enrollment::create([
    'user_id' => (int)$user->UserId,
    'course_id' => (int)$course1->C_ID,
    'status' => 'active',
    'progress_percent' => 45,
    'enrolled_at' => date('Y-m-d H:i:s'),
]);
assert($enrollment->id > 0, "Enrollment ID should be generated");
assert(Enrollment::isEnrolled((int)$user->UserId, (int)$course1->C_ID) === true, "User should be enrolled");
assert(Enrollment::isEnrolled((int)$user->UserId, 9999) === false, "User should not be enrolled in 9999");
echo "✓ Enrollment relational integrity test passed\n";

// 5. Test Parameterized Query Builder & SQL Injection Immunity
$maliciousInput = "' OR '1'='1";
$injected = User::query()->where('Email', $maliciousInput)->first();
assert($injected === null, "SQL Injection attempt must return null with parameterized queries");
echo "✓ SQL Injection immunity test passed\n";

// 6. Test Database Transactions
$txResult = Connection::transaction(function ($pdo) {
    Course::create([
        'C_NAME' => 'Transactional Test Course',
        'C_CARD_DESCRIPTION' => 'Testing transactions',
        'C_DESCRIPTION' => 'Test',
        'C_CARD_IMAGE' => '',
        'C_DONE_BY' => 'Test Runner',
        'C_LINK' => '',
        'TOP_COURSE' => 'No',
        'C_ADD_DATE' => date('Y-m-d H:i:s'),
    ]);
    return true;
});
assert($txResult === true, "Transaction should commit successfully");

try {
    Connection::transaction(function ($pdo) {
        Course::create([
            'C_NAME' => 'Will Rollback',
            'C_CARD_DESCRIPTION' => 'Rollback',
            'C_DESCRIPTION' => 'Test',
            'C_CARD_IMAGE' => '',
            'C_DONE_BY' => 'Test Runner',
            'C_LINK' => '',
            'TOP_COURSE' => 'No',
            'C_ADD_DATE' => date('Y-m-d H:i:s'),
        ]);
        throw new RuntimeException("Simulated error to trigger rollback");
    });
} catch (RuntimeException $e) {
    // Expected rollback
}
$rolledBack = Course::query()->where('C_NAME', 'Will Rollback')->first();
assert($rolledBack === null, "Rolled-back record must not exist in database");
echo "✓ Database transaction commit and rollback tests passed\n";

// 7. Verify all PHP files syntax
$verifyScript = dirname(__DIR__) . '/bin/verify_baseline.php';
$output = [];
$returnCode = 0;
exec('php ' . escapeshellarg($verifyScript), $output, $returnCode);
assert($returnCode === 0, "All PHP files should pass syntax check");
echo "✓ All files syntax check passed: " . implode(" ", $output) . "\n";

echo "ALL DATABASE ARCHITECTURE & ACCESS TESTS PASSED SUCCESSFULLY!\n";
