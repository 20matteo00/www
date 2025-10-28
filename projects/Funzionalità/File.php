<?php
include $_SERVER['DOCUMENT_ROOT'] . '/libraries/pdfparser/alt_autoload.php'; // percorso corretto

use Smalot\PdfParser\Parser;

$docDir = __DIR__ . "/Documenti";
$files = array_diff(scandir($docDir), ['.', '..']);

$search = $_POST['search'] ?? '';
$fileToSearch = $_POST['file'] ?? '';

// Funzione TXT
function searchTxt($path) {
    return file_get_contents($path);
}

// Funzione DOCX
function searchDocx($path) {
    $zip = new ZipArchive;
    $text = '';
    if ($zip->open($path) === TRUE) {
        $xml = $zip->getFromName("word/document.xml");
        $zip->close();
        if ($xml !== false) {
            // Sostituisci i tag Word che rappresentano a capo
            $xml = str_replace(['</w:p>', '<w:br/>', '<w:br />'], "\n", $xml);
            // Togli il resto dei tag XML
            $text = strip_tags($xml);
            // Ripulisci spazi doppi e righe vuote in eccesso
            $text = preg_replace("/\n{2,}/", "\n", $text);
            $text = trim($text);
        }
    }
    return $text;
}


// Funzione PDF usando PdfParser
function searchPdf($path) {
    $parser = new Parser();
    $pdf = $parser->parseFile($path);
    return $pdf->getText();
}

// Evidenzia la parola
function highlight($text, $search) {
    if (!$search) return htmlspecialchars($text);
    $escaped = preg_quote($search, '/');
    return preg_replace("/($escaped)/i", '<mark>$1</mark>', htmlspecialchars($text));
}
?>

<div class="container my-4">
    <h1 class="text-center mb-4">File presenti</h1>

    <form action="" method="post" class="mb-4 d-flex gap-2">
        <input type="text" name="search" class="form-control" placeholder="Cerca parola..."
               value="<?= htmlspecialchars($search) ?>" required>
        <select name="file" class="form-select">
            <option value="">Seleziona un file</option>
            <?php foreach ($files as $file): ?>
                <option value="<?= htmlspecialchars($file) ?>" <?= $file === $fileToSearch ? 'selected' : '' ?>>
                    <?= htmlspecialchars($file) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Cerca</button>
    </form>

    <?php
    if ($search && $fileToSearch) {
        $path = $docDir . '/' . $fileToSearch;
        $ext = strtolower(pathinfo($fileToSearch, PATHINFO_EXTENSION));
        $text = '';

        if ($ext === 'txt') $text = searchTxt($path);
        elseif ($ext === 'docx') $text = searchDocx($path);
        elseif ($ext === 'pdf') $text = searchPdf($path);

        if ($text !== '') {
            $found = stripos($text, $search) !== false;
            echo "<h4>Risultato ricerca in <strong>".htmlspecialchars($fileToSearch)."</strong>:</h4>";
            echo "<div class='border p-3 mb-3' style='max-height:400px; overflow:auto; white-space:pre-wrap; font-family:monospace'>";
            echo highlight($text, $search);
            echo "</div>";

            if ($found) {
                echo "<div class='alert alert-success'>Parola '<strong>".htmlspecialchars($search)."</strong>' trovata ✅</div>";
            } else {
                echo "<div class='alert alert-warning'>Parola '<strong>".htmlspecialchars($search)."</strong>' NON trovata ❌</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Impossibile leggere il file o file vuoto.</div>";
        }
    }
    ?>
</div>
