<div class="row d-flex justify-content-center m-5">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h1 class="fw-bold mb-0"><?= $editIndex !== null ? 'Modifica Gruppo' : 'Crea Gruppo' ?></h1>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nome" class="form-label">Nome *</label>
                            <input type="text" class="form-control" name="nome" placeholder="Nome del gruppo"
                                value="<?= htmlspecialchars($nomeForm) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="descrizione" class="form-label">Descrizione</label>
                            <input type="text" class="form-control" name="descrizione" placeholder="Descrizione breve"
                                value="<?= htmlspecialchars($descrizioneForm) ?>">
                        </div>
                    </div>

                    <?php if ($editIndex !== null): ?>
                        <input type="hidden" name="index" value="<?= $editIndex ?>">
                    <?php endif; ?>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary" name="azione"
                            value="<?= $editIndex !== null ? 'modifica' : 'crea' ?>">
                            <?= $editIndex !== null ? 'Salva Modifica' : 'Crea' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>