<?php
checkLogin();

$table = 'categorie';

$editId = $_GET['edit'] ?? null;

// Inserimento categoria
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

// Elimina categoria
if (isset($_GET['delete'])) {
    $db->delete($table, ['id' => (int) $_GET['delete']]);
    $msg = "<div class='alert alert-danger'>Categoria eliminata!</div>";
    header('Location: index.php?page=' . $table . '.php');
    exit();
}

// Recupera lista categorie
$categorie = $db->select($table, [
    'orderBy' => 'nome ASC',
]);

// Se sto modificando recupero la categoria
$editCategoria = null;
if ($editId) {
    $editCategoria = $db->select($table, [
        'where' => ['id' => $editId]
    ]);
    $editCategoria = $editCategoria ? $editCategoria[0] : null;
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow rounded-3">
                <div class="card-header">
                    <h2 class="m-0 py-3 text-center fw-bold fs-1">Aggiungi Categorie</h2>
                </div>
                <div class="card-body p-4">
                    <?= $msg ?>
                    <form method="POST" autocomplete="on">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="nome">Nome <span class="text-danger">*</span></label>
                                <input type="text" name="nome" id="nome" class="form-control" required
                                    value="<?= htmlspecialchars($editCategoria['nome'] ?? $_POST['nome'] ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="color">Colore</label>
                                <input type="color" name="color" id="color" class="form-control" required
                                    value="<?= htmlspecialchars(json_decode($editCategoria['dati'], true)['color'] ?? $_POST['color'] ?? '') ?>">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="descrizione">Descrizione</label>
                                <textarea name="descrizione" id="descrizione" class="form-control"
                                    rows="4"><?= htmlspecialchars($editCategoria['descrizione'] ?? $_POST['descrizione'] ?? '') ?></textarea>
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
    <!-- Tabella categorie -->
    <div class="row justify-content-center my-5">
        <div class="col-12">
            <div class="card">
                <h2 class="card-header fw-bold text-center">Lista Categorie</h2>
                <div class="card-body">
                    <?php if ($categorie): ?>
                        <table class="table table-striped border sortable">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Descrizione</th>
                                    <th>Colore</th>
                                    <th>Sottocategorie</th>
                                    <th>Data Aggiunta</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categorie as $c): ?>
                                    <?php
                                    $subcat = $db->runQuery('
                                    SELECT id_categoria, COUNT(*) as totale_subcat
                                    FROM sottocategorie
                                    WHERE id_categoria = ' . $c['id'] . '
                                    GROUP BY id_categoria
                                    ');
                                    $color = '#000000';
                                    if (isset($c['dati'])) {
                                        $color = json_decode($c['dati'], true)['color'];
                                    }
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($c['nome']) ?></td>
                                        <td><?= htmlspecialchars($c['descrizione']) ?></td>
                                        <td><span class="badge w-100 h-100"
                                                style="background-color: <?= $color ?>; min-height: 1.5rem;"> </span></td>
                                        <td><?= $subcat[0]['totale_subcat'] ?? 0 ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="?page=<?= $table ?>.php&edit=<?= $c['id'] ?>"
                                                    class="btn btn-sm btn-warning">Modifica</a>
                                                <a href="?page=<?= $table ?>.php&delete=<?= $c['id'] ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Sicuro di volerlo eliminare?')">Elimina</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-info">Non ci sono categorie inserite.</div>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</div>