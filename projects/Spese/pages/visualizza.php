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
                $importoTotale = array_sum($datiTotali['importi']);
                $quantitaTotale = array_sum($datiTotali['quantita']);
                if (empty($datiTotali['nomi'])) {
                    echo "<p class='text-center'>Nessun dato disponibile per i membri.</p>";
                    break;
                }
                generaLayoutGrafico('', 'Somma Spese (Totale: '.$importoTotale.')', 'chartImportiTot', 'pie', 'Totale Importi (€)', $datiTotali, 'importi');
                generaLayoutGrafico('', 'Numero Spese (Totale: '.$quantitaTotale.')', 'chartQuantitaTot', 'doughnut', 'Numero di Spese', $datiTotali, 'quantita');
                break;

            case 'categorie':
                $datiTotali = getDatiSpeseCategorie($db);
                $importoTotale = array_sum($datiTotali['importi']);
                $quantitaTotale = array_sum($datiTotali['quantita']);
                if (empty($datiTotali['nomi'])) {
                    echo "<p class='text-center'>Nessun dato disponibile per le categorie.</p>";
                    break;
                }
                generaLayoutGrafico('', 'Somma Spese (Totale: '.$importoTotale.')', 'chartImportiTot', 'pie', 'Totale Importi (€)', $datiTotali, 'importi');
                generaLayoutGrafico('', 'Numero Spese (Totale: '.$quantitaTotale.')', 'chartQuantitaTot', 'doughnut', 'Numero di Spese', $datiTotali, 'quantita');
                break;

            case 'sottocategorie':
                $datiTotali = getDatiSpeseSottocategorie($db);
                $importoTotale = array_sum($datiTotali['importi']);
                $quantitaTotale = array_sum($datiTotali['quantita']);
                if (empty($datiTotali['nomi'])) {
                    echo "<p class='text-center'>Nessun dato disponibile per le sottocategorie.</p>";
                    break;
                }
                generaLayoutGrafico('', 'Somma Spese (Totale: '.$importoTotale.')', 'chartImportiTot', 'pie', 'Totale Importi (€)', $datiTotali, 'importi');
                generaLayoutGrafico('', 'Numero Spese (Totale: '.$quantitaTotale.')', 'chartQuantitaTot', 'doughnut', 'Numero di Spese', $datiTotali, 'quantita');
                break;

            default:
                echo "<p>Modalità non riconosciuta.</p>";
                break;
        }
        ?>
    </div>
</div>