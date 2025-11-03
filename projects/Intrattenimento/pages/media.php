<?php
checkLogin();

$table = 'intrattenimento';

$editId = $_GET['edit'] ?? null;
$msg = '';
$errors = [];

$type = $_GET['type'] ?? null;

// POST: Inserimento o Modifica
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $descrizione = $_POST['descrizione'] ?? '';

    if (!$nome)
        $errors[] = "Devi inserire un nome!";
    if (!$tipo)
        $errors[] = "Devi selezionare un tipo valido!";

    // Generi
    $g = $_POST['genere'] ?? [];

    // Piattaforme
    $p = $_POST['piattaforma'] ?? [];

    // Dati specifici
    if ($tipo === 'film') {
        $durata = $_POST['durata'] ?? null;
        if ($durata < 0 || $durata > 600)
            $errors[] = "Durata non valido!";
        $anno = $_POST['anno'] ?? null;
        if ($anno < 1900 || $anno > 2050)
            $errors[] = "Anno non valido!";
        $dati = [
            "generi" => $g,
            "piattaforme" => $p,
            "durata" => $durata,
            "anno" => $anno
        ];
    } elseif ($tipo === 'serie_tv') {
        $durata_inizio = $_POST['durata_media_inizio'] ?? null;
        $durata_fine = $_POST['durata_media_fine'] ?? null;

        if ($durata_inizio < 0 || $durata_inizio > 600 || $durata_fine < 0 || $durata_fine > 600)
            $errors[] = "Durata non valida!";
        if ($durata_inizio > $durata_fine)
            $durata = $durata_fine . "-" . $durata_inizio;
        elseif ($durata_inizio == $durata_fine)
            $durata = $durata_inizio;
        else
            $durata = $durata_inizio . "-" . $durata_fine;

        $anno_inizio = $_POST['anno_inizio'] ?? null;
        $anno_fine = $_POST['anno_fine'] ?? null;

        if ($anno_inizio < 1900 || $anno_inizio > 2050 || $anno_fine < 1900 || $anno_fine > 2050)
            $errors[] = "Anno non valido!";
        if ($anno_inizio > $anno_fine)
            $anno = $anno_fine . "-" . $anno_inizio;
        elseif ($anno_inizio == $anno_fine)
            $anno = $anno_inizio;
        else
            $anno = $anno_inizio . "-" . $anno_fine;

        $stagioni = $_POST['stagioni'] ?? null;
        $episodi = $_POST['episodi'] ?? null;
        $finita = $_POST['finita'] ?? null;
        $dati = [
            "generi" => $g,
            "piattaforme" => $p,
            "durata" => $durata,
            "anno" => $anno,
            "stagioni" => $stagioni,
            "episodi" => $episodi,
            "finita" => $finita
        ];
    } else {
        $errors[] = "Tipo media non valido!";
    }

    $dati_json = json_encode($dati, JSON_UNESCAPED_UNICODE);

    if (!$errors) {
        if ($editId) {
            // Modifica
            $db->update($table, [
                'nome' => $nome,
                'tipo' => $tipo,
                'descrizione' => $descrizione,
                'dati' => $dati_json
            ], ['id' => $editId]);
            $msg = "<div class='alert alert-success'>Media modificato correttamente.</div>";
        } else {
            // Inserimento nuovo
            $db->insert($table, [
                'nome' => $nome,
                'tipo' => $tipo,
                'descrizione' => $descrizione,
                'dati' => $dati_json,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $msg = "<div class='alert alert-success'>Media inserito correttamente.</div>";
        }
        header('Location: index.php?page=media.php');
        exit();
    } else {
        $msg = "<div class='alert alert-danger'>" . implode('<br>', $errors) . "</div>";
    }
}

// DELETE
if (isset($_GET['delete'])) {
    $db->delete($table, ['id' => (int) $_GET['delete']]);
    header('Location: index.php?page=media.php');
    exit();
}

// Recupera media per lista
if ($type) {
    $media = $db->select($table, [
        "where" => ['tipo' => $type]
    ]);
} else {
    $media = $db->select($table);
}

// Recupera media per edit
$editMedia = null;
if ($editId) {
    $editMedia = $db->select($table, ['where' => ['id' => $editId]]);
    $editMedia = $editMedia ? $editMedia[0] : null;
}

// Recupera generi selezionati
$selectedGeneri = [];
if ($editMedia && $editMedia['dati']) {
    $datiDecodificati = json_decode($editMedia['dati'], true);
    if (isset($datiDecodificati['generi']) && is_array($datiDecodificati['generi'])) {
        $selectedGeneri = $datiDecodificati['generi'];
    }
} elseif (isset($_POST['genere'])) {
    $selectedGeneri = $_POST['genere'];
}

// Recupera piattaforme selezionate
$selectedPiattaforme = [];
if ($editMedia && $editMedia['dati']) {
    $datiDecodificati = $datiDecodificati ?? json_decode($editMedia['dati'], true);
    if (isset($datiDecodificati['piattaforme']) && is_array($datiDecodificati['piattaforme'])) {
        $selectedPiattaforme = $datiDecodificati['piattaforme'];
    }
} elseif (isset($_POST['piattaforma'])) {
    $selectedPiattaforme = $_POST['piattaforma'];
}
?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow rounded-3">
                <div class="card-header">
                    <h2 class="m-0 py-3 text-center fw-bold fs-1">
                        <?= $editId ? 'Modifica' : 'Aggiungi' ?> Media
                    </h2>
                </div>
                <div class="card-body p-4">
                    <?= $msg ?>
                    <form method="POST" autocomplete="on">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="nome">Nome <span class="text-danger">*</span></label>
                                <input type="text" name="nome" id="nome" class="form-control" required
                                    value="<?= htmlspecialchars($editMedia['nome'] ?? $_POST['nome'] ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="tipo">Tipo <span class="text-danger">*</span></label>
                                <select name="tipo" id="tipo" class="form-select" required onchange="toggleCampi()">
                                    <option value="">Seleziona un tipo</option>
                                    <?php foreach ($tipi as $key => $t): ?>
                                        <option value="<?= $key ?>" <?= ($editMedia['tipo'] ?? $_POST['tipo'] ?? '') == $key ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($t) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="genere">Genere/i</label>
                                <select name="genere[]" id="genere" class="form-select" multiple>
                                    <?php foreach ($generi as $key => $g): ?>
                                        <option value="<?= $key ?>" <?= in_array($key, $selectedGeneri) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($g) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="piattaforma">Piattaforma/e</label>
                                <select name="piattaforma[]" id="piattaforma" class="form-select" multiple>
                                    <?php foreach ($piattaforme as $key => $p): ?>
                                        <option value="<?= $key ?>" <?= in_array($key, $selectedPiattaforme) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($p) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="descrizione">Descrizione</label>
                                <textarea name="descrizione" id="descrizione" class="form-control" rows="3"
                                    placeholder="Inserisci una descrizione..."><?= htmlspecialchars($editMedia['descrizione'] ?? $_POST['descrizione'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <!-- Campi Film -->
                        <div id="campi-film" style="display:none;">
                            <hr>
                            <h6>Dettagli Film</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="anno" class="form-label">Anno</label>
                                    <input type="number" name="anno" id="anno" class="form-control" min="1900"
                                        max="2050"
                                        value="<?= $editMedia && $editMedia['tipo'] == 'film' ? json_decode($editMedia['dati'], true)['anno'] : '' ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="durata" class="form-label">Durata</label>
                                    <div class="input-group">
                                        <input type="number" name="durata" id="durata" class="form-control" min="0"
                                            max="600"
                                            value="<?= $editMedia && $editMedia['tipo'] == 'film' ? json_decode($editMedia['dati'], true)['durata'] : '' ?>">
                                        <span class="input-group-text">Min</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campi Serie TV -->
                        <div id="campi-serie" style="display:none;">
                            <hr>
                            <h6>Dettagli Serie TV</h6>
                            <?php
                            $serieData = $editMedia && $editMedia['tipo'] == 'serie_tv' ? json_decode($editMedia['dati'], true) : [];
                            $anno_split = explode('-', $serieData['anno'] ?? '');
                            $durata_split = explode('-', $serieData['durata'] ?? '');
                            ?>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Anni di uscita</label>
                                    <div class="input-group">
                                        <input type="number" name="anno_inizio" class="form-control" min="1900"
                                            max="2050" placeholder="Inizio" value="<?= $anno_split[0] ?? '' ?>">
                                        <span class="input-group-text">-</span>
                                        <input type="number" name="anno_fine" class="form-control" min="1900" max="2050"
                                            placeholder="Fine" value="<?= $anno_split[1] ?? $anno_split[0] ?? '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Durata media episodi</label>
                                    <div class="input-group">
                                        <input type="number" name="durata_media_inizio" class="form-control" min="0"
                                            max="600" placeholder="Inizio" value="<?= $durata_split[0] ?? '' ?>">
                                        <span class="input-group-text">-</span>
                                        <input type="number" name="durata_media_fine" class="form-control" min="0"
                                            max="600" placeholder="Fine"
                                            value="<?= $durata_split[1] ?? $durata_split[0] ?? '' ?>">
                                        <span class="input-group-text">Min</span>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Stagioni</label>
                                    <input type="number" name="stagioni" class="form-control" min="0" max="50"
                                        value="<?= $serieData['stagioni'] ?? '' ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Episodi totali</label>
                                    <input type="number" name="episodi" class="form-control" min="0" max="1000"
                                        value="<?= $serieData['episodi'] ?? '' ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label d-block">Serie conclusa?</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="finita" value="1"
                                            <?= ($serieData['finita'] ?? 0) == 1 ? 'checked' : '' ?>>
                                        <label class="form-check-label">Sì</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="finita" value="0"
                                            <?= ($serieData['finita'] ?? 0) == 0 ? 'checked' : '' ?>>
                                        <label class="form-check-label">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                            <a href="?page=media.php" class="btn btn-secondary me-md-2">Annulla</a>
                            <button type="submit"
                                class="btn btn-primary"><?= $editId ? 'Modifica' : 'Aggiungi' ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista Media -->
    <div class="row justify-content-center my-5" id="lista">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="fw-bold mb-0">Lista Media</h2>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                        <a href="?page=media.php&type=film#lista" class="btn btn-primary me-md-2">Film</a>
                        <a href="?page=media.php&type=serie_tv#lista" class="btn btn-primary me-md-2">Serie TV</a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if ($media): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover border sortable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nome</th>
                                        <th>Tipo</th>
                                        <th>Descrizione</th>
                                        <th>Generi</th>
                                        <th>Dettagli</th>
                                        <th>Dove Vedere</th>
                                        <th>Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($media as $m):
                                        $dati = json_decode($m['dati'], true);
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($m['nome']) ?></td>
                                            <td><?= htmlspecialchars($tipi[$m['tipo']] ?? $m['tipo']) ?></td>
                                            <td><?= htmlspecialchars($m['descrizione']) ?></td>
                                            <td><?= implode(', ', $dati['generi'] ?? []) ?></td>
                                            <td>
                                                <?php if ($m['tipo'] == 'film'): ?>
                                                    Anno: <?= $dati['anno'] ?? '' ?><br>
                                                    Durata: <?= $dati['durata'] ?? '' ?> min<br>
                                                <?php else: ?>
                                                    Anni: <?= $dati['anno'] ?? '' ?><br>
                                                    Durata media: <?= $dati['durata'] ?? '' ?> min<br>
                                                    Stagioni: <?= $dati['stagioni'] ?? '' ?><br>
                                                    Episodi: <?= $dati['episodi'] ?? '' ?><br>
                                                    Conclusa: <?= ($dati['finita'] ?? 0) ? 'Sì' : 'No' ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= implode(', ', $dati['piattaforme'] ?? []) ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="?page=media.php&edit=<?= $m['id'] ?>"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i> Modifica
                                                    </a>
                                                    <a href="?page=media.php&delete=<?= $m['id'] ?>"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Sicuro di voler eliminare questo media?')">
                                                        <i class="fas fa-trash"></i> Elimina
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            Non ci sono media inseriti.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>