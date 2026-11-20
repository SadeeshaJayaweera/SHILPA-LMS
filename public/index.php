<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Application;
use App\Core\Request;

$app = new Application(dirname(__DIR__));
$response = $app->handle(Request::capture());
$response->send();
