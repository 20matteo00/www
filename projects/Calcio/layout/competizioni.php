<div class="container my-5">
    <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered align-middle sortable">
            <thead class="table-dark">
                <tr>
                    <th>Nome</th>
                    <th>Modalità</th>
                    <th>Dati</th>
                    <th>Squadre</th>
                    <th>Vincitore</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comp as $c): ?>
                    <?php
                    $dati = json_decode($c['dati'], true);
                    $squadre = json_decode($c['squadre'], true);
                    ?>
                    <tr>
                        <td>
                            <div class="text-center fw-bold fs-3 ">
                                <?= htmlspecialchars($c['nome']); ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($c['modalita']); ?></td>
                        <td>
                            <?php foreach ($dati as $key => $dato): ?>
                                <?php
                                if ($dato === false)
                                    $dato = "No";
                                elseif ($dato === true)
                                    $dato = "Si";
                                ?>
                                <?php if ($dato != 0): ?>
                                    <span class="fw-bold"><?= $key ?>: <?= $dato ?></span><br>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($squadre as $squadra): ?>
                                    <?php $colori = getTeamColors($db, $squadra); ?>
                                    <a class="fw-bold p-3 rounded-pill"
                                        href="index.php?page=visualizza_squadra.php&squadra=<?= $squadra ?>"
                                        style="<?= generatestyle($colori['bg'], $colori['text'], $colori['border']) ?>">
                                        <?= $squadra ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </td>
                        <td>
                            <?php
                            $giornate = json_decode($c['partite'], true);
                            $finita = json_decode($c['dati'], true)['Completata'];
                            $winner = getCompWinner($giornate, $finita);
                            if ($winner) {
                                $colori = getTeamColors($db, $winner);
                                ?>
                                <a class="fw-bold p-3 rounded-pill"
                                    href="index.php?page=visualizza_squadra.php&squadra=<?= $winner ?>"
                                    style="<?= generatestyle($colori['bg'], $colori['text'], $colori['border']) ?>">
                                    <?= $winner ?>
                                </a>
                                <?php
                            }
                            ?>
                        </td>
                        <td>
                            <div class="d-flex justify-content-around">
                                <a href="index.php?page=visualizza_competizione.php&id=<?= $c['id'] ?>&azione=calendario"
                                    class="btn btn-sm btn-success" title="Visualizza Competizione">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <form method="POST" onsubmit="return confirm('Sei sicuro?');">
                                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Elimina Competizione">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>