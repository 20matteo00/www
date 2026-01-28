<div class="row d-flex justify-content-center m-5">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h1 class="fw-bold mb-0"><?= $editIndex !== null ? 'Modifica Squadra' : 'Crea Squadra' ?></h1>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="nome" class="form-label">Nome *</label>
                            <input type="text" class="form-control" name="nome" placeholder="Nome della squadra"
                                value="<?= htmlspecialchars($nomeForm) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="descrizione" class="form-label">Descrizione</label>
                            <input type="text" class="form-control" name="descrizione" placeholder="Descrizione breve"
                                value="<?= htmlspecialchars($descrizioneForm) ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="gruppi" class="form-label">Gruppi</label>
                            <select class="form-select" name="gruppi[]" id="gruppi" multiple>
                                <option value="">Nessun gruppo</option>
                                <?php foreach ($gruppiDisponibili as $gruppo): ?>
                                    <option value="<?= htmlspecialchars($gruppo['nome']) ?>"
                                        <?= (isset($gruppiForm) && in_array($gruppo['nome'], $gruppiForm)) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($gruppo['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <h2>Colori</h2>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="testo" class="form-label">Testo</label>
                            <input type="color" class="form-control" name="testo" placeholder="Testo della squadra"
                                value="<?= htmlspecialchars($testoForm ?? '#fff') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="sfondo" class="form-label">Sfondo</label>
                            <input type="color" class="form-control" name="sfondo" placeholder="Sfondo della squadra"
                                value="<?= htmlspecialchars($sfondoForm ?? '#000') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="bordo" class="form-label">Bordo</label>
                            <input type="color" class="form-control" name="bordo" placeholder="Bordo della squadra"
                                value="<?= htmlspecialchars($bordoForm ?? '#000') ?>" required>
                        </div>
                    </div>
                    <hr>
                    <h2>Valore</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="attacco" class="form-label">Attacco</label>
                            <input type="number" class="form-control" name="attacco" placeholder="Attacco della squadra" min="0" max="1000"
                                value="<?= htmlspecialchars($attaccoForm ?? '50') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="difesa" class="form-label">Difesa</label>
                            <input type="number" class="form-control" name="difesa" placeholder="Difesa della squadra" min="0" max="1000"
                                value="<?= htmlspecialchars($difesaForm ?? '50') ?>" required>
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