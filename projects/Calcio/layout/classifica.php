<?php
$ar = $dati['A/R'];
$modalita = $_POST['modalita'] ?? 'totale';
$rows = calcolaClassifica($giornate, $modalita, $ar);
?>

<div class="container my-5 classifica">
    <?php include "layout/menu_classifica.php"; ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h1 class="fw-bold text-center">Classifica</h1>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table text-center sortable">
                            <thead>
                                <tr class="fw-bold">
                                    <td scope="col" colspan="2">Info</td>
                                    <td scope="col" colspan="8"><?= ucfirst($modalita) ?></td>
                                </tr>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Squadra</th>
                                    <th scope="col">Pt</th>
                                    <th scope="col">G</th>
                                    <th scope="col">V</th>
                                    <th scope="col">N</th>
                                    <th scope="col">P</th>
                                    <th scope="col">GF</th>
                                    <th scope="col">GS</th>
                                    <th scope="col">DR</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $pos = 1; ?>
                                <?php $mod = ($modalita == "andata" || $modalita == "ritorno") ? "totale" : $modalita; ?>
                                <?php foreach ($rows as $key => $row): ?>
                                    <?php
                                    $colori = getTeamColors($db, $key);
                                    ?>
                                    <tr>
                                        <td scope="row"><?= $pos ?></td>
                                        <td>
                                            <span class="d-flex justify-content-center fw-bold fs-5 rounded-pill px-3"
                                                style="<?= generatestyle($colori['bg'], $colori['text'], $colori['border']) ?>">
                                                <?= htmlspecialchars($key) ?>
                                            </span>
                                        </td>
                                        <td><?= $row[$mod]['punti'] ?></td>
                                        <td><?= $row[$mod]['giocate'] ?></td>
                                        <td><?= $row[$mod]['vinte'] ?></td>
                                        <td><?= $row[$mod]['pari'] ?></td>
                                        <td><?= $row[$mod]['perse'] ?></td>
                                        <td><?= $row[$mod]['gol_fatti'] ?></td>
                                        <td><?= $row[$mod]['gol_subiti'] ?></td>
                                        <td><?= $row[$mod]['diff_reti'] ?></td>
                                    </tr>
                                    <?php $pos++; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">

                </div>
            </div>
        </div>
    </div>
</div>