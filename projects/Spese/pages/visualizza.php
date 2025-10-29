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
                ?>

                <!-- Totali -->
                <div class="col-md-6 my-3 text-center">
                    <h3>Somma Spese (Totale)</h3>
                    <?php generaGrafico('chartImportiTot', 'pie', 'Totale Importi (€)', $datiTotali['nomi'], $datiTotali['importi'], $datiTotali['colori']); ?>
                </div>
                <div class="col-md-6 my-3 text-center">
                    <h3>Numero Spese (Totale)</h3>
                    <?php generaGrafico('chartQuantitaTot', 'doughnut', 'Numero di Spese', $datiTotali['nomi'], $datiTotali['quantita'], $datiTotali['colori']); ?>
                </div>

                <!-- 2025 -->
                <div class="col-md-6 my-3 text-center">
                    <h3>Somma Spese (2025)</h3>
                    <?php generaGrafico('chartImporti2025', 'pie', 'Totale Importi 2025 (€)', $dati2025['nomi'], $dati2025['importi'], $dati2025['colori']); ?>
                </div>
                <div class="col-md-6 my-3 text-center">
                    <h3>Numero Spese (2025)</h3>
                    <?php generaGrafico('chartQuantita2025', 'doughnut', 'Numero di Spese 2025', $dati2025['nomi'], $dati2025['quantita'], $dati2025['colori']); ?>
                </div>

                <?php
                break;

            default:
                echo "<p>Modalità non riconosciuta.</p>";
                break;
        }
        ?>
    </div>
</div>
