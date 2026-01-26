<?php if (!empty($gruppi)): ?>
    <div class="mt-5">
        <h2 class="mb-4 text-center">Elenco Gruppi</h2>
        <div class="row g-4">
            <?php foreach ($gruppi as $i => $gruppo): ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($gruppo['nome']) ?></h5>
                            <p class="card-text text-muted"><?= htmlspecialchars($gruppo['descrizione']) ?></p>
                            <div class="d-flex gap-2 mt-auto">
                                <form method="post" class="flex-grow-1">
                                    <input type="hidden" name="index" value="<?= $i ?>">
                                    <button type="submit" class="btn btn-warning w-100" name="azione"
                                        value="carica_modifica">Modifica</button>
                                </form>
                                <form method="post" class="flex-grow-1">
                                    <input type="hidden" name="index" value="<?= $i ?>">
                                    <button type="submit" class="btn btn-danger w-100" name="azione"
                                        value="elimina">Elimina</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-info text-center mt-5" role="alert">
        Nessun gruppo creato.
    </div>
<?php endif; ?>