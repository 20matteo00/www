<?php

$logged_menu = [
    'Home' => 'home.php',
    'Visualizza' => 'visualizza.php',
    'Dati' => 'dati.php',
    'Media' => 'media.php',
    'Esporta DB' => 'esporta_db.php',
    'Esci' => 'esci.php',
];

$not_logged_menu = [
    'Home' => 'home.php',
    'Accedi' => 'accedi.php',
];

$dbname = 'intrattenimento';

function create_message($type, $text)
{
    return "<div class='alert alert-$type'>" . htmlspecialchars($text) . "</div>";
}

function checkLogin()
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?page=accedi.php');
        exit;
    }
}

/**
 * Genera un grafico Chart.js.
 */
function generaGrafico($id, $tipo, $label, $labels, $data, $colori)
{
    ?>
    <canvas id="<?= $id ?>" class="canvas"></canvas>
    <script>
        new Chart(document.getElementById('<?= $id ?>'), {
            type: '<?= $tipo ?>',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    label: <?= json_encode($label) ?>,
                    data: <?= json_encode($data) ?>,
                    backgroundColor: <?= json_encode($colori) ?>
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true, position: 'top' },
                    datalabels: {
                        color: '#000',
                        anchor: 'center',     // centra il testo dentro la fetta
                        clamp: true,          // non fa uscire il testo fuori dal canvas
                        clip: true,           // taglia eventuali overflow
                        offset: 4,            // piccolo margine per non toccare il bordo
                        formatter: (value, context) => {
                            const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            const percent = (value / total * 100).toFixed(1);
                            // se la percentuale è troppo piccola, nascondi l'etichetta
                            if (percent < 5) return ''; // nasconde le etichette sotto il 5%
                            return [`${value.toLocaleString('it-IT')}`, `(${percent}%)`];
                        },
                        font: (context) => {
                            // riduce dinamicamente la dimensione se ci sono molte fette
                            const count = context.chart.data.labels.length;
                            return {
                                size: count > 6 ? 10 : 12,
                                weight: 'bold'
                            };
                        },
                        padding: {
                            top: 4,
                            bottom: 4
                        }
                    }

                }
            },
            plugins: [ChartDataLabels] // registra il plugin
        });
    </script>

    <?php
}


/* creare layout grafici */
function generaLayoutGrafico($classe_div, $h3, $id, $tipo, $label, $datiTotali, $mod = 'importi')
{
    if ($classe_div == '')
        $classe_div = 'col-md-6 my-3 text-center';

    ?>

    <div class="<?= htmlspecialchars($classe_div) ?>">
        <h3><?= htmlspecialchars($h3) ?></h3>
        <?php generaGrafico($id, $tipo, $label, $datiTotali['nomi'], $datiTotali[$mod], $datiTotali['colori']); ?>
    </div>

    <?php

}

