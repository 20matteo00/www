<?php
// Elimina tutti i dati della sessione
$user = $_SESSION['username'] ?? '';
$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Distruggi la sessione
session_destroy();

// Messaggio di conferma (o redirect immediato)
$msg = create_message('success', 'Logout eseguito correttamente. A presto ' . htmlspecialchars($user) . '!');

// Redirect dopo 2 secondi alla home (o al login)
header("Refresh:2; url=index.php"); // Modifica url se vuoi
?>

<div class="container mt-5">

    <?= $msg ?>

</div>