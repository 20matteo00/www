<?php
// Campi da escludere
$excludeColumns = ['id', 'created_at'];

// Termini di ricerca
$searchTerm = trim($_POST['text'] ?? "");

// Prepara query base
$sql = "SELECT * FROM intrattenimento";
$params = [];

if ($searchTerm !== "") {
    $sql = "SELECT * FROM intrattenimento WHERE 
        nome LIKE ? OR 
        tipo LIKE ? OR 
        descrizione LIKE ? OR 
        dati LIKE ?
        LIMIT 20";

    $params = [
        '%' . $searchTerm . '%',
        '%' . $searchTerm . '%',
        '%' . $searchTerm . '%',
        '%' . $searchTerm . '%',
    ];

}

// Esegui query
$results = $db->runRaw($sql, $params);
?>

<div class="container my-5">
    <h1 class="mb-4">Intrattenimento</h1>
    <?php if (!empty($results)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle sortable">
                <thead class="table-dark">
                    <tr>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Descrizione</th>
                        <th>Generi</th>
                        <th>Dettagli</th>
                        <th>Dove Vedere</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $m):
                        $dati = json_decode($m['dati'], true);
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($m['nome']) ?></td>
                            <td><?= htmlspecialchars($tipi[$m['tipo']] ?? $m['tipo']) ?></td>
                            <td><?= htmlspecialchars($m['descrizione']) ?></td>
                            <td>
                                <?php
                                $keys = $dati['generi'] ?? []; // es. ["netflix","prime_video"]
                                $names = array_map(fn($k) => $generi[$k] ?? $k, $keys);
                                echo implode(', ', $names);
                                ?>
                            </td>
                            <td>
                                <?php if ($m['tipo'] == 'film'): ?>
                                    Anno: <?= $dati['anno'] ?? '' ?><br>
                                    Durata: <?= $dati['durata'] ?? '' ?> min<br>
                                <?php else: ?>
                                    Anni: <?= $dati['anno'] ?? '' ?><br>
                                    Durata media: <?= $dati['durata'] ?? '' ?> min<br>
                                    Stagioni: <?= $dati['stagioni'] ?? '' ?><br>
                                    Episodi: <?= $dati['episodi'] ?? '' ?><br>
                                    Conclusa: <?= ($dati['finita'] ?? 0) ? 'Sì' : 'No' ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $keys = $dati['piattaforme'] ?? []; // es. ["netflix","prime_video"]
                                $names = array_map(fn($k) => $piattaforme[$k] ?? $k, $keys);
                                echo implode(', ', $names);
                                ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="?page=media.php&edit=<?= $m['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Modifica
                                    </a>
                                    <a href="?page=media.php&delete=<?= $m['id'] ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Sicuro di voler eliminare questo media?')">
                                        <i class="fas fa-trash"></i> Elimina
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-info">Nessun risultato trovato.</p>
    <?php endif; ?>
</div>