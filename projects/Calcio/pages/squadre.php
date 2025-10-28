<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=accedi.php');
    exit;
}
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'delete') {
        // ELIMINA
        $trovato = searchTeaminCompetitions($db, $_POST['nome']);

        if (!$trovato) {
            $db->delete('teams', [
                'nome' => $_POST['nome'],
                'id_utente' => $_SESSION['user_id']
            ]);
            $msg = create_message('success', 'Squadra eliminata con successo.');
        } else {
            $msg = create_message('danger', 'Squadra non eliminata in quanto presente in competizioni.');
        }
    } elseif (in_array($action, ['create', 'update'])) {
        // DATI COMUNI
        $name = trim($_POST['name'] ?? "");
        $name = mb_convert_case($name, MB_CASE_TITLE, "UTF-8");
        $bg_color = $_POST['bg_color'] ?? "#ffffff";
        $text_color = $_POST['text_color'] ?? "#000000";
        $border_color = $_POST['border_color'] ?? "#ffffff";
        $attack = intval($_POST['attack'] ?? 100);
        $defense = intval($_POST['defense'] ?? 100);

        if ($name === "" || $attack < 0 || $defense < 0) {
            $msg = create_message('warning', 'Inserisci tutti i campi richiesti correttamente.');
        } else {
            $dati = json_encode([
                'color' => [
                    'bg' => $bg_color,
                    'text' => $text_color,
                    'border' => $border_color
                ],
                'power' => [
                    'attack' => $attack,
                    'defense' => $defense
                ]
            ]);

            if ($action === 'create') {
                // CREA
                if (
                    $db->exists('teams', [
                        'id_utente' => $_SESSION['user_id'],
                        'nome' => $name
                    ])
                ) {
                    $msg = create_message('warning', 'Hai già una squadra con questo nome ⚠️');
                } else {
                    $res = $db->insert('teams', [
                        'id_utente' => $_SESSION['user_id'],
                        'nome' => $name,
                        'dati' => $dati
                    ]);
                    $msg = $res
                        ? create_message('success', 'Squadra creata con successo 🎉')
                        : create_message('danger', 'Errore durante la creazione.');
                }
            } else {
                // UPDATE
                $old_name = $_POST['old_name'] ?? $name;
                $trovato = searchTeaminCompetitions($db, $_POST['old_name']);
                if (!$trovato) {
                    $db->update('teams', [
                        'nome' => $name,
                        'dati' => $dati
                    ], [
                        'nome' => $old_name,
                        'id_utente' => $_SESSION['user_id']
                    ]);
                    $msg = create_message('success', 'Squadra aggiornata 👍');
                } else {
                    $db->update('teams', [
                        'dati' => $dati
                    ], [
                        'nome' => $old_name,
                        'id_utente' => $_SESSION['user_id']
                    ]);
                    $msg = create_message('danger', 'Errore aggiornamento.');
                }
            }
        }
    }

    header("Location: index.php?page=squadre.php");
    exit; // sempre mettere exit dopo un redirect
}

// Recupera lista squadre
$teams = $db->select('teams', [
    'where' => ['id_utente' => $_SESSION['user_id']],
    'orderBy' => 'created_at ASC',
]);
?>


<div class="container mt-5">
    <div class="justify-content-center">
        <div class="card shadow rounded-3">
            <div class="card-header">
                <h3 class="m-0 py-3 text-center fw-bold fs-1">Squadre</h3>
            </div>
            <div class="card-body p-4">
                <?= $msg ?>
                <form method="POST" id="teamForm" autocomplete="on">
                    <input type="hidden" name="action" id="formAction" value="create">
                    <input type="hidden" name="id" id="teamId" value="">

                    <div class="mb-3">
                        <label for="name" class="form-label">Nome Squadra</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>

                    <div class="row g-3">
                        <div class="col-auto col-md">
                            <label for="bg_color" class="form-label">Colore Sfondo</label>
                            <input type="color" id="bg_color" name="bg_color" value="#ffffff"
                                class="form-control form-control-color p-1 w-100">
                        </div>
                        <div class="col-auto col-md">
                            <label for="text_color" class="form-label">Colore Testo</label>
                            <input type="color" id="text_color" name="text_color" value="#000000"
                                class="form-control form-control-color p-1 w-100">
                        </div>
                        <div class="col-auto col-md">
                            <label for="border_color" class="form-label">Colore Bordo</label>
                            <input type="color" id="border_color" name="border_color" value="#ffffff"
                                class="form-control form-control-color p-1 w-100">
                        </div>
                        <div class="col-auto col-md" style="max-width: 6rem;">
                            <label for="attack" class="form-label">Attacco</label>
                            <input type="number" id="attack" name="attack" min="0" max="999" value="100" required
                                class="form-control">
                        </div>
                        <div class="col-auto col-md" style="max-width: 6rem;">
                            <label for="defense" class="form-label">Difesa</label>
                            <input type="number" id="defense" name="defense" min="0" max="999" value="100" required
                                class="form-control">
                        </div>
                    </div>
                    <input type="hidden" name="old_name" id="old_name" value="">

                    <button type="submit" class="btn btn-primary w-100 mt-3">Salva Squadra</button>
                </form>
            </div>
        </div>
    </div>
</div>


<?php
include "layout/squadre.php";
?>