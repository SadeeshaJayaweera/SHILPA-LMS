<?php
require_once __DIR__ . '/vendor/autoload.php';
use App\Models\Course;

$id = isset($_GET['updateID']) ? (int)$_GET['updateID'] : 0;
$course = $id > 0 ? Course::find($id) : null;

$name = $course ? (string)$course->C_NAME : '';
$csd = $course ? (string)$course->C_CARD_DESCRIPTION : '';
$cd = $course ? (string)$course->C_DESCRIPTION : '';
$cci = $course ? (string)$course->C_CARD_IMAGE : '';
$cdb = $course ? (string)$course->C_DONE_BY : '';
$cl = $course ? (string)$course->C_LINK : '';
$tc = $course ? (string)$course->TOP_COURSE : 'No';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update']) && $course) {
    $course->C_NAME = trim((string)($_POST['name'] ?? ''));
    $course->C_CARD_DESCRIPTION = trim((string)($_POST['csd'] ?? ''));
    $course->C_DESCRIPTION = trim((string)($_POST['cd'] ?? ''));
    $course->C_CARD_IMAGE = trim((string)($_POST['cci'] ?? ''));
    $course->C_DONE_BY = trim((string)($_POST['db'] ?? ''));
    $course->C_LINK = trim((string)($_POST['cl'] ?? ''));
    $course->TOP_COURSE = trim((string)($_POST['tc'] ?? 'No'));
    $course->save();

    header('Location: admin.php');
    exit;
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Course Form</title>
    <style>
         body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }

        form {
            margin-top: 30px;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }

        h3 {
            text-align: center;
        }

        table {
            width: 100%;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            resize: vertical;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<form action="" method="post" enctype="multipart/form-data">
    <table align="center">
        <tr>
            <th colspan="2"><h3> Course Form </h3></th>
        </tr>
        <tr>
            <td>Course Name:</td>
            <td><input type="text" id="name" name="name" size="50" autocomplete="off" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>"></td>
        </tr>
        <tr>
            <td>Course Short Description:</td>
            <td><textarea name="csd" rows="4" cols="60" autocomplete="off"><?php echo htmlspecialchars($csd, ENT_QUOTES, 'UTF-8'); ?></textarea></td>
        </tr>
        <tr>
            <td>Course Description:</td>
            <td><textarea name="cd" rows="10" cols="60" autocomplete="off"><?php echo htmlspecialchars($cd, ENT_QUOTES, 'UTF-8'); ?></textarea></td>
        </tr>
        <tr>
            <td>Course Card Image:</td>
            <td><input type="text" id="cci" name="cci" size="40" autocomplete="off" value="<?php echo htmlspecialchars($cci, ENT_QUOTES, 'UTF-8'); ?>"></td>
        </tr>
        <tr>
            <td>Course Done by:</td>
            <td><input type="text" id="db" name="db" size="50" autocomplete="off" value="<?php echo htmlspecialchars($cdb, ENT_QUOTES, 'UTF-8'); ?>"></td>
        </tr>
        <tr>
            <td>Course Link:</td>
            <td><input type="text" id="cl" name="cl" size="50" autocomplete="off" value="<?php echo htmlspecialchars($cl, ENT_QUOTES, 'UTF-8'); ?>"></td>
        </tr>
        <tr>
            <td>Top Course:</td>
            <td>
                <input type="radio" name="tc" value="Yes" <?php echo strcasecmp($tc, 'yes') === 0 ? 'checked' : ''; ?>>Yes
                <input type="radio" name="tc" value="No" <?php echo strcasecmp($tc, 'no') === 0 ? 'checked' : ''; ?>>No
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <input type="submit" id="update" name="update" value="Update">
            </th>
        </tr>
    </table>
</form>
</body>
</html>
