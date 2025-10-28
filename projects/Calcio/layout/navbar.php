<?php

?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <span class="navbar-brand">
            <img class="logo" src="media/images/logo.png" alt="Calcio">
        </span>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <?php foreach ($menu as $m => $link): ?>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php?page=<?= $link ?>"><?= $m ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <?php if (isset($_SESSION['username'])): ?>
                    <li class="nav-item">
                        <span class="nav-link active"><?= $_SESSION['username'] ?></span>
                    </li>
                <?php endif; ?>
            </ul>
            <!-- Optional: Right side buttons -->
            <form class="d-flex ps-2" role="search" method="post" action="index.php?page=ricerca.php">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="text">
                <button class="btn btn-light" type="submit"><i class="bi bi-search"></i></button>
            </form>
        </div>
    </div>
</nav>