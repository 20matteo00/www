<div class="container my-5">
    <?php

    $searchTerm = trim($_POST['text'] ?? "");

    $tablesToSearch = [
        'teams' => ['nome', 'dati'],
        'competitions' => ['nome', 'modalita', 'dati', 'squadre'],
    ];

    $finalResults = [];
    if ($searchTerm != "") {
        foreach ($tablesToSearch as $table => $columns) {
            $whereParts = [];
            $params = [];

            foreach ($columns as $i => $col) {
                $paramName = ":search_$i";
                $whereParts[] = "`$col` LIKE $paramName";
                $params[$paramName] = '%' . $searchTerm . '%';
            }

            $where = implode(' OR ', $whereParts);
            $sql = "SELECT * FROM `$table` WHERE $where LIMIT 20";

            $results = $db->runRaw($sql, $params);

            if (!empty($results)) {
                $finalResults[$table] = $results;
            }
        }
        if (!empty($finalResults)) {

            foreach ($finalResults as $table => $rows): ?>

                <h1 class="mb-3 text-capitalize fw-bold"><?= htmlspecialchars($table) ?></h1>

                <div class="table-responsive mb-5">
                    <table class="table table-bordered table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Nome</th>
                                <?php if ($table == 'teams'): ?>
                                    <th>Attacco</th>
                                    <th>Difesa</th>
                                <?php elseif ($table == 'competitions'): ?>
                                    <th>Modalità</th>
                                    <th>Dati</th>
                                <?php endif; ?>
                                <th>Data Creazione</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $row):
                                $dati = json_decode($row['dati'], true);
                                $bg_color = $dati['color']['bg'] ?? '#ffffff';
                                $text_color = $dati['color']['text'] ?? '#000000';
                                $border_color = $dati['color']['border'] ?? '#cccccc';
                                if ($table == 'teams'):
                                    $attacco = $dati['power']['attack'] ?? 'N/A';
                                    $difesa = $dati['power']['defense'] ?? 'N/A';
                                elseif ($table == 'competitions'):

                                endif;
                                ?>
                                <tr>
                                    <td>
                                        <div class="text-center fw-bold fs-3 rounded-pill"
                                            style="<?= generatestyle($bg_color, $text_color, $border_color) ?>">
                                            <?= htmlspecialchars($row['nome']); ?>
                                        </div>
                                    </td>
                                    <?php if ($table == 'teams'): ?>
                                        <td><?= htmlspecialchars($attacco) ?></td>
                                        <td><?= htmlspecialchars($difesa) ?></td>
                                    <?php elseif ($table == 'competitions'): ?>
                                        <td><?= htmlspecialchars($row['modalita']) ?></td>
                                        <td>
                                            <?php foreach ($dati as $key => $dato): ?>
                                                <?php
                                                if ($dato === false)
                                                    $dato = "No";
                                                elseif ($dato === true)
                                                    $dato = "Si";
                                                ?>
                                                <?php if ($dato != 0): ?>
                                                    <span class="fw-bold"><?= $key ?>: <?= $dato ?></span><br>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </td>
                                    <?php endif; ?>
                                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php
            endforeach;
        } else {
            echo create_message('info', 'Nessun risultato trovato.');
        }
    } else {
        echo create_message('info', 'Inserisci un termine di ricerca.');
    }



    ?>

</div>