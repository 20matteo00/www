<?php

function lang($key) {
    static $translations = null;
    
    if ($translations === null) {
        $lang_file = $_SESSION['lang'] ?? 'it'; // lingua di default
        $translations = include $_SERVER['DOCUMENT_ROOT'] . "/utility/language/$lang_file.php";
    }

    return $translations[$key] ?? $key;
}


?>