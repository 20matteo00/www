<?php
$records = [
    //Generali
    "partite_totali" => "Partite Totali",
    "gol_totali" => "Gol Totali",
    "partita_piu_gol" => "Partita con Maggior Numero di Gol",
    "partita_piu_scarto" => "Partita con Maggior Scarto",

    //Non Consecutivi
    "totale,vinte,max" => "Maggior Numero di Vittorie",
    "totale,vinte,min" => "Minor Numero di Vittorie",
    "totale,pari,max" => "Maggior Numero di Pareggi",
    "totale,pari,min" => "Minor Numero di Pareggi",
    "totale,perse,max" => "Maggior Numero di Sconfitte",
    "totale,perse,min" => "Minor Numero di Sconfitte",
    "totale,gol_fatti,max" => "Maggior Numero di Gol Fatti",
    "totale,gol_fatti,min" => "Minor Numero di Gol Fatti",
    "totale,gol_subiti,max" => "Maggior Numero di Gol Subiti",
    "totale,gol_subiti,min" => "Minor Numero di Gol Subiti",
    "totale,diff_reti,max" => "Miglior Differenza Reti",
    "totale,diff_reti,min" => "Peggior Differenza Reti",
    "casa,vinte,max" => "Maggior Numero di Vittorie in Casa",
    "casa,vinte,min" => "Minor Numero di Vittorie in Casa",
    "casa,pari,max" => "Maggior Numero di Pareggi in Casa",
    "casa,pari,min" => "Minor Numero di Pareggi in Casa",
    "casa,perse,max" => "Maggior Numero di Sconfitte in Casa",
    "casa,perse,min" => "Minor Numero di Sconfitte in Casa",
    "casa,gol_fatti,max" => "Maggior Numero di Gol Fatti in Casa",
    "casa,gol_fatti,min" => "Minor Numero di Gol Fatti in Casa",
    "casa,gol_subiti,max" => "Maggior Numero di Gol Subiti in Casa",
    "casa,gol_subiti,min" => "Minor Numero di Gol Subiti in Casa",
    "casa,diff_reti,max" => "Miglior Differenza Reti in Casa",
    "casa,diff_reti,min" => "Peggior Differenza Reti in Casa",
    "trasferta,vinte,max" => "Maggior Numero di Vittorie in Trasferta",
    "trasferta,vinte,min" => "Minor Numero di Vittorie in Trasferta",
    "trasferta,pari,max" => "Maggior Numero di Pareggi in Trasferta",
    "trasferta,pari,min" => "Minor Numero di Pareggi in Trasferta",
    "trasferta,perse,max" => "Maggior Numero di Sconfitte in Trasferta",
    "trasferta,perse,min" => "Minor Numero di Sconfitte in Trasferta",
    "trasferta,gol_fatti,max" => "Maggior Numero di Gol Fatti in Trasferta",
    "trasferta,gol_fatti,min" => "Minor Numero di Gol Fatti in Trasferta",
    "trasferta,gol_subiti,max" => "Maggior Numero di Gol Subiti in Trasferta",
    "trasferta,gol_subiti,min" => "Minor Numero di Gol Subiti in Trasferta",
    "trasferta,diff_reti,max" => "Miglior Differenza Reti in Trasferta",
    "trasferta,diff_reti,min" => "Peggior Differenza Reti in Trasferta",
    //Consecutivi
];

?>

<div class="container my-5 statistiche">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h1 class="fw-bold text-center">Statistiche</h1>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead>
                                <tr>
                                    <th scope="col" colspan="2">Record</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($records as $key => $record): ?>
                                    <tr>
                                        <td class="fw-bold"><?= $record ?></td>
                                        <td><?= calcolaStatistiche($giornate, $key) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-center">
                </div>
            </div>
        </div>
    </div>
</div>