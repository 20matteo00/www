<?php

$logged_menu = [
    'Home' => 'home.php',
    'Membri' => 'membri.php',
    'Categorie' => 'categorie.php',
    'Sottocategorie' => 'sottocategorie.php',
    'Spese' => 'spese.php',
    'Visualizza' => 'visualizza.php',
    'Esci' => 'esci.php',
];

$not_logged_menu = [
    'Home' => 'home.php',
    'Visualizza' => 'visualizza.php',
    'Accedi' => 'accedi.php',
];


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