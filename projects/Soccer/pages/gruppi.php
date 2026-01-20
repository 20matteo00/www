<?php
$jsonFile = $h->getFileGruppiPath();
$gruppi = $h->getGruppi();

$editIndex = null;
$nomeForm = '';
$descrizioneForm = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $azione = $_POST['azione'] ?? '';
    $nome = trim($_POST['nome'] ?? '');
    $descrizione = trim($_POST['descrizione'] ?? '');
    $index = isset($_POST['index']) ? (int)$_POST['index'] : null;

    if ($azione === 'crea' && $nome !== '') {
        $h->addItem($gruppi, ['nome' => $nome, 'descrizione' => $descrizione]);
    } elseif ($azione === 'elimina' && $index !== null) {
        $h->deleteItem($gruppi, $index);
    } elseif ($azione === 'modifica' && $index !== null && $nome !== '') {
        $h->editItem($gruppi, $index, ['nome' => $nome, 'descrizione' => $descrizione]);
    } elseif ($azione === 'carica_modifica' && $index !== null) {
        $editIndex = $index;
        $item = $h->loadItem($gruppi, $index);
        if ($item) {
            $nomeForm = $item['nome'];
            $descrizioneForm = $item['descrizione'];
        }
    }

    $h->saveJson($jsonFile, $gruppi);

    if ($azione !== 'carica_modifica') {
        header('Location: ' . $_SERVER['PHP_SELF'] . '?page=gruppi');
        exit();
    }
}

?>

<div class="container">
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
                                <input type="text" class="form-control" name="nome" placeholder="Nome del gruppo" value="<?= htmlspecialchars($nomeForm) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="descrizione" class="form-label">Descrizione</label>
                                <input type="text" class="form-control" name="descrizione" placeholder="Descrizione breve" value="<?= htmlspecialchars($descrizioneForm) ?>">
                            </div>
                        </div>

                        <?php if ($editIndex !== null): ?>
                            <input type="hidden" name="index" value="<?= $editIndex ?>">
                        <?php endif; ?>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary" name="azione" value="<?= $editIndex !== null ? 'modifica' : 'crea' ?>">
                                <?= $editIndex !== null ? 'Salva Modifica' : 'Crea' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <hr>

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
                                    <button type="submit" class="btn btn-warning w-100" name="azione" value="carica_modifica">Modifica</button>
                                </form>
                                <form method="post" class="flex-grow-1">
                                    <input type="hidden" name="index" value="<?= $i ?>">
                                    <button type="submit" class="btn btn-danger w-100" name="azione" value="elimina">Elimina</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
