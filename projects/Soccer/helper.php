<?php

class Helper
{

    public function getMenu()
    {
        return [
            [
                'label' => 'Gruppi',
                'url' => 'index.php?page=gruppi'
            ],
            [
                'label' => 'Squadre',
                'url' => 'index.php?page=squadre'
            ],
        ];
    }

    public function getFileGruppiPath()
    {
        return 'data/gruppi.json';
    }

    public function getGruppi()
    {
        $filename = $this->getFileGruppiPath();
        return $this->getFile($filename);
    }


    private function getFile($filename)
    {
        // Controlla se il file esiste
        if (file_exists($filename)) {
            // Legge il contenuto
            $jsonContent = file_get_contents($filename);
            // Decodifica in array
            $data = json_decode($jsonContent, true);

            // Se il JSON non è valido, inizializza array vuoto
            if (!is_array($data)) {
                $data = [];
            }
        } else {
            // File non esiste, crea array vuoto e salva
            $data = [];
            file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
        }

        return $data;
    }

    // Salva array su JSON
    public function saveJson(string $file, array $data): void
    {
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
    }

    // Aggiungi nuovo elemento
    public function addItem(array &$array, array $item): void
    {
        $array[] = $item;
    }

    // Modifica elemento dato l'indice
    public function editItem(array &$array, int $index, array $item): void
    {
        if (isset($array[$index])) {
            $array[$index] = $item;
        }
    }

    // Elimina elemento dato l'indice
    public function deleteItem(array &$array, int $index): void
    {
        if (isset($array[$index])) {
            unset($array[$index]);
            $array = array_values($array); // ri-indicizza
        }
    }

    // Carica un elemento per modificarlo
    public function loadItem(array $array, int $index): ?array
    {
        return $array[$index] ?? null;
    }


}

?>