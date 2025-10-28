<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=accedi.php');
    exit;
}
if (!isset($_GET['id'])) {
    header('Location: index.php?page=competizioni.php');
    exit;
}

$_POST['azione'] = $_POST['azione'] ?? $_GET['azione'] ?? 'calendario';

if (isset($_POST['azione'])) {
    $comp = $db->select("competitions", [
        "where" => ["id" => $_GET['id'], "id_utente" => $_SESSION['user_id']],
    ]);
    $dati = json_decode($comp[0]['dati'], true);
    $giornate = json_decode($comp[0]['partite'], true);
    $squadre = json_decode($comp[0]['squadre'], true);

    $modalita = $comp[0]['modalita'];
    $finita = $dati['Completata'];
    $ar = $dati['A/R'];
    $disabled = ($finita==1) ? 'disabled' : '';
    $gamenull = checkAllMatches($giornate);

    $azione = $_POST['azione'];
    switch ($azione) {
        case "calendario":
        case "classifica":
        case "andamento":
        case "tabellone":
        case "statistiche":
            include "layout/$azione.php";
            break;
        case "simula":
            foreach ($giornate as &$giornata) {
                foreach ($giornata as &$partita) {
                    $goal = simulapartita($db, $partita['casa'], $partita['trasferta']);
                    $partita['gol_casa'] = $partita['gol_casa'] ?? $goal['gol_casa'];
                    $partita['gol_trasferta'] = $partita['gol_trasferta'] ?? $goal['gol_trasferta'];
                }
            }
            $db->update(
                'competitions',           // tabella
                ['partite' => json_encode($giornate)], // dati da aggiornare
                ['id' => $_GET['id']]             // condizione WHERE
            );
            include "layout/calendario.php";
            break;
        case "cancella":
            foreach ($giornate as &$giornata) {
                foreach ($giornata as &$partita) {
                    $partita['gol_casa'] = null;
                    $partita['gol_trasferta'] = null;
                }
            }
            $db->update(
                'competitions',           // tabella
                ['partite' => json_encode($giornate)], // dati da aggiornare
                ['id' => $_GET['id']]             // condizione WHERE
            );
            include "layout/calendario.php";
            break;
        case "chiudi":
            setCompletataTrue($db, $_GET['id']);
            include "layout/calendario.php";
            break;
        default:
            include "layout/calendario.php";
            break;
    }
}

?>



<?php
include "layout/menu_competizione.php";
?>