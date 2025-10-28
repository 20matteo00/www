<?php
// Connessione a $conn (da fare all’inizio nel tuo script)

$msg = "";

// Funzione semplice per validare email


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? "");
    $email = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";
    $confirm = $_POST['confirm_password'] ?? "";

    // Validazione essenziale
    if ($username === "" || $email === "" || $password === "" || $confirm === "") {
        $msg = create_message('warning', 'Tutti i campi sono obbligatori.');
    } elseif (!isValidEmail($email)) {
        $msg = create_message('warning', 'Email non valida.');
    } elseif ($password !== $confirm) {
        $msg = create_message('warning', 'Le password non coincidono.');
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $data = [
            'username' => $username,
            'email' => $email,
            'password' => $hash
        ];

        $res = $db->insert('users', $data);

        if ($res) {
            $msg = create_message('success', 'Registrazione avvenuta con successo. Puoi ora accedere.');
            header('Refresh: 2; URL=index.php?page=accedi.php');
        } else {
            $msg = create_message('danger', 'Errore durante la registrazione. Riprova.');
        }
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow rounded-3">
                <div class="card-header">
                    <h3 class="m-0 py-3 text-center fw-bold fs-1">Registrazione</h3>
                </div>
                <div class="card-body p-4">
                    <?= $msg ?>
                    <form method="POST" validate autocomplete="on">
                        <div class="mb-3">
                            <label class="form-label" for="username">Username</label>
                            <input type="text" name="username" id="username" class="form-control" required
                                value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                        <div class="mb-3 position-relative">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-group">
                                <input type="password" id="password" name="password" class="form-control" required>
                                <button type="button" class="btn btn-outline-secondary"
                                    onclick="togglePassword('password', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3 position-relative">
                            <label class="form-label" for="confirm_password">Conferma Password</label>
                            <div class="input-group">
                                <input type="password" id="confirm_password" name="confirm_password"
                                    class="form-control" required>
                                <button type="button" class="btn btn-outline-secondary"
                                    onclick="togglePassword('confirm_password', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div id="passwordHelp" class="form-text text-danger d-none">Le password non coincidono.
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Registrati</button>
                    </form>
                </div>
                <div class="card-footer py-3">
                    <p class="m-0 text-center">Hai già un account? <a href="index.php?page=accedi.php">Accedi qui</a>.</p>
                </div>
            </div>
        </div>
    </div>
</div>