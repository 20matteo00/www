<?php
checkLogin();

$table = 'membri';

$editId = $_GET['edit'] ?? null;

// Inserimento membro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $descrizione = $_POST['descrizione'] ?? '';
    $color = $_POST['color'] ?? '#000000';
    if ($editId) {
        // Modifica
        $db->update($table, [
            'nome' => $nome,
            'descrizione' => $descrizione,
            'dati' => json_encode(['color' => $color])
        ], ['id' => $editId]);
        $msg = "<div class='alert alert-success'>Membro modificato correttamente.</div>";
    } else {
        // Nuovo
        $db->insert($table, [
            'nome' => $nome,
            'descrizione' => $descrizione,
            'dati' => json_encode(['color' => $color]),
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $msg = "<div class='alert alert-success'>Membro inserito correttamente.</div>";
    }
    header('Location: index.php?page=' . $table . '.php');
    exit();
}

// Elimina membro
if (isset($_GET['delete'])) {
    $db->delete($table, ['id' => (int) $_GET['delete']]);
    $msg = "<div class='alert alert-danger'>Membro eliminato!</div>";
    header('Location: index.php?page=' . $table . '.php');
    exit();
}

// Recupera lista membri
$membri = $db->runQuery(
    'SELECT m.*, IFNULL(SUM(s.importo), 0) AS totale_importo
            FROM membri m
            LEFT JOIN spese s ON s.id_membro = m.id
            GROUP BY m.id, m.nome
            ORDER BY totale_importo DESC'
);

// Se sto modificando recupero il membro
$editMembro = null;
if ($editId) {
    $editMembro = $db->select($table, [
        'where' => ['id' => $editId]
    ]);
    $editMembro = $editMembro ? $editMembro[0] : null;
}
?>


<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow rounded-3">
                <div class="card-header">
                    <h2 class="m-0 py-3 text-center fw-bold fs-1">Aggiungi Membri</h2>
                </div>
                <div class="card-body p-4">
                    <?= $msg ?>
                    <form method="POST" autocomplete="on">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="nome">Nome <span class="text-danger">*</span></label>
                                <input type="text" name="nome" id="nome" class="form-control" required
                                    value="<?= htmlspecialchars($editMembro['nome'] ?? $_POST['nome'] ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="color">Colore</label>
                                <input type="color" name="color" id="color" class="form-control" required
                                    value="<?= htmlspecialchars(json_decode($editMembro['dati'], true)['color'] ?? $_POST['color'] ?? '') ?>">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="descrizione">Descrizione</label>
                                <textarea name="descrizione" id="descrizione" class="form-control"
                                    rows="4"><?= htmlspecialchars($editMembro['descrizione'] ?? $_POST['descrizione'] ?? '') ?></textarea>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="?page=<?= $table ?>.php" class="btn btn-secondary me-md-2">Annulla</a>
                                <button type="submit" class="btn btn-primary">
                                    <?= $editId ? 'Modifica' : 'Aggiungi' ?>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Tabella membri -->
    <div class="row justify-content-center my-5">
        <div class="col-12">
            <div class="card">
                <h2 class="card-header fw-bold text-center">Lista Membri</h2>
                <div class="card-body">
                    <?php if ($membri): ?>
                        <table class="table table-striped border sortable">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Descrizione</th>
                                    <th>Colore</th>
                                    <th>Spese Totali</th>
                                    <th>Importo Totale</th>
                                    <th>Data Aggiunta</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($membri as $m): ?>
                                    <?php
                                    $spese = $db->runQuery('
                                    SELECT id_membro, COUNT(*) as totale_spese, SUM(importo) as totale_importo
                                    FROM spese
                                    WHERE id_membro = ' . $m['id'] . '
                                    GROUP BY id_membro
                                    ');
                                    $color = '#000000';
                                    if (isset($m['dati'])) {
                                        $color = json_decode($m['dati'], true)['color'];
                                    }
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($m['nome']) ?></td>
                                        <td><?= htmlspecialchars($m['descrizione']) ?></td>
                                        <td><span class="badge w-100 h-100" style="background-color: <?= $color ?>; min-height: 1.5rem;"> </span></td>
                                        <td><?= $spese[0]['totale_spese'] ?? 0 ?></td>
                                        <td><?= number_format($spese[0]['totale_importo'] ?? 0.00, 2) ?> €</td>
                                        <td><?= date('d/m/Y H:i', strtotime($m['created_at'])) ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="?page=<?= $table ?>.php&edit=<?= $m['id'] ?>"
                                                    class="btn btn-sm btn-warning">Modifica</a>
                                                <a href="?page=<?= $table ?>.php&delete=<?= $m['id'] ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Sicuro di volerlo eliminare?')">Elimina</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-info">Non ci sono membri inseriti.</div>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</div>