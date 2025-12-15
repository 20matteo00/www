<?php
include $_SERVER['DOCUMENT_ROOT'] . '/utility/path.php';
include $_SERVER['DOCUMENT_ROOT'] . '/utility/classes/DB.php';
include 'utility.php';

session_start();
$logged = $_SESSION['logged'] ?? false;
if ($logged){
    $menu = $menu_logged;
} else {
    $menu = $menu_guest;
}


$db = new DB('simulatore');

$page = $_GET['page'] ?? 'home';
$file = PAGES_PATH . $page . '.php';


?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'it' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITENAME ?></title>

    <!-- favicon -->
    <link rel="icon" type="image/png" href="<?= IMAGES_PATH ?>logo.png" />

    <?php echo $bootstrap_css ?? ''; ?>
    <?php echo $bootstrap_js ?? ''; ?>
    <?php echo $bootstrap_icons ?? ''; ?>
    <link rel="stylesheet" href="<?= CSS_PATH ?>style.css">
    <script src="<?= JS_PATH ?>script.js" defer></script>
</head>

<body>
    <?php 
    include LAYOUT_PATH . 'navbar.php';
    if (file_exists($file)) {
        include $file;
    } else {
        include PAGES_PATH . 'home.php';
    }
    include LAYOUT_PATH . 'footer.php'; ?>
</body>

</html>