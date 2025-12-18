// Mostra/nasconde password
function togglePassword(fieldId, btn) {
    const input = document.getElementById(fieldId);
    const icon = btn.querySelector("i");
    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("bi-eye", "bi-eye-slash");
    } else {
        input.type = "password";
        icon.classList.replace("bi-eye-slash", "bi-eye");
    }
}

function editTeam(team) {
    const dati = JSON.parse(team.dati);

    document.getElementById("formAction").value = "update";
    document.getElementById("old_name").value = team.nome; // salvo nome originale
    document.getElementById("name").value = team.nome;
    document.getElementById("bg_color").value = dati.color.bg;
    document.getElementById("text_color").value = dati.color.text;
    document.getElementById("border_color").value = dati.color.border;
    document.getElementById("attack").value = dati.power.attack;
    document.getElementById("defense").value = dati.power.defense;

    window.scrollTo({ top: 0, behavior: "smooth" });
}

function toggleInputs(value) {
    const options = ['campionato', 'eliminazione', 'gironi'];
    options.forEach(opt => {
        const elem = document.getElementById(opt + '-partecipanti');
        if (!elem) return;
        if (opt === value) {
            elem.classList.remove('d-none');
        } else {
            elem.classList.add('d-none');
        }
    });
}

window.addEventListener('load', function () {
    const select = document.getElementById('modalita');
    if (select) {
        toggleInputs(select.value);
    }
});



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
});

