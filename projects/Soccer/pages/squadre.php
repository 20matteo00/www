<?php
$jsonFile = Helper::FILE_SQUADRE;
$squadre = $h->getSquadre();

$editIndex = null;
$nomeForm = '';
$descrizioneForm = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $azione = $_POST['azione'] ?? '';
    $nome = trim($_POST['nome'] ?? '');
    $descrizione = trim($_POST['descrizione'] ?? '');
    $index = isset($_POST['index']) ? (int) $_POST['index'] : null;

    if ($azione === 'crea' && $nome !== '') {
        $h->addItem($squadre, ['nome' => $nome, 'descrizione' => $descrizione]);
    } elseif ($azione === 'elimina' && $index !== null) {
        $h->deleteItem($squadre, $index);
    } elseif ($azione === 'modifica' && $index !== null && $nome !== '') {
        $h->editItem($squadre, $index, ['nome' => $nome, 'descrizione' => $descrizione]);
    } elseif ($azione === 'carica_modifica' && $index !== null) {
        $editIndex = $index;
        $item = $h->loadItem($squadre, $index);
        if ($item) {
            $nomeForm = $item['nome'];
            $descrizioneForm = $item['descrizione'];
        }
    }

    $h->saveJson($jsonFile, $squadre);

    if ($azione !== 'carica_modifica') {
        header('Location: ' . $_SERVER['PHP_SELF'] . '?page=squadre');
        exit();
    }
}




include 'layout/squadre.php';
?>