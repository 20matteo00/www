<?php
include $_SERVER['DOCUMENT_ROOT'] . '/utility/path.php';

?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Streamfield Builder</title>
    <?= $bootstrap_css ?? '' ?>
    <?= $bootstrap_js ?? '' ?>
    <?= $bootstrap_icons ?? '' ?>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>

<body>
    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Streamfield Builder</h1>
            <div class="gap-2 d-flex">
                <button id="exportJson" class="btn btn-secondary">📥 JSON</button>
                <button id="exportHtml" class="btn btn-secondary">📥 HTML</button>
            </div>
        </div>

        <div class="row g-3">
            <!-- Sidebar Blocchi -->
            <div class="col-lg-3">
                <div class="config-section">
                    <h5 class="mb-3">Blocchi disponibili</h5>
                    <div class="d-flex flex-column gap-2" id="blockButtons"></div>

                    <div id="blocksList" style="display: none;">
                        <h5 class="mt-4 mb-3">Blocchi aggiunti</h5>
                        <div class="d-flex flex-column gap-2" id="blocksListContainer"></div>
                    </div>
                </div>
            </div>

            <!-- Configurazione -->
            <div class="col-lg-3">
                <div class="config-section">
                    <h5 class="mb-3">Configurazione</h5>
                    <div id="configArea">
                        <p class="text-muted">Seleziona un blocco per configurarlo</p>
                    </div>
                </div>
            </div>

            <!-- Anteprima -->
            <div class="col-lg-6">
                <div class="config-section">
                    <h5 class="mb-3">Anteprima</h5>
                    <div id="preview"
                        style="min-height: 400px; background-color: #f8f9fa; padding: 1.5rem; border-radius: 0.5rem;">
                        <p class="text-muted text-center">Nessun blocco aggiunto</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>