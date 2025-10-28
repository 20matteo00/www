<?php
include $_SERVER['DOCUMENT_ROOT'] . '/utility/path.php';

$cartelle = array_diff(scandir(__DIR__), ['.', '..', 'index.php']);

function includeFile($cartella)
{
    $dir = __DIR__ . "/$cartella";
    $files = array_diff(scandir($dir), ['.', '..']);
    if (empty($files)) {
        return;
    }
    echo "<div class='row my-5'>";
    echo "<h2 class='mb-4'>" . ucfirst($cartella) . ":</h2>";
    foreach ($files as $file):
        if (pathinfo($file, PATHINFO_EXTENSION) !== 'html' && pathinfo($file, PATHINFO_EXTENSION) !== 'php')
            continue;
        $nome = pathinfo($file, PATHINFO_FILENAME);
        $layoutPath = "$cartella/$file";
        ?>
        <div class="col">
            <a class="card text-center text-decoration-none text-dark p-3 h-100"
                href="?layout=<?= urlencode($layoutPath) ?>#ant">
                <?= ucfirst(htmlspecialchars($nome)) ?>
            </a>
        </div>
        <?php
    endforeach;
    echo "</div><hr>";
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layouts</title>
    <?= $bootstrap_css ?? '' ?>
    <?= $bootstrap_js ?? '' ?>
    <?= $bootstrap_icons ?? '' ?>
</head>

<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="m-0">Layout</h1>
            <a class="btn btn-outline-danger" href="index.php">Reset</a>
        </div>
        <?php foreach ($cartelle as $cartella): ?>
            <?php includeFile($cartella); ?>
        <?php endforeach; ?>
    </div>
    <?php
    // Se è stato selezionato un layout, lo includo
    if (isset($_GET['layout']) && !empty($_GET['layout'])) {
        $layout = $_GET['layout'];
        $path = __DIR__ . '/' . $layout;

        if (file_exists($path)) {
            echo "<h2 class='container mb-3' id='ant'>Anteprima: <small>$layout</small></h2>";
            echo "<div class='py-5 bg-secondary'>";
            include $path;
            echo "</div>";
        } else {
            echo "<p class='text-danger'>Il file selezionato non esiste.</p>";
        }
    }
    ?>
</body>

</html>