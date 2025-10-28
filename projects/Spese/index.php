<?php
include $_SERVER['DOCUMENT_ROOT'] . '/utility/path.php';
include $_SERVER['DOCUMENT_ROOT'] . '/utility/classes/DB.php';
include 'utility.php';
session_start();
$db = new DB($dbname);
$logged = $_SESSION['logged'] ?? false;
$menu = $logged ? $logged_menu : $not_logged_menu;
$page = $_GET['page'] ?? 'home.php';
$favicon = $logo = 'media/images/logo.webp';

$msg = '';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'it' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spese</title>

    <!-- favicon -->
    <link rel="icon" type="image/png" href="<?= $favicon ?>" />

    <?php echo $chart_js ?? ''; ?>
    <?php echo $bootstrap_css ?? ''; ?>
    <?php echo $bootstrap_js ?? ''; ?>
    <?php echo $bootstrap_icons ?? ''; ?>
    <link rel="stylesheet" href="media/css/style.css">
    <script src="media/js/script.js" defer></script>
</head>

<body>
    <?php include "layout/navbar.php"; ?>
    <?php include "pages/$page"; ?>
</body>

</html>