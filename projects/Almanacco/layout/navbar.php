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
                <li class="nav-item">
                    <a class="nav-link active fw-bold" aria-current="page" href="index.php?page=perpetua">Classifica Perpetua</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active fw-bold" aria-current="page" href="index.php?page=stagione">Dettaglio Stagione</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active fw-bold" aria-current="page" href="index.php?page=scontri">Scontri Diretti</a>
                </li>
            </ul>

            <!-- Optional: Right side buttons -->
            <form class="d-flex ps-2" role="search" method="post" action="index.php?page=ricerca">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="text">
                <button class="btn btn-light" type="submit"><i class="bi bi-search"></i></button>
            </form>
        </div>
    </div>
</nav>