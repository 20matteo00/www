const endpoint = "create_context.php";
let teca;

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
        dossierId: "",
        qualifica: "ASSOCIAZIONE"
    },
    "LINK_RESOURCE": {
        conservativeId: "7BB7F34626A24BA4BC230D7636CF03AF",
        conservativeIdType: "IPAC",
        logicalId: "1",
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

async function openTeca() {
    if (!teca) teca = new Teca(endpoint);

    const selectedViewer = document.getElementById("viewer-select").value;
    const selectedMode = document.getElementById("mode-select").value;
    const params = predefinedParams[selectedViewer] || {};

    const tecaBox = document.getElementById("teca-box");

    // Reset classe fullpage quando non serve
    tecaBox.classList.remove("fullpage-container");

    let tagConfig = {
        id: "teca-box",
        fade: false,
        width: "100%",
        height: "1000"
    };

    const config = {
        mode: selectedMode,
        tagEmbedded: tagConfig,
        viewer: selectedViewer,
        parameters: params
    };

    console.log("Opening Teca with config:", config);

    teca.open(config);
}
