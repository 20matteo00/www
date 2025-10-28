const blockTemplates = {
  titolo: {
    label: 'Titolo',
    fields: [
      { name: 'heading', type: 'select', label: 'Livello', options: ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], default: 'h1' },
      { name: 'text', type: 'text', label: 'Testo', placeholder: 'Inserisci il titolo', default: '' },
      { name: 'class', type: 'text', label: 'Classi CSS', placeholder: 'es: fw-bold text-primary', default: '' }
    ]
  },
  paragrafo: {
    label: 'Paragrafo',
    fields: [
      { name: 'text', type: 'textarea', label: 'Testo', placeholder: 'Inserisci il paragrafo', default: '' },
      { name: 'class', type: 'text', label: 'Classi CSS', placeholder: 'es: text-muted', default: '' }
    ]
  },
  immagine: {
    label: 'Immagine',
    fields: [
      { name: 'src', type: 'text', label: 'URL', placeholder: 'https://...', default: '' },
      { name: 'alt', type: 'text', label: 'Testo alternativo', default: '' },
      { name: 'class', type: 'text', label: 'Classi CSS', default: 'img-fluid rounded' }
    ]
  },
  lista: {
    label: 'Lista',
    fields: [
      { name: 'items', type: 'textarea', label: 'Elementi', placeholder: 'Uno per riga', default: '' },
      { name: 'class', type: 'text', label: 'Classi CSS', default: '' }
    ]
  },
  citazione: {
    label: 'Citazione',
    fields: [
      { name: 'text', type: 'textarea', label: 'Testo citazione', placeholder: 'Inserisci il testo della citazione', default: '' },
      { name: 'author', type: 'text', label: 'Autore', placeholder: 'Nome autore', default: '' },
      { name: 'class', type: 'text', label: 'Classi CSS', default: 'blockquote' }
    ]
  },
  video: {
    label: 'Video',
    fields: [
      { name: 'url', type: 'text', label: 'URL video', placeholder: 'https://www.youtube.com/...', default: '' },
      { name: 'class', type: 'text', label: 'Classi CSS', default: 'ratio ratio-16x9' }
    ]
  },
  pulsante: {
    label: 'Pulsante',
    fields: [
      { name: 'text', type: 'text', label: 'Etichetta', placeholder: 'Testo del pulsante', default: '' },
      { name: 'url', type: 'text', label: 'Link', placeholder: 'https://...', default: '' },
      { name: 'class', type: 'text', label: 'Classi CSS', default: 'btn btn-primary' }
    ]
  },
  tabella: {
    label: 'Tabella',
    fields: [
      { name: 'rows', type: 'textarea', label: 'Righe (usa | per colonne)', placeholder: 'Col1 | Col2 | Col3', default: '' },
      { name: 'class', type: 'text', label: 'Classi CSS', default: 'table table-striped' }
    ]
  },
  evidenziazione: {
    label: 'Box evidenziato',
    fields: [
      { name: 'tipo', type: 'select', label: 'Tipo', options: ['info', 'avviso', 'errore', 'successo'], default: 'info' },
      { name: 'text', type: 'textarea', label: 'Testo', placeholder: 'Inserisci il messaggio', default: '' }
    ]
  },
  codice: {
    label: 'Codice',
    fields: [
      { name: 'language', type: 'text', label: 'Linguaggio', placeholder: 'es: javascript, html, css', default: '' },
      { name: 'code', type: 'textarea', label: 'Codice', placeholder: 'Incolla il codice qui...', default: '' }
    ]
  },
  embed: {
    label: 'Embed / HTML personalizzato',
    fields: [
      { name: 'html', type: 'textarea', label: 'Codice HTML', placeholder: '<iframe>...</iframe>', default: '' }
    ]
  },
  separatore: {
    label: 'Separatore',
    fields: [
      { name: 'class', type: 'text', label: 'Classi CSS', default: 'my-4 border-top' }
    ]
  }
};

let blocks = [];
let selectedBlockId = null;

/* Bottoni blocchi */
function renderBlockButtons() {
  const container = document.getElementById('blockButtons');
  container.innerHTML = Object.entries(blockTemplates).map(([key, template]) =>
    `<button type="button" class="btn btn-outline-primary text-start w-100 mb-1" onclick="addBlock('${key}')">+ ${template.label}</button>`
  ).join('');
}

/* Aggiungi blocco */
function addBlock(type) {
  const newBlock = {
    id: Date.now(),
    type,
    data: blockTemplates[type].fields.reduce((acc, field) => {
      acc[field.name] = field.default;
      return acc;
    }, {})
  };
  blocks.push(newBlock);
  selectedBlockId = newBlock.id;
  render();
}

/* Aggiorna dati blocco */
function updateBlock(fieldName, value) {
  const block = blocks.find(b => b.id === selectedBlockId);
  if (block) {
    block.data[fieldName] = value;
    render();
  }
}

/* Elimina blocco */
function deleteBlock(id) {
  blocks = blocks.filter(b => b.id !== id);
  selectedBlockId = blocks[0]?.id || null;
  render();
}

/* Sposta blocco */
function moveBlock(id, direction) {
  const index = blocks.findIndex(b => b.id === id);
  const swapIndex = direction === 'up' ? index - 1 : index + 1;
  if (swapIndex >= 0 && swapIndex < blocks.length) {
    [blocks[index], blocks[swapIndex]] = [blocks[swapIndex], blocks[index]];
    render();
  }
}

/* Configurazione blocco */
function renderConfigArea() {
  const container = document.getElementById('configArea');
  const block = blocks.find(b => b.id === selectedBlockId);

  if (!block) {
    container.innerHTML = '<p class="text-muted">Seleziona un blocco per configurarlo</p>';
    return;
  }

  const template = blockTemplates[block.type];
  let html = `
    <div class="bg-light p-2 rounded mb-3 d-flex justify-content-between align-items-center">
      <strong>${template.label}</strong>
      <div class="gap-1 d-flex">
        <button class="btn btn-sm btn-outline-secondary" onclick="moveBlock(${block.id}, 'up')">↑</button>
        <button class="btn btn-sm btn-outline-secondary" onclick="moveBlock(${block.id}, 'down')">↓</button>
        <button class="btn btn-sm btn-outline-danger" onclick="deleteBlock(${block.id})">🗑</button>
      </div>
    </div>
  `;

  template.fields.forEach(field => {
    const value = block.data[field.name];
    html += `<div class="mb-3"><label class="form-label">${field.label}</label>`;
    if (field.type === 'select') {
      html += `<select class="form-select" onchange="updateBlock('${field.name}', this.value)">`;
      field.options.forEach(opt => html += `<option value="${opt}" ${value === opt ? 'selected' : ''}>${opt}</option>`);
      html += `</select>`;
    } else if (field.type === 'textarea') {
      html += `<textarea class="form-control" rows="3" placeholder="${field.placeholder}" onchange="updateBlock('${field.name}', this.value)">${value}</textarea>`;
    } else {
      html += `<input type="text" class="form-control" placeholder="${field.placeholder}" value="${value}" onchange="updateBlock('${field.name}', this.value)">`;
    }
    html += `</div>`;
  });

  container.innerHTML = html;
}

/* Anteprima blocchi */
function renderPreview() {
  const container = document.getElementById('preview');
  if (blocks.length === 0) {
    container.innerHTML = '<p class="text-muted text-center">Nessun blocco aggiunto</p>';
    return;
  }

  container.innerHTML = blocks.map(block => {
    const d = block.data;
    let html = '';
    switch (block.type) {
      case 'titolo':
        html = `<${d.heading} class="${d.class}">${d.text}</${d.heading}>`; break;
      case 'paragrafo':
        html = `<p class="${d.class}">${d.text}</p>`; break;
      case 'lista':
        const items = d.items.split('\n').filter(i => i.trim()).map(i => `<li>${i}</li>`).join('');
        html = `<ul class="${d.class}">${items}</ul>`; break;
      case 'citazione':
        html = `<blockquote class="${d.class}"><p>${d.text}</p><footer>${d.author}</footer></blockquote>`; break;
      case 'video':
        html = `<div class="${d.class}"><iframe src="${d.url}" frameborder="0" allowfullscreen></iframe></div>`; break;
      case 'pulsante':
        html = `<a href="${d.url}" class="${d.class}">${d.text}</a>`; break;
      case 'tabella':
        const rows = d.rows.split('\n').map(r => `<tr>${r.split('|').map(c => `<td>${c.trim()}</td>`).join('')}</tr>`).join('');
        html = `<table class="${d.class}">${rows}</table>`; break;
      case 'evidenziazione':
        html = `<div class="alert alert-${d.tipo}">${d.text}</div>`; break;
      case 'codice':
        html = `<pre><code class="language-${d.language}">${d.code}</code></pre>`; break;
      case 'embed':
        html = d.html; break;
      case 'immagine':
        html = d.src ? `<img src="${d.src}" alt="${d.alt}" class="${d.class}">` : '<div class="bg-light p-4 text-center text-muted">Immagine</div>'; break;
      case 'separatore':
        html = `<hr class="${d.class}">`; break;
    }
    return `<div class="preview-item p-2 rounded ${selectedBlockId === block.id ? 'border border-primary' : 'border'} mb-2" onclick="selectedBlockId=${block.id}; render();">${html}</div>`;
  }).join('');
}

/* Lista blocchi */
function renderBlocksList() {
  const container = document.getElementById('blocksListContainer');
  const listDiv = document.getElementById('blocksList');
  if (blocks.length === 0) {
    listDiv.style.display = 'none';
    return;
  }
  listDiv.style.display = 'block';
  container.innerHTML = blocks.map((b, idx) =>
    `<div class="p-2 border rounded mb-1 ${selectedBlockId === b.id ? 'bg-light border-primary' : ''}" onclick="selectedBlockId=${b.id}; render();">
      <strong>${blockTemplates[b.type].label}</strong> <span class="small text-muted">#${idx + 1}</span>
    </div>`
  ).join('');
}

/* Render generale */
function render() {
  renderConfigArea();
  renderPreview();
  renderBlocksList();
}

/* Esportazioni */
function exportJSON() {
  const blob = new Blob([JSON.stringify(blocks, null, 2)], { type: 'application/json' });
  const a = document.createElement('a');
  a.href = URL.createObjectURL(blob);
  a.download = 'content.json';
  a.click();
  URL.revokeObjectURL(a.href);
}

function exportHTML() {
  let html = '<div class="container mt-5">';
  blocks.forEach(b => {
    const d = b.data;
    switch (b.type) {
      case 'titolo':
        html += `<${d.heading} class="${d.class}">${d.text}</${d.heading}>`; break;
      case 'paragrafo':
        html += `<p class="${d.class}">${d.text}</p>`; break;
      case 'lista':
        html += `<ul class="${d.class}">${d.items.split('\n').map(i => `<li>${i}</li>`).join('')}</ul>`; break;
      case 'citazione':
        html += `<blockquote class="${d.class}"><p>${d.text}</p><footer>${d.author}</footer></blockquote>`; break;
      case 'video':
        html += `<div class="${d.class}"><iframe src="${d.url}" allowfullscreen></iframe></div>`; break;
      case 'pulsante':
        html += `<a href="${d.url}" class="${d.class}">${d.text}</a>`; break;
      case 'tabella':
        html += `<table class="${d.class}">${d.rows.split('\n').map(r => `<tr>${r.split('|').map(c => `<td>${c.trim()}</td>`).join('')}</tr>`).join('')}</table>`; break;
      case 'evidenziazione':
        html += `<div class="alert alert-${d.tipo}">${d.text}</div>`; break;
      case 'codice':
        html += `<pre><code class="language-${d.language}">${d.code}</code></pre>`; break;
      case 'embed':
        html += d.html; break;
      case 'immagine':
        html += `<img src="${d.src}" alt="${d.alt}" class="${d.class}">`; break;
      case 'separatore':
        html += `<hr class="${d.class}">`; break;
    }
  });
  html += '</div>';
  const blob = new Blob([html], { type: 'text/html' });
  const a = document.createElement('a');
  a.href = URL.createObjectURL(blob);
  a.download = 'content.html';
  a.click();
  URL.revokeObjectURL(a.href);
}

renderBlockButtons();

document.getElementById('exportJson').addEventListener('click', exportJSON);
document.getElementById('exportHtml').addEventListener('click', exportHTML);