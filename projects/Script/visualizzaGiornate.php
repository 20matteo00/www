<?php
$input_json = "giornate/stagioni.json";
$dati = json_decode(file_get_contents($input_json), true);
$stagioni = array_keys($dati);
?>

<div class="container my-4">
    <label for="stagioneSelect" class="form-label text-white">Seleziona una stagione:</label>
    <select id="stagioneSelect" class="form-select mb-4">
        <option value="">-- Scegli --</option>
        <?php foreach ($stagioni as $stagione): ?>
            <option value="<?= $stagione ?>"><?= $stagione ?></option>
        <?php endforeach; ?>
    </select>

    <div id="giornateContainer"></div>
</div>

<script>
    const dati = <?= json_encode($dati) ?>;

    document.getElementById('stagioneSelect').addEventListener('change', function () {
        const stagione = this.value;
        const container = document.getElementById('giornateContainer');
        container.innerHTML = ''; // svuota il contenitore

        if (!stagione || !dati[stagione]) return;

        const stagioneData = dati[stagione];

        const h1 = document.createElement('h1');
        h1.className = 'bg-primary text-white text-center p-3 rounded mb-4';
        h1.textContent = "Stagione: " + stagione;
        container.appendChild(h1);

        const row = document.createElement('div');
        row.className = 'row g-3 my-2';

        Object.entries(stagioneData.giornate).forEach(([giornata, partite]) => {
            const col = document.createElement('div');
            col.className = 'col-12 col-md-6 col-lg-4';

            const card = document.createElement('div');
            card.className = 'card shadow-sm';

            const header = document.createElement('div');
            header.className = 'card-header bg-warning text-dark text-center fw-bold';
            header.textContent = 'Giornata ' + giornata;

            const ul = document.createElement('ul');
            ul.className = 'list-group list-group-flush';

            partite.forEach(p => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';

                li.innerHTML = `<span class="fw-bold">${p.data}</span>
                            <span class="flex-fill text-center">${p.squadre}</span>
                            <span class="badge bg-primary rounded-pill">${p.risultato}</span>`;
                ul.appendChild(li);
            });

            card.appendChild(header);
            card.appendChild(ul);
            col.appendChild(card);
            row.appendChild(col);
        });

        container.appendChild(row);
    });
</script>