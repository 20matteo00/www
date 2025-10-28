<div class="sidebar p-2 mx-auto">
    <h5 class="text-white ps-3 mt-3 mb-3">Menu</h5>
    <ul class="list-unstyled">
        <li>
            <a data-bs-toggle="collapse" href="#menu1" role="button" aria-expanded="false" aria-controls="menu1">
                ⚙️ Gestione
            </a>
            <div class="collapse" id="menu1">
                <ul class="list-unstyled submenu">
                    <li>
                        <a data-bs-toggle="collapse" href="#menu1-1" aria-expanded="false"
                            aria-controls="menu1-1">Utenti</a>
                        <div class="collapse" id="menu1-1">
                            <ul class="list-unstyled submenu">
                                <li><a href="#" class="">Elenco utenti</a></li>
                                <li><a href="#" class="active">Permessi</a></li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="#">Ruoli</a>
                    </li>
                </ul>
            </div>
        </li>

        <li>
            <a data-bs-toggle="collapse" href="#menu2" aria-expanded="false" aria-controls="menu2">
                📊 Report
            </a>
            <div class="collapse" id="menu2">
                <ul class="list-unstyled submenu">
                    <li><a href="#">Mensili</a></li>
                    <li><a href="#">Annuali</a></li>
                </ul>
            </div>
        </li>

        <li><a href="#">📁 Archivio</a></li>
    </ul>
</div>

<style>
    .sidebar {
        width: 260px;
        background-color: #212529;
    }

    .sidebar a {
        color: #adb5bd;
        text-decoration: none;
        display: block;
        padding: 10px 15px;
    }

    .sidebar a:hover,
    .sidebar a.active {
        color: #fff;
        background-color: #343a40;
    }

    .submenu {
        padding-left: 1rem;
        border-left: 1px solid #343a40;
    }
</style>
<script>
    // Mantiene aperti i menu in base all'elemento .active
    document.addEventListener("DOMContentLoaded", function () {
        const activeLink = document.querySelector('.sidebar a.active');
        if (activeLink) {
            let parent = activeLink.closest('.collapse');
            while (parent) {
                const bsCollapse = new bootstrap.Collapse(parent, { toggle: true });
                parent = parent.parentElement.closest('.collapse');
            }
        }
    });
</script>