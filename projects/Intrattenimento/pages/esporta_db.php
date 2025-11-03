<?php

// percorso dove salvare il dump
$dumpPath = 'dump/dump.sql';
if (!is_dir(dirname($dumpPath))) {
    mkdir(dirname($dumpPath), 0777, true);
}

// crea il dump completo
$result = $db->backup(null, $dumpPath);

if ($result) {
    echo "✅ Dump creato con successo in: $result\n";
} else {
    echo "❌ Errore durante la creazione del dump\n";
}

header('Refresh: 2; URL=index.php?page=home.php');