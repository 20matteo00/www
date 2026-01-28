<?php
$jsonFile = Helper::FILE_GRUPPI;
$gruppi = $h->getGruppi();

$editIndex = null;
$nomeForm = '';
$descrizioneForm = '';
$idForm = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $azione = $_POST['azione'] ?? '';
    $nome = trim($_POST['nome'] ?? '');
    $descrizione = trim($_POST['descrizione'] ?? '');
    $id = isset($_POST['id']) ? (int) $_POST['id'] : null;
    $index = isset($_POST['index']) ? (int) $_POST['index'] : null;

    if ($azione === 'crea' && $nome !== '') {
        $lastID = $h->getUltimoID($gruppi);
        $h->addItem($gruppi, ['id' => $lastID + 1, 'nome' => $nome, 'descrizione' => $descrizione]);
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
    <?php
    include 'block/gruppi/form.php';
    ?>

    <hr>

    <?php
    include 'block/gruppi/elenco.php';
    ?>
</div>