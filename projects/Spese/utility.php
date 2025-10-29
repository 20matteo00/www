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


function getDatiSpeseMembri(DB $db, ?string $anno = null): array
{
    $membri = $db->select('membri');
    $nomi = $importi = $quantita = $colori = [];

    foreach ($membri as $m) {
        $query = "
            SELECT COUNT(*) AS totale_spese, SUM(importo) AS totale_importo
            FROM spese
            WHERE id_membro = {$m['id']}
        ";

        if ($anno) {
            $query .= " AND YEAR(data) = {$anno}";
        }

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

function getDatiSpeseCategorie(DB $db, ?string $anno = null): array
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

        if ($anno) {
            $query .= " AND YEAR(s.data) = {$anno}";
        }

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

function getDatiSpeseSottocategorie(DB $db, ?string $anno = null): array
{
    $sottocategorie = $db->select('sottocategorie');
    $nomi = $importi = $quantita = $colori = [];

    foreach ($sottocategorie as $sc) {
        $query = "
            SELECT COUNT(*) AS totale_spese, SUM(importo) AS totale_importo
            FROM spese
            WHERE id_sottocategoria = {$sc['id']}
        ";

        if ($anno) {
            $query .= " AND YEAR(data) = {$anno}";
        }

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
                    legend: { display: true, position: 'top' }
                }
            }
        });
    </script>
    <?php
}


/* creare layout grafici */
function generaLayoutGrafico($classe_div, $h3, $id, $tipo, $label, $datiTotali)
{
    if ($classe_div == '') $classe_div = 'col-md-6 my-3 text-center';
    
    ?>

    <div class="<?= htmlspecialchars($classe_div) ?>">
        <h3><?= htmlspecialchars($h3) ?></h3>
        <?php generaGrafico($id, $tipo, $label, $datiTotali['nomi'], $datiTotali['importi'], $datiTotali['colori']); ?>
    </div>

    <?php

}