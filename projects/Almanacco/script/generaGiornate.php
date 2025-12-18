<?php
$id_esclusi = [48, 49];
$stagioni_dati = []; // qui accumuliamo tutte le stagioni

$dirpath = "../json";
$filepath = "../json/partite.json";

for ($i = 34; $i <= 129; $i++) {
    if (in_array($i, $id_esclusi)) {
        echo "=================== Stagione " . getStagione($i) . " esclusa =================== <br>";
        continue;
    }

    $url = "http://www.italia1910.com/serie-a-risultati-e-classifica.asp?idstagione=$i";
    $stagione_nome = getStagione($i);

    $html = file_get_contents($url, false);
    if (!$html) {
        echo "Impossibile scaricare la pagina per la stagione $stagione_nome<br>";
        continue;
    }

    $giornate = generateTable($html);

    // Salva la stagione nell'array principale
    $stagioni_dati[$stagione_nome] = $giornate;

    echo "=================== Stagione $stagione_nome processata, giornate totali: " . count($giornate["giornate"]) . " =================== <br>";
}

// Crea la cartella se non esiste
if (!is_dir($dirpath)) {
    mkdir($dirpath, 0755, true);
}

// Salva tutto in un unico file
file_put_contents($filepath, json_encode($stagioni_dati, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "Tutte le stagioni salvate in json/partite.json\n";


// ---------------- FUNZIONI ----------------
function getStagione($id_stagione)
{
    if ($id_stagione < 50)
        $anno_base = 1929 + ($id_stagione - 34);
    else
        $anno_base = 1929 + ($id_stagione - 33);
    return $anno_base . '-' . ($anno_base + 1);
}

function generateTable($html)
{
    // Crea DOM
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    libxml_clear_errors();

    // XPath
    $xpath = new DOMXPath($dom);

    // Cerca tutte le tabelle con class="cssBordoTabella"
    $tabelle = $xpath->query("//table[@class='cssBordoTabella']");

    $andata_count = 0; // contatore giornate andata
    $dati = [
        "giornate" => []
    ];

    foreach ($tabelle as $tabella) {
        $rows = $tabella->getElementsByTagName('tr');
        $giornata_corrente = null;
        $partite = [];

        // Controlla prima se questa tabella è valida (ha andata/ritorno nell'intestazione)
        $tabella_valida = false;
        $prima_riga = $rows->item(0);
        if ($prima_riga && $prima_riga->getAttribute('class') === 'cssCategoria1') {
            $testo_intestazione = trim($prima_riga->textContent);
            if (preg_match('/(andata|ritorno)/i', $testo_intestazione)) {
                $tabella_valida = true;
            }
        }

        // Salta questa tabella se non è valida
        if (!$tabella_valida) {
            continue;
        }

        foreach ($rows as $r) {
            $classe = $r->getAttribute('class');

            // Riga con la giornata (cssCategoria1)
            if ($classe === 'cssCategoria1') {
                $testo = trim($r->textContent);

                // Estrai il numero della giornata e se è andata o ritorno
                if (preg_match('/(\d+)\s+(andata|ritorno)/i', $testo, $matches)) {
                    $numero_giornata = (int) $matches[1];
                    $tipo = strtolower($matches[2]);

                    if ($tipo === 'andata') {
                        $giornata_corrente = $numero_giornata;
                        $andata_count++;
                    } else { // ritorno
                        $giornata_corrente = $andata_count + $numero_giornata;
                    }
                }
            }

            // Riga con dati partita (cssSfondo3)
            if ($classe === 'cssSfondo3' && $giornata_corrente !== null) {
                $cols = $r->getElementsByTagName('td');

                if ($cols->length >= 3) {
                    $data = trim($cols->item(0)->textContent);
                    $squadre = trim($cols->item(1)->textContent);
                    $risultato = trim($cols->item(2)->textContent);

                    if (!empty($squadre) && !empty($risultato)) {
                        $partite[] = [
                            "data" => $data,
                            "squadre" => $squadre,
                            "risultato" => $risultato
                        ];
                    }
                }
            }
        }

        // Salva le partite della giornata
        if ($giornata_corrente !== null && !empty($partite)) {
            if (!isset($dati["giornate"][$giornata_corrente])) {
                $dati["giornate"][$giornata_corrente] = [];
            }
            $dati["giornate"][$giornata_corrente] = array_merge($dati["giornate"][$giornata_corrente], $partite);
        }
    }

    return $dati;
}
?>