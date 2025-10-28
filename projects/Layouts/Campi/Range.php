<?php
$min = 1;
$max = 10;
$rating = isset($_POST['rating']) ? (int) $_POST['rating'] : intval(($max + $min) / 2); // default a metà
?>

<div class="container my-3">
    <p class="mb-1 fs-5">Valuta il nostro servizio:</p>
    <form action="" method="post" class="d-flex align-items-center gap-3">
        <div class="flex-grow-1 position-relative">
            <input autocomplete="off" type="range" name="rating" min="<?= $min ?>" max="<?= $max ?>"
                value="<?= $rating ?>" class="form-range w-100" id="ratingRange">

            <!-- Indicatori min/max sotto lo slider -->
            <div class="d-flex justify-content-between position-absolute w-100 top-100 px-1">
                <span><?= $min ?></span>
                <span><?= $max ?></span>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-sm">Invia</button>
        <span class="fw-bold text-warning" id="ratingValue"><?= $rating ?>/<?= $max ?></span>
    </form>

    <?php if (isset($_POST['rating'])): ?>
        <p class="mt-4">Hai dato un voto di: <strong><?= $rating ?>/<?= $max ?></strong></p>
    <?php endif; ?>
</div>

<script>
    const range = document.getElementById('ratingRange');
    const valueDisplay = document.getElementById('ratingValue');
    const maxValue = <?= $max ?>;

    // Inizializza il valore all'avvio
    valueDisplay.textContent = range.value + "/" + maxValue;

    // Aggiorna il numero dinamicamente mentre muovi lo slider
    range.addEventListener('input', () => {
        valueDisplay.textContent = range.value + "/" + maxValue;
    });
</script>

<style>
    /* Min/max sotto lo slider */
    .position-relative .d-flex span {
        font-weight: bold;
        font-size: 0.9rem;
    }
</style>