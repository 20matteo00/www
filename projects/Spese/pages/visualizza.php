<?php
include 'layout/menu_visualizza.php';
$modalita = $_GET['modalita'] ?? 'membri';
?>

<div class="container">
    <div class="row">



        <?php
        switch ($modalita) {
            case 'membri':
                ?>
                <div class="col-md-6">
                    <h3 class="text-center">Distribuzione Importi per Membro</h3>
                    <canvas id="chartImporti" class="canvas"></canvas>

                </div>
                <div class="col-md-6">
                    <h3 class="text-center">Numero di Spese per Membro</h3>
                    <canvas id="chartQuantita" class="canvas"></canvas>

                </div>
                <?php
                $membri = $db->select('membri');
                $nomi = $importi = $quantita = [];

                foreach ($membri as $m) {
                    $spese = $db->runQuery("
                SELECT COUNT(*) AS totale_spese, SUM(importo) AS totale_importo
                FROM spese WHERE id_membro = {$m['id']}
            ");

                    if (!empty($spese) && $spese[0]['totale_importo'] !== null) {
                        $nomi[] = $m['nome'];
                        $colori[] = json_decode($m['dati'], true)['color'] ?? '#000000';
                        $importi[] = (float) $spese[0]['totale_importo'];
                        $quantita[] = (int) $spese[0]['totale_spese'];
                    }
                }

                if (empty($nomi)) {
                    echo "<p>Nessun dato disponibile per i membri.</p>";
                    break;
                }

                ?>

                <script>
                    const colori = <?= json_encode($colori) ?>;
                    const labels = <?= json_encode($nomi) ?>;
                    const datiImporti = <?= json_encode($importi) ?>;
                    const datiQuantita = <?= json_encode($quantita) ?>;

                    const optBase = {
                        responsive: true,
                        plugins: { legend: { display: true, position: 'bottom' } }
                    };

                    new Chart(document.getElementById('chartImporti'), {
                        type: 'pie',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Totale Importi (€)',
                                data: datiImporti,
                                backgroundColor: colori
                            }]
                        },
                        options: optBase
                    });

                    new Chart(document.getElementById('chartQuantita'), {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Numero di Spese',
                                data: datiQuantita,
                                backgroundColor: colori
                            }]
                        },
                        options: optBase
                    });
                </script>
                <?php
                break;

            default:
                echo "<p>Modalità non riconosciuta.</p>";
                break;
        }
        ?>
    </div>
</div>