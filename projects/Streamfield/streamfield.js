import React, { useState } from 'react';
import { Trash2, ChevronUp, ChevronDown } from 'lucide-react';

const BlockBuilder = () => {
  const [blocks, setBlocks] = useState([]);
  const [selectedBlockId, setSelectedBlockId] = useState(null);

  const blockTemplates = {
    titolo: {
      label: 'Titolo',
      fields: [
        { name: 'heading', type: 'select', label: 'Livello', options: ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], default: 'h1' },
        { name: 'text', type: 'text', label: 'Testo', placeholder: 'Inserisci il titolo', default: '' },
        { name: 'class', type: 'text', label: 'Classi CSS', placeholder: 'es: text-primary fw-bold', default: '' }
      ]
    },
    paragrafo: {
      label: 'Paragrafo',
      fields: [
        { name: 'text', type: 'textarea', label: 'Testo', placeholder: 'Inserisci il paragrafo', default: '' },
        { name: 'class', type: 'text', label: 'Classi CSS', placeholder: 'es: text-muted', default: '' }
      ]
    },
    lista: {
      label: 'Lista',
      fields: [
        { name: 'items', type: 'textarea', label: 'Elementi', placeholder: 'Uno per riga', default: '' },
        { name: 'class', type: 'text', label: 'Classi CSS', default: '' }
      ]
    },
    immagine: {
      label: 'Immagine',
      fields: [
        { name: 'src', type: 'text', label: 'URL', placeholder: 'https://...', default: '' },
        { name: 'alt', type: 'text', label: 'Testo alternativo', default: '' },
        { name: 'class', type: 'text', label: 'Classi CSS', default: 'img-fluid' }
      ]
    }
  };

  const addBlock = (type) => {
    const newBlock = {
      id: Date.now(),
      type,
      data: blockTemplates[type].fields.reduce((acc, field) => {
        acc[field.name] = field.default;
        return acc;
      }, {})
    };
    setBlocks([...blocks, newBlock]);
    setSelectedBlockId(newBlock.id);
  };

  const updateBlock = (id, fieldName, value) => {
    setBlocks(blocks.map(block =>
      block.id === id ? { ...block, data: { ...block.data, [fieldName]: value } } : block
    ));
  };

  const deleteBlock = (id) => {
    const newBlocks = blocks.filter(block => block.id !== id);
    setBlocks(newBlocks);
    if (selectedBlockId === id) setSelectedBlockId(newBlocks[0]?.id || null);
  };

  const moveBlock = (id, direction) => {
    const index = blocks.findIndex(b => b.id === id);
    if ((direction === 'up' && index > 0) || (direction === 'down' && index < blocks.length - 1)) {
      const newBlocks = [...blocks];
      const swapIndex = direction === 'up' ? index - 1 : index + 1;
      [newBlocks[index], newBlocks[swapIndex]] = [newBlocks[swapIndex], newBlocks[index]];
      setBlocks(newBlocks);
    }
  };

  const renderPreview = (block) => {
    const { type, data } = block;
    const Tag = data.heading || 'div';

    switch (type) {
      case 'titolo':
        return React.createElement(Tag, { className: `fw-bold ${data.class}` }, data.text || 'Titolo');
      case 'paragrafo':
        return <p className={data.class}>{data.text || 'Paragrafo'}</p>;
      case 'lista':
        const items = data.items.split('\n').filter(i => i.trim());
        return (
          <ul className={data.class}>
            {items.map((item, i) => <li key={i}>{item}</li>)}
          </ul>
        );
      case 'immagine':
        return data.src ? <img src={data.src} alt={data.alt} className={data.class} /> : <div className="bg-light p-4 text-center text-muted">Immagine</div>;
      default:
        return null;
    }
  };

  const selectedBlock = blocks.find(b => b.id === selectedBlockId);
  const selectedTemplate = selectedBlock ? blockTemplates[selectedBlock.type] : null;

  const exportJSON = () => {
    const json = JSON.stringify(blocks, null, 2);
    const blob = new Blob([json], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'streamfield.json';
    a.click();
  };

  const exportHTML = () => {
    const html = `<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-5">
    ${blocks.map(block => {
      const { type, data } = block;
      const Tag = data.heading || 'div';
      if (type === 'titolo') return `<${Tag} class="${data.class}">${data.text}</${Tag}>`;
      if (type === 'paragrafo') return `<p class="${data.class}">${data.text}</p>`;
      if (type === 'lista') return `<ul class="${data.class}">${data.items.split('\n').map(i => `<li>${i}</li>`).join('')}</ul>`;
      if (type === 'immagine') return `<img src="${data.src}" alt="${data.alt}" class="${data.class}">`;
    }).join('\n    ')}
  </div>
</body>
</html>`;
    const blob = new Blob([html], { type: 'text/html' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'streamfield.html';
    a.click();
  };

  return (
    <div className="min-h-screen bg-gray-50 p-4">
      <div className="max-w-7xl mx-auto">
        <div className="flex justify-between items-center mb-6">
          <h1 className="text-3xl font-bold">Streamfield Builder</h1>
          <div className="flex gap-2">
            <button onClick={exportJSON} className="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
              📥 JSON
            </button>
            <button onClick={exportHTML} className="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
              📥 HTML
            </button>
          </div>
        </div>

        <div className="grid grid-cols-12 gap-4">
          {/* Sidebar Blocchi */}
          <div className="col-span-3 bg-white p-4 rounded-lg shadow">
            <h5 className="font-bold mb-4">Blocchi disponibili</h5>
            <div className="flex flex-col gap-2">
              {Object.entries(blockTemplates).map(([key, template]) => (
                <button
                  key={key}
                  onClick={() => addBlock(key)}
                  className="px-4 py-2 border-2 border-blue-500 text-blue-500 rounded hover:bg-blue-50"
                >
                  + {template.label}
                </button>
              ))}
            </div>
            {blocks.length > 0 && (
              <>
                <h5 className="font-bold mt-6 mb-3">Blocchi aggiunti</h5>
                <div className="flex flex-col gap-2">
                  {blocks.map((block, idx) => (
                    <div
                      key={block.id}
                      onClick={() => setSelectedBlockId(block.id)}
                      className={`p-3 rounded cursor-pointer transition ${
                        selectedBlockId === block.id
                          ? 'bg-blue-500 text-white'
                          : 'bg-gray-100 hover:bg-gray-200'
                      }`}
                    >
                      <div className="text-sm font-semibold">{blockTemplates[block.type].label}</div>
                      <div className="text-xs opacity-75">#{idx + 1}</div>
                    </div>
                  ))}
                </div>
              </>
            )}
          </div>

          {/* Configurazione */}
          <div className="col-span-3 bg-white p-4 rounded-lg shadow">
            <h5 className="font-bold mb-4">Configurazione</h5>
            {selectedBlock && selectedTemplate ? (
              <div>
                <div className="bg-blue-50 p-3 rounded mb-4 flex justify-between items-center">
                  <span className="font-semibold">{selectedTemplate.label}</span>
                  <div className="flex gap-1">
                    <button
                      onClick={() => moveBlock(selectedBlock.id, 'up')}
                      className="p-1 hover:bg-blue-200 rounded"
                      title="Sposta su"
                    >
                      <ChevronUp size={16} />
                    </button>
                    <button
                      onClick={() => moveBlock(selectedBlock.id, 'down')}
                      className="p-1 hover:bg-blue-200 rounded"
                      title="Sposta giù"
                    >
                      <ChevronDown size={16} />
                    </button>
                    <button
                      onClick={() => deleteBlock(selectedBlock.id)}
                      className="p-1 hover:bg-red-200 rounded text-red-600"
                      title="Elimina"
                    >
                      <Trash2 size={16} />
                    </button>
                  </div>
                </div>
                <div className="space-y-3">
                  {selectedTemplate.fields.map(field => (
                    <div key={field.name}>
                      <label className="block text-sm font-medium mb-1">{field.label}</label>
                      {field.type === 'select' ? (
                        <select
                          value={selectedBlock.data[field.name]}
                          onChange={(e) => updateBlock(selectedBlock.id, field.name, e.target.value)}
                          className="w-full px-3 py-2 border border-gray-300 rounded"
                        >
                          {field.options.map(opt => (
                            <option key={opt} value={opt}>{opt.toUpperCase()}</option>
                          ))}
                        </select>
                      ) : field.type === 'textarea' ? (
                        <textarea
                          value={selectedBlock.data[field.name]}
                          onChange={(e) => updateBlock(selectedBlock.id, field.name, e.target.value)}
                          placeholder={field.placeholder}
                          className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                          rows="3"
                        />
                      ) : (
                        <input
                          type="text"
                          value={selectedBlock.data[field.name]}
                          onChange={(e) => updateBlock(selectedBlock.id, field.name, e.target.value)}
                          placeholder={field.placeholder}
                          className="w-full px-3 py-2 border border-gray-300 rounded"
                        />
                      )}
                    </div>
                  ))}
                </div>
              </div>
            ) : (
              <p className="text-gray-500">Seleziona un blocco per configurarlo</p>
            )}
          </div>

          {/* Anteprima */}
          <div className="col-span-6 bg-white p-6 rounded-lg shadow">
            <h5 className="font-bold mb-4">Anteprima</h5>
            <div className="space-y-4 min-h-96 bg-gray-50 p-4 rounded">
              {blocks.length === 0 ? (
                <p className="text-gray-400 text-center">Nessun blocco aggiunto</p>
              ) : (
                blocks.map(block => (
                  <div
                    key={block.id}
                    onClick={() => setSelectedBlockId(block.id)}
                    className={`p-3 rounded cursor-pointer transition border-2 ${
                      selectedBlockId === block.id ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'
                    }`}
                  >
                    {renderPreview(block)}
                  </div>
                ))
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default BlockBuilder;