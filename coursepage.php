<?php
require_once __DIR__ . '/vendor/autoload.php';

$id = isset($_GET["ID"]) ? (int)$_GET["ID"] : 0;
if ($id > 0) {
    header("Location: /courses/$id");
    exit;
}
header("Location: /courses");
exit;
