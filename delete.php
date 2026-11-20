<?php
require_once __DIR__ . '/vendor/autoload.php';
use App\Models\Course;

if (isset($_GET['deleteID'])) {
    $deleteID = (int)$_GET['deleteID'];
    if ($deleteID > 0) {
        $course = Course::find($deleteID);
        if ($course) {
            $course->delete();
        }
    }
    header('Location: admin.php');
    exit;
}
?>
