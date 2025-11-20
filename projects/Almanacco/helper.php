<?php
class Helper
{

    public static function getSeasons($json)
    {
        $seasons = [];
        foreach ($json as $key => $season) {
            $seasons[] = $key;
        }
        return array_reverse($seasons);
    }

    public static function viewDaysForSeason($json, $season)
    {
        ?>
        <div class="h1 text-center">Giornate <?= htmlspecialchars($season) ?></div>
        <div class="row">
            <?php foreach ($json[$season]['giornate'] as $giornata => $matches): ?>
                <div class="col-12 col-lg-6 my-2">
                    <div class="card">
                        <div class="card-header fw-bold text-center fs-4 bg-warning">Giornata <?= $giornata ?></div>
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
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }

    public static function viewTableForSeason($json, $season)
    {
        $table = self::calculateTableForSeason($json, $season);
        ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header fw-bold text-center fs-4 bg-warning">Classifica <?= htmlspecialchars($season) ?>
                    </div>
                    <div class="card-body d-flex border-bottom">
                        <div class="table-responsive w-100">
                            <table class="table table-striped table-bordered text-center align-middle">
                                <thead>
                                    <tr>
                                        <th rowspan="2">Pos</th>
                                        <th rowspan="2">Squadra</th>
                                        <th colspan="8">Totale</th>
                                        <th colspan="8">Casa</th>
                                        <th colspan="8">Trasferta</th>
                                    </tr>
                                    <tr>
                                        <th>Pt</th>
                                        <th>G</th>
                                        <th>V</th>
                                        <th>N</th>
                                        <th>P</th>
                                        <th>GF</th>
                                        <th>GS</th>
                                        <th>DR</th>

                                        <th>Pt</th>
                                        <th>G</th>
                                        <th>V</th>
                                        <th>N</th>
                                        <th>P</th>
                                        <th>GF</th>
                                        <th>GS</th>
                                        <th>DR</th>

                                        <th>Pt</th>
                                        <th>G</th>
                                        <th>V</th>
                                        <th>N</th>
                                        <th>P</th>
                                        <th>GF</th>
                                        <th>GS</th>
                                        <th>DR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($table as $squadra => $row): ?>
                                        <tr>
                                            <td><?= array_search($squadra, array_keys($table)) + 1 ?></td>
                                            <td class="text-start"><?= htmlspecialchars($squadra) ?></td>
                                            <td><?= $row['totale']['pt'] ?></td>
                                            <td><?= $row['totale']['g'] ?></td>
                                            <td><?= $row['totale']['v'] ?></td>
                                            <td><?= $row['totale']['n'] ?></td>
                                            <td><?= $row['totale']['p'] ?></td>
                                            <td><?= $row['totale']['gf'] ?></td>
                                            <td><?= $row['totale']['gs'] ?></td>
                                            <td><?= $row['totale']['dr'] ?></td>
                                            <td><?= $row['casa']['pt'] ?></td>
                                            <td><?= $row['casa']['g'] ?></td>
                                            <td><?= $row['casa']['v'] ?></td>
                                            <td><?= $row['casa']['n'] ?></td>
                                            <td><?= $row['casa']['p'] ?></td>
                                            <td><?= $row['casa']['gf'] ?></td>
                                            <td><?= $row['casa']['gs'] ?></td>
                                            <td><?= $row['casa']['dr'] ?></td>
                                            <td><?= $row['trasferta']['pt'] ?></td>
                                            <td><?= $row['trasferta']['g'] ?></td>
                                            <td><?= $row['trasferta']['v'] ?></td>
                                            <td><?= $row['trasferta']['n'] ?></td>
                                            <td><?= $row['trasferta']['p'] ?></td>
                                            <td><?= $row['trasferta']['gf'] ?></td>
                                            <td><?= $row['trasferta']['gs'] ?></td>
                                            <td><?= $row['trasferta']['dr'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>
            </div>
        </div>
        <?php
    }

    private static function calculateTableForSeason($json, $season)
    {
        $classifica = [];
        $moltiplicatore_punti = self::getMoltiplicatorePunti($season);

        // helper per inizializzare le statistiche di una squadra se non esiste
        $initStats = function (&$classifica, string $squadra) {
            if (!isset($classifica[$squadra])) {
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
                $classifica[$squadra] = [
                    'totale' => $base,
                    'casa' => $base,
                    'trasferta' => $base,
                ];
            }
        };

        foreach ($json[$season]['giornate'] as $giornata => $partite) {
            foreach ($partite as $partita) {
                // es: "Benevento-Inter"
                [$casa, $trasferta] = explode('-', $partita['squadre']);
                $casa = trim($casa);
                $trasferta = trim($trasferta);

                // es: "2-5"
                [$golCasa, $golTrasferta] = explode('-', $partita['risultato']);
                if (
                    trim($partita['risultato']) === '-' ||
                    $golCasa === '' || $golTrasferta === '' ||
                    is_null($golCasa) || is_null($golTrasferta)
                ) {
                    continue;  // salta questa partita
                }
                $golCasa = (int) $golCasa;
                $golTrasferta = (int) $golTrasferta;

                // inizializza se necessario
                $initStats($classifica, $casa);
                $initStats($classifica, $trasferta);

                // funzione per aggiornare un blocco (totale/casa/trasferta)
                $aggiorna = function (&$bloc, int $gf, int $gs, int $punti, int $moltiplicatore_punti) {
                    $bloc['g']++;
                    $bloc['gf'] += $gf;
                    $bloc['gs'] += $gs;
                    $bloc['dr'] = $bloc['gf'] - $bloc['gs'];
                    $bloc['pt'] += $punti;

                    if ($punti === $moltiplicatore_punti) {
                        $bloc['v']++;
                    } elseif ($punti === 1) {
                        $bloc['n']++;
                    } else {
                        $bloc['p']++;
                    }
                };

                // calcolo punti
                if ($golCasa > $golTrasferta) {
                    // vittoria casa
                    $puntiCasa = $moltiplicatore_punti;
                    $puntiTrasferta = 0;
                } elseif ($golCasa < $golTrasferta) {
                    // vittoria trasferta
                    $puntiCasa = 0;
                    $puntiTrasferta = $moltiplicatore_punti;
                } else {
                    // pareggio
                    $puntiCasa = 1;
                    $puntiTrasferta = 1;
                }

                // aggiorno CASA
                $aggiorna($classifica[$casa]['casa'], $golCasa, $golTrasferta, $puntiCasa, $moltiplicatore_punti);
                // aggiorno TRASFERTA
                $aggiorna($classifica[$trasferta]['trasferta'], $golTrasferta, $golCasa, $puntiTrasferta, $moltiplicatore_punti);

                // aggiorno TOTALE (somma di ciò che ho appena aggiunto)
                $aggiorna($classifica[$casa]['totale'], $golCasa, $golTrasferta, $puntiCasa, $moltiplicatore_punti);
                $aggiorna($classifica[$trasferta]['totale'], $golTrasferta, $golCasa, $puntiTrasferta, $moltiplicatore_punti);
            }
        }


        // ordino la classifica per punti totali (poi differenza reti, poi gol fatti)
        uasort($classifica, function ($a, $b) {
            $pa = $a['totale']['pt'];
            $pb = $b['totale']['pt'];
            if ($pa !== $pb) {
                return $pb <=> $pa; // punti desc
            }

            $da = $a['totale']['dr'];
            $db = $b['totale']['dr'];
            if ($da !== $db) {
                return $db <=> $da; // diff reti desc
            }

            $gfa = $a['totale']['gf'];
            $gfb = $b['totale']['gf'];
            return $gfb <=> $gfa; // gol fatti desc
        });

        return $classifica;


    }

    private static function getMoltiplicatorePunti($season)
    {
        $s = explode('-', $season) ?? [];
        if (isset($s[0]) && (int) $s[0] < 1994) {
            return 2; // prima del 1994/1995, vittoria vale 2 punti
        } else {
            return 3; // dal 1994/1995 in poi, vittoria vale 3 punti
        }
    }
}


?>