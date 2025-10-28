<div class="container my-5">
    <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered align-middle sortable">
            <thead class="table-dark">
                <tr>
                    <th>Squadra</th>
                    <th>Attacco</th>
                    <th>Difesa</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teams as $team): ?>
                    <?php
                    $dati = json_decode($team['dati'], true);
                    ?>
                    <tr>
                        <td>
                            <a class="text-center fw-bold fs-3 rounded-pill d-block" href="index.php?page=visualizza_squadra.php&squadra=<?=$team['nome']?>"
                                style="<?= generatestyle($dati['color']['bg'], $dati['color']['text'], $dati['color']['border']) ?>">
                                <?= htmlspecialchars($team['nome']); ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($dati['power']['attack']); ?></td>
                        <td><?= htmlspecialchars($dati['power']['defense']); ?></td>
                        <td>
                            <div class="d-flex justify-content-around">
                                <button type="button" class="btn btn-sm btn-warning"
                                    onclick='editTeam(<?= json_encode($team) ?>)' title="Modifica Squadra">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <form method="POST" onsubmit="return confirm('Sei sicuro?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="nome" value="<?= $team['nome'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Elimina Squadra">
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