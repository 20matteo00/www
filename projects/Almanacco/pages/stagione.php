<?php
$seasons = $helper->getSeasons($json);

?>

<div class="container my-5">
    <form class="row g-3 align-items-end p-3 m-0 mb-4 bg-info text-white rounded" method="post">
        <div class="col">
            <label for="season" class="form-label">Seleziona la stagione</label>
            <select class="form-select" name="season" id="season">
                <option value="">-- Seleziona --</option>
                <?php foreach ($seasons as $season): ?>
                    <option value="<?= $season ?>" <?= ((isset($_POST['season']) && $_POST['season'] === $season) || (!isset($_POST['season']) && isset($_GET['season']) && $_GET['season'] === $season)) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($season) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-auto">
            <button class="btn btn-success w-100" name="action" type="submit" value="viewdays">
                Visualizza Giornate
            </button>
        </div>
        <div class="col-auto">
            <button class="btn btn-success w-100" name="action" type="submit" value="viewtable">
                Visualizza Classifica
            </button>
        </div>
        <div class="col-auto">
            <button class="btn btn-danger w-100" type="button" onclick="window.location.href='?page=stagione';">
                Reset
            </button>
        </div>
    </form>
    <?php

    $selectedSeason = $_POST['season'] ?? $_GET['season'] ?? null;
    $action = $_POST['action'] ?? $_GET['action'] ?? null;

    if ($action && $selectedSeason) {
        if ($action === 'viewdays') {
            $helper->viewDaysForSeason($json, $selectedSeason);
        } elseif ($action === 'viewtable') {
            $helper->viewTableForSeason($json, $selectedSeason);
        }
    }


    ?>

</div>