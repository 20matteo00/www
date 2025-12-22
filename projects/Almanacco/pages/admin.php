<?php
/*************************************************
 * CONFIG BASE
 *************************************************/

$dirpath = __DIR__ . "/../json";
$filepath = $dirpath . "/partite.json";

$range_inizio = 34;
$range_fine = 129;

$messaggi = [];


/*************************************************
 * SUBMIT FORM
 *************************************************/

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["azione"])) {

    $azione = $_POST["azione"];

    // carica json se esiste
    $stagioni_dati = [];
    if (file_exists($filepath)) {
        $stagioni_dati = json_decode(file_get_contents($filepath), true) ?? [];
        $messaggi[] = "File JSON caricato";
    } else {
        $messaggi[] = "File JSON non presente: verrà creato";
    }

    // -------------------------------
    // AGGIORNA STAGIONE SINGOLA
    // -------------------------------
    if ($azione === "stagione" && isset($_POST["stagione_id"]) && is_numeric($_POST["stagione_id"])) {

        $id = (int) $_POST["stagione_id"];
        $stagione_nome = getStagione($id);

        aggiornaStagione($id, $stagione_nome, $stagioni_dati, $messaggi, $dirpath, $filepath);
    }

    // -------------------------------
    // AGGIORNA TUTTO
    // -------------------------------
    if ($azione === "tutto") {

        for ($i = $range_inizio; $i <= $range_fine; $i++) {
            $stagione_nome = getStagione($i);
            aggiornaStagione($i, $stagione_nome, $stagioni_dati, $messaggi, $dirpath, $filepath);
        }

        $messaggi[] = "✅ Aggiornamento COMPLETO terminato";
    }

    // -------------------------------
    // AGGIORNA PENALITÀ (STUB)
    // -------------------------------
    if ($azione === "penalita") {
        aggiornaPenalita($filepath, $dirpath, $messaggi);
    }


    //header("Refresh: 2; url=index.php?page=admin");
}

?>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">

            <div class="card shadow-lg border-0">
                <div class="card-body p-4">

                    <h3 class="card-title mb-4 text-center">
                        ⚽ Aggiorna Json Serie A
                    </h3>

                    <form method="post">
                        <div class="my-2">
                            <label class="form-label fw-semibold">
                                Seleziona stagione
                            </label>
                            <select name="stagione_id" class="form-select">
                                <option value="">Seleziona la stagione</option>
                                <?php for ($i = $range_fine; $i >= $range_inizio; $i--): ?>
                                    <option value="<?= $i ?>">
                                        <?= getStagione($i) ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="d-grid my-2">
                            <button type="submit" name="azione" value="stagione" class="btn btn-success btn-lg">
                                Aggiorna stagione selezionata
                            </button>
                        </div>

                        <div class="d-grid my-2">
                            <button type="submit" name="azione" value="tutto" class="btn btn-warning btn-lg">
                                Aggiorna tutte le stagioni
                            </button>
                        </div>

                        <div class="d-grid my-2">
                            <button type="submit" name="azione" value="penalita" class="btn btn-danger btn-lg">
                                Aggiorna penalizzazioni
                            </button>
                        </div>

                    </form>

                    <?php if (!empty($messaggi)): ?>
                        <div class="mt-4">
                            <?php foreach ($messaggi as $m): ?>
                                <?php
                                $classe = "alert-secondary";
                                if (str_starts_with($m, "✅"))
                                    $classe = "alert-success";
                                if (str_starts_with($m, "❌"))
                                    $classe = "alert-danger";
                                if (str_starts_with($m, "ℹ️"))
                                    $classe = "alert-info";
                                ?>
                                <div class="alert <?= $classe ?> mb-2 py-2">
                                    <?= htmlspecialchars($m) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>
</div>


<?php
/*************************************************
 * FUNZIONI
 *************************************************/

function getStagione($id)
{
    if ($id < 50)
        $anno = 1929 + ($id - 34);
    else
        $anno = 1929 + ($id - 33);

    return $anno . "-" . ($anno + 1);
}

function generateTable($html, $type = 0)
{
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);
    $tabelle = $xpath->query("//table[@class='cssBordoTabella']");

    if ($type === 0) {
        return generateTableGionate($tabelle);
    } else {
        return generateTablePenalita($tabelle); // placeholder for future types
    }
}

function generateTableGionate($tabelle)
{
    $andata = 0;
    $out = ["giornate" => []];
    foreach ($tabelle as $tab) {
        $rows = $tab->getElementsByTagName("tr");
        $giornata = null;
        $partite = [];

        if (!$rows->item(0) || $rows->item(0)->getAttribute("class") !== "cssCategoria1") {
            continue;
        }

        if (!preg_match('/(andata|ritorno)/i', $rows->item(0)->textContent)) {
            continue;
        }

        foreach ($rows as $r) {
            if ($r->getAttribute("class") === "cssCategoria1") {
                if (preg_match('/(\d+)\s+(andata|ritorno)/i', $r->textContent, $m)) {
                    if ($m[2] === "andata") {
                        $giornata = (int) $m[1];
                        $andata++;
                    } else {
                        $giornata = $andata + (int) $m[1];
                    }
                }
            }

            if ($r->getAttribute("class") === "cssSfondo3" && $giornata) {
                $td = $r->getElementsByTagName("td");
                if ($td->length >= 3) {
                    $partite[] = [
                        "data" => trim($td->item(0)->textContent),
                        "squadre" => trim($td->item(1)->textContent),
                        "risultato" => trim($td->item(2)->textContent)
                    ];
                }
            }
        }

        if ($giornata && $partite) {
            $out["giornate"][$giornata] =
                array_merge($out["giornate"][$giornata] ?? [], $partite);
        }
    }
    return $out;
}

function generateTablePenalita(DOMNodeList $tabelle)
{
    $penalita = [];

    foreach ($tabelle as $tab) {

        // controlla che sia la tabella giusta
        if (!preg_match('/Dettaglio penalizzazioni/i', $tab->textContent)) {
            continue;
        }

        $rows = $tab->getElementsByTagName("tr");

        foreach ($rows as $index => $row) {

            $td = $row->getElementsByTagName("td");

            // servono almeno 3 colonne
            if ($td->length < 3) {
                continue;
            }

            $stagione = trim($td->item(0)->textContent);
            $squadra = trim($td->item(1)->textContent);
            $punti = (int) trim($td->item(2)->textContent);

            // salta intestazioni o righe vuote
            if (!preg_match('/\d{4}-\d{4}/', $stagione)) {
                continue;
            }

            $penalita[$stagione][] = [
                "squadra" => $squadra,
                "punti" => $punti
            ];
        }
    }
    return $penalita;
}

function aggiornaStagione(
    int $id,
    string $stagione_nome,
    array &$stagioni_dati,
    array &$messaggi,
    string $dirpath,
    string $filepath
) {
    $url = "https://www.italia1910.com/serie-a-risultati-e-classifica.asp?idstagione=$id";
    $html = @file_get_contents($url);

    if (!$html) {
        $messaggi[] = "❌ Errore download $stagione_nome";
        return;
    }

    $nuovi_dati = generateTable($html);

    if (empty($nuovi_dati["giornate"])) {
        $messaggi[] = "❌ Nessun dato per $stagione_nome";
        return;
    }

    $cambiata = !isset($stagioni_dati[$stagione_nome]) ||
        json_encode($stagioni_dati[$stagione_nome]) !== json_encode($nuovi_dati);

    $stagioni_dati[$stagione_nome] = $nuovi_dati;

    if (!is_dir($dirpath)) {
        mkdir($dirpath, 0755, true);
    }

    file_put_contents(
        $filepath,
        json_encode($stagioni_dati, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        LOCK_EX
    );


    $messaggi[] = $cambiata
        ? "✅ $stagione_nome aggiornata"
        : "ℹ️ $stagione_nome invariata (riscritta)";
}

function aggiornaPenalita(
    string $filepath,
    string $dirpath,
    array &$messaggi
) {
    $url = "https://www.italia1910.com/serie-a-squadre-penalizzate.asp";
    $html = @file_get_contents($url);

    if (!$html) {
        $messaggi[] = "❌ Errore download penalità";
        return;
    }

    // parsing HTML
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);
    $tabelle = $xpath->query("//table[@class='cssBordoTabella']");

    $penalita_per_stagione = generateTablePenalita($tabelle);

    if (empty($penalita_per_stagione)) {
        $messaggi[] = "❌ Nessuna penalità trovata";
        return;
    }

    // carica json esistente
    $stagioni_dati = [];
    if (file_exists($filepath)) {
        $stagioni_dati = json_decode(file_get_contents($filepath), true) ?? [];
    }

    // integra penalità
    foreach ($penalita_per_stagione as $stagione => $penalita) {

        if (!isset($stagioni_dati[$stagione])) {
            $stagioni_dati[$stagione] = [];
        }

        $stagioni_dati[$stagione]["penalita"] = $penalita;
    }

    // crea cartella se serve
    if (!is_dir($dirpath)) {
        mkdir($dirpath, 0755, true);
    }

    // salva UNA volta
    file_put_contents(
        $filepath,
        json_encode($stagioni_dati, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        LOCK_EX
    );

    $messaggi[] = "✅ Penalità aggiornate correttamente";
}
