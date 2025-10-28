<?php
checkLogin();

$table = 'spese';

$editId = $_GET['edit'] ?? null;

// Recupera i membri per la select
$membri = $db->select('membri', ['orderBy' => 'created_at ASC']);

// Recupera le categorie per la select
$categorie = $db->select('categorie', ['orderBy' => 'created_at ASC']);
// Mappa id_categoria => nome_categoria
$categorie_nome = [];
foreach ($categorie as $cat) {
    $categorie_nome[$cat['id']] = $cat['nome'];
}

// Recupera le sottocategorie per la select
$sottocategorie = $db->select('sottocategorie', ['orderBy' => 'created_at ASC']);

// Inserimento/modifica spesa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_membro = $_POST['membro'] ?? '';
    $id_sottocategoria = $_POST['sottocategoria'] ?? '';
    $importo = $_POST['importo'] ?? '';
    $data = $_POST['data'] ?? '';
    $descrizione = $_POST['descrizione'] ?? '';

    // Validazione
    $errors = [];
    if (!$id_membro)
        $errors[] = "Devi selezionare un membro!";
    if (!$id_sottocategoria)
        $errors[] = "Devi selezionare una sottocategoria!";
    if (!$importo || !is_numeric($importo))
        $errors[] = "Devi inserire un importo valido!";
    if (!$data)
        $errors[] = "Devi inserire una data!";

    if ($errors) {
        $msg = "<div class='alert alert-danger'>" . implode('<br>', $errors) . "</div>";
    } else {
        if ($editId) {
            // Modifica
            $db->update($table, [
                'id_membro' => $id_membro,
                'id_sottocategoria' => $id_sottocategoria,
                'importo' => $importo,
                'data' => $data,
                'descrizione' => $descrizione
            ], ['id' => $editId]);
            $msg = "<div class='alert alert-success'>Spesa modificata correttamente.</div>";
        } else {
            // Nuovo
            $db->insert($table, [
                'id_membro' => $id_membro,
                'id_sottocategoria' => $id_sottocategoria,
                'importo' => $importo,
                'data' => $data,
                'descrizione' => $descrizione,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $msg = "<div class='alert alert-success'>Spesa inserita correttamente.</div>";
        }
        header('Location: index.php?page=' . $table . '.php');
        exit();
    }
}

// Elimina spesa
if (isset($_GET['delete'])) {
    $db->delete($table, ['id' => (int) $_GET['delete']]);
    $msg = "<div class='alert alert-danger'>Spesa eliminata!</div>";
    header('Location: index.php?page=' . $table . '.php');
    exit();
}

// Recupera lista spese 
$spese = $db->select($table, [
    'orderBy' => 'created_at ASC',
]);

// Recupero spesa per edit
$editSpesa = null;
if ($editId) {
    $editSpesa = $db->select($table, [
        'where' => ['id' => $editId]
    ]);
    $editSpesa = $editSpesa ? $editSpesa[0] : null;
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow rounded-3">
                <div class="card-header">
                    <h2 class="m-0 py-3 text-center fw-bold fs-1">
                        <?= $editId ? 'Modifica' : 'Aggiungi' ?> Spesa
                    </h2>
                </div>
                <div class="card-body p-4">
                    <?= $msg ?? '' ?>
                    <form method="POST" autocomplete="on">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="membro">Membro <span class="text-danger">*</span></label>
                                <select name="membro" id="membro" class="form-select" required>
                                    <option value="">Seleziona un membro</option>
                                    <?php foreach ($membri as $m): ?>
                                        <option value="<?= $m['id'] ?>" <?= ($editSpesa['id_membro'] ?? $_POST['membro'] ?? '') == $m['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($m['nome']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="sottocategoria">Sottocategoria <span
                                        class="text-danger">*</span></label>
                                <select name="sottocategoria" id="sottocategoria" class="form-select">
                                    <option value="">Seleziona una sottocategoria</option>
                                    <?php foreach ($sottocategorie as $sc): ?>
                                        <option value="<?= $sc['id'] ?>" <?= ($editSpesa['id_sottocategoria'] ?? $_POST['sottocategoria'] ?? '') == $sc['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($sc['nome']) ?>
                                            <?php if (isset($categorie_nome[$sc['id_categoria']])): ?>
                                                (<?= htmlspecialchars($categorie_nome[$sc['id_categoria']]) ?>)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="importo">Importo <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">€</span>
                                    <input type="number" step="0.01" name="importo" id="importo" class="form-control"
                                        required
                                        value="<?= htmlspecialchars($editSpesa['importo'] ?? $_POST['importo'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="data">Data <span class="text-danger">*</span></label>
                                <input type="date" name="data" id="data" class="form-control" required
                                    value="<?= htmlspecialchars($editSpesa['data'] ?? $_POST['data'] ?? date('Y-m-d')) ?>">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="descrizione">Descrizione</label>
                                <textarea name="descrizione" id="descrizione" class="form-control" rows="3"
                                    placeholder="Inserisci una descrizione della spesa..."><?= htmlspecialchars($editSpesa['descrizione'] ?? $_POST['descrizione'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="?page=<?= $table ?>.php" class="btn btn-secondary me-md-2">Annulla</a>
                            <button type="submit" class="btn btn-primary">
                                <?= $editId ? 'Modifica' : 'Aggiungi' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabella spese -->
    <div class="row justify-content-center my-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="fw-bold mb-0">Lista Spese</h2>
                    <div class="text-end">
                        <?php if ($spese):
                            $totale = array_sum(array_column($spese, 'importo'));
                            ?>
                            <strong>Totale: €<?= number_format($totale, 2, ',', '.') ?></strong>
                        <?php endif ?>
                    </div>
                </div>
                <div class="card-body">
                    <?php if ($spese): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover border sortable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Data</th>
                                        <th>Membro</th>
                                        <th>Sottocategoria</th>
                                        <th>Importo</th>
                                        <th>Descrizione</th>
                                        <th>Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($spese as $s): ?>
                                        <?php
                                        $mem = array_filter($membri, fn($m) => $m['id'] == $s['id_membro']);
                                        $membro_nome = count($mem) ? reset($mem)['nome'] : 'N/D';

                                        $subcat = array_filter($sottocategorie, fn($sc) => $sc['id'] == $s['id_sottocategoria']);
                                        $sottocategoria_nome = count($subcat) ? reset($subcat)['nome'] : '-';
                                        ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($s['data'])) ?></td>
                                            <td><?= htmlspecialchars($membro_nome) ?></td>
                                            <td><?= htmlspecialchars($sottocategoria_nome) ?></td>
                                            <td class="text-end">€<?= number_format($s['importo'], 2, ',', '.') ?></td>
                                            <td><?= htmlspecialchars($s['descrizione']) ?: '-' ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="?page=<?= $table ?>.php&edit=<?= $s['id'] ?>"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i> Modifica
                                                    </a>
                                                    <a href="?page=<?= $table ?>.php&delete=<?= $s['id'] ?>"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Sicuro di voler eliminare questa spesa?')">
                                                        <i class="fas fa-trash"></i> Elimina
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> Non ci sono spese inserite.
                        </div>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</div>