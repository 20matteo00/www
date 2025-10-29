<?php

$logged_menu = [
    'Home' => 'home.php',
    'Visualizza' => 'visualizza.php',
    'Membri' => 'membri.php',
    'Categorie' => 'categorie.php',
    'Sottocategorie' => 'sottocategorie.php',
    'Spese' => 'spese.php',
    'Esporta DB' => 'esporta_db.php',
    'Esci' => 'esci.php',
];

$not_logged_menu = [
    'Home' => 'home.php',
    'Accedi' => 'accedi.php',
];

$dbname = 'spese';

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


function getDatiSpeseMembri(DB $db): array
{
    $membri = $db->select('membri');
    $nomi = $importi = $quantita = $colori = [];

    foreach ($membri as $m) {
        $query = "
            SELECT COUNT(*) AS totale_spese, SUM(importo) AS totale_importo
            FROM spese
            WHERE id_membro = {$m['id']}
        ";

        $spese = $db->runQuery($query);

        if (!empty($spese) && $spese[0]['totale_importo'] !== null) {
            $nomi[] = $m['nome'];
            $colori[] = json_decode($m['dati'] ?? '', true)['color'] ?? '#000000';
            $importi[] = (float) $spese[0]['totale_importo'];
            $quantita[] = (int) $spese[0]['totale_spese'];
        }
    }

    return compact('nomi', 'importi', 'quantita', 'colori');
}

function getDatiSpeseCategorie(DB $db): array
{
    $categorie = $db->select('categorie');
    $nomi = $importi = $quantita = $colori = [];

    foreach ($categorie as $c) {
        $query = "
            SELECT COUNT(s.id) AS totale_spese, SUM(s.importo) AS totale_importo
            FROM spese s
            INNER JOIN sottocategorie sc ON s.id_sottocategoria = sc.id
            WHERE sc.id_categoria = {$c['id']}
        ";


        $spese = $db->runQuery($query);

        if (!empty($spese) && $spese[0]['totale_importo'] !== null) {
            $nomi[] = $c['nome'];
            $colori[] = json_decode($c['dati'] ?? '', true)['color'] ?? '#000000';
            $importi[] = (float) $spese[0]['totale_importo'];
            $quantita[] = (int) $spese[0]['totale_spese'];
        }
    }

    return compact('nomi', 'importi', 'quantita', 'colori');
}

function getDatiSpeseSottocategorie(DB $db): array
{
    $sottocategorie = $db->select('sottocategorie');
    $nomi = $importi = $quantita = $colori = [];

    foreach ($sottocategorie as $sc) {
        $query = "
            SELECT COUNT(*) AS totale_spese, SUM(importo) AS totale_importo
            FROM spese
            WHERE id_sottocategoria = {$sc['id']}
        ";

        $spese = $db->runQuery($query);

        if (!empty($spese) && $spese[0]['totale_importo'] !== null) {
            $nomi[] = $sc['nome'];
            $colori[] = json_decode($sc['dati'] ?? '', true)['color'] ?? '#000000';
            $importi[] = (float) $spese[0]['totale_importo'];
            $quantita[] = (int) $spese[0]['totale_spese'];
        }
    }

    return compact('nomi', 'importi', 'quantita', 'colori');
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


/* DB */
function getNomeCategoriaBySottocategoria(DB $db, int $idSottocategoria): ?string
{
    $query = "
        SELECT c.nome
        FROM sottocategorie s
        INNER JOIN categorie c ON s.id_categoria = c.id
        WHERE s.id = {$idSottocategoria}
        LIMIT 1
    ";
    $result = $db->runQuery($query);
    return $result[0]['nome'] ?? null;
}

function getNumeroSpeseEImportiTotali()
{

}