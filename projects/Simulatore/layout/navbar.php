<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">

        <!-- Logo -->
        <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>">
            <img src="<?= IMAGES_PATH ?>logo.png" alt="<?= SITENAME ?>" height="30">
        </a>

        <!-- Hamburger -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Collapsable -->
        <div class="collapse navbar-collapse" id="mainNavbar">

            <!-- Menu centrale -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php foreach ($menu as $key => $value): ?>
                <li class="nav-item">
                    <a class="nav-link active" href="?page=<?= $key ?>"><?= $value ?></a>
                </li>
                <?php endforeach; ?>
            </ul>

            <!-- Cerca a destra -->
            <form class="d-flex" role="search" method="post" action="?page=cerca">
                <input class="form-control me-2" type="search" placeholder="Cerca..." aria-label="Search">
                <button class="btn btn-outline-light" type="submit"><i class="bi bi-search"></i></button>
            </form>

        </div>
    </div>
</nav>