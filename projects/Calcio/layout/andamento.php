<?php
$numgiornate = count($giornate);

$andamento = calcolaAndamento($giornate);
$testadiserie = testaDiSerie($andamento);
?>

<div class="container my-5 andamento">
    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead class="table-light">
                        <tr>
                            <?php for ($i = 1; $i <= $numgiornate; $i++): ?>
                                <th><?= $i ?></th>
                            <?php endfor; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?= renderTestaDiSerieRow($testadiserie, $db) ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="container my-5 andamento">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h1 class="fw-bold text-center">Andamento</h1>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table text-center sortable">
                            <thead>
                                <tr>
                                    <th></th>
                                    <?php for ($i = 1; $i <= $numgiornate; $i++): ?>
                                        <th><?= $i ?></th>
                                    <?php endfor; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // prendo tutte le squadre da tutte le giornate
                                $squadre = [];
                                foreach ($andamento as $giornata => $classifica) {
                                    $squadre = array_merge($squadre, array_keys($classifica));
                                }
                                $squadre = array_unique($squadre); // rimuove duplicati
                                sort($squadre); // ordina alfabeticamente
                                
                                foreach ($squadre as $squadra):
                                    $colori = getTeamColors($db, $squadra);
                                    ?>
                                    <tr>
                                        <td class="fw-bold">
                                            <span class="d-flex justify-content-center fw-bold fs-5 rounded-pill px-3"
                                                style="<?= generatestyle($colori['bg'], $colori['text'], $colori['border']) ?>">
                                                <?= htmlspecialchars($squadra) ?>
                                            </span>
                                        </td>
                                        <?php for ($i = 1; $i <= $numgiornate; $i++):
                                            $giornata = "Giornata $i";
                                            $punti = $andamento[$giornata][$squadra] ?? 0; // 0 se non ha giocato ancora
                                            ?>
                                            <td><?= $punti ?></td>
                                        <?php endfor; ?>
                                    </tr>
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