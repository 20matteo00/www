<?php
$jsonFile = Helper::FILE_SQUADRE;
$squadre = $h->getSquadre();

$gruppiDisponibili = $h->getGruppi();

$editIndex = null;
$nomeForm = '';
$descrizioneForm = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $azione = $_POST['azione'] ?? '';
    $nome = trim($_POST['nome'] ?? '');
    $descrizione = trim($_POST['descrizione'] ?? '');
    $gruppi = $_POST['gruppi'] ?? [];
    $testo = $_POST['testo'] ?? '#ffffff';
    $sfondo = $_POST['sfondo'] ?? '#000000';
    $bordo = $_POST['bordo'] ?? '#000000';
    $attacco = (int) ($_POST['attacco'] ?? 50);
    $difesa = (int) ($_POST['difesa'] ?? 50);
    $index = isset($_POST['index']) ? (int) $_POST['index'] : null;

    if ($azione === 'crea' && $nome !== '') {
        $h->addItem($squadre, ['nome' => $nome, 'descrizione' => $descrizione, 'gruppi' => $gruppi, 'testo' => $testo, 'sfondo' => $sfondo, 'bordo' => $bordo, 'attacco' => $attacco, 'difesa' => $difesa]);
    } elseif ($azione === 'elimina' && $index !== null) {
        $h->deleteItem($squadre, $index);
    } elseif ($azione === 'modifica' && $index !== null && $nome !== '') {
        $h->editItem($squadre, $index, ['nome' => $nome, 'descrizione' => $descrizione, 'gruppi' => $gruppi, 'testo' => $testo, 'sfondo' => $sfondo, 'bordo' => $bordo, 'attacco' => $attacco, 'difesa' => $difesa]);
    } elseif ($azione === 'carica_modifica' && $index !== null) {
        $editIndex = $index;
        $item = $h->loadItem($squadre, $index);
        if ($item) {
            $nomeForm = $item['nome'];
            $descrizioneForm = $item['descrizione'];
            $gruppiForm = $item['gruppi'];
            $testoForm = $item['testo'];
            $sfondoForm = $item['sfondo'];
            $bordoForm = $item['bordo'];
            $attaccoForm = $item['attacco'];
            $difesaForm = $item['difesa'];
        }
    }

    $h->saveJson($jsonFile, $squadre);

    if ($azione !== 'carica_modifica') {
        header('Location: ' . $_SERVER['PHP_SELF'] . '?page=squadre');
        exit();
    }
}

?>

<div class="container">
    <?php
    include 'block/squadre/form.php';
    ?>

    <hr>

    <?php
    include 'block/squadre/elenco.php';
    ?>
</div>