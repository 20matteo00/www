<?php
$teams = $db->select("teams", [
    "where" => ["id_utente" => $_SESSION['user_id']],
    "orderBy" => "nome ASC",
]);

// Valori di default
$squadra_casa = $_POST['squadra_casa'] ?? '';
$squadra_trasferta = $_POST['squadra_trasferta'] ?? '';
$m = $_POST['modalita'] ?? 'generale';
$l = $_POST['luogo'] ?? 'generale';

$msg = '';
$result = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['invia'])) {
    if (!empty($squadra_casa) && !empty($squadra_trasferta) && $squadra_casa !== $squadra_trasferta) {
        if ($m == "generale") {
            $comp = $db->select("competitions", [
                "where" => ["id_utente" => $_SESSION['user_id']],
                "orderBy" => "created_at DESC",
            ]);
        } else {
            $comp = $db->select("competitions", [
                "where" => ["id_utente" => $_SESSION['user_id'], "modalita" => $m],
                "orderBy" => "created_at DESC",
            ]);
        }
        foreach ($comp as $c) {
            $giornate = json_decode($c['partite'], true);
            $finita = json_decode($c['dati'], true)['Completata'];
            $giornate = array_reverse($giornate);
            if (!$finita)
                continue;
            foreach ($giornate as $giornata => $matches) {
                foreach ($matches as $match) {
                    switch ($l) {
                        case 'casa':
                            $condizione = $match['casa'] === $squadra_casa && $match['trasferta'] === $squadra_trasferta;
                            break;
                        case 'trasferta':
                            $condizione = $match['casa'] === $squadra_trasferta && $match['trasferta'] === $squadra_casa;
                            break;
                        default:
                            $condizione = ($match['casa'] === $squadra_casa && $match['trasferta'] === $squadra_trasferta) || ($match['casa'] === $squadra_trasferta && $match['trasferta'] === $squadra_casa);
                            break;
                    }
                    if ($condizione) {
                        $result[] = [
                            [
                                'nome' => $c['nome'],
                                'modalita' => $c['modalita'],
                                'giornata' => $giornata,
                                'casa' => $match['casa'],
                                'gol_casa' => $match['gol_casa'],
                                'trasferta' => $match['trasferta'],
                                'gol_trasferta' => $match['gol_trasferta']
                            ]
                        ];
                    }
                }
            }
        }
        if (empty($result)){
            $msg = "Nessuna Partita Trovata";
        }
    } else {
        $msg = "Squadre non valide o uguali.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancella'])) {
    unset($_POST);
    $squadra_casa = $_POST['squadra_casa'] ?? '';
    $squadra_trasferta = $_POST['squadra_trasferta'] ?? '';
    $m = $_POST['modalita'] ?? 'generale';
    $l = $_POST['luogo'] ?? 'generale';
}
?>
<div class="container py-4">
    <?php if ($msg): ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>
    <form action="" method="post" class="card-body bg-white shadow p-3 rounded">
        <div class="row g-3">
            <!-- Squadra Casa -->
            <div class="col">
                <label for="squadra_casa" class="form-label">Squadra Casa</label>
                <select name="squadra_casa" id="squadra_casa" class="form-select">
                    <option value="">-- Seleziona --</option>
                    <?php foreach ($teams as $team): ?>
                        <?php $colori = getTeamColors($db, $team); ?>
                        <option value="<?= $team['nome'] ?>" <?= $team['nome'] == $squadra_casa ? 'selected' : '' ?>
                            style="<?= generatestyle($colori['bg'], $colori['text'], $colori['border']) ?>">
                            <?= $team['nome'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Squadra Trasferta -->
            <div class="col">
                <label for="squadra_trasferta" class="form-label">Squadra Trasferta</label>
                <select name="squadra_trasferta" id="squadra_trasferta" class="form-select">
                    <option value="">-- Seleziona --</option>
                    <?php foreach ($teams as $team): ?>
                        <?php $colori = getTeamColors($db, $team); ?>
                        <option value="<?= $team['nome'] ?>" <?= $team['nome'] == $squadra_trasferta ? 'selected' : '' ?>
                            style="<?= generatestyle($colori['bg'], $colori['text'], $colori['border']) ?>">
                            <?= $team['nome'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Modalità -->
            <div class="col">
                <label for="modalita" class="form-label">Modalità</label>
                <select name="modalita" id="modalita" class="form-select">
                    <option value="generale">Generale</option>
                    <?php foreach ($modalita as $mod): ?>
                        <option value="<?= $mod ?>" <?= $mod == $m ? 'selected' : '' ?>>
                            <?= ucfirst($mod) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Luogo -->
            <div class="col">
                <label for="luogo" class="form-label">Luogo</label>
                <select name="luogo" id="luogo" class="form-select">
                    <?php foreach ($luogo as $luo): ?>
                        <option value="<?= $luo ?>" <?= $luo == $l ? 'selected' : '' ?>>
                            <?= ucfirst($luo) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Submit Button -->
            <div class="col-auto mt-auto">
                <button type="submit" class="btn btn-primary" name="invia">Invia</button>
                <button type="submit" class="btn btn-danger" name="cancella">Cancella</button>
            </div>
        </div>
    </form>
    <?php if (!empty($result)): ?>
        <div class="table-responsive my-5">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Modalità</th>
                        <th>Giornata</th>
                        <th>Partita</th>
                        <th>Risultato</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result as $res): ?>
                        <?php foreach ($res as $r): ?>
                            <tr>
                                <td><?= $r['nome'] ?></td>
                                <td><?= $r['modalita'] ?></td>
                                <td><?= $r['giornata'] ?></td>
                                <td><?= $r['casa'] ?> vs <?= $r['trasferta'] ?></td>
                                <td><?= $r['gol_casa'] ?> - <?= $r['gol_trasferta'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="statistiche">
            <?php
            $stats = calcolaClassifica($result);
            ?>
            <div class="row">
                <?= renderStatisticheScontriDiretti($squadra_casa, $stats) ?>
                <?= renderStatisticheScontriDiretti($squadra_trasferta, $stats) ?>
            </div>
        </div>
    <?php endif; ?>
</div>