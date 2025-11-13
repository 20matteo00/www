<?php
include $_SERVER['DOCUMENT_ROOT'] . '/utility/path.php';
include $_SERVER['DOCUMENT_ROOT'] . '/utility/classes/DB.php';

$page = $_GET['page'] ?? null;

$msg = '';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'it' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Script</title>

    <?= $bootstrap_css ?? ''; ?>
    <?= $bootstrap_js ?? ''; ?>
    <?= $bootstrap_icons ?? ''; ?>
</head>

<body class="container my-5">
    <div class="d-flex justify-content-evenly mb-4">
        <a class="btn btn-warning" href="?page=generaGiornate.php">Genera Giornate</a>
        <a class="btn btn-warning" href="?page=generaClassifiche.php">Genera Classifiche</a>
    </div>
    <div class="d-flex justify-content-evenly mb-4">
        <a class="btn btn-success" href="?page=visualizzaGiornate.php">Visualizza Giornate</a>
        <a class="btn btn-success" href="?page=visualizzaClassifiche.php">Visualizza Classifiche</a>
    </div>
    <?php if (isset($page))
        include "$page"; ?>
</body>

</html>

