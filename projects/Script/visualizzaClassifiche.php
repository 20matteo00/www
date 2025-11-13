<?php
$input_json = "classifiche/stagioni.json";
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

    <div id="classificaContainer"></div>
</div>

<script>
    const dati = <?= json_encode($dati) ?>;

    document.getElementById('stagioneSelect').addEventListener('change', function () {
        const stagione = this.value;
        const container = document.getElementById('classificaContainer');
        container.innerHTML = ''; // svuota il contenitore

        if (!stagione || !dati[stagione]) return;

        const stagioneData = dati[stagione];
        const seasonYear = parseInt(stagione.split('-')[0]) || 0;
        const moltiplicatore = (seasonYear >= 1994) ? 3 : 2;

        const h1 = document.createElement('h1');
        h1.className = 'bg-primary text-white text-center p-3 rounded mb-4';
        h1.textContent = "Stagione: " + stagione;
        container.appendChild(h1);

        const card = document.createElement('div');
        card.className = 'card shadow-sm';

        const header = document.createElement('div');
        header.className = 'card-header bg-warning text-dark text-center fw-bold';
        header.textContent = 'Classifica';
        card.appendChild(header);

        const tableDiv = document.createElement('div');
        tableDiv.className = 'table-responsive';

        // --- Prepariamo e ordiniamo le squadre ---
        let squadreArray = Object.entries(stagioneData).map(([squadra, s]) => {
            const total_vinte = s.Casa.Vinte + s.Trasferta.Vinte;
            const total_pari = s.Casa.Pareggiate + s.Trasferta.Pareggiate;
            const total_perse = s.Casa.Perse + s.Trasferta.Perse;
            const total_golf = s.Casa.GolFatti + s.Trasferta.GolFatti;
            const total_gols = s.Casa.GolSubiti + s.Trasferta.GolSubiti;

            const total_punti = (total_vinte * moltiplicatore) + total_pari;
            const total_diff = total_golf - total_gols;

            return {
                squadra,
                s,
                total_punti,
                total_diff,
                total_golf
            };
        });

        squadreArray.sort((a, b) => {
            if (b.total_punti !== a.total_punti) return b.total_punti - a.total_punti;
            if (b.total_diff !== a.total_diff) return b.total_diff - a.total_diff;
            return b.total_golf - a.total_golf;
        });

        // --- Generiamo la tabella ---
        let tableHTML = `
        <table class="table table-striped table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th></th>
                    <th colspan="8" class="text-center">Totale</th>
                    <th colspan="8" class="text-center">Casa</th>
                    <th colspan="8" class="text-center">Trasferta</th>
                </tr>
                <tr>
                    <th>Squadra</th>
                    <th>Pt</th><th>G</th><th>V</th><th>N</th><th>P</th><th>GF</th><th>GS</th><th>DR</th>
                    <th>Pt</th><th>G</th><th>V</th><th>N</th><th>P</th><th>GF</th><th>GS</th><th>DR</th>
                    <th>Pt</th><th>G</th><th>V</th><th>N</th><th>P</th><th>GF</th><th>GS</th><th>DR</th>
                </tr>
            </thead>
            <tbody>
        `;

        squadreArray.forEach(({ squadra, s }) => {
            const total_vinte = s.Casa.Vinte + s.Trasferta.Vinte;
            const total_pari = s.Casa.Pareggiate + s.Trasferta.Pareggiate;
            const total_perse = s.Casa.Perse + s.Trasferta.Perse;
            const total_golf = s.Casa.GolFatti + s.Trasferta.GolFatti;
            const total_gols = s.Casa.GolSubiti + s.Trasferta.GolSubiti;

            const total_punti = (total_vinte * moltiplicatore) + total_pari;
            const casa_punti = (s.Casa.Vinte * moltiplicatore) + s.Casa.Pareggiate;
            const trasferta_punti = (s.Trasferta.Vinte * moltiplicatore) + s.Trasferta.Pareggiate;

            const total_diff = total_golf - total_gols;
            const casa_diff = s.Casa.GolFatti - s.Casa.GolSubiti;
            const trasferta_diff = s.Trasferta.GolFatti - s.Trasferta.GolSubiti;

            const total_giocate = total_vinte + total_pari + total_perse;
            const casa_giocate = s.Casa.Vinte + s.Casa.Pareggiate + s.Casa.Perse;
            const trasferta_giocate = s.Trasferta.Vinte + s.Trasferta.Pareggiate + s.Trasferta.Perse;

            tableHTML += `
            <tr>
                <td class="fw-bold">${squadra}</td>
                <td class="fw-bold">${total_punti}</td><td class="fw-bold">${total_giocate}</td>
                <td class="fw-bold">${total_vinte}</td><td class="fw-bold">${total_pari}</td><td class="fw-bold">${total_perse}</td>
                <td class="fw-bold">${total_golf}</td><td class="fw-bold">${total_gols}</td><td class="fw-bold">${total_diff}</td>

                <td>${casa_punti}</td><td>${casa_giocate}</td><td>${s.Casa.Vinte}</td><td>${s.Casa.Pareggiate}</td><td>${s.Casa.Perse}</td>
                <td>${s.Casa.GolFatti}</td><td>${s.Casa.GolSubiti}</td><td>${casa_diff}</td>

                <td>${trasferta_punti}</td><td>${trasferta_giocate}</td><td>${s.Trasferta.Vinte}</td><td>${s.Trasferta.Pareggiate}</td><td>${s.Trasferta.Perse}</td>
                <td>${s.Trasferta.GolFatti}</td><td>${s.Trasferta.GolSubiti}</td><td>${trasferta_diff}</td>
            </tr>
            `;
        });

        tableHTML += '</tbody></table>';
        tableDiv.innerHTML = tableHTML;
        card.appendChild(tableDiv);
        container.appendChild(card);
    });
</script>
