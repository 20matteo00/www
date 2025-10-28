<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=accedi.php');
    exit;
}
if (!isset($_GET['squadra'])) {
    header('Location: index.php?page=squadre.php');
    exit;
}
$team = $_GET['squadra'];
$colori = getTeamColors($db, $team);
$power = getTeamStats($db, $team);
$comp_non_finite = getCompetitionsByTeams($db, $team, false);
$comp_finite = getCompetitionsByTeams($db, $team);
?>

<div class="container my-5">
    <h1 class="badge rounded-pill d-flex align-items-center shadow-sm p-3"
        style="<?= generatestyle($colori['bg'], $colori['text'], $colori['border']) ?>">
        <strong class="me-3 flex-grow-1 fs-3"><?= htmlspecialchars($team) ?></strong>
        <div class="d-flex flex-column text-end fs-6 gap-2">
            <span>⚔️ <?= htmlspecialchars($power['attack']) ?></span>
            <span>🛡️ <?= htmlspecialchars($power['defense']) ?></span>
        </div>
    </h1>
    <?php if (!empty($comp_non_finite)): ?>
        <p class="h2 fw-bold mt-4">Competizioni In corso:</p>
        <div class="d-flex flex-wrap gap-3">
            <?php foreach ($comp_non_finite as $comp): ?>
                <div class="col badge rounded-pill d-flex align-items-center shadow-sm p-3">
                    <a class="me-3 flex-grow-1 fs-3 fw-bold bg-white text-black text-decoration-none"
                        href="index.php?page=visualizza_competizione.php&id=<?= $comp['id'] ?>"><?= htmlspecialchars($comp['nome']) ?></a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($comp_finite)): ?>
        <p class="h2 fw-bold mt-4">Competizioni Finite:</p>
        <div class="d-flex flex-wrap gap-3">
            <?php foreach ($comp_finite as $comp): ?>
                <?php
                $giornate = json_decode($comp['partite'], true);
                $ar = json_decode($comp['dati'], true)['A/R'];
                $class = calcolaClassifica($giornate, 'totale', $ar);
                $punti = $class[$team]['totale']['punti'];
                $diff_reti = $class[$team]['totale']['diff_reti'];
                $posizione = array_search($team, array_keys($class))+1; // ti dà 0
                ?>
                <a class="col badge rounded-pill d-flex align-items-center shadow-sm p-3 fw-bold bg-white text-black text-decoration-none"
                    href="index.php?page=visualizza_competizione.php&id=<?= $comp['id'] ?>">
                    <div class="flex-grow-1 fs-3">
                        <?= htmlspecialchars($comp['nome']) ?>
                    </div>
                    <div class="d-flex flex-column text-start fs-6 gap-2">
                        <span>📊 <?= htmlspecialchars("# ".$posizione."°") ?></span>
                        <span>🏆 <?= htmlspecialchars("PT ".$punti) ?></span>
                        <span>⚖️ <?= htmlspecialchars("DR ".$diff_reti) ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if (empty($comp_finite) && empty($comp_non_finite)): ?>
        <div class="alert alert-info mt-5" role="alert">
            Nessuna competizione al momento.
        </div>
    <?php endif; ?>
</div>

<?php
?>