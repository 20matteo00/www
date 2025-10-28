<div class="container my-5">
    <?php
$excludeColumns = ['id', 'created_at', 'id_membro', 'id_categoria', 'id_sottocategoria']; // Metti qui i campi che NON vuoi stampare

$searchTerm = trim($_POST['text'] ?? "");

$tablesToSearch = [
    'membri' => ['nome', 'descrizione', 'dati'],
    'categorie' => ['nome', 'descrizione', 'dati'],
    'sottocategorie' => ['nome', 'descrizione', 'dati'],
    'spese' => ['importo', 'data', 'descrizione', 'dati'],
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
                <table class="table table-bordered table-striped table-hover align-middle sortable">
                    <thead class="table-dark">
                        <tr>
                            <?php
                            foreach (array_keys($rows[0]) as $header) {
                                if (!in_array($header, $excludeColumns)) {
                                    echo '<th>' . htmlspecialchars(ucwords(str_replace('_', ' ', $header))) . '</th>';
                                }
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($rows as $row) {
                            echo '<tr>';
                            foreach ($row as $col => $cell) {
                                if (!in_array($col, $excludeColumns)) {
                                    echo '<td>' . htmlspecialchars((string) $cell) . '</td>';
                                }
                            }
                            echo '</tr>';
                        }
                        ?>
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