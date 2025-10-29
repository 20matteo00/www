<?php
include 'layout/menu_visualizza.php';
$modalita = $_GET['modalita'] ?? 'membri';

?>

<div class="container">
    <div class="row">
        <?php
        switch ($modalita) {
            case 'membri':
                $datiTotali = getDatiSpeseMembri($db);
                $dati2025 = getDatiSpeseMembri($db, '2025');

                if (empty($datiTotali['nomi'])) {
                    echo "<p class='text-center'>Nessun dato disponibile per i membri.</p>";
                    break;
                }
                generaLayoutGrafico('', 'Somma Spese (Totale)', 'chartImportiTot', 'pie', 'Totale Importi (€)', $datiTotali);
                generaLayoutGrafico('', 'Numero Spese (Totale)', 'chartQuantitaTot', 'doughnut', 'Numero di Spese', $datiTotali);
                generaLayoutGrafico('', 'Somma Spese (2025)', 'chartImporti2025', 'pie', 'Totale Importi 2025 (€)', $dati2025);
                generaLayoutGrafico('', 'Numero Spese (2025)', 'chartQuantita2025', 'doughnut', 'Numero di Spese 2025', $dati2025);
                break;

            case 'categorie':
                $datiTotali = getDatiSpeseCategorie($db);
                $dati2025 = getDatiSpeseCategorie($db, '2025');
                if (empty($datiTotali['nomi'])) {
                    echo "<p class='text-center'>Nessun dato disponibile per le categorie.</p>";
                    break;
                }
                generaLayoutGrafico('', 'Somma Spese (Totale)', 'chartImportiTot', 'pie', 'Totale Importi (€)', $datiTotali);
                generaLayoutGrafico('', 'Numero Spese (Totale)', 'chartQuantitaTot', 'doughnut', 'Numero di Spese', $datiTotali);
                generaLayoutGrafico('', 'Somma Spese (2025)', 'chartImporti2025', 'pie', 'Totale Importi 2025 (€)', $dati2025);
                generaLayoutGrafico('', 'Numero Spese (2025)', 'chartQuantita2025', 'doughnut', 'Numero di Spese 2025', $dati2025);
                break;

            case 'sottocategorie':
                $datiTotali = getDatiSpeseSottocategorie($db);
                $dati2025 = getDatiSpeseSottocategorie($db, '2025');
                if (empty($datiTotali['nomi'])) {
                    echo "<p class='text-center'>Nessun dato disponibile per le sottocategorie.</p>";
                    break;
                }
                generaLayoutGrafico('', 'Somma Spese (Totale)', 'chartImportiTot', 'pie', 'Totale Importi (€)', $datiTotali);
                generaLayoutGrafico('', 'Numero Spese (Totale)', 'chartQuantitaTot', 'doughnut', 'Numero di Spese', $datiTotali);
                generaLayoutGrafico('', 'Somma Spese (2025)', 'chartImporti2025', 'pie', 'Totale Importi 2025 (€)', $dati2025);
                generaLayoutGrafico('', 'Numero Spese (2025)', 'chartQuantita2025', 'doughnut', 'Numero di Spese 2025', $dati2025);
                break;

            default:
                echo "<p>Modalità non riconosciuta.</p>";
                break;
        }
        ?>
    </div>
</div>