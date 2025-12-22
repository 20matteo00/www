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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["stagione_id"])) {

    $id = (int) $_POST["stagione_id"];
    $stagione_nome = getStagione($id);

    // carica json se esiste
    $stagioni_dati = [];
    if (file_exists($filepath)) {
        $stagioni_dati = json_decode(file_get_contents($filepath), true) ?? [];
        $messaggi[] = "File JSON caricato";
    } else {
        $messaggi[] = "File JSON non presente: verrà creato";
    }

    // scarica html
    $url = "http://www.italia1910.com/serie-a-risultati-e-classifica.asp?idstagione=$id";
    $html = @file_get_contents($url);

    if (!$html) {
        $messaggi[] = "❌ Errore nel download della stagione $stagione_nome";
    } else {

        $nuovi_dati = generateTable($html);

        if (empty($nuovi_dati["giornate"])) {
            $messaggi[] = "❌ Nessun dato valido trovato";
        } else {

            // confronto semplice
            $cambiata = !isset($stagioni_dati[$stagione_nome]) ||
                json_encode($stagioni_dati[$stagione_nome]) !== json_encode($nuovi_dati);

            // sovrascrivi / aggiungi
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
                ? "✅ Stagione $stagione_nome aggiornata"
                : "ℹ️ Stagione $stagione_nome identica (riscritta comunque)";
        }
    }
}
?>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">

            <div class="card shadow-lg border-0">
                <div class="card-body p-4">

                    <h3 class="card-title mb-4 text-center">
                        ⚽ Aggiorna stagione Serie A
                    </h3>

                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Seleziona stagione
                            </label>
                            <select name="stagione_id" class="form-select" required>
                                <?php for ($i = $range_fine; $i >= $range_inizio; $i--): ?>
                                    <option value="<?= $i ?>">
                                        <?= getStagione($i) ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">
                                Aggiorna stagione
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

function generateTable($html)
{
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);
    $tabelle = $xpath->query("//table[@class='cssBordoTabella']");

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
