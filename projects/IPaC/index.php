<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Teca</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://ispc-preprod.prod.os01.ocp.cineca.it/teca-library"></script>
    <script src="script.js"></script>
</head>

<body>

    <h1>Test Creazione Widget - Teca</h1>

    <label for="mode-select">Scegli Modalità:</label>
    <select id="mode-select">
        <option value="EMBEDDED">Embedded</option>
        <option value="FULLPAGE">Full Page</option>
        <option value="POPUP">Popup</option>
    </select>

    <label for="viewer-select">Scegli Viewer:</label>
    <select id="viewer-select">
        <option value="CREATE_RESOURCE">Creazione Risorsa</option>
        <option value="LINK_RESOURCE_FULL">Elenco Risorse</option>
        <option value="LINK_RESOURCE">Cerca Risorsa</option>
        <option value="RESOURCE_DETAIL">Dettaglio Risorsa</option>
        <option value="OPEN_VIEWER">Visualizza Risorsa</option>
        <option value="LINK_CHILD_RESOURCE_LIST">Elenco Risorse Figlie</option>
        <option value="MEDIA_PICKER">Elenco Media Risorsa</option>
    </select>

    <button onclick="openTeca()">Apri Teca</button>

    <div id="teca-box"></div>

</body>

</html>