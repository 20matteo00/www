const endpoint = "context.php";
let teca;

const predefinedParams = {
    "CREATE_RESOURCE": {
        qualifica: "ASSOCIAZIONE",
        conservativeId: "IPAC-ENTE-7BB7F34626A24BA4BC230D7636CF03AF",
        conservativeIdType: "IPAC"
    },
    "LINK_RESOURCE_FULL": {
        conservativeId: "IPAC-ENTE-7BB7F34626A24BA4BC230D7636CF03AF",
        conservativeIdType: "IPAC",
        logicalId: "1",
        dossierId: "",
        qualifica: "ASSOCIAZIONE"
    },
    "LINK_RESOURCE": {
        conservativeId: "IPAC-ENTE-7BB7F34626A24BA4BC230D7636CF03AF",
        conservativeIdType: "IPAC",
        logicalId: "1",
        dossierId: "",
        qualifica: "ASSOCIAZIONE"
    },
    "RESOURCE_DETAIL": { uuid: "62" },
    "OPEN_VIEWER": { uuid: "62", uuidManifest: "1" },
    "LINK_CHILD_RESOURCE_LIST": { parentDigitalResource: "62" },
    "MEDIA_PICKER": { uuid: "62" }
};

async function openTeca() {

    // 1) Inizializza TECA una volta sola
    if (!teca) {
        teca = new Teca(endpoint, true);
        console.log(teca);
        // 2) Aggancia ascolto eventi TECA
        teca.setEventListener((evt) => {
            console.log("Evento TECA:", evt);

            // 👉 Token aggiornato dal backend TECA
            if (evt.cod === "REFRESH_TOKEN") {
                const newToken = evt.data.token;
                console.log("TOKEN AGGIORNATO:", newToken);

                // Se devi reiniettare il token in TECA:
                teca.refreshAccessToken(newToken);
            }

            // 👉 Callback creazione risorsa
            if (evt.cod === "CREAZIONE_RISORSA_DIGITALE") {
                console.log("UUID Risorsa Digitale:", evt.data.uuid);
            }
        });
    }

    // 3) Parametri per viewer
    const selectedViewer = document.getElementById("viewer-select").value;
    const selectedMode = document.getElementById("mode-select").value;
    const params = predefinedParams[selectedViewer] || {};

    // 4) Config DOM
    const tagConfig = {
        id: "teca-box",
        fade: false,
        width: "100%",
        height: "1000"
    };

    // 5) Config viewer
    const config = {
        mode: selectedMode,
        tagEmbedded: tagConfig,
        viewer: selectedViewer,
        parameters: params
    };
    console.log(config);

    // 6) Apri TECA
    teca.open(config);
}
