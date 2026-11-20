<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Application;
use App\Database\MigrationRunner;

$app = new Application(dirname(__DIR__));
$app->bootstrap();

echo "Running SHILPA LMS Database Migrations...\n";
$runner = new MigrationRunner();
$executed = $runner->run();

if (empty($executed)) {
    echo "Nothing to migrate. Database is up to date.\n";
} else {
    foreach ($executed as $mig) {
        echo "✓ Migrated: $mig\n";
    }
    echo "Successfully migrated " . count($executed) . " migrations.\n";
}
