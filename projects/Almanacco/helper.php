<?php

class Helper
{

    public static function getJson()
    {
        if (file_exists("json/partite.json")) {
            return json_decode(file_get_contents("json/partite.json"), true);
        }
        return [];
    }

    /**
     * Ottiene l'elenco delle stagioni dal JSON
     */
    public static function getSeasons($json)
    {
        return array_reverse(array_keys($json));
    }

    /**
     * Ottiene l'elenco delle squadre dal JSON
     */
    public static function getTeams($json, $sort = 0)
    {
        $teams = [];

        foreach ($json as $season => $data) {
            foreach ($data['giornate'] as $matches) {
                foreach ($matches as $match) {
                    $squadre = explode('-', $match['squadre']);
                    if (count($squadre) === 2) {
                        $teams[] = trim($squadre[0]);
                        $teams[] = trim($squadre[1]);
                    }
                }
            }
        }
        if ($sort === 1) {
            sort($teams);
        }

        return array_values(array_unique($teams));
    }





    /**
     * Visualizza tutte le giornate di una stagione
     */
    public static function viewDaysForSeason($json, $season)
    {
        if (!isset($json[$season])) {
            echo "<div class='alert alert-warning'>Stagione non trovata</div>";
            return;
        }

        ?>
        <div class="fw-bold text-center fs-4 bg-info text-white p-3 rounded-pill mb-3">Giornate <?= htmlspecialchars($season) ?>
        </div>
        <div class="row">
            <?php foreach ($json[$season]['giornate'] as $giornata => $matches): ?>
                <div class="col-12 col-lg-6 my-2">
                    <?php self::renderDayCard($giornata, $matches); ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }

    /**
     * Visualizza la classifica di una singola stagione
     */
    public static function viewTableForSeason($json, $season)
    {
        $table = self::calculateTableForSeason($json, $season);

        ?>
        <div class="row">
            <div class="col-12">
                <div class="fw-bold text-center fs-4 bg-info text-white p-3 rounded-pill mb-3">
                    Classifica <?= htmlspecialchars($season) ?>
                </div>
                <div class="">
                    <?php self::renderTable($table); ?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Visualizza le statistiche di una squadra in tutte le stagioni
     */
    public static function viewSeasonsForTeam($json, $team)
    {
        ?>
        <div class="row">
            <div class="col-12">
                <div class="fw-bold text-center fs-4 bg-info text-white p-3 rounded-pill mb-3">
                    Stagioni <?= htmlspecialchars($team) ?>
                </div>
                <div class="">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered text-center align-middle">
                            <thead>
                                <tr>
                                    <th rowspan="2">Stagione</th>
                                    <th rowspan="2">Pos</th>
                                    <th colspan="8">Totale</th>
                                    <th colspan="8">Casa</th>
                                    <th colspan="8">Trasferta</th>
                                </tr>
                                <tr>
                                    <?php for ($i = 0; $i < 3; $i++): ?>
                                        <th>Pt</th>
                                        <th>G</th>
                                        <th>V</th>
                                        <th>N</th>
                                        <th>P</th>
                                        <th>GF</th>
                                        <th>GS</th>
                                        <th>DR</th>
                                    <?php endfor; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach (self::getSeasons($json) as $season) {
                                    echo "<tr>";
                                    // Calcola la classifica per la stagione
                                    $table = self::calculateTableForSeason($json, $season);
                                    if (isset($table[$team])) {
                                        echo "<td><b>" . htmlspecialchars($season) . "</b></td>";
                                        $position = array_search($team, array_keys($table)) + 1;
                                        // Mostra solo la riga della squadra selezionata
                                        self::renderTbody([$team => $table[$team]], false, false, $position, $team, $table[$team]);
                                    }
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th rowspan="2">Stagione</th>
                                    <th rowspan="2">Pos</th>
                                    <?php for ($i = 0; $i < 3; $i++): ?>
                                        <th>Pt</th>
                                        <th>G</th>
                                        <th>V</th>
                                        <th>N</th>
                                        <th>P</th>
                                        <th>GF</th>
                                        <th>GS</th>
                                        <th>DR</th>
                                    <?php endfor; ?>
                                </tr>
                                <tr>

                                    <th colspan="8">Totale</th>
                                    <th colspan="8">Casa</th>
                                    <th colspan="8">Trasferta</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Visualizza i dettagli di una squadra
     */
    public static function viewMatchesForTeam($json, $team)
    {
        ?>
        <div class="row">
            <div class="col-12">
                <div class="fw-bold text-center fs-4 bg-info text-white p-3 rounded-pill mb-3">
                    Partite <?= htmlspecialchars($team) ?>
                </div>
                <div class="">
                    <?php
                    foreach (self::getSeasons($json) as $season) {
                        $matchesForTeam = [];
                        foreach ($json[$season]['giornate'] as $giornata => $matches) {
                            foreach ($matches as $match) {
                                if (strpos($match['squadre'], $team) !== false) {
                                    $matchesForTeam[] = $match;
                                }
                            }
                        }
                        if (!empty($matchesForTeam)) {
                            echo "<div class='mb-4'>";                                // Filtra le giornate per la squadra selezionata
                            self::renderDayCard("Stagione " . htmlspecialchars($season), $matchesForTeam);
                            echo "</div>";
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Visualizza la classifica di tutti i tempi (somma di tutte le stagioni)
     */
    public static function viewTableAllTime($json, $location = 'tutto', $seasonStart = null, $seasonEnd = null)
    {
        $allTimeTable = self::calculateAllTimeTable($json, $location, $seasonStart, $seasonEnd);
        $loc = $location;
        if ($location == 'tutto')
            $loc == null;

        ?>
        <div class="row">
            <div class="col-12">
                <div class="fw-bold text-center fs-4 bg-info text-white p-3 rounded-pill mb-3">
                    Classifica Perpetua
                </div>
                <div class="">
                    <?php self::renderTable($allTimeTable, true, true, true, $loc); ?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Visualizza gli scontri diretti tra due squadre
     */
    public static function viewMatchesBetweenTeams($json, $team1, $team2, $location = 'tutto', $seasonStart = null, $seasonEnd = null)
    {
        $result = self::calculateMatchesBetweenTeams($json, $team1, $team2, $location, $seasonStart, $seasonEnd);
        $matchesFound = $result['matches'];
        $stats = $result['stats'];

        if ($seasonStart != null && $seasonEnd != null) {
            $texttoadd = " dalla stagione " . htmlspecialchars($seasonStart) . " alla stagione " . htmlspecialchars($seasonEnd);
        } elseif ($seasonStart != null) {
            $texttoadd = " dalla stagione " . htmlspecialchars($seasonStart);
        } elseif ($seasonEnd != null) {
            $texttoadd = " fino alla stagione " . htmlspecialchars($seasonEnd);
        } else {
            $texttoadd = "";
        }

        if (empty($matchesFound)) {
            echo "<div class='alert alert-info'>Nessun incontro trovato tra " . htmlspecialchars($team1) . " e " . htmlspecialchars($team2) . " " . $texttoadd . ".</div>";
            return;
        }

        ?>
        <div class="fw-bold text-center fs-4 bg-info text-white p-3 rounded-pill mb-3">
            Scontri Diretti tra <?= htmlspecialchars($team1) ?> e <?= htmlspecialchars($team2) ?>         <?= $texttoadd ?>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Stagione</th>
                        <th>Data</th>
                        <th>Squadre</th>
                        <th>Risultato</th>
                        <th>Esito</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($matchesFound as $match): ?>
                        <?php
                        if ($match['vincitore'] !== null) {
                            $teams = explode("-", $match['squadre']);
                            if (count($teams) === 2) {
                                $homeTeam = trim($teams[0]);
                                $awayTeam = trim($teams[1]);
                                if ($match['vincitore'] === $homeTeam) {
                                    // Home team won
                                    $match['squadre'] = "<span class='text-success fw-bold'>" . htmlspecialchars($homeTeam) . "</span> - <span class='text-danger fw-bold'>" . htmlspecialchars($awayTeam) . "</span>";
                                } else {
                                    // Away team won
                                    $match['squadre'] = "<span class='text-danger fw-bold'>" . htmlspecialchars($homeTeam) . "</span> - <span class='text-success fw-bold'>" . htmlspecialchars($awayTeam) . "</span>";
                                }
                            }
                        } else {
                            // It's a draw
                            $teams = explode("-", $match['squadre']);
                            if (count($teams) === 2) {
                                $homeTeam = trim($teams[0]);
                                $awayTeam = trim($teams[1]);
                                $match['squadre'] = "<span class='text-warning fw-bold'>" . htmlspecialchars($homeTeam) . "</span> - <span class='text-warning fw-bold'>" . htmlspecialchars($awayTeam) . "</span>";
                            }
                        }
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($match['stagione']) ?></td>
                            <td><?= htmlspecialchars($match['data']) ?></td>
                            <td class="fw-bold"><?= $match['squadre'] ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($match['risultato']) ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($match['esito']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="card border-0 shadow-sm mt-5">
            <div class="card-header fw-bold text-center fs-4 bg-info text-white p-3 rounded-pill mb-4">
                Statistiche
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <?php foreach ($stats as $team => $teamStats): ?>
                        <?php
                        $giocate = $teamStats['vinte'] + $teamStats['pari'] + $teamStats['perse'];
                        $differenza = $teamStats['fatti'] - $teamStats['subiti'];
                        if ($differenza > 0) {
                            $differenza = "+" . $differenza;
                            $badgeClass = "bg-success";
                        } elseif ($differenza < 0) {
                            $badgeClass = "bg-danger";
                        } else {
                            $badgeClass = "bg-warning";
                        }
                        ?>
                        <div class="col-12 col-md-6">
                            <div class="card h-100 border-primary shadow-sm">
                                <div class="card-body text-center">
                                    <h5 class="card-title fw-bold mb-3"><?= htmlspecialchars($team) ?></h5>
                                    <hr>
                                    <ul class="list-group list-group-flush text-start mb-3">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Giocate
                                            <span class="badge bg-dark rounded-pill"><?= $giocate ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Vinte
                                            <span class="badge bg-success rounded-pill"><?= $teamStats['vinte'] ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Pari
                                            <span class="badge bg-warning rounded-pill"><?= $teamStats['pari'] ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Perse
                                            <span class="badge bg-danger rounded-pill"><?= $teamStats['perse'] ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Gol Fatti
                                            <span class="badge bg-info rounded-pill"><?= $teamStats['fatti'] ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Gol Subiti
                                            <span class="badge bg-info rounded-pill"><?= $teamStats['subiti'] ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Differenza Reti
                                            <span class="badge <?= $badgeClass ?> rounded-pill"><?= $differenza ?></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php
    }


    /**
     * Calcola la classifica all-time sommando tutte le stagioni
     */
    private static function calculateAllTimeTable($json, $location = 'tutto', $seasonStart = null, $seasonEnd = null)
    {
        $allTimeTable = [];

        if ($seasonStart != null) {
            $seasonStart = (int) explode('-', $seasonStart)[0] ?? null;
        }
        if ($seasonEnd != null) {
            $seasonEnd = (int) explode('-', $seasonEnd)[0] ?? null;
        }
        foreach ($json as $season => $data) {
            if ($seasonStart != null) {
                $year = (int) explode('-', $season)[0] ?? null;
                if ($year < $seasonStart) {
                    continue;
                }
            }
            if ($seasonEnd != null) {
                $year = (int) explode('-', $season)[0] ?? null;
                if ($year > $seasonEnd) {
                    continue;
                }
            }
            $seasonTable = self::calculateTableForSeason($json, $season);

            foreach ($seasonTable as $squadra => $stats) {
                if (!isset($allTimeTable[$squadra])) {

                    $allTimeTable[$squadra] = self::initTeamStats();
                    $allTimeTable[$squadra]['stagioni'] = 0; // Contatore stagioni
                }

                // Incrementa il contatore delle stagioni partecipate
                $allTimeTable[$squadra]['stagioni']++;

                // Somma le statistiche
                foreach (['totale', 'casa', 'trasferta'] as $tipo) {
                    foreach (['pt', 'g', 'v', 'n', 'p', 'gf', 'gs'] as $stat) {
                        $allTimeTable[$squadra][$tipo][$stat] += $stats[$tipo][$stat];
                    }
                    // Ricalcola la differenza reti
                    $allTimeTable[$squadra][$tipo]['dr'] =
                        $allTimeTable[$squadra][$tipo]['gf'] - $allTimeTable[$squadra][$tipo]['gs'];
                }
            }
        }

        // Ordina la classifica
        return self::sortTable($allTimeTable);
    }

    /**
     * Calcola la classifica per una singola stagione
     */
    private static function calculateTableForSeason($json, $season)
    {
        if (!isset($json[$season])) {
            return [];
        }

        $classifica = [];
        $moltiplicatorePunti = self::getPointsMultiplier($season);

        foreach ($json[$season]['giornate'] as $giornata => $partite) {
            foreach ($partite as $partita) {
                $matchData = self::parseMatch($partita);

                if (!$matchData) {
                    continue; // Salta partite non valide
                }

                [$casa, $trasferta, $golCasa, $golTrasferta] = $matchData;

                // Inizializza le squadre se necessario
                self::ensureTeamExists($classifica, $casa, $season, $json);
                self::ensureTeamExists($classifica, $trasferta, $season, $json);

                // Calcola i punti
                [$puntiCasa, $puntiTrasferta] = self::calculatePoints($golCasa, $golTrasferta, $moltiplicatorePunti);

                // Aggiorna le statistiche
                self::updateTeamStats($classifica[$casa], $golCasa, $golTrasferta, $puntiCasa, $moltiplicatorePunti, true);
                self::updateTeamStats($classifica[$trasferta], $golTrasferta, $golCasa, $puntiTrasferta, $moltiplicatorePunti, false);
            }
        }

        return self::sortTable($classifica);
    }

    /**
     * Inizializza le statistiche di una squadra
     */
    private static function initTeamStats($penalita = 0)
    {
        $base = [
            'pt' => 0,
            'g' => 0,
            'v' => 0,
            'n' => 0,
            'p' => 0,
            'gf' => 0,
            'gs' => 0,
            'dr' => 0,
        ];

        return [
            'penalita' => $penalita,
            'totale' => array_merge($base, ['pt' => -$penalita]),
            'casa' => $base,
            'trasferta' => $base,
        ];
    }


    /**
     * Assicura che una squadra esista nella classifica
     */
    private static function ensureTeamExists(&$classifica, $squadra, $season, $json)
    {
        if (!isset($classifica[$squadra])) {
            $penalita = self::getPenalitaForTeamBySeason($json, $squadra, $season);
            $classifica[$squadra] = self::initTeamStats($penalita);
        }
    }

    /**
     * Parsifica i dati di una partita
     */
    private static function parseMatch($partita)
    {
        // Parsifica le squadre
        $squadre = explode('-', $partita['squadre']);
        if (count($squadre) !== 2) {
            return null;
        }

        $casa = trim($squadre[0]);
        $trasferta = trim($squadre[1]);

        // Parsifica il risultato
        $risultato = trim($partita['risultato']);
        if ($risultato === '-' || empty($risultato)) {
            return null;
        }

        $gol = explode('-', $risultato);
        if (count($gol) !== 2 || $gol[0] === '' || $gol[1] === '') {
            return null;
        }

        $golCasa = (int) $gol[0];
        $golTrasferta = (int) $gol[1];

        return [$casa, $trasferta, $golCasa, $golTrasferta];
    }

    /**
     * Calcola i punti per casa e trasferta
     */
    private static function calculatePoints($golCasa, $golTrasferta, $moltiplicatore)
    {
        if ($golCasa > $golTrasferta) {
            return [$moltiplicatore, 0]; // Vittoria casa
        } elseif ($golCasa < $golTrasferta) {
            return [0, $moltiplicatore]; // Vittoria trasferta
        } else {
            return [1, 1]; // Pareggio
        }
    }

    /**
     * Aggiorna le statistiche di una squadra
     */
    private static function updateTeamStats(&$team, $gf, $gs, $punti, $moltiplicatore, $isCasa)
    {
        $tipo = $isCasa ? 'casa' : 'trasferta';

        // Aggiorna le statistiche specifiche (casa o trasferta)
        self::updateStatsBlock($team[$tipo], $gf, $gs, $punti, $moltiplicatore);

        // Aggiorna le statistiche totali
        self::updateStatsBlock($team['totale'], $gf, $gs, $punti, $moltiplicatore);
    }

    /**
     * Aggiorna un blocco di statistiche
     */
    private static function updateStatsBlock(&$stats, $gf, $gs, $punti, $moltiplicatore)
    {
        $stats['g']++;
        $stats['gf'] += $gf;
        $stats['gs'] += $gs;
        $stats['dr'] = $stats['gf'] - $stats['gs'];
        $stats['pt'] += $punti;

        if ($punti === $moltiplicatore) {
            $stats['v']++;
        } elseif ($punti === 1) {
            $stats['n']++;
        } else {
            $stats['p']++;
        }
    }

    /**
     * Ordina la classifica
     */
    private static function sortTable($classifica)
    {
        uasort($classifica, function ($a, $b) {
            // Prima per punti
            if ($a['totale']['pt'] !== $b['totale']['pt']) {
                return $b['totale']['pt'] <=> $a['totale']['pt'];
            }

            // Poi per differenza reti
            if ($a['totale']['dr'] !== $b['totale']['dr']) {
                return $b['totale']['dr'] <=> $a['totale']['dr'];
            }

            // Infine per gol fatti
            return $b['totale']['gf'] <=> $a['totale']['gf'];
        });

        return $classifica;
    }

    /**
     * Ottiene il moltiplicatore di punti per la stagione
     */
    private static function getPointsMultiplier($season)
    {
        $parts = explode('-', $season);
        $year = isset($parts[0]) ? (int) $parts[0] : 0;

        return ($year < 1994) ? 2 : 3;
    }

    /**
     * Renderizza una card per una giornata
     */
    private static function renderDayCard($giornata, $matches)
    {
        ?>
        <div class="card">
            <div class="card-header fw-bold text-center fs-4 bg-info text-white">
                <?php if (is_numeric($giornata)) { ?>
                    Giornata <?= htmlspecialchars($giornata) ?>
                <?php } else { ?>
                    <?= htmlspecialchars($giornata) ?>
                <?php } ?>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex fw-bold">
                        <div style="width: 30%;">Data</div>
                        <div style="width: 50%;">Partita</div>
                        <div style="width: 20%;">Risultato</div>
                    </li>
                    <?php foreach ($matches as $match): ?>
                        <li class="list-group-item d-flex">
                            <div style="width: 30%;"><?= htmlspecialchars($match['data']) ?></div>
                            <div style="width: 50%;"><?= htmlspecialchars($match['squadre']) ?></div>
                            <div style="width: 20%;"><?= htmlspecialchars($match['risultato']) ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php
    }

    /**
     * Renderizza la tabella della classifica
     */
    private static function renderTable($table, $showSeasons = false, $showTeams = true, $isalltime = false, $location = null)
    {
        $count = $location ? 1 : 3;
        ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered text-center align-middle">
                <thead>
                    <tr>
                        <th rowspan="2">Pos</th>
                        <th rowspan="2">Squadra</th>
                        <?php if ($showSeasons): ?>
                            <th rowspan="2">Stagioni</th>
                        <?php endif; ?>
                        <?php if ($location == null): ?>
                            <th colspan="8">Totale</th>
                        <?php endif; ?>
                        <?php if ($location == null || $location == 'casa'): ?>
                            <th colspan="8">Casa</th>
                        <?php endif; ?>
                        <?php if ($location == null || $location == 'trasferta'): ?>
                            <th colspan="8">Trasferta</th>
                        <?php endif; ?>
                    </tr>
                    <tr>
                        <?php for ($i = 0; $i < $count; $i++): ?>
                            <th>Pt</th>
                            <th>G</th>
                            <th>V</th>
                            <th>N</th>
                            <th>P</th>
                            <th>GF</th>
                            <th>GS</th>
                            <th>DR</th>
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $position = 0;
                    foreach ($table as $squadra => $row):
                        ?>
                        <tr>
                            <?php
                            $position++;
                            self::renderTbody($table, $showSeasons, $showTeams, $position, $squadra, $row, $isalltime, $location);
                            ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th rowspan="2">Pos</th>
                        <th rowspan="2">Squadra</th>
                        <?php if ($showSeasons): ?>
                            <th rowspan="2">Stagioni</th>
                        <?php endif; ?>
                        <?php for ($i = 0; $i < $count; $i++): ?>
                            <th>Pt</th>
                            <th>G</th>
                            <th>V</th>
                            <th>N</th>
                            <th>P</th>
                            <th>GF</th>
                            <th>GS</th>
                            <th>DR</th>
                        <?php endfor; ?>
                    </tr>
                    <tr>

                        <?php if ($location == null): ?>
                            <th colspan="8">Totale</th>
                        <?php endif; ?>
                        <?php if ($location == null || $location == 'casa'): ?>
                            <th colspan="8">Casa</th>
                        <?php endif; ?>
                        <?php if ($location == null || $location == 'trasferta'): ?>
                            <th colspan="8">Trasferta</th>
                        <?php endif; ?>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php
    }

    private static function renderTbody($table, $showSeasons, $showTeams, $position, $squadra, $row, $isalltime = false, $location = null)
    {
        $p = $row['penalita'];
        if ($isalltime) {
            $p = self::getPenalitaForTeam($squadra);
        }
        $pen = $penpt = '';
        if ($p > 0) {
            $pen = " <span class='text-danger' title='Penalità'>(-$p)</span>";
            $sum = $row['totale']['pt'] + $p;
            $penpt = " <span class='text-warning' title='Penalità'>($sum)</span>";
        }
        if ($location == null) {
            $arr = ['totale', 'casa', 'trasferta'];
        } else {
            $arr = [$location];
        }

        ?>
        <td><b><?= $position ?>°</b></td>
        <?php if ($showTeams): ?>
            <td class="text-start fw-bold"><?= htmlspecialchars($squadra) ?><span><?= $pen ?></span></td>
        <?php endif; ?>
        <?php if ($showSeasons): ?>
            <td class="fw-bold text-primary"><?= $row['stagioni'] ?? 0 ?></td>
        <?php endif; ?>
        <?php foreach ($arr as $tipo): ?>
            <?php if ($tipo == 'totale' && $p > 0): ?>
                <td class="fw-bold"><?= $row[$tipo]['pt'] ?>                 <?= $penpt ?></td>
            <?php else: ?>
                <td class="fw-bold"><?= $row[$tipo]['pt'] ?></td>
            <?php endif; ?>
            <td><?= $row[$tipo]['g'] ?></td>
            <td><?= $row[$tipo]['v'] ?></td>
            <td><?= $row[$tipo]['n'] ?></td>
            <td><?= $row[$tipo]['p'] ?></td>
            <td><?= $row[$tipo]['gf'] ?></td>
            <td><?= $row[$tipo]['gs'] ?></td>
            <td><?= $row[$tipo]['dr'] >= 0 ? '+' : '' ?><?= $row[$tipo]['dr'] ?></td>
        <?php endforeach; ?>
    <?php
    }

    /**
     * Calcola i match tra due squadre
     */
    private static function calculateMatchesBetweenTeams($json, $team1, $team2, $location = 'tutto', $seasonStart = null, $seasonEnd = null)
    {
        $matchesFound = [];
        $stats = []; // statistiche aggregate

        if ($seasonStart != null) {
            $seasonStart = (int) explode('-', $seasonStart)[0] ?? null;
        }
        if ($seasonEnd != null) {
            $seasonEnd = (int) explode('-', $seasonEnd)[0] ?? null;
        }
        foreach ($json as $season => $data) {
            if ($seasonStart != null) {
                $year = (int) explode('-', $season)[0] ?? null;
                if ($year < $seasonStart) {
                    continue;
                }
            }
            if ($seasonEnd != null) {
                $year = (int) explode('-', $season)[0] ?? null;
                if ($year > $seasonEnd) {
                    continue;
                }
            }
            foreach ($data['giornate'] as $matches) {
                foreach ($matches as $match) {

                    // Controllo squadre
                    if (empty($match['squadre']))
                        continue;
                    $squadre = array_map('trim', explode('-', $match['squadre']));
                    if (count($squadre) !== 2)
                        continue;

                    $homeTeam = $squadre[0];
                    $awayTeam = $squadre[1];

                    // Controllo risultato valido
                    if (empty($match['risultato']) || !preg_match('/^(\d+)\s*-\s*(\d+)$/', $match['risultato'], $res)) {
                        continue;
                    }

                    $homeGoals = (int) $res[1];
                    $awayGoals = (int) $res[2];

                    // Controllo match tra le squadre specificate
                    $isMatch = ($homeTeam === $team1 && $awayTeam === $team2) || ($homeTeam === $team2 && $awayTeam === $team1);
                    if (!$isMatch)
                        continue;

                    // Controllo location
                    if ($location === 's1' && $homeTeam !== $team1)
                        continue;
                    if ($location === 's2' && $homeTeam !== $team2)
                        continue;

                    // Determina vincitore
                    $winner = null;
                    $esito = 'X';
                    if ($homeGoals > $awayGoals) {
                        $winner = $homeTeam;
                        $esito = 1;
                    } elseif ($homeGoals < $awayGoals) {
                        $winner = $awayTeam;
                        $esito = 2;
                    }

                    // Aggiunge il match trovato (solo dati del match)
                    $matchesFound[] = [
                        'data' => $match['data'],
                        'squadre' => $match['squadre'],
                        'risultato' => $match['risultato'],
                        'stagione' => $season,
                        'vincitore' => $winner,
                        'esito' => $esito,
                    ];

                    // Aggiorna statistiche aggregate (man mano, senza inserire nel match)
                    foreach ([$homeTeam, $awayTeam] as $team) {
                        if (!isset($stats[$team])) {
                            $stats[$team] = [
                                'vinte' => 0,
                                'pari' => 0,
                                'perse' => 0,
                                'fatti' => 0,
                                'subiti' => 0,
                            ];
                        }
                    }

                    if ($homeGoals > $awayGoals) {
                        $stats[$homeTeam]['vinte']++;
                        $stats[$awayTeam]['perse']++;
                    } elseif ($homeGoals < $awayGoals) {
                        $stats[$awayTeam]['vinte']++;
                        $stats[$homeTeam]['perse']++;
                    } else {
                        $stats[$homeTeam]['pari']++;
                        $stats[$awayTeam]['pari']++;
                    }

                    $stats[$homeTeam]['fatti'] += $homeGoals;
                    $stats[$homeTeam]['subiti'] += $awayGoals;
                    $stats[$awayTeam]['fatti'] += $awayGoals;
                    $stats[$awayTeam]['subiti'] += $homeGoals;
                }
            }
        }

        // Alla fine restituisci i match E le statistiche aggregate FINALMENTE calcolate
        return [
            'matches' => $matchesFound,
            'stats' => $stats, // solo il totale finale
        ];
    }

    /**
     * Ottiene le penalità per una squadra in una stagione specifica
     */
    private static function getPenalitaForTeamBySeason($json, $team, $season)
    {
        $penalità = 0;
        foreach ($json[$season]['penalita'] ?? [] as $penalty) {
            if ($penalty['squadra'] === $team) {
                $penalità = (int) $penalty['punti'];
                break;
            }
        }
        return $penalità;
    }

    /**
     * Ottiene le penalità totali per una squadra in tutte le stagioni
     */
    private static function getPenalitaForTeam($team)
    {
        $totalPenalità = 0;
        foreach (self::getJson() as $season => $data) {
            foreach ($data['penalita'] ?? [] as $penalty) {
                if ($penalty['squadra'] === $team) {
                    $totalPenalità += (int) $penalty['punti'];
                }
            }
        }
        return $totalPenalità;
    }

}
?>