<footer class="bg-dark text-light mt-5">
    <div class="container py-5">

        <div class="row gy-4">

            <!-- Brand -->
            <div class="col-lg-6 col-md-6">
                <h5 class="fw-bold mb-3">
                    <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>">
                        <img src="<?= IMAGES_PATH ?>logo.png" alt="<?= SITENAME ?>" height="30">
                    </a>
                </h5>
                <p class="text-secondary mb-0">
                    Simulatore sportivo semplice e veloce.
                    Crea squadre, competizioni e partite in pochi click.
                </p>
            </div>

            <!-- Menu -->
            <div class="col-lg-3 col-md-6">
                <h6 class="text-uppercase fw-semibold mb-3">Menu</h6>
                <ul class="list-unstyled">
                    <?php foreach ($menu as $key => $value): ?>
                        <li class="nav-item">
                            <a class="nav-link active" href="?page=<?= $key ?>"><?= $value ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Social / Info -->
            <div class="col-lg-3 col-md-6">
                <h6 class="text-uppercase fw-semibold mb-3">Contatti</h6>
                <p class="text-secondary mb-2">📧 support@simulatore.local</p>
                <div class="d-flex gap-3">
                    <a href="#" class="text-secondary fs-5"><i class="bi bi-github"></i></a>
                    <a href="#" class="text-secondary fs-5"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="text-secondary fs-5"><i class="bi bi-discord"></i></a>
                </div>
            </div>

        </div>
    </div>

    <!-- Bottom -->
    <div class="bg-black text-center py-3">
        <small class="text-secondary">
            © <?= date('Y') ?> Simulatore — Tutti i diritti riservati
        </small>
    </div>
</footer>