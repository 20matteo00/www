<?php
class Helper
{
    /**
     * Ottiene l'elenco delle stagioni dal JSON
     */
    public static function getSeasons($json)
    {
        return array_reverse(array_keys($json));
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
        <div class="h1 text-center">Giornate <?= htmlspecialchars($season) ?></div>
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
                <div class="card">
                    <div class="card-header fw-bold text-center fs-4 bg-warning">
                        Classifica <?= htmlspecialchars($season) ?>
                    </div>
                    <div class="card-body">
                        <?php self::renderTable($table); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Visualizza la classifica di tutti i tempi (somma di tutte le stagioni)
     */
    public static function viewTableAllTime($json)
    {
        $allTimeTable = self::calculateAllTimeTable($json);
        
        ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header fw-bold text-center fs-4 bg-success text-white">
                        Classifica All-Time (Tutte le Stagioni)
                    </div>
                    <div class="card-body">
                        <?php self::renderTable($allTimeTable, true); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Calcola la classifica all-time sommando tutte le stagioni
     */
    private static function calculateAllTimeTable($json)
    {
        $allTimeTable = [];

        foreach ($json as $season => $data) {
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
                self::ensureTeamExists($classifica, $casa);
                self::ensureTeamExists($classifica, $trasferta);

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
    private static function initTeamStats()
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
            'totale' => $base,
            'casa' => $base,
            'trasferta' => $base,
        ];
    }

    /**
     * Assicura che una squadra esista nella classifica
     */
    private static function ensureTeamExists(&$classifica, $squadra)
    {
        if (!isset($classifica[$squadra])) {
            $classifica[$squadra] = self::initTeamStats();
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
            <div class="card-header fw-bold text-center fs-4 bg-warning">
                Giornata <?= htmlspecialchars($giornata) ?>
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
    private static function renderTable($table, $showSeasons = false)
    {
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
                    $position = 1;
                    foreach ($table as $squadra => $row): 
                    ?>
                        <tr>
                            <td><?= $position++ ?></td>
                            <td class="text-start fw-bold"><?= htmlspecialchars($squadra) ?></td>
                            <?php if ($showSeasons): ?>
                                <td class="fw-bold text-primary"><?= $row['stagioni'] ?? 0 ?></td>
                            <?php endif; ?>
                            <?php foreach (['totale', 'casa', 'trasferta'] as $tipo): ?>
                                <td class="fw-bold"><?= $row[$tipo]['pt'] ?></td>
                                <td><?= $row[$tipo]['g'] ?></td>
                                <td><?= $row[$tipo]['v'] ?></td>
                                <td><?= $row[$tipo]['n'] ?></td>
                                <td><?= $row[$tipo]['p'] ?></td>
                                <td><?= $row[$tipo]['gf'] ?></td>
                                <td><?= $row[$tipo]['gs'] ?></td>
                                <td><?= $row[$tipo]['dr'] >= 0 ? '+' : '' ?><?= $row[$tipo]['dr'] ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
?>