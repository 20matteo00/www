<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? "");
    $password = $_POST['password'] ?? "";

    if ($username === "" || $password === "") {
        $msg = create_message('warning', 'Inserisci sia username che password.');
    } else {
        if ($username === "admin" && $password === "admin") {
            $_SESSION['user_id'] = 1;
            $_SESSION['username'] = "Admin";
            $_SESSION['logged'] = true;
            $msg = create_message('success', "Benvenuto " . htmlspecialchars($_SESSION['username']) . "!");
            header('Refresh: 2; URL=index.php');
        } else {
            $msg = create_message('danger', 'Credenziali non valide.');
            session_destroy();
            header('Refresh: 2; URL=index.php?page=accedi.php');
        }
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow rounded-3">
                <div class="card-header">
                    <h3 class="m-0 py-3 text-center fw-bold fs-1">Accedi</h3>
                </div>
                <div class="card-body p-4">
                    <?= $msg ?>
                    <form method="POST" autocomplete="on">
                        <div class="mb-3">
                            <label class="form-label" for="username">Username</label>
                            <input type="text" name="username" id="username" class="form-control" required
                                value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
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
                        <button type="submit" class="btn btn-primary w-100">Accedi</button>
                    </form>
                </div>
                <div class="card-footer py-3">
                    <p class="m-0 text-center">Non hai ancora un account? <a
                            href="index.php?page=registrati.php">Registrati qui</a>.</p>
                </div>
            </div>
        </div>
    </div>
</div>