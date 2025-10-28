<div class="bg-white">
    <form class="d-flex justify-content-around py-2" method="post">
        <input type="hidden" name="azione" value="classifica">
        <button type="submit" name="modalita" value="totale" class="btn btn-success text-center">
            <i class="bi bi-stack fs-4"></i><br>
            <small class="d-none d-md-block">Totale</small>
        </button>
        <button type="submit" name="modalita" value="casa" class="btn btn-success text-center">
            <i class="bi bi-house-door fs-4"></i><br>
            <small class="d-none d-md-block">Casa</small>
        </button>
        <button type="submit" name="modalita" value="trasferta" class="btn btn-success text-center">
            <i class="bi bi-bus-front fs-4"></i><br>
            <small class="d-none d-md-block">Trasferta</small>
        </button>
        <?php if ($ar == "Si"): ?>
            <button type="submit" name="modalita" value="andata" class="btn btn-success text-center">
                <i class="bi bi-arrow-right-circle fs-4"></i><br>
                <small class="d-none d-md-block">Andata</small>
            </button>
            <button type="submit" name="modalita" value="ritorno" class="btn btn-success text-center">
                <i class="bi bi-arrow-left-circle fs-4"></i><br>
                <small class="d-none d-md-block">Ritorno</small>
            </button>
        <?php endif; ?>
    </form>
</div>