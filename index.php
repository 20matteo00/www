<?php
include $_SERVER['DOCUMENT_ROOT'] . '/utility/path.php';
include $_SERVER['DOCUMENT_ROOT'] . '/utility/lang.php';

session_start();
$site_directory = 'projects'; // nome della cartella dove ci sono i siti

$directory = __DIR__ . "/$site_directory"; // cartella dove ci sono i siti
$sites = array_diff(scandir($directory), array('..', '.')); // legge tutto tranne . e ..

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'it'; // lingua di default
} 

?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= lang("sitename") ?></title>

    <?php echo $bootstrap_css ?? ''; ?>
    <?php echo $bootstrap_js ?? ''; ?>
    <?php echo $bootstrap_icons ?? ''; ?>
</head>

<body>
    <div class="container p-4">
        <h1 class="fw-bold text-center mb-4"><?= lang("sitename") ?></h1>
        <ul class="list-group">
            <?php foreach ($sites as $site) : ?>
                <?php 
                    $site_path = "/$site_directory/$site"; // percorso relativo dal root www
                ?>
                <li class="list-group-item">
                    <a href="<?php echo $site_path; ?>" target="_blank">
                        <i class="bi bi-link-45deg me-2"></i><?php echo htmlspecialchars($site); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>

</html>
