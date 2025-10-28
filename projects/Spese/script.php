<?php
include $_SERVER['DOCUMENT_ROOT'] . '/utility/classes/DB.php';

$db = new DB('spese');

$db->createTable('membri', [
    'id INT PRIMARY KEY AUTO_INCREMENT',
    'nome VARCHAR(100) NOT NULL',
    'descrizione VARCHAR(255)',
    'dati JSON',
    'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
]);

$db->createTable('categorie', [
    'id INT PRIMARY KEY AUTO_INCREMENT',
    'nome VARCHAR(100) NOT NULL',
    'descrizione VARCHAR(255)', 
    'dati JSON',
    'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
]);

$db->createTable('sottocategorie', [
    'id INT PRIMARY KEY AUTO_INCREMENT',
    'id_categoria INT NOT NULL',
    'nome VARCHAR(100) NOT NULL',
    'descrizione VARCHAR(255)', 
    'dati JSON',
    'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
    'FOREIGN KEY (id_categoria) REFERENCES categorie(id) ON DELETE CASCADE ON UPDATE CASCADE'
]);

$db->createTable('spese', [
    'id INT PRIMARY KEY AUTO_INCREMENT',
    'importo DECIMAL(10,2) NOT NULL',
    'data DATE NOT NULL',
    'descrizione VARCHAR(255)',
    'id_membro INT NOT NULL',                       
    'id_sottocategoria INT NOT NULL',               
    'dati JSON',
    'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
    'FOREIGN KEY (id_membro) REFERENCES membri(id) ON DELETE CASCADE ON UPDATE CASCADE',
    'FOREIGN KEY (id_sottocategoria) REFERENCES sottocategorie(id) ON DELETE CASCADE ON UPDATE CASCADE'
]);
