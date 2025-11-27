<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Mini Teca</title>
</head>

<body>

    <h2>Teca Mini Test</h2>


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

    <script src="https://ispc-preprod.prod.os01.ocp.cineca.it/teca-library"></script>

    <script>
        const endpoint = "create_context.php";
        let teca;

        // Parametri predefiniti per ciascun viewer
        const predefinedParams = {
            "CREATE_RESOURCE": {
                qualifica: "ASSOCIAZIONE",
                conservativeId: "7BB7F34626A24BA4BC230D7636CF03AF",
                conservativeIdType: "IPAC"
            },
            "LINK_RESOURCE_FULL": {
                conservativeId: "7BB7F34626A24BA4BC230D7636CF03AF",
                conservativeIdType: "IPAC",
                logicalId: "1",
                managementId: "",
                dossierId: "",
                qualifica: "ASSOCIAZIONE"
            },
            "LINK_RESOURCE": {
                conservativeId: "7BB7F34626A24BA4BC230D7636CF03AF",
                conservativeIdType: "IPAC",
                logicalId: "1",
                managementId: "",
                dossierId: "",
                qualifica: "ASSOCIAZIONE"
            },
            "RESOURCE_DETAIL": {
                uuid: "62"
            },
            "OPEN_VIEWER": {
                uuid: "62",
                uuidManifest: "1"
            },
            "LINK_CHILD_RESOURCE_LIST": {
                parentDigitalResource: "62"
            },
            "MEDIA_PICKER": {
                uuid: "62"
            }
        };


        const modeSelect = document.getElementById("mode-select");
        const viewerSelect = document.getElementById("viewer-select");

        async function openTeca() {
            if (!teca) teca = new Teca(endpoint);

            const selectedViewer = viewerSelect.value;
            const selectedMode = modeSelect.value;
            const params = predefinedParams[selectedViewer] || {};

            const config = {
                mode: selectedMode,
                tagEmbedded: {
                    id: "teca-box",
                },
                viewer: selectedViewer,
                parameters: params
            };

            console.log("Opening Teca with config:", config);

            teca.open(config);
        }
    </script>

</body>

</html>