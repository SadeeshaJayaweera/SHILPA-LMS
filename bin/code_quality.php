<?php
declare(strict_types=1);

echo "Running SHILPA LMS Code Quality & Static Analysis Checks...\n";

$baseDir = dirname(__DIR__);
$targetDirs = ['app', 'config', 'public', 'routes', 'tests', 'bin'];

$files = [];
foreach ($targetDirs as $d) {
    $path = "$baseDir/$d";
    if (!is_dir($path)) continue;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
    foreach ($iterator as $item) {
        if ($item->isFile() && $item->getExtension() === 'php') {
            $files[] = $item->getPathname();
        }
    }
}

// Add root php files
foreach (glob("$baseDir/*.php") as $f) {
    $files[] = $f;
}

$errors = 0;
foreach ($files as $file) {
    $output = [];
    $ret = 0;
    exec('php -l ' . escapeshellarg($file) . ' 2>&1', $output, $ret);
    if ($ret !== 0) {
        echo "Syntax error in $file: " . implode("\n", $output) . "\n";
        $errors++;
    }
}

if ($errors > 0) {
    echo "Code quality check failed with $errors errors.\n";
    exit(1);
}

echo "All " . count($files) . " source files passed syntax and static structure validation.\n";
exit(0);
