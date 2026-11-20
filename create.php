<?php
require_once __DIR__ . '/vendor/autoload.php';
use App\Models\Course;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['name'] ?? ''));
    $csd = trim((string)($_POST['csd'] ?? ''));
    $cd = trim((string)($_POST['cd'] ?? ''));
    $cci = trim((string)($_POST['cci'] ?? ''));
    $db = trim((string)($_POST['db'] ?? ''));
    $cl = trim((string)($_POST['cl'] ?? ''));
    $tc = trim((string)($_POST['tc'] ?? 'No'));

    if ($name !== '') {
        Course::create([
            'C_NAME' => $name,
            'C_CARD_DESCRIPTION' => $csd,
            'C_DESCRIPTION' => $cd,
            'C_CARD_IMAGE' => $cci,
            'C_DONE_BY' => $db,
            'C_LINK' => $cl,
            'TOP_COURSE' => $tc,
            'C_ADD_DATE' => date('Y-m-d H:i:s'),
        ]);
    }
    header('Location: admin.php');
    exit;
}
?>
