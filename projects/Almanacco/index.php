<?php
include $_SERVER['DOCUMENT_ROOT'] . '/utility/path.php';
include $_SERVER['DOCUMENT_ROOT'] . '/utility/classes/DB.php';
include "helper.php";

$json = json_decode(file_get_contents('json/partite.json'), true);
$helper = new Helper();

$page = $_GET['page'] ?? null;
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'it' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Almanacco</title>

    <?= $bootstrap_css ?? ''; ?>
    <?= $bootstrap_js ?? ''; ?>
    <?= $bootstrap_icons ?? ''; ?>
    <link rel="stylesheet" href="media/css/style.css">
    <script src="media/js/script.js"></script>
</head>

<body>
    <?php include "layout/navbar.php"; ?>
    <?php
    if ($page) {
        include "pages/{$page}.php";
    }
    ?>
</body>

</html>