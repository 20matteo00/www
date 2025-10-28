<?php
sort($squadre);

// costruisco una mappa per velocizzare i lookup partita
$partiteMap = [];
foreach ($giornate as $giornata) {
    foreach ($giornata as $partita) {
        $casa = $partita['casa'];
        $trasferta = $partita['trasferta'];
        $partiteMap[$casa][$trasferta] = $partita;
    }
}
?>

<div class="container my-5 tabellone">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h1 class="fw-bold text-center">Tabellone</h1>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead>
                                <tr>
                                    <th scope="col"></th>
                                    <?php foreach ($squadre as $squadra): ?>
                                        <?php $colori = getTeamColors($db, $squadra); ?>
                                        <th scope="col">
                                            <span class="d-flex justify-content-center fw-bold fs-5 rounded-pill px-3"
                                                style="<?= generatestyle($colori['bg'], $colori['text'], $colori['border']) ?>">
                                                <?= htmlspecialchars(substr($squadra, 0, 3)) ?>
                                            </span>
                                        </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($squadre as $rigaSquadra): ?>
                                    <tr>
                                        <th>
                                            <?php $colori = getTeamColors($db, $rigaSquadra); ?>
                                            <span class="d-flex justify-content-center fw-bold fs-5 rounded-pill px-3"
                                                style="<?= generatestyle($colori['bg'], $colori['text'], $colori['border']) ?>">
                                                <?= htmlspecialchars($rigaSquadra) ?>
                                            </span>
                                        </th>
                                        <?php foreach ($squadre as $colonnaSquadra): ?>
                                            <?php if ($rigaSquadra === $colonnaSquadra): ?>
                                                <td class="bg-light"></td>
                                            <?php else: ?>
                                                <?php if (isset($partiteMap[$rigaSquadra][$colonnaSquadra])): ?>
                                                    <?php $p = $partiteMap[$rigaSquadra][$colonnaSquadra]; ?>
                                                    <td><?= $p['gol_casa'] ?> - <?= $p['gol_trasferta'] ?></td>
                                                <?php else: ?>
                                                    <td></td>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-center">
                </div>
            </div>
        </div>
    </div>
</div>
