<?php
include $_SERVER['DOCUMENT_ROOT'] . '/utility/path.php';
$files = array_diff(scandir(__DIR__), array('.', '..', 'index.php', 'media'));
$page = $_GET['page'] ?? '';
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Games</title>
    <?php echo $bootstrap_css ?? ''; ?>
    <?php echo $bootstrap_js ?? ''; ?>
    <?php echo $bootstrap_icons ?? ''; ?>
    <link rel="stylesheet" href="media/style.css">
    <script src="media/script.js"></script>
</head>

<body>
    <header class="text-center text-white fs-1 py-4">Mini Games 🎉</header>

    <main class="container py-4">
        <div class="row g-4">
            <?php foreach ($files as $file): ?>
                <div class="col">
                    <a class="card text-center text-decoration-none text-dark p-3 h-100" href="?page=<?= $file ?>">
                        <?= explode(".", $file)[0] ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <div class="gioco">
        <?php if ($page != ''): ?>
            <?php include $page; ?>
        <?php endif; ?>
    </div>
    <footer class="text-center bg-primary text-white mt-auto p-4">© 2025 Matteo Inc. | Tutte i Mini Games</footer>
</body>

</html>