<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Application;
use App\Core\Request;

echo "Running Architecture & Runtime Verification Tests...\n";

$app = new Application(dirname(__DIR__));
$app->bootstrap();

// Test 1: Config loading
assert(config('app.name') === 'SHILPA LMS', "Config app.name should match");
assert(config('database.default') === 'mysql', "Default DB connection should be mysql");
echo "✓ Config repository test passed\n";

// Test 2: Healthcheck Route
$healthRequest = new Request('GET', '/health');
$healthResponse = $app->handle($healthRequest);
assert($healthResponse->getStatusCode() === 200, "Health status code should be 200");
$healthData = json_decode($healthResponse->getContent(), true);
assert($healthData['status'] === 'healthy', "Health status should be healthy");
assert($healthData['php'] === PHP_VERSION, "PHP version should match runtime");
echo "✓ Route dispatch (/health) test passed: " . json_encode($healthData) . "\n";

// Test 3: Home Route View Rendering
$homeRequest = new Request('GET', '/');
$homeResponse = $app->handle($homeRequest);
assert($homeResponse->getStatusCode() === 200, "Home status code should be 200");
assert(str_contains($homeResponse->getContent(), 'SHILPA'), "Home content should contain SHILPA");
assert(str_contains($homeResponse->getContent(), 'IT Innovators'), "Home content should contain hero title");
echo "✓ View rendering and home route test passed\n";

// Test 4: 404 Route Handling
$missingRequest = new Request('GET', '/non-existent-endpoint');
$missingResponse = $app->handle($missingRequest);
assert($missingResponse->getStatusCode() === 404, "Unknown route should return 404");
assert(str_contains($missingResponse->getContent(), '404'), "Missing content should contain 404");
echo "✓ 404 error handler test passed\n";

// Test 5: Verify all PHP files syntax
$verifyScript = dirname(__DIR__) . '/bin/verify_baseline.php';
$output = [];
$returnCode = 0;
exec('php ' . escapeshellarg($verifyScript), $output, $returnCode);
assert($returnCode === 0, "All PHP files should pass syntax check");
echo "✓ All files syntax check passed: " . implode(" ", $output) . "\n";

echo "ALL ARCHITECTURE & RUNTIME TESTS PASSED SUCCESSFULLY!\n";
