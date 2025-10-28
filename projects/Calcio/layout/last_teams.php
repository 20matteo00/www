<div class="container my-4">
    <h1 class="fw-bold fs-1 mb-3">Le tue ultime squadre</h1>
    <div class="d-flex flex-wrap gap-3">
        <?php foreach ($last_teams as $team):
            $dati = json_decode($team['dati'], true);
            $bg = $dati['color']['bg'] ?? '#eee';
            $text = $dati['color']['text'] ?? '#000';
            $border = $dati['color']['border'] ?? '#ccc';
            $attack = $dati['power']['attack'] ?? 'N/A';
            $defense = $dati['power']['defense'] ?? 'N/A';
            ?>
            <a class="col badge rounded-pill d-flex align-items-center shadow-sm p-3" href="index.php?page=visualizza_squadra.php&squadra=<?=$team['nome']?>"
                style="<?= generatestyle($bg, $text, $border) ?>">
                <strong class="me-3 flex-grow-1 fs-3"><?= htmlspecialchars($team['nome']) ?></strong>
                <div class="d-flex flex-column text-end fs-6 gap-2">
                    <span>⚔️ <?= htmlspecialchars($attack) ?></span>
                    <span>🛡️ <?= htmlspecialchars($defense) ?></span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
    <div class="mt-5">
        <p class="m-0 text-center"><a class="btn btn-primary rounded-pill" href="index.php?page=squadre.php">Vedi tutte le
                squadre</a></p>
    </div>
</div>