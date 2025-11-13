<?php
/**
 * create_context.php - Creare contesto di sicurezza in I.PaC
 */

// ============================================================================
// CONFIGURAZIONE
// ============================================================================

$CLIENT_ID = 'I2ywPWn7zxQd2FBMTuu1Ogm0Ygoa';
$CLIENT_SECRET = 'i34lXT3mtNGvJBjBOyLLXxrYuwIa';
$IAM_URL = 'https://identity-collaudo.cloud.sbn.it/t/coll.ispc.it/oauth2/token';
$CAP_ENDPOINT = 'https://cap-apicast.prod.os01.ocp.cineca.it/api/v1/cap/autorizzazionesoggettosistema/predisponeAutenticazione';
$UUID_SISTEMA = '89CB662C0CE44126A31E6FD4D37AC8C4';
$UUID_TENANCY = ['FB5F15FBE0904D0CAB75298F4D687640', '2D86DFEBF5614DB6A07F7F667E902F9F', 'BB77E9C87564459691116893C645E179', 'FA4BED808FB041ED91598ECA263C09D4'];
$UUID_ENTE = ['DFDA683E0AF14D7894596DEFEF908BE7', 'DFDA683E0AF14D7894596DEFEF908BE7', 'DFDA683E0AF14D7894596DEFEF908BE7', '17DD47EA7F5C92EAE0630204FE0AF650'];

header('Content-Type: text/plain; charset=utf-8');

// ============================================================================
// FUNZIONE: Ottieni token da IAM
// ============================================================================

/**
 * Richiede un token OAuth2 all'IAM usando le credenziali client
 * 
 * @param string $iam_url URL dell'endpoint token
 * @param string $client_id ID del client
 * @param string $client_secret Secret del client
 * @return string|null Token di accesso o null se errore
 */
function getAccessToken($iam_url, $client_id, $client_secret)
{
    echo "=== STEP 1: OTTENERE TOKEN ===\n\n";

    // Prepara i dati da inviare (form-encoded)
    $postData = http_build_query([
        'grant_type' => 'client_credentials',
        'client_id' => $client_id,
        'client_secret' => $client_secret,
    ]);

    // Inizializza connessione curl
    $ch = curl_init($iam_url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 15,
    ]);

    // Esegui richiesta
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    // Stampa debug
    echo "URL: $iam_url\n";
    echo "HTTP Code: $httpCode\n";

    // Verifica errori curl
    if ($curlError) {
        echo "CURL Error: $curlError\n";
        return null;
    }

    // Decodifica risposta JSON
    $json = json_decode($response, true);
    if (!isset($json['access_token'])) {
        echo "❌ Token non ricevuto!\n";
        echo "Response: $response\n";
        return null;
    }

    // Token ricevuto con successo
    $token = $json['access_token'];
    echo "✅ Token ottenuto: $token\n";

    return $token;
}

// ============================================================================
// FUNZIONE: Crea contesto di sicurezza
// ============================================================================

/**
 * Crea un contesto di sicurezza presso il CAP di I.PaC
 * Questo contesto permette di effettuare chiamate successive ai servizi
 * 
 * @param string $cap_endpoint URL dell'endpoint CAP
 * @param string $access_token Token di accesso OAuth2
 * @param array $body_data Dati del contesto (sistema, ente, tenancy, ecc)
 * @return array Contiene 'httpCode' e 'response' della richiesta
 */
function createSecurityContext($cap_endpoint, $access_token, $body_data)
{
    echo "\n=== STEP 2: CREAZIONE CONTESTO DI SICUREZZA ===\n\n";

    // Stampa parametri
    echo "URL: $cap_endpoint\n";
    echo "Body:\n";
    echo json_encode($body_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n";

    // Inizializza connessione curl
    $ch2 = curl_init($cap_endpoint);
    curl_setopt_array($ch2, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($body_data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $access_token,
            'Content-Type: application/json',
        ],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 15,
    ]);

    // Esegui richiesta
    $response2 = curl_exec($ch2);
    $httpCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
    curl_close($ch2);

    // Separa header da body della risposta
    $header_size = curl_getinfo($ch2, CURLINFO_HEADER_SIZE);
    $headers = substr($response2, 0, $header_size);
    $body = substr($response2, $header_size);

    return [
        'httpCode' => $httpCode2,
        'headers' => $headers,
        'body' => $body,
    ];
}

// ============================================================================
// FUNZIONE: Stampa risultati
// ============================================================================

/**
 * Stampa i risultati della creazione del contesto di sicurezza
 * 
 * @param array $result Array con httpCode, headers, body
 */
function printResults($result)
{
    $httpCode = $result['httpCode'];
    $headers = $result['headers'];
    $body = $result['body'];

    // Stampa codice HTTP
    echo "HTTP Code: $httpCode\n\n";

    // Stampa header
    echo "=== HEADERS ===\n";
    echo $headers . "\n";

    // Stampa body
    echo "=== BODY ===\n";
    echo $body . "\n";

    // Verifica successo
    if ($httpCode === 200 || $httpCode === 201) {
        echo "\n✅ CONTESTO CREATO CON SUCCESSO!\n";
    } else {
        echo "\n⚠️ Errore HTTP $httpCode\n";
    }
}

// ============================================================================
// ESECUZIONE PRINCIPALE
// ============================================================================

// Step 1: Ottieni token
$access_token = getAccessToken($IAM_URL, $CLIENT_ID, $CLIENT_SECRET);

// Verifica se token è stato ottenuto
if ($access_token === null) {
    echo "❌ Impossibile continuare senza token\n";
    exit(1);
}

// Step 2: Prepara dati per il contesto di sicurezza
$body_data = [
    'sistemaUUID' => $UUID_SISTEMA,
    'enteAderenteUUID' => $UUID_ENTE[0],
    'tenancyUUID' => $UUID_TENANCY[0],
    'labelDescrittivaUtente' => 'prova',
    'idUtente' => $CLIENT_ID,
    'codiceRuolo' => 'Amministratore',
];

// Step 3: Crea contesto di sicurezza
$result = createSecurityContext($CAP_ENDPOINT, $access_token, $body_data);

// Step 4: Stampa risultati
printResults($result);

?>