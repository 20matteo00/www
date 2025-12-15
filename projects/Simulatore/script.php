<?php
include $_SERVER['DOCUMENT_ROOT'] . '/utility/classes/DB.php';

$db = new DB('simulatore');

$db->createTable('utenti', [
    'id INT AUTO_INCREMENT PRIMARY KEY',
    'username VARCHAR(100) NOT NULL UNIQUE',
    'email VARCHAR(100) UNIQUE',
    'password VARCHAR(255) NOT NULL',
    'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
]);

$db->createTable('squadre', [
    'id INT AUTO_INCREMENT PRIMARY KEY',
    'id_utente INT NOT NULL',
    'nome VARCHAR(100) NOT NULL',
    'dati JSON',
    'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',

    // vincolo logico, NON primary
    'UNIQUE KEY uk_utente_nome (id_utente, nome)',

    // cascade SOLO sull’utente
    'FOREIGN KEY (id_utente) REFERENCES utenti(id) ON DELETE CASCADE ON UPDATE CASCADE'
]);

$db->createTable('competizioni', [
    'id INT AUTO_INCREMENT PRIMARY KEY',
    'id_utente INT NOT NULL',
    'nome VARCHAR(100) NOT NULL',
    'modalita INT NOT NULL DEFAULT 0',

    'dati JSON',
    'squadre JSON',
    'partite JSON',

    'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',

    // cascade SOLO sull’utente
    'FOREIGN KEY (id_utente) REFERENCES utenti(id) ON DELETE CASCADE ON UPDATE CASCADE'
]);
