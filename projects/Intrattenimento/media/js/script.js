document.addEventListener('DOMContentLoaded', () => {
  // solo tabelle con classe "sortable"
  const tables = document.querySelectorAll('table.sortable');
  
  tables.forEach(table => {
    const headers = table.querySelectorAll('th');
    
    headers.forEach((th, index) => {
      th.style.cursor = 'pointer';
      th.addEventListener('click', () => {
        sortTableByColumn(table, index);
      });
    });
  });

  function sortTableByColumn(table, colIndex) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));

    const asc = !table.dataset.sortAsc || table.dataset.sortAsc === "false";
    table.dataset.sortAsc = asc;

    rows.sort((a, b) => {
      const aText = a.children[colIndex].textContent.trim();
      const bText = b.children[colIndex].textContent.trim();

      const aNum = parseFloat(aText.replace(',', '.'));
      const bNum = parseFloat(bText.replace(',', '.'));

      if (!isNaN(aNum) && !isNaN(bNum)) {
        return asc ? aNum - bNum : bNum - aNum;
      } else {
        return asc ? aText.localeCompare(bText) : bText.localeCompare(aText);
      }
    });

    rows.forEach(row => tbody.appendChild(row));
  }


  const tipoSelect = document.getElementById('tipo');
    const campiFilm = document.getElementById('campi-film');
    const campiSerie = document.getElementById('campi-serie');

    function aggiornaCampi() {
        const tipo = tipoSelect.value;

        // Nascondi tutto
        campiFilm.style.display = 'none';
        campiSerie.style.display = 'none';

        // Mostra in base al tipo
        if (tipo === 'film') {
            campiFilm.style.display = 'block';
        } else if (tipo === 'serie_tv') {
            campiSerie.style.display = 'block';
        }
    }

    tipoSelect.addEventListener('change', aggiornaCampi);

    // Inizializza stato corretto se stai modificando un record
    aggiornaCampi();
});