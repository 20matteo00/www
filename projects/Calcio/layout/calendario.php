<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $azione_partita = $_POST['azione_partita'] ?? null;
    $azione_giornata = $_POST['azione_giornata'] ?? null;
    if ($azione_partita) {
        $giornata = $_POST['giornata'] ?? null;
        $casa = $_POST['squadra_casa'] ?? null;
        $trasferta = $_POST['squadra_trasferta'] ?? null;
        switch ($azione_partita) {
            case "salva":
                foreach ($giornate[$giornata] as &$partita) {
                    if ($partita['casa'] === $casa && $partita['trasferta'] === $trasferta) {
                        $partita['gol_casa'] = $_POST['gol_casa'] === "" ? null : (int) $_POST['gol_casa'];
                        $partita['gol_trasferta'] = $_POST['gol_trasferta'] === "" ? null : (int) $_POST['gol_trasferta'];
                        break; // una volta trovata la partita esci dal ciclo
                    }
                }
                $db->update(
                    'competitions',           // tabella
                    ['partite' => json_encode($giornate)], // dati da aggiornare
                    ['id' => $_GET['id']]             // condizione WHERE
                );
                break;
            case "simula":
                foreach ($giornate[$giornata] as &$partita) {
                    if ($partita['casa'] === $casa && $partita['trasferta'] === $trasferta) {
                        $goal = simulapartita($db, $partita['casa'], $partita['trasferta']);
                        $partita['gol_casa'] = $partita['gol_casa'] ?? $goal['gol_casa'];
                        $partita['gol_trasferta'] = $partita['gol_trasferta'] ?? $goal['gol_trasferta'];
                        break; // una volta trovata la partita esci dal ciclo
                    }
                }
                $db->update(
                    'competitions',           // tabella
                    ['partite' => json_encode($giornate)], // dati da aggiornare
                    ['id' => $_GET['id']]             // condizione WHERE
                );
                break;
            case "cancella":
                foreach ($giornate[$giornata] as &$partita) {
                    if ($partita['casa'] === $casa && $partita['trasferta'] === $trasferta) {
                        $partita['gol_casa'] = null;
                        $partita['gol_trasferta'] = null;
                        break; // una volta trovata la partita esci dal ciclo
                    }
                }
                $db->update(
                    'competitions',           // tabella
                    ['partite' => json_encode($giornate)], // dati da aggiornare
                    ['id' => $_GET['id']]             // condizione WHERE
                );
                break;

            default:
                break;
        }
    } elseif ($azione_giornata) {
        $giornata = $_POST['giornata'] ?? null;
        switch ($azione_giornata) {
            case "simula":
                foreach ($giornate[$giornata] as &$partita) {
                    $goal = simulapartita($db, $partita['casa'], $partita['trasferta']);
                    $partita['gol_casa'] = $partita['gol_casa'] ?? $goal['gol_casa'];
                    $partita['gol_trasferta'] = $partita['gol_trasferta'] ?? $goal['gol_trasferta'];
                }
                $db->update(
                    'competitions',           // tabella
                    ['partite' => json_encode($giornate)], // dati da aggiornare
                    ['id' => $_GET['id']]             // condizione WHERE
                );
                break;
            case "cancella":
                foreach ($giornate[$giornata] as &$partita) {
                    $partita['gol_casa'] = null;
                    $partita['gol_trasferta'] = null;
                }
                $db->update(
                    'competitions',           // tabella
                    ['partite' => json_encode($giornate)], // dati da aggiornare
                    ['id' => $_GET['id']]             // condizione WHERE
                );
                break;

            default:
                break;
        }
    }

    if ($modalita) creaEliminazione($db, $squadre,$ar, $giornate, $_GET['id']);

    header("Location: index.php?page=visualizza_competizione.php&id=" . $_GET['id'] . "&azione=calendario#" . $giornata);
    exit; // sempre mettere exit dopo un redirect

}
?>

<div class="container my-5 calendario">
    <div class="row">
        <?php foreach ($giornate as $key => $g): ?>
            <div class="col-12 col-xl-6 my-3">
                <div class="card">
                    <div class="card-header" id="<?= $key ?>">
                        <h1 class="fw-bold text-center"><?= $key ?></h1>
                    </div>
                    <div class="card-body">
                        <?php foreach ($g as $partite): ?>
                            <?php
                            $colori_casa = getTeamColors($db, $partite['casa']);
                            $colori_trasferta = getTeamColors($db, $partite['trasferta']);
                            ?>
                            <form method="post" class="row text-center align-items-center border-bottom py-2 g-2 overflow-auto flex-nowrap">

                                <!-- Squadra Casa -->
                                <div class="col text-end">
                                    <span class="d-flex w-100 justify-content-center fw-bold fs-5 rounded-pill px-3"
                                        style="<?= generatestyle($colori_casa['bg'], $colori_casa['text'], $colori_casa['border']) ?>">
                                        <?= htmlspecialchars($partite['casa']) ?>
                                    </span>
                                </div>

                                <!-- Gol Casa / vs / Gol Trasferta -->
                                <div class="col-auto d-flex align-items-center gap-1">
                                    <input type="number" name="gol_casa" min="0" <?= $disabled ?>
                                        value="<?= htmlspecialchars($partite['gol_casa'] ?? '') ?>" placeholder="-"
                                        class="form-control form-control-sm text-center" style="width: 50px;">

                                    <span class="text-muted">-</span>

                                    <input type="number" name="gol_trasferta" min="0" <?= $disabled ?>
                                        value="<?= htmlspecialchars($partite['gol_trasferta'] ?? '') ?>" placeholder="-"
                                        class="form-control form-control-sm text-center" style="width: 50px;">
                                </div>

                                <!-- Squadra Trasferta -->
                                <div class="col text-start">
                                    <span class="d-flex w-100 justify-content-center fw-bold fs-5 rounded-pill px-3"
                                        style="<?= generatestyle($colori_trasferta['bg'], $colori_trasferta['text'], $colori_trasferta['border']) ?>">
                                        <?= htmlspecialchars($partite['trasferta']) ?>
                                    </span>
                                </div>

                                <!-- Mini-div bottoni azione partita -->
                                <div class="col-auto d-flex gap-1">
                                    <button type="submit" name="azione_partita" value="salva" class="btn btn-success btn-sm" <?= $disabled ?>>
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button type="submit" name="azione_partita" value="simula" class="btn btn-warning btn-sm" <?= $disabled ?>>
                                        <i class="bi bi-play-circle"></i>
                                    </button>
                                    <button type="submit" name="azione_partita" value="cancella" class="btn btn-danger btn-sm" <?= $disabled ?>>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <!-- Input nascosti per info della partita -->
                                <input type="hidden" name="squadra_casa" value="<?= htmlspecialchars($partite['casa']) ?>">
                                <input type="hidden" name="squadra_trasferta"
                                    value="<?= htmlspecialchars($partite['trasferta']) ?>">
                                <input type="hidden" name="giornata" value="<?= htmlspecialchars($key ?? '') ?>">
                                <input type="hidden" name="azione" value="calendario">
                            </form>
                        <?php endforeach; ?>
                    </div>
                    <div class="card-footer">
                        <form method="post" class="d-flex justify-content-around py-2 g-2">
                            <input type="hidden" name="giornata" value="<?= htmlspecialchars($key ?? '') ?>">
                            <!-- <button type="submit" name="azione_giornata" value="salva" class="btn btn-success mx-2">
                                <i class="bi bi-check-lg"></i> Salva
                            </button> -->
                            <button type="submit" name="azione_giornata" value="simula" class="btn btn-warning mx-2" <?= $disabled ?>>
                                <i class="bi bi-play-circle"></i> Simula
                            </button>
                            <button type="submit" name="azione_giornata" value="cancella" class="btn btn-danger mx-2" <?= $disabled ?>>
                                <i class="bi bi-trash"></i> Cancella
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>