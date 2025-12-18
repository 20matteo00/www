<?php
$teams = $helper->getTeams($json, 1);

?>

<div class="container my-5">
    <form class="row g-3 align-items-end p-3 m-0 mb-4 bg-info text-white rounded" method="post">
        <div class="col">
            <label for="team" class="form-label">Seleziona la squadra</label>
            <select class="form-select" name="team" id="team">
                <option value="">-- Seleziona --</option>
                <?php foreach ($teams as $team): ?>
                    <option value="<?= $team ?>" <?= (isset($_POST['team']) && $_POST['team'] === $team) ? 'selected' : '' ?>>
                        <?= $team ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-auto">
            <button class="btn btn-success w-100" name="action" type="submit" value="viewstats">
                Visualizza Statistiche
            </button>
        </div>
        <div class="col-auto">
            <button class="btn btn-danger w-100" type="button" onclick="window.location.href='?page=squadra';">
                Reset
            </button>
        </div>
    </form>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $selectedTeam = $_POST['team'] ?? null;
        $action = $_POST['action'] ?? null;

        if ($action && $selectedTeam) {
            if ($action === 'viewstats') {
                $helper->viewStatsForTeam($json, $selectedTeam);
            }
        }
    }
    ?>
</div>