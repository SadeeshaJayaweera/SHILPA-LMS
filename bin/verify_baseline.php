<?php
declare(strict_types=1);

$dir = dirname(__DIR__);
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
);

$phpFiles = [];
foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getPathname();
        if (str_contains($path, '/vendor/') || str_contains($path, '/node_modules/')) {
            continue;
        }
        $phpFiles[] = $path;
    }
}

$errors = 0;
foreach ($phpFiles as $phpFile) {
    $output = [];
    $returnCode = 0;
    exec('php -l ' . escapeshellarg($phpFile) . ' 2>&1', $output, $returnCode);
    if ($returnCode !== 0) {
        echo "Syntax error in $phpFile: " . implode("\n", $output) . "\n";
        $errors++;
    }
}

if ($errors === 0) {
    echo "Baseline syntax verification passed for " . count($phpFiles) . " PHP files.\n";
    exit(0);
}

echo "Found $errors syntax errors during baseline check.\n";
exit(1);
