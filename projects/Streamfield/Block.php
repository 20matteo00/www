<?php

class Block
{
    private $type;
    private $i = 0;

    public function __construct($type)
    {
        $this->type = $type;
        $this->header($this->type, $this->i);
        $this->render($this->type, $this->i);
    }

    private function header($type, $i)
    {
        echo '
        <div class="d-flex justify-content-between align-items-center bg-light p-2 border mb-2 rounded">
            <span class="fw-bold text-uppercase">' . htmlspecialchars($type) . '</span>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary" title="Sposta su">↑</button>
                <button class="btn btn-sm btn-outline-secondary" title="Sposta giù">↓</button>
                <button class="btn btn-sm btn-outline-danger" title="Elimina">🗑</button>
            </div>
        </div>

        ';
        return;
    }

    private function render($type, $i)
    {
        switch ($type) {
            case 'titolo':
                echo $this->renderList('titolo_heading', 'heading', 'form-control mb-2');
                echo $this->renderText('titolo_text', '', 'Inserisci il titolo', 'form-control mb-2');
                echo $this->renderText('titolo_class', '', 'Inserisci le classi', 'form-control mb-2');
                break;

            case 'paragrafo':
                echo $this->renderText('paragrafo_text', '', 'Inserisci il paragrafo', 'form-control mb-2');
                echo $this->renderText('paragrafo_class', '', 'Inserisci le classi', 'form-control mb-2');
                break;

            default:
                echo '<p>Tipo di blocco non riconosciuto.</p>';
        }
    }

    private function renderText($name, $value = '', $placeholder = '', $class = '')
    {
        return "<input 
                    type='text'
                    name='$name'
                    id='$name'
                    value='$value'
                    placeholder='$placeholder'
                    class='$class'
                    >";
    }

    private function renderList($name, $type = '', $class = '')
    {
        $list = [];
        $listOptions = '';

        switch ($type) {
            case 'heading':
                $list = [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                ];
                break;
            default:
                break;
        }

        if (!empty($list)) {
            foreach ($list as $key => $label) {
                $listOptions .= "<option value='$key'>$label</option>";
            }
        }
        
        return "<select 
                    name='$name'
                    id='$name'
                    class='$class'
                    default='h1'
                >
                    $listOptions
                </select>";

    }

}

?>