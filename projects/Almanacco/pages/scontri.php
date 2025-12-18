<?php
$squadre = $helper->getTeams($json, 1);
$sede = [
    's1' => 'Casa Squadra 1',
    's2' => 'Casa Squadra 2',
    'tutto' => 'Tutto'
];
?>

<div class="container my-5">
    <form class="row g-3 align-items-end p-3 m-0 mb-4 bg-info text-white rounded" method="post">
        <div class="col">
            <label for="squadra1" class="form-label">Seleziona la prima squadra</label>
            <select class="form-select" name="squadra1" id="squadra1">
                <option value="">-- Seleziona --</option>
                <?php foreach ($squadre as $squadra): ?>
                    <option value="<?= $squadra ?>" <?= (isset($_POST['squadra1']) && $_POST['squadra1'] === $squadra) ? 'selected' : '' ?>><?= $squadra ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col">
            <label for="squadra2" class="form-label">Seleziona la seconda squadra</label>
            <select class="form-select" name="squadra2" id="squadra2">
                <option value="">-- Seleziona --</option>
                <?php foreach ($squadre as $squadra): ?>
                    <option value="<?= $squadra ?>" <?= (isset($_POST['squadra2']) && $_POST['squadra2'] === $squadra) ? 'selected' : '' ?>><?= $squadra ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto">
            <label for="sede" class="form-label">Seleziona la sede</label>
            <select class="form-select" name="sede" id="sede">
                <option value="">-- Seleziona --</option>
                <?php foreach ($sede as $key => $value): ?>
                    <option value="<?= $key ?>" <?= (isset($_POST['sede']) && $_POST['sede'] === $key) ? 'selected' : '' ?>><?= $value ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto">
            <button class="btn btn-success w-100" type="submit">
                Invia
            </button>
        </div>
        <div class="col-auto">
            <button class="btn btn-danger w-100" type="button" onclick="window.location.href='?page=scontri';">
                Reset
            </button>
        </div>
    </form>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $squadra1 = $_POST['squadra1'] ?? null;
        $squadra2 = $_POST['squadra2'] ?? null;
        $sedeSelezionata = $_POST['sede'] ?? null;
        if ($squadra1 && $squadra2 && $sedeSelezionata) {
            $helper->viewMatchesBetweenTeams($json, $squadra1, $squadra2, $sedeSelezionata);
        }
    }
    ?>

</div>