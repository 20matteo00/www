<?php
include $_SERVER['DOCUMENT_ROOT'] . '/utility/path.php';

$files = array_diff(scandir(__DIR__), ['.', '..', 'index.php', 'Documenti']);
function includeFile($files)
{
    if (empty($files)) {
        return;
    }
    echo "<div class='row my-5'>";
    foreach ($files as $file):
        if (pathinfo($file, PATHINFO_EXTENSION) !== 'html' && pathinfo($file, PATHINFO_EXTENSION) !== 'php')
            continue;
        $nome = pathinfo($file, PATHINFO_FILENAME);
        ?>
        <div class="col">
            <a class="card text-center text-decoration-none text-dark p-3 h-100"
                href="?funzionalita=<?= urlencode($file) ?>#ant">
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
    <title>Funzionalità</title>
    <?= $bootstrap_css ?? '' ?>
    <?= $bootstrap_js ?? '' ?>
    <?= $bootstrap_icons ?? '' ?>
</head>

<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="m-0">Funzionalità</h1>
            <a class="btn btn-outline-danger" href="index.php">Reset</a>
        </div>
        <?php includeFile($files); ?>
    </div>
    <?php
    // Se è stato selezionato un funzionalità, lo includo
    if (isset($_GET['funzionalita']) && !empty($_GET['funzionalita'])) {
        $funzionalita = $_GET['funzionalita'];
        $path = __DIR__ . '/' . $funzionalita;

        if (file_exists($path)) {
            echo "<h2 class='container mb-3' id='ant'>Anteprima: <small>$funzionalita</small></h2>";
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