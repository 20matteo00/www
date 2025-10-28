<?php

$logged_menu = [
    'Home' => 'home.php',
    'Squadre' => 'squadre.php',
    'Competizioni' => 'competizioni.php',
    'Scontri Diretti' => 'scontri_diretti.php',
    'Esci' => 'esci.php',
];

$not_logged_menu = [
    'Home' => 'home.php',
    'Accedi' => 'accedi.php',
    'Registrati' => 'registrati.php',
];

$modalita = [
    'Campionato',
    'Eliminazione',
    'Gironi'
];

$luogo = [
    'generale',
    'casa',
    'trasferta'
];



function create_message($type, $text)
{
    return "<div class='alert alert-$type'>" . htmlspecialchars($text) . "</div>";
}

function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function generatestyle($bg, $text, $border)
{
    return "background-color: $bg; color: $text; border: 2px solid $border;";
}

function getTeamColors($db, $squadra)
{
    if ($squadra == null)
        return;
    $teams = $db->select("teams", [
        "column" => ['dati'],
        "where" => ['id_utente' => $_SESSION['user_id'], 'nome' => $squadra]
    ]);
    $colori = json_decode($teams[0]['dati'])->color;

    return [
        'bg' => $colori->bg,
        'text' => $colori->text,
        'border' => $colori->border
    ];
}

function getTeamStats($db, $squadra)
{
    $teams = $db->select("teams", [
        "column" => ['dati'],
        "where" => ['id_utente' => $_SESSION['user_id'], 'nome' => $squadra]
    ]);
    $power = json_decode($teams[0]['dati'])->power;

    return [
        'attack' => $power->attack,
        'defense' => $power->defense
    ];
}

function getCompetitionsByTeams($db, $squadra, $finite = true)
{
    $finiteVal = $finite ? 'true' : 'false';  // Prima converto in testo SQL booleano

    $sql = "SELECT *
            FROM competitions
            WHERE id_utente = :userId
              AND JSON_CONTAINS(squadre, JSON_QUOTE(:squadra), '$')
              AND JSON_EXTRACT(dati, '$.Completata') = $finiteVal";

    $params = [
        'userId' => $_SESSION['user_id'],
        'squadra' => $squadra
    ];

    return $db->runRaw($sql, $params);
}

function getCompWinner($giornate, $finita)
{
    if (empty($giornate) || !$finita)
        return;
    $classifica = calcolaClassifica($giornate);
    return array_keys($classifica)[0];
}

function setCompletataTrue($db, $idcomp)
{
    $sql = "UPDATE competitions
            SET dati = JSON_SET(dati, '$.Completata', true)
            WHERE id_utente = :userId AND id = :competitionId";

    $params = [
        'userId' => $_SESSION['user_id'],
        'competitionId' => $idcomp
    ];

    return $db->runRaw($sql, $params);
}

function searchTeaminCompetitions($db, $searchteam)
{
    $competizioni = $db->select('competitions', [
        'columns' => ['squadre'],
        'where' => ['id_utente' => $_SESSION['user_id']],
    ]);
    $trovato = false;
    foreach ($competizioni as $comp) {
        $squadre = json_decode($comp['squadre']);
        foreach ($squadre as $squadra) {
            if ($squadra == $searchteam) {
                $trovato = true;
                break 2;
            }
        }
    }
    return $trovato;
}

function creaCampionato(array $squadre, bool $ar = true): array
{
    shuffle($squadre);
    $numSquadre = count($squadre);
    if ($numSquadre % 2 !== 0) {
        $squadre[] = "Riposo";
        $numSquadre++;
    }

    $giornate = $numSquadre - 1;
    $partitePerGiornata = $numSquadre / 2;
    $giornateCalendario = [];
    $squadreTemp = $squadre;
    array_shift($squadreTemp);
    $numSquadreTemp = count($squadreTemp);

    // Creo il girone di andata
    for ($i = 0; $i < $giornate; $i++) {
        $giornata = [];
        $giornata[] = [$squadre[0], $squadreTemp[$i % $numSquadreTemp]];
        for ($j = 1; $j < $partitePerGiornata; $j++) {
            $home = $squadreTemp[($i + $j) % $numSquadreTemp];
            $away = $squadreTemp[($i + $numSquadreTemp - $j) % $numSquadreTemp];
            $giornata[] = [$home, $away];
        }
        $giornateCalendario[] = $giornata;
    }

    // Creo il girone di ritorno (invertito)
    $ritorno = [];
    foreach ($giornateCalendario as $giornata) {
        $nuova = [];
        foreach ($giornata as $partita) {
            $nuova[] = [$partita[1], $partita[0]];
        }
        $ritorno[] = $nuova;
    }

    // Scambio giornate pari di andata con quelle di ritorno corrispondenti
    for ($g = 1; $g < $giornate; $g += 2) {
        $temp = $giornateCalendario[$g];
        $giornateCalendario[$g] = $ritorno[$g];
        $ritorno[$g] = $temp;
    }

    // Unisco andata modificata con ritorno
    $giornateCalendario = array_merge($giornateCalendario, $ritorno);

    // Se solo andata: restituisco metà calendario
    if (!$ar) {
        $giornateCalendario = array_slice($giornateCalendario, 0, $giornate);
    }

    // Preparo output leggibile escludendo "Riposo"
    $result = [];
    foreach ($giornateCalendario as $k => $giornata) {
        $giornataLabel = "Giornata " . ($k + 1);
        $partite = [];
        foreach ($giornata as $match) {
            if ($match[0] === "Riposo" || $match[1] === "Riposo")
                continue;
            $partite[] = [
                "casa" => $match[0],
                "trasferta" => $match[1],
                "gol_casa" => null,
                "gol_trasferta" => null
            ];
        }
        $result[$giornataLabel] = $partite;
    }

    return $result;
}

function creaEliminazione($db, array $squadre, bool $andataRitorno = true, array $giornate = [], $id = null)
{
    $turni = [
        64 => 'Trentaduesimi',
        32 => 'Sedicesimi',
        16 => 'Ottavi',
        8 => 'Quarti',
        4 => 'Semifinali',
        2 => 'Finale',
        1 => 'Vincitore'
    ];
    if (empty($giornate)) {
        shuffle($squadre);
        $numSquadre = count($squadre);
        if (!in_array($numSquadre, [4, 8, 16, 32, 64]))
            return [];
        $nome = $turni[$numSquadre] ?? "Turno $numSquadre";

        for ($i = 0; $i < ($numSquadre / 2); $i++) {
            $giornate[$nome . " - Andata"][] = [
                "casa" => $squadre[$i],
                "trasferta" => $squadre[$numSquadre - $i - 1],
                "gol_casa" => null,
                "gol_trasferta" => null
            ];
        }
        if ($andataRitorno) {
            for ($i = 0; $i < ($numSquadre / 2); $i++) {
                $giornate[$nome . " - Ritorno"][] = [
                    "casa" => $squadre[$numSquadre - $i - 1],
                    "trasferta" => $squadre[$i],
                    "gol_casa" => null,
                    "gol_trasferta" => null
                ];
            }
        }
        return $giornate;
    } else {
        $accoppiamenti = [];
        $ultimeGiornate = ($andataRitorno)
            ? array_slice($giornate, -2, 2, true)
            : array_slice($giornate, -1, 1, true); // -2 se andata/ritorno, -1 se solo andata

        foreach ($ultimeGiornate as $nomeGiornata => $partite) {
            foreach ($partite as $partita) {
                // Partita ancora incompleta
                if ($partita['gol_casa'] === null || $partita['gol_trasferta'] === null) {
                    return $giornate;
                }

                // Chiave unica per le due squadre (indipendente da chi gioca in casa)
                $s1 = $partita['casa'];
                $s2 = $partita['trasferta'];
                $key = implode('-', [min($s1, $s2), max($s1, $s2)]);

                // Sommo i gol totali (andata + ritorno se presenti)
                if (!isset($accoppiamenti[$key])) {
                    $accoppiamenti[$key] = [
                        $s1 => 0,
                        $s2 => 0
                    ];
                }

                $accoppiamenti[$key][$s1] += $partita['gol_casa'];
                $accoppiamenti[$key][$s2] += $partita['gol_trasferta'];
            }
        }
        $qualificate = [];
        // Ora controllo se qualche accoppiamento è pari
        foreach ($accoppiamenti as $sfida => $risultati) {
            $valori = array_values($risultati);
            if ($valori[0] === $valori[1]) {
                return $giornate;
            }
            $squadre = array_keys($risultati);
            $gol = array_values($risultati);

            $qualificate[] = $gol[0] > $gol[1] ? $squadre[0] : $squadre[1];
        }
        $numSquadre = count($qualificate);
        $nome = $turni[$numSquadre] ?? "Turno $numSquadre";

        for ($i = 0; $i < ($numSquadre / 2); $i++) {
            $giornate[$nome . " - Andata"][] = [
                "casa" => $qualificate[$i],
                "trasferta" => $qualificate[$numSquadre - $i - 1],
                "gol_casa" => null,
                "gol_trasferta" => null
            ];
        }
        if ($andataRitorno) {
            for ($i = 0; $i < ($numSquadre / 2); $i++) {
                $giornate[$nome . " - Ritorno"][] = [
                    "casa" => $qualificate[$numSquadre - $i - 1],
                    "trasferta" => $qualificate[$i],
                    "gol_casa" => null,
                    "gol_trasferta" => null
                ];
            }
        }
        $db->update(
            'competitions',           // tabella
            ['partite' => json_encode($giornate)], // dati da aggiornare
            ['id' => $id]             // condizione WHERE
        );
        return creaEliminazione($db, $qualificate, $andataRitorno, $giornate, $id);

    }
}

function checkAllMatches($giornate)
{
    $gamenull = false;
    foreach ($giornate as $giornata):
        foreach ($giornata as $partita):
            if (is_null(value: $partita['gol_casa']) || is_null($partita['gol_trasferta'])) {
                $gamenull = true;
                break 2;
            }
        endforeach;
    endforeach;

    return $gamenull;
}

function calcolaClassifica($giornate, $fase = null, $ar = true)
{
    $squadre = [];

    $chiaveOrdine = $fase ?? "totale"; // default

    // numero squadre = partite * 2 della prima giornata
    $numGiornate = intdiv(count($giornate), 2);
    // filtra giornate in base alla fase
    if ($fase === "andata") {
        $giornate = ($ar) ? array_slice($giornate, 0, $numGiornate) : $giornate;
        $chiaveOrdine = "totale";
    } elseif ($fase === "ritorno") {
        $giornate = ($ar) ? array_slice($giornate, $numGiornate) : $giornate;
        $chiaveOrdine = "totale";
    }

    foreach ($giornate as $partite) {
        foreach ($partite as $match) {
            $casa = $match['casa'];
            $trasferta = $match['trasferta'];
            $golCasa = $match['gol_casa'];
            $golTrasferta = $match['gol_trasferta'];

            // inizializza la squadra se non esiste
            foreach ([$casa, $trasferta] as $team) {
                if (!isset($squadre[$team])) {
                    $squadre[$team] = [
                        "casa" => [
                            "punti" => 0,
                            "giocate" => 0,
                            "vinte" => 0,
                            "pari" => 0,
                            "perse" => 0,
                            "gol_fatti" => 0,
                            "gol_subiti" => 0,
                            "diff_reti" => 0
                        ],
                        "trasferta" => [
                            "punti" => 0,
                            "giocate" => 0,
                            "vinte" => 0,
                            "pari" => 0,
                            "perse" => 0,
                            "gol_fatti" => 0,
                            "gol_subiti" => 0,
                            "diff_reti" => 0
                        ],
                        "totale" => [
                            "punti" => 0,
                            "giocate" => 0,
                            "vinte" => 0,
                            "pari" => 0,
                            "perse" => 0,
                            "gol_fatti" => 0,
                            "gol_subiti" => 0,
                            "diff_reti" => 0
                        ]
                    ];
                }
            }

            // se i gol non sono null => partita giocata
            if ($golCasa !== null && $golTrasferta !== null) {
                // aggiorna gol e giocate
                $squadre[$casa]["casa"]["gol_fatti"] += $golCasa;
                $squadre[$casa]["casa"]["gol_subiti"] += $golTrasferta;
                $squadre[$casa]["casa"]["giocate"]++;

                $squadre[$trasferta]["trasferta"]["gol_fatti"] += $golTrasferta;
                $squadre[$trasferta]["trasferta"]["gol_subiti"] += $golCasa;
                $squadre[$trasferta]["trasferta"]["giocate"]++;

                // aggiorna risultati + punti
                if ($golCasa > $golTrasferta) {
                    // vittoria casa
                    $squadre[$casa]["casa"]["vinte"]++;
                    $squadre[$casa]["casa"]["punti"] += 3;
                    $squadre[$trasferta]["trasferta"]["perse"]++;
                } elseif ($golCasa < $golTrasferta) {
                    // vittoria trasferta
                    $squadre[$trasferta]["trasferta"]["vinte"]++;
                    $squadre[$trasferta]["trasferta"]["punti"] += 3;
                    $squadre[$casa]["casa"]["perse"]++;
                } else {
                    // pareggio
                    $squadre[$casa]["casa"]["pari"]++;
                    $squadre[$casa]["casa"]["punti"] += 1;
                    $squadre[$trasferta]["trasferta"]["pari"]++;
                    $squadre[$trasferta]["trasferta"]["punti"] += 1;
                }
            }
        }
    }

    // calcolo i totali
    foreach ($squadre as $team => &$stats) {
        foreach (["punti", "giocate", "vinte", "pari", "perse", "gol_fatti", "gol_subiti"] as $chiave) {
            $stats["totale"][$chiave] = $stats["casa"][$chiave] + $stats["trasferta"][$chiave];
        }
        $stats["casa"]["diff_reti"] = $stats["casa"]["gol_fatti"] - $stats["casa"]["gol_subiti"];
        $stats["trasferta"]["diff_reti"] = $stats["trasferta"]["gol_fatti"] - $stats["trasferta"]["gol_subiti"];
        $stats["totale"]["diff_reti"] = $stats["totale"]["gol_fatti"] - $stats["totale"]["gol_subiti"];
    }



    // ordina
    uasort($squadre, function ($a, $b) use ($chiaveOrdine) {
        // confronto punti
        if ($b[$chiaveOrdine]["punti"] !== $a[$chiaveOrdine]["punti"]) {
            return $b[$chiaveOrdine]["punti"] - $a[$chiaveOrdine]["punti"];
        }
        // confronto diff reti
        if ($b[$chiaveOrdine]["diff_reti"] !== $a[$chiaveOrdine]["diff_reti"]) {
            return $b[$chiaveOrdine]["diff_reti"] - $a[$chiaveOrdine]["diff_reti"];
        }
        // confronto gol fatti
        return $b[$chiaveOrdine]["gol_fatti"] - $a[$chiaveOrdine]["gol_fatti"];
    });

    return $squadre;


}

function calcolaAndamento($giornate)
{
    $avanzamento = []; // qui salviamo la classifica ad ogni giornata
    $partiteCumulative = [];

    foreach ($giornate as $nomeGiornata => $partite) {
        // accumulo le giornate giocate fino a quella corrente
        $partiteCumulative[] = $partite;

        // calcolo classifica fino ad oggi
        $classifica = calcolaClassifica($partiteCumulative);

        // estraggo solo nome squadra e punti totali
        $puntiSquadre = [];
        foreach ($classifica as $squadra => $stats) {
            $puntiSquadre[$squadra] = $stats["totale"]["punti"];
        }

        // ordino per punti decrescenti
        arsort($puntiSquadre);

        // salvo la classifica compatta per questa giornata
        $avanzamento[$nomeGiornata] = $puntiSquadre;
    }

    return $avanzamento;
}

function testaDiSerie($andamento)
{
    $teste = [];

    foreach ($andamento as $giornata => $classifica) {
        // ordina per punti decrescenti
        arsort($classifica);

        $valori = array_values($classifica);
        $chiavi = array_keys($classifica);

        if (count($valori) === 0) {
            $teste[$giornata] = null; // nessuna squadra
            continue;
        }

        $massimo = $valori[0];
        $primi = [];

        foreach ($classifica as $squadra => $punti) {
            if ($punti === $massimo) {
                $primi[] = $squadra;
            } else {
                break; // visto che è ordinato decrescente
            }
        }

        // se c'è un solo leader => testa di serie, altrimenti null
        $teste[$giornata] = count($primi) === 1 ? $primi[0] : null;
    }

    return $teste;
}

function renderTestaDiSerieRow(array $testadiserie, $db): string
{
    $html = '';
    $last = null;
    $span = 0;

    foreach ($testadiserie as $team) {
        if ($team === $last) {
            $span++;
            continue;
        }

        if ($last !== null || $span > 0) {
            $html .= renderTd($last, $span, $db);
        }

        $last = $team;
        $span = 1;
    }

    // stampa ultimo blocco
    $html .= renderTd($last, $span, $db);

    return "<tr>$html</tr>";
}

function renderTd(?string $team, int $span, $db): string
{
    if ($team === null) {
        return "<td colspan=\"$span\"><span>-</span></td>";
    }
    $colori = getTeamColors($db, $team);
    $style = generatestyle($colori['bg'], $colori['text'], $colori['border']);
    $abbr = htmlspecialchars(substr($team, 0, 3));
    $title = htmlspecialchars($team);
    return "<td colspan=\"$span\">
                <span class=\"d-flex justify-content-center fw-bold fs-5 rounded-pill px-3\"
                      style=\"$style\" title=\"$title\">$abbr</span>
            </td>";
}

function simulapartita($db, $casa, $trasferta)
{
    $gol_casa = 0;
    $gol_trasferta = 0;

    $casa_stats = getTeamStats($db, $casa);
    $trasferta_stats = getTeamStats($db, $trasferta);

    $casa_attack = rand(1, $casa_stats['attack']);
    $casa_defense = rand(1, $casa_stats['defense']);
    $trasferta_attack = rand(1, $trasferta_stats['attack']);
    $trasferta_defense = rand(1, $trasferta_stats['defense']);

    $diff = ($casa_attack + $casa_defense) - ($trasferta_attack + $trasferta_defense);

    if ($diff > 1000):
        $gol_casa = pesoRand(4, 7);
        $gol_trasferta = pesoRand(0, $gol_casa - 4);
    elseif ($diff > 800):
        $gol_casa = pesoRand(3, 6);
        $gol_trasferta = pesoRand(0, $gol_casa - 3);
    elseif ($diff > 600):
        $gol_casa = pesoRand(2, 5);
        $gol_trasferta = pesoRand(0, $gol_casa - 2);
    elseif ($diff > 400):
        $gol_casa = pesoRand(2, 4);
        $gol_trasferta = pesoRand(0, $gol_casa - 1);
    elseif ($diff > 200):
        $gol_casa = pesoRand(0, 4);
        $gol_trasferta = pesoRand(0, $gol_casa);
    elseif ($diff > -200):
        $gol_casa = pesoRand(0, 3);
        $gol_trasferta = pesoRand(0, 3);
    elseif ($diff > -400):
        $gol_trasferta = pesoRand(0, 4);
        $gol_casa = pesoRand(0, $gol_trasferta);
    elseif ($diff > -600):
        $gol_trasferta = pesoRand(2, 4);
        $gol_casa = pesoRand(0, $gol_trasferta - 1);
    elseif ($diff > -800):
        $gol_trasferta = pesoRand(2, 5);
        $gol_casa = pesoRand(0, $gol_trasferta - 2);
    elseif ($diff > -1000):
        $gol_trasferta = pesoRand(3, 6);
        $gol_casa = pesoRand(0, $gol_trasferta - 3);
    else:
        $gol_trasferta = pesoRand(4, 7);
        $gol_casa = pesoRand(0, $gol_trasferta - 4);
    endif;

    return [
        "gol_casa" => $gol_casa,
        "gol_trasferta" => $gol_trasferta,
    ];
}

function pesoRand($min, $max)
{
    // Imposta pesi per risultati bassi con piccola probabilità di risultati alti
    $pesi = [
        0 => 50,
        1 => 60,
        2 => 40,
        3 => 25,
        4 => 12,
        5 => 8,
        6 => 4,
        7 => 1
    ];

    // Filtra i pesi per l'intervallo desiderato
    $pesiFiltrati = array_filter($pesi, function ($k) use ($min, $max) {
        return $k >= $min && $k <= $max;
    }, ARRAY_FILTER_USE_KEY);

    // Calcola la somma dei pesi filtrati
    $sommaPesi = array_sum($pesiFiltrati);

    // Genera un numero casuale tra 0 e la somma dei pesi
    $random = rand(0, $sommaPesi - 1);

    // Seleziona un numero in base ai pesi
    $soglia = 0;
    foreach ($pesiFiltrati as $numero => $peso) {
        $soglia += $peso;
        if ($random < $soglia) {
            return $numero;
        }
    }
}

function calcolaStatistiche($giornate, $record)
{
    $totpartite = array_sum(array_map('count', $giornate));
    $classifica = calcolaClassifica($giornate);
    $key = explode(",", $record);
    if (in_array($key[0], ["totale", "casa", "trasferta"]))
        $record = $key[0];

    switch ($record) {
        case "partite_totali":
            return $totpartite;

        case "gol_totali":
            $totgol = 0;
            foreach ($giornate as $giornata) {
                foreach ($giornata as $partita) {
                    $totgol += $partita['gol_casa'] + $partita['gol_trasferta'];
                }
            }
            $golpermatch = number_format($totgol / $totpartite, 2);
            return "$totgol ($golpermatch per incontro)";

        case "partita_piu_gol":
            [$totgol, $partite_max] = trovaPartiteMax(
                $giornate,
                fn($p) => $p['gol_casa'] + $p['gol_trasferta']
            );
            $output = $totgol . ": ";
            foreach ($partite_max as $p) {
                $output .= "{$p['casa']} {$p['trasferta']} {$p['gol_casa']}-{$p['gol_trasferta']}, ";
            }
            return substr($output, 0, -2);

        case "partita_piu_scarto":
            [$max_scarto, $partite_max] = trovaPartiteMax(
                $giornate,
                fn($p) => abs($p['gol_casa'] - $p['gol_trasferta'])
            );
            $output = $max_scarto . ": ";
            foreach ($partite_max as $p) {
                $output .= "{$p['casa']} {$p['trasferta']} {$p['gol_casa']}-{$p['gol_trasferta']}, ";
            }
            return substr($output, 0, -2);

        case "totale":
        case "casa":
        case "trasferta":
            $max = $key[2] == "max";
            $res = statisticheEstreme($classifica, $record, $key[1], $max);
            return $res['valore'] . ": " . $res['squadre'];

        default:
            return 1;
    }
}

function statisticheEstreme($classifica, $tipo, $campo, $massimo = true)
{
    // estrai i valori del campo selezionato
    $valori = array_map(fn($s) => $s[$tipo][$campo], $classifica);

    // calcola max o min
    $valoreEstremo = $massimo ? max($valori) : min($valori);

    // prendi tutte le squadre che hanno quel valore
    $teams = array_keys(array_filter($classifica, fn($s) => $s[$tipo][$campo] === $valoreEstremo));

    $squadre = "";
    foreach ($teams as $squadra)
        $squadre .= $squadra . ", ";
    $squadre = substr($squadre, 0, -2);

    return [
        'valore' => $valoreEstremo,
        'squadre' => $squadre
    ];
}

function trovaPartiteMax($giornate, callable $metrica)
{
    $max = 0;
    $partite_max = [];

    foreach ($giornate as $giornata) {
        foreach ($giornata as $partita) {
            $val = $metrica($partita);

            if ($val > $max) {
                $max = $val;
                $partite_max = [$partita];
            } elseif ($val == $max) {
                $partite_max[] = $partita;
            }
        }
    }

    return [$max, $partite_max];
}

function renderStatisticheScontriDiretti($squadra, $stats)
{
    ?>
    <div class="col">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white text-center fs-3 fw-bold"><?= $squadra ?></div>
            <div class="card-body">
                <div class="row text-center">
                    <?php
                    if ($stats[$squadra]['casa']['giocate'] != 0 && $stats[$squadra]['trasferta']['giocate'] != 0) {
                        renderScontriDiretti('Generale', $stats[$squadra]['totale']);
                    }
                    if ($stats[$squadra]['casa']['giocate'] != 0) {
                        renderScontriDiretti('Casa', $stats[$squadra]['casa']);
                    }
                    if ($stats[$squadra]['trasferta']['giocate'] != 0) {
                        renderScontriDiretti('Trasferta', $stats[$squadra]['trasferta']);
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <?php
}

function renderScontriDiretti($title, $stats)
{
    ?>
    <div class="col px-3">
        <span class="fs-5 fw-bold d-block mb-3 border-bottom border-success pb-1"><?= $title ?></span>
        <div class="text-start fs-6">
            <p class="mb-1 d-flex justify-content-between">
                <span>Vinte:</span>
                <span><?= $stats['vinte'] . " (" . calcolaPercentuale($stats['vinte'], $stats['giocate']) . ")" ?></span>
            </p>
            <p class="mb-1 d-flex justify-content-between">
                <span>Pari:</span>
                <span><?= $stats['pari'] . " (" . calcolaPercentuale($stats['pari'], $stats['giocate']) . ")" ?></span>
            </p>
            <p class="mb-1 d-flex justify-content-between">
                <span>Perse:</span>
                <span><?= $stats['perse'] . " (" . calcolaPercentuale($stats['perse'], $stats['giocate']) . ")" ?></span>
            </p>
            <p class="mb-1 d-flex justify-content-between">
                <span>Gol Fatti:</span>
                <span><?= $stats['gol_fatti'] . " (" . calcolaPercentuale($stats['gol_fatti'], $stats['giocate'], false) . ")" ?></span>
            </p>
            <p class="mb-1 d-flex justify-content-between">
                <span>Gol Subiti:</span>
                <span><?= $stats['gol_subiti'] . " (" . calcolaPercentuale($stats['gol_subiti'], $stats['giocate'], false) . ")" ?></span>
            </p>
            <p class="mb-1 d-flex justify-content-between">
                <span>Differenza Reti:</span>
                <span><?= $stats['diff_reti'] ?></span>
            </p>
        </div>
    </div>
    <?php
}

function calcolaPercentuale($numeratore, $denominatore, $perc = true)
{
    if ($denominatore == 0) {
        return "Divisione per zero non permessa";
    }
    if ($perc) {
        $res = ($numeratore / $denominatore) * 100;
        return number_format($res, 2) . '%';
    } else {
        $res = ($numeratore / $denominatore);
        return number_format($res, 2);
    }
}


