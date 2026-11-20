<?php

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

function getDbConnect() {
    $servername = function_exists('env') ? env('DB_HOST', 'localhost') : 'localhost';
    $username = function_exists('env') ? env('DB_USERNAME', 'root') : 'root';
    $password = function_exists('env') ? env('DB_PASSWORD', '') : '';
    $dbname = function_exists('env') ? env('DB_DATABASE', 'shilpa') : 'shilpa';
    $port = function_exists('env') ? (int)env('DB_PORT', 3306) : 3306;

    // Create connection
    $conn = @mysqli_connect($servername, $username, $password, $dbname, $port);

    // Check connection
    if (!$conn) {
        // Return dummy connection or handle gracefully if running in mock/isolated test environment
        if (defined('SHILPA_TEST_MODE') || php_sapi_name() === 'cli') {
            return false;
        }
        die("Connection failed: " . mysqli_connect_error());
    }
    
    mysqli_set_charset($conn, "utf8mb4");
    return $conn;
}
?>

