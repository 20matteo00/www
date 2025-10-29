<?php
checkLogin();

$table = 'sottocategorie';

$editId = $_GET['edit'] ?? null;

// Recupera le categorie per la select
$categorie = $db->select('categorie', ['orderBy' => 'created_at ASC']);

// Inserimento/modifica sottocategoria
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $descrizione = $_POST['descrizione'] ?? '';
    $id_categoria = $_POST['categoria'] ?? '';
    $color = $_POST['color'] ?? '#000000';

    if (!$id_categoria) {
        $msg = "<div class='alert alert-danger'>Devi selezionare una categoria!</div>";
    } else {
        if ($editId) {
            // Modifica
            $db->update($table, [
                'nome' => $nome,
                'descrizione' => $descrizione,
                'id_categoria' => $id_categoria,
                'dati' => json_encode(['color' => $color])

            ], ['id' => $editId]);
            $msg = "<div class='alert alert-success'>Sottocategoria modificata correttamente.</div>";
        } else {
            // Nuovo
            $db->insert($table, [
                'nome' => $nome,
                'descrizione' => $descrizione,
                'id_categoria' => $id_categoria,
                'dati' => json_encode(['color' => $color]),
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $msg = "<div class='alert alert-success'>Sottocategoria inserita correttamente.</div>";
        }
        header('Location: index.php?page=' . $table . '.php');
        exit();
    }
}

// Elimina sottocategoria
if (isset($_GET['delete'])) {
    $db->delete($table, ['id' => (int) $_GET['delete']]);
    $msg = "<div class='alert alert-danger'>Sottocategoria eliminata!</div>";
    header('Location: index.php?page=' . $table . '.php');
    exit();
}

// Recupera lista sottocategorie
$sottocategorie = $db->runQuery(
    'SELECT sc.*, IFNULL(SUM(s.importo), 0) AS totale_importo
            FROM sottocategorie sc
            LEFT JOIN spese s ON s.id_sottocategoria = sc.id
            GROUP BY sc.id, sc.nome
            ORDER BY totale_importo DESC'
);

// Recupero sottocategoria per edit
$editSottocategoria = null;
if ($editId) {
    $editSottocategoria = $db->select($table, [
        'where' => ['id' => $editId]
    ]);
    $editSottocategoria = $editSottocategoria ? $editSottocategoria[0] : null;
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow rounded-3">
                <div class="card-header">
                    <h2 class="m-0 py-3 text-center fw-bold fs-1">Aggiungi Sottocategorie</h2>
                </div>
                <div class="card-body p-4">
                    <?= $msg ?? '' ?>
                    <form method="POST" autocomplete="on">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="nome">Nome <span class="text-danger">*</span></label>
                                <input type="text" name="nome" id="nome" class="form-control" required
                                    value="<?= htmlspecialchars($editSottocategoria['nome'] ?? $_POST['nome'] ?? '') ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="categoria">Categoria <span
                                        class="text-danger">*</span></label>
                                <select name="categoria" id="categoria" class="form-select" required>
                                    <option value="">Seleziona una categoria</option>
                                    <?php foreach ($categorie as $c): ?>
                                        <option value="<?= $c['id'] ?>" <?= ($editSottocategoria['id_categoria'] ?? $_POST['categoria'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($c['nome']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="color">Colore</label>
                                <input type="color" name="color" id="color" class="form-control" required
                                    value="<?= htmlspecialchars(json_decode($editSottocategoria['dati'], true)['color'] ?? $_POST['color'] ?? '') ?>">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="descrizione">Descrizione</label>
                                <textarea name="descrizione" id="descrizione" class="form-control"
                                    rows="4"><?= htmlspecialchars($editSottocategoria['descrizione'] ?? $_POST['descrizione'] ?? '') ?></textarea>
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
    <!-- Tabella sottocategorie -->
    <div class="row justify-content-center my-5">
        <div class="col-12">
            <div class="card">
                <h2 class="card-header fw-bold text-center">Lista Sottocategorie</h2>
                <div class="card-body">
                    <?php if ($sottocategorie): ?>
                        <table class="table table-striped border sortable">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Categoria</th>
                                    <th>Descrizione</th>
                                    <th>Colore</th>
                                    <th>Spese Totali</th>
                                    <th>Importo Totale</th>
                                    <th>Data Aggiunta</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sottocategorie as $s):
                                    $cat = array_filter($categorie, fn($c) => $c['id'] == $s['id_categoria']);
                                    $categoria_nome = count($cat) ? reset($cat)['nome'] : 'N/D';

                                    $spese = $db->runQuery('
                                    SELECT id_sottocategoria, COUNT(*) as totale_spese, SUM(importo) as totale_importo
                                    FROM spese
                                    WHERE id_sottocategoria = ' . $s['id'] . '
                                    GROUP BY id_sottocategoria
                                    ');
                                    $color = '#000000';
                                    if (isset($s['dati'])) {
                                        $color = json_decode($s['dati'], true)['color'];
                                    }
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($s['nome']) ?></td>
                                        <td><?= htmlspecialchars($categoria_nome) ?></td>
                                        <td><?= htmlspecialchars($s['descrizione']) ?></td>
                                        <td><span class="badge w-100 h-100"
                                                style="background-color: <?= $color ?>; min-height: 1.5rem;"> </span></td>
                                        <td><?= $spese[0]['totale_spese'] ?? 0 ?></td>
                                        <td><?= number_format($spese[0]['totale_importo'] ?? 0.00, 2) ?> €</td>
                                        <td><?= date('d/m/Y H:i', strtotime($s['created_at'])) ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="?page=<?= $table ?>.php&edit=<?= $s['id'] ?>"
                                                    class="btn btn-sm btn-warning">Modifica</a>
                                                <a href="?page=<?= $table ?>.php&delete=<?= $s['id'] ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Sicuro di volerlo eliminare?')">Elimina</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-info">Non ci sono sottocategorie inserite.</div>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</div>