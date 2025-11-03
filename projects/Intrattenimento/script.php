<?php
include $_SERVER['DOCUMENT_ROOT'] . '/utility/classes/DB.php';
include 'utility.php';

$db = new DB($dbname);

$db->createTable('intrattenimento', [
    'id INT PRIMARY KEY AUTO_INCREMENT',
    'nome VARCHAR(255) NOT NULL',
    'tipo VARCHAR(255) NOT NULL',
    'descrizione TEXT',
    'dati JSON',
    'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
]);
