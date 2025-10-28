<div class="fixed-bottom bg-secondary border-top">
    <form class="d-flex justify-content-around py-2" method="post">
        <button type="submit" name="azione" value="calendario" class="btn btn-light text-center">
            <i class="bi bi-calendar3 fs-4"></i><br>
            <small class="d-none d-md-block">Calendario</small>
        </button>
        <button type="submit" name="azione" value="classifica" class="btn btn-light text-center">
            <i class="bi bi-list-ol fs-4"></i><br>
            <small class="d-none d-md-block">Classifica</small>
        </button>
        <button type="submit" name="azione" value="andamento" class="btn btn-light text-center">
            <i class="bi bi-graph-up fs-4"></i><br>
            <small class="d-none d-md-block">Andamento</small>
        </button>
        <button type="submit" name="azione" value="tabellone" class="btn btn-light text-center">
            <i class="bi bi-grid-3x3-gap fs-4"></i><br>
            <small class="d-none d-md-block">Tabellone</small>
        </button>
        <button type="submit" name="azione" value="statistiche" class="btn btn-light text-center">
            <i class="bi bi-bar-chart-line fs-4"></i><br>
            <small class="d-none d-md-block">Statistiche</small>
        </button>
        <?php if ($_POST['azione'] == "calendario"): ?>
            <button type="submit" name="azione" value="simula" class="btn btn-light text-center text-warning" <?= $disabled ?>>
                <i class="bi bi-play-circle fs-4"></i><br>
                <small class="d-none d-md-block">Simula</small>
            </button>
            <button type="submit" name="azione" value="cancella" class="btn btn-light text-center text-danger" <?= $disabled ?>>
                <i class="bi bi-trash fs-4"></i><br>
                <small class="d-none d-md-block">Cancella</small>
            </button>
        <?php endif; ?>
        <?php if (!$gamenull && !$finita): ?>
            <button type="submit" name="azione" value="chiudi" class="btn btn-light text-center text-warning">
                <i class="bi bi-door-closed fs-4"></i><br>
                <small class="d-none d-md-block">Chiudi</small>
            </button>
        <?php endif; ?>
    </form>
</div>