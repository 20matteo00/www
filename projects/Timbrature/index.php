<?php
include $_SERVER['DOCUMENT_ROOT'] . '/utility/path.php';
// ---- CONFIG ---- //
$FILE = __DIR__ . "/timbrature.json";

// Carica il JSON esistente o crea un nuovo array
$data = file_exists($FILE) ? json_decode(file_get_contents($FILE), true) : [];

// Funzione per salvare
function salva_json($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

// --- GESTIONE ELIMINA --- //
if (isset($_GET["elimina"])) {
    list($anno, $mese, $giorno, $id) = explode("-", $_GET["elimina"]);
    unset($data[$anno][$mese][$giorno][$id]);

    if (empty($data[$anno][$mese][$giorno])) unset($data[$anno][$mese][$giorno]);
    if (empty($data[$anno][$mese])) unset($data[$anno][$mese]);
    if (empty($data[$anno])) unset($data[$anno]);

    salva_json($FILE, $data);
    header("Location: index.php");
    exit;
}

// --- GESTIONE MODIFICA --- //
$editing = false;
$edit_id = null;
$edit_data = null;

if (isset($_GET["modifica"])) {
    $editing = true;
    list($anno, $mese, $giorno, $id) = explode("-", $_GET["modifica"]);
    $edit_id = "$anno-$mese-$giorno-$id";
    $edit_data = $data[$anno][$mese][$giorno][$id];
}

// --- GESTIONE INSERIMENTO / UPDATE --- //
$errore = "";

if (isset($_POST["giorno"])) {

    $giorno = $_POST["giorno"];
    $orario = $_POST["orario"];
    $verso = $_POST["verso"];

    list($anno, $mese, $gg) = explode("-", $giorno);

    // MODIFICA → rimuovi la vecchia timbratura
    if (isset($_POST["edit_id"]) && $_POST["edit_id"] != "") {
        list($a, $m, $g, $id) = explode("-", $_POST["edit_id"]);
        unset($data[$a][$m][$g][$id]);

        if (empty($data[$a][$m][$g])) unset($data[$a][$m][$g]);
        if (empty($data[$a][$m])) unset($data[$a][$m]);
        if (empty($data[$a])) unset($data[$a]);
    }

    // Controllo duplicati
    if (isset($data[$anno][$mese][$gg])) {
        foreach ($data[$anno][$mese][$gg] as $t) {
            if ($t["orario"] === $orario) {
                $errore = "Timbratura già esistente per questa data e ora.";
            }
        }
    }

    if ($errore === "") {
        if (!isset($data[$anno])) $data[$anno] = [];
        if (!isset($data[$anno][$mese])) $data[$anno][$mese] = [];
        if (!isset($data[$anno][$mese][$gg])) $data[$anno][$mese][$gg] = [];

        $new_id = count($data[$anno][$mese][$gg]) + 1;

        $data[$anno][$mese][$gg][$new_id] = [
            "orario" => $orario,
            "verso"  => $verso
        ];

        salva_json($FILE, $data);
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timbrature</title>
    <?= $bootstrap_css ?? '' ?>
    <?= $bootstrap_js ?? '' ?>
    <?= $bootstrap_icons ?? '' ?>
</head>

<body class="bg-light">

<div class="container py-4">

    <h2 class="mb-4"><?= $editing ? "Modifica Timbratura" : "Inserisci Timbratura" ?></h2>

    <?php if ($errore): ?>
        <div class="alert alert-danger"><?= $errore ?></div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <form method="POST" class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Data</label>
                    <input type="date" name="giorno" class="form-control" required
                           value="<?= $editing ? explode("-", $edit_id)[0] . "-" . explode("-", $edit_id)[1] . "-" . explode("-", $edit_id)[2] : "" ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Orario</label>
                    <input type="time" name="orario" class="form-control" required
                           value="<?= $editing ? $edit_data["orario"] : "" ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Verso</label>
                    <select name="verso" class="form-select">
                        <option value="entrata" <?= $editing && $edit_data["verso"]=="entrata" ? "selected" : "" ?>>Entrata</option>
                        <option value="uscita"  <?= $editing && $edit_data["verso"]=="uscita" ? "selected" : "" ?>>Uscita</option>
                    </select>
                </div>

                <?php if ($editing): ?>
                    <input type="hidden" name="edit_id" value="<?= $edit_id ?>">
                <?php endif; ?>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <?= $editing ? "Salva Modifica" : "Aggiungi" ?>
                    </button>
                    <a href="index.php" class="btn btn-secondary">Annulla</a>
                </div>

            </form>

        </div>
    </div>

    <h3 class="mb-3">Storico Timbrature</h3>

    <div class="card shadow-sm">
        <div class="card-body p-0">

            <table class="table table-striped table-hover mb-0">
                <thead class="table-dark">
                <tr>
                    <th>Data</th>
                    <th>Orario</th>
                    <th>Verso</th>
                    <th>Azioni</th>
                </tr>
                </thead>
                <tbody>

                <?php foreach ($data as $anno => $mesi): ?>
                    <?php foreach ($mesi as $mese => $giorni): ?>
                        <?php foreach ($giorni as $giorno => $timbrature): ?>
                            <?php foreach ($timbrature as $id => $t): ?>
                                <tr>
                                    <td><?= "$giorno/$mese/$anno" ?></td>
                                    <td><?= $t["orario"] ?></td>
                                    <td>
                                        <span class="badge <?= $t["verso"] == "entrata" ? "bg-success" : "bg-danger" ?>">
                                            <?= ucfirst($t["verso"]) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a class="btn btn-sm btn-warning"
                                           href="?modifica=<?= "$anno-$mese-$giorno-$id" ?>">
                                            Modifica
                                        </a>
                                        <a class="btn btn-sm btn-danger"
                                           href="?elimina=<?= "$anno-$mese-$giorno-$id" ?>"
                                           onclick="return confirm('Eliminare questa timbratura?')">
                                            Elimina
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>

                </tbody>
            </table>

        </div>
    </div>

</div>

</body>
</html>
