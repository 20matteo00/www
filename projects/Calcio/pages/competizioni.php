<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=accedi.php');
    exit;
}
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'create') {
        $nome = $_POST['nome'];
        $modalita = $_POST['modalita'];
        $ar = isset($_POST['andata_ritorno']) ?? false;
        $squadre = $_POST['squadre'] ?? [];
        $partite = [];
        $gironi = 0;
        $partecipanti = 0;
        $fase_finale = 0;
        switch ($modalita) {
            case "campionato":
                $partecipanti = intval($_POST['campionato_partecipanti']);
                $partite = creaCampionato($squadre, $ar);
                break;

            case "eliminazione":
                $partecipanti = intval($_POST['eliminazione_partecipanti']);
                $partite = creaEliminazione($db, $squadre, $ar, $partite);
                break;

            case "gironi":
                $exp = explode("_", $_POST['gironi_partecipanti']);
                $gironi = intval($exp[0]);
                $partecipanti = intval($exp[1]);
                $fase_finale = intval($_POST['fase_finale']);
                break;

            default:
                break;
        }

        if (is_null($nome)) {
            $msg = create_message('danger', 'Devi inserire un nome.');
        }
        if (!is_numeric($partecipanti) || !is_numeric($gironi) || !is_numeric($fase_finale)) {
            $msg = create_message('danger', 'Partecipanti non numerico.');
        }
        if (count($squadre) != $partecipanti) {
            $msg = create_message('danger', 'Il numero di partecipanti non coincide con le squadre selezionate.');
        }

        $dati = [
            'A/R' => $ar,
            'Partecipanti' => $partecipanti,
            'Gironi' => $gironi,
            'Fase Finale' => $fase_finale,
            'Completata' => false,
        ];

        $res = $db->insert('competitions', [
            'id_utente' => $_SESSION['user_id'],
            'nome' => $nome,
            'modalita' => ucwords($modalita),
            'dati' => json_encode($dati),
            'squadre' => json_encode($squadre),
            'partite' => json_encode($partite)
        ]);
        $msg = $res
            ? create_message('success', 'Competizione creata con successo 🎉')
            : create_message('danger', 'Errore durante la creazione.');
    } elseif ($action === "delete") {
        if (isset($_POST['id'])) {
            $db->delete('competitions', [
                'id' => $_POST['id']
            ]);
            $msg = create_message('success', 'Competizione eliminata con successo.');
        } else {
            $msg = create_message('danger', 'Competizione non eliminata');
        }
    }
    header("Location: index.php?page=competizioni.php");
    exit; // sempre mettere exit dopo un redirect
}

// Recupera lista squadre
$teams = $db->select('teams', [
    'where' => ['id_utente' => $_SESSION['user_id']],
    'orderBy' => 'created_at ASC',
]);

// Recupera lista squadre
$comp = $db->select('competitions', [
    'where' => ['id_utente' => $_SESSION['user_id']],
    'orderBy' => 'created_at DESC',
]);
?>

<div class="container mt-5">
    <div class="justify-content-center">
        <div class="card shadow rounded-3">
            <div class="card-header">
                <h3 class="m-0 py-3 text-center fw-bold fs-1">Competizioni</h3>
            </div>
            <div class="card-body p-4">
                <?= $msg ?>
                <form method="POST" id="compForm" autocomplete="on">
                    <input type="hidden" name="action" id="formAction" value="create">

                    <div class="row g-3 mb-2">
                        <div class="col-auto col-md">
                            <label for="nome" class="form-label">Nome Competizione</label>
                            <input type="text" id="nome" name="nome" class="form-control" required>
                        </div>
                        <div class="col-auto col-md">
                            <label for="modalita" class="form-label">Modalità</label>
                            <select name="modalita" id="modalita" class="form-select"
                                onchange="toggleInputs(this.value)">
                                <?php foreach ($modalita as $mod): ?>
                                    <option value="<?= strtolower($mod) ?>"><?= $mod ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-auto col-md d-flex align-items-end">
                            <div class="form-check pb-2">
                                <label for="andata_ritorno" class="form-check-label">Andata e Ritorno</label>
                                <input type="checkbox" id="andata_ritorno" name="andata_ritorno" value="1"
                                    class="form-check-input">
                            </div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div id="campionato-partecipanti" class="d-none col-auto col-md">
                            <label for="campionato_partecipanti" class="form-label">Partecipanti</label>
                            <input type="number" id="campionato_partecipanti" name="campionato_partecipanti" min="4"
                                max="24" value="20" class="form-control">
                        </div>
                        <div id="eliminazione-partecipanti" class="d-none col-auto col-md">
                            <label for="eliminazione_partecipanti" class="form-label">Partecipanti</label>
                            <select name="eliminazione_partecipanti" id="eliminazione_partecipanti" class="form-select">
                                <option value="4">4</option>
                                <option value="8">8</option>
                                <option value="16">16</option>
                                <option value="32">32</option>
                                <option value="64">64</option>
                                <option value="128">128</option>
                            </select>
                        </div>
                        <div id="gironi-partecipanti" class="d-none col-auto col-md">
                            <div class="row">
                                <div class="col-6">
                                    <label for="gironi_partecipanti" class="form-label">Partecipanti</label>
                                    <select name="gironi_partecipanti" id="gironi_partecipanti" class="form-select">
                                        <option value="2_4">2 Gironi da 4 Squadre</option>
                                        <option value="2_8">2 Gironi da 8 Squadre</option>
                                        <option value="4_4">4 Gironi da 4 Squadre</option>
                                        <option value="4_8">4 Gironi da 8 Squadre</option>
                                        <option value="8_4">8 Gironi da 4 Squadre</option>
                                        <option value="8_8">8 Gironi da 8 Squadre</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label mb-2 d-block">Fase Finale</label>
                                    <div class="form-check form-check">
                                        <input class="form-check-input" type="radio" name="fase_finale" id="fase1"
                                            value="1">
                                        <label class="form-check-label" for="fase1">Prima di ogni girone</label>
                                    </div>
                                    <div class="form-check form-check">
                                        <input class="form-check-input" type="radio" name="fase_finale" id="fase2"
                                            value="2" checked>
                                        <label class="form-check-label" for="fase2">Prime 2 di ogni girone</label>
                                    </div>
                                    <div class="form-check form-check">
                                        <input class="form-check-input" type="radio" name="fase_finale" id="fase3"
                                            value="4">
                                        <label class="form-check-label" for="fase3">prime 4 di ogni girone</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto col-md">
                            <label for="squadre" class="form-label">Squadre</label>
                            <select name="squadre[]" id="squadre" class="form-select" multiple="true">
                                <?php foreach ($teams as $team): ?>
                                    <option value="<?= $team['nome'] ?>"><?= $team['nome'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>


                    <button type="submit" class="btn btn-primary w-100 mt-3">Salva Competizione</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include "layout/competizioni.php";
?>