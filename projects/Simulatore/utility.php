<?php

/* COSTANTI */

/* Percorsi */
define('BASE_URL', '?page=');
define('MEDIA_PATH', 'media/');
define('CSS_PATH', MEDIA_PATH . 'css/');
define('JS_PATH', MEDIA_PATH . 'js/');
define('IMAGES_PATH', MEDIA_PATH . 'images/');
define('PAGES_PATH', 'pages/');
define('LAYOUT_PATH', 'layout/');

/* Globali */
define('SITENAME', 'Simulatore di Calcio');





/* Variabili */
$menu_guest = [
    'squadre' => 'Squadre',
    'competizioni' => 'Competizioni',

    'accedi' => 'Accedi',
    'registrati' => 'Registrati',
];
$menu_logged = [
    'squadre' => 'Squadre',
    'competizioni' => 'Competizioni',

    'profilo' => 'Profilo',
    'esci' => 'Esci',
];

/* Funzioni */
function create_message($type, $text)
{
    return "<div class='alert alert-$type'>" . htmlspecialchars($text) . "</div>";
}
function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}