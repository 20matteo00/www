<div class="container my-4">
    <h1 class="fw-bold fs-1 mb-3">Le tue ultime competizioni</h1>
    <div class="d-flex flex-wrap gap-3">
        <?php foreach ($last_comp as $comp): ?>
            <div class="col badge rounded-pill d-flex align-items-center shadow-sm p-3">
                <a class="me-3 flex-grow-1 fs-3 fw-bold bg-white text-black text-decoration-none" href="index.php?page=visualizza_competizione.php&id=<?= $comp['id'] ?>"><?= htmlspecialchars($comp['nome']) ?></a>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="mt-5">
        <p class="m-0 text-center"><a class="btn btn-primary rounded-pill" href="index.php?page=competizioni.php">Vedi tutte le competizioni</a></p>
    </div>
</div>