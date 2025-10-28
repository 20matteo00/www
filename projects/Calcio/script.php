<?php
include $_SERVER['DOCUMENT_ROOT'] . '/utility/classes/DB.php';

$db = new DB('calcio');

$db->createTable('users', [
    'id INT PRIMARY KEY AUTO_INCREMENT',
    'username VARCHAR(100) NOT NULL',
    'email VARCHAR(100) UNIQUE',
    'password VARCHAR(255)',
    'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
]);

$db->createTable('teams', [
    'id_utente INT NOT NULL',
    'nome VARCHAR(100) NOT NULL',
    'dati JSON',
    'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
    'PRIMARY KEY (id_utente, nome)',
    'FOREIGN KEY (id_utente) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE'
]);

$db->createTable('competitions', [
    'id INT PRIMARY KEY AUTO_INCREMENT',
    'id_utente INT NOT NULL',
    'nome VARCHAR(100) NOT NULL',
    'modalita VARCHAR(50) DEFAULT "campionato"',
    'dati JSON',
    'squadre JSON',
    'partite JSON',
    'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
    'FOREIGN KEY (id_utente) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE'
]);
