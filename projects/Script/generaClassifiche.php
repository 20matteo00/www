<?php
$input_json = "giornate/stagioni.json";
$output_json = "classifiche/stagioni.json";

// Crea la cartella se non esiste
if (!is_dir('classifiche')) {
    mkdir('classifiche', 0755, true);
}

// Leggi il file delle giornate
$dati = json_decode(file_get_contents($input_json), true);
$classifiche = [];

foreach ($dati as $stagione => $stagione_data) {
    $classifica_stagione = [];

    foreach ($stagione_data['giornate'] as $giornata => $partite) {
        foreach ($partite as $p) {
            // Split squadre e risultato
            list($casa, $trasferta) = explode('-', $p['squadre']);
            list($gol_casa, $gol_trasferta) = explode('-', $p['risultato']);
            $gol_casa = (int)$gol_casa;
            $gol_trasferta = (int)$gol_trasferta;

            // Inizializza array squadra se non esiste
            if (!isset($classifica_stagione[$casa])) {
                $classifica_stagione[$casa] = [
                    "Casa" => ["Vinte"=>0,"Pareggiate"=>0,"Perse"=>0,"GolFatti"=>0,"GolSubiti"=>0],
                    "Trasferta" => ["Vinte"=>0,"Pareggiate"=>0,"Perse"=>0,"GolFatti"=>0,"GolSubiti"=>0],
                ];
            }
            if (!isset($classifica_stagione[$trasferta])) {
                $classifica_stagione[$trasferta] = [
                    "Casa" => ["Vinte"=>0,"Pareggiate"=>0,"Perse"=>0,"GolFatti"=>0,"GolSubiti"=>0],
                    "Trasferta" => ["Vinte"=>0,"Pareggiate"=>0,"Perse"=>0,"GolFatti"=>0,"GolSubiti"=>0],
                ];
            }

            // Aggiorna gol fatti/subiti
            $classifica_stagione[$casa]["Casa"]["GolFatti"] += $gol_casa;
            $classifica_stagione[$casa]["Casa"]["GolSubiti"] += $gol_trasferta;

            $classifica_stagione[$trasferta]["Trasferta"]["GolFatti"] += $gol_trasferta;
            $classifica_stagione[$trasferta]["Trasferta"]["GolSubiti"] += $gol_casa;

            // Aggiorna risultati
            if ($gol_casa > $gol_trasferta) {
                $classifica_stagione[$casa]["Casa"]["Vinte"]++;
                $classifica_stagione[$trasferta]["Trasferta"]["Perse"]++;
            } elseif ($gol_casa < $gol_trasferta) {
                $classifica_stagione[$trasferta]["Trasferta"]["Vinte"]++;
                $classifica_stagione[$casa]["Casa"]["Perse"]++;
            } else {
                $classifica_stagione[$casa]["Casa"]["Pareggiate"]++;
                $classifica_stagione[$trasferta]["Trasferta"]["Pareggiate"]++;
            }
        }
    }

    $classifiche[$stagione] = $classifica_stagione;
}

// Salva tutto in JSON
file_put_contents($output_json, json_encode($classifiche, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "Classifiche generate e salvate in $output_json\n";
?>
