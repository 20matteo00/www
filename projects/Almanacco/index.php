<?php
include $_SERVER['DOCUMENT_ROOT'] . '/utility/path.php';
include $_SERVER['DOCUMENT_ROOT'] . '/utility/classes/DB.php';
include "helper.php";

$json = json_decode(file_get_contents('json/partite.json'), true);
$helper = new Helper();
$seasons = $helper->getSeasons($json);
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'it' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Almanacco</title>

    <?= $bootstrap_css ?? ''; ?>
    <?= $bootstrap_js ?? ''; ?>
    <?= $bootstrap_icons ?? ''; ?>
</head>

<body>
    <div class="bg-secondary sticky-top py-2">
        <div class="container">
            <form class="row g-3 align-items-end mb-4" method="post">
                <nav class="navbar">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <button class="btn btn-primary w-100" name="action" type="submit" value="viewtablealltime">
                                Visualizza Classifica All Time
                            </button>
                        </li>
                    </ul>
                </nav>

                <div class="col">
                    <label for="season" class="form-label">Seleziona la stagione</label>
                    <select class="form-select" name="season" id="season">
                        <option value="">-- Seleziona --</option>
                        <?php foreach ($seasons as $season): ?>
                            <option value="<?= $season ?>" <?= (isset($_POST['season']) && $_POST['season'] === $season) ? 'selected' : '' ?>><?= $season ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-auto">
                    <button class="btn btn-success w-100" name="action" type="submit" value="viewdays">
                        Visualizza Giornate
                    </button>
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary w-100" name="action" type="submit" value="viewtable">
                        Visualizza Classifica
                    </button>
                </div>
                <div class="col-auto">
                    <button class="btn btn-danger w-100" type="button"
                        onclick="window.location.href=window.location.pathname;">
                        Reset
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="container my-3">
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $selectedSeason = $_POST['season'] ?? null;
            $action = $_POST['action'] ?? null;

            if ($action) {
                if ($action === 'viewtablealltime') {
                    $helper->viewTableAllTime($json);
                }
                elseif ($selectedSeason) {
                    if ($action === 'viewdays') {
                        $helper->viewDaysForSeason($json, $selectedSeason);
                    } elseif ($action === 'viewtable') {
                        $helper->viewTableForSeason($json, $selectedSeason);
                    }
                }
            }
        }
        ?>

    </div>
</body>

</html>