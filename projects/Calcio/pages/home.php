<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=accedi.php');
    exit;
}

$last_teams = $db->select("teams", [
    "where" => ["id_utente" => $_SESSION['user_id']],
    "orderBy" => "created_at DESC",
    "limit" => 5
]);
if (!empty($last_teams)) {
    include 'layout/last_teams.php';
}

$last_comp = $db->select("competitions", [
    "where" => ["id_utente" => $_SESSION['user_id']],
    "orderBy" => "created_at DESC",
    "limit" => 5
]);
if (!empty($last_comp)) {
    include 'layout/last_comp.php';
}
?>