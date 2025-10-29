<?php

$logged_menu = [
    'Home' => 'home.php',
    'Visualizza' => 'visualizza.php',
    'Membri' => 'membri.php',
    'Categorie' => 'categorie.php',
    'Sottocategorie' => 'sottocategorie.php',
    'Spese' => 'spese.php',
    'Esporta DB' => 'esporta_db.php',
    'Esci' => 'esci.php',
];

$not_logged_menu = [
    'Home' => 'home.php',
    'Accedi' => 'accedi.php',
];

$dbname = 'spese';

function create_message($type, $text)
{
    return "<div class='alert alert-$type'>" . htmlspecialchars($text) . "</div>";
}

function checkLogin()
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?page=accedi.php');
        exit;
    }
}