<?php
/**
 * create_context.php - Creare contesto di sicurezza in I.PaC
 */

// ============================================================================
// CONFIGURAZIONE
// ============================================================================

$config = [
    'client_id' => 'I2ywPWn7zxQd2FBMTuu1Ogm0Ygoa',
    'client_secret' => 'i34lXT3mtNGvJBjBOyLLXxrYuwIa',
    'iam_url' => 'https://identity-collaudo.cloud.sbn.it/oauth2/token',
    'cap_endpoint' => 'https://cap-apicast-preprod.prod.os01.ocp.cineca.it/capautorizzazionesoggettosistema/api/v1/cap/autorizzazionesoggettosistema/predisponeAutenticazione',
    'sistemaUUID' => '89CB662C0CE44126A31E6FD4D37AC8C4',
    'tenancyUUIDs' => [
        'FB5F15FBE0904D0CAB75298F4D687640',
        '2D86DFEBF5614DB6A07F7F667E902F9F',
        'BB77E9C87564459691116893C645E179',
        'FA4BED808FB041ED91598ECA263C09D4'
    ],
    'enteAderenteUUIDs' => [
        'DFDA683E0AF14D7894596DEFEF908BE7',
        'DFDA683E0AF14D7894596DEFEF908BE7',
        'DFDA683E0AF14D7894596DEFEF908BE7',
        '17DD47EA7F5C92EAE0630204FE0AF650'
    ],
    'utente' => [
        'labelDescrittivaUtente' => 'TEST', // cambia con l'utente reale
        'idUtente' => '12345',                   // cambia con l'ID reale
        'codiceRuolo' => 'Amministratore'
    ]
];

header('Content-Type: text/plain; charset=utf-8');

// ============================================================================
// FUNZIONE: Ottieni token da IAM
// ============================================================================

function getAccessToken($config)
{
    echo "=== STEP 1: OTTENERE TOKEN ===\n";

    $postData = http_build_query([
        'grant_type' => 'client_credentials',
        'client_id' => $config['client_id'],
        'client_secret' => $config['client_secret'],
    ]);

    $ch = curl_init($config['iam_url']);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 15,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        echo "❌ CURL Error: $curlError\n";
        return null;
    }

    $json = json_decode($response, true);
    if (!isset($json['access_token'])) {
        echo "❌ Token non ricevuto! Response: $response\n";
        return null;
    }

    echo "✅ Token ottenuto: {$json['access_token']}\n";
    return $json['access_token'];
}

// ============================================================================
// FUNZIONE: Crea contesto di sicurezza
// ============================================================================

function createSecurityContext($config, $accessToken, $index = 0)
{
    echo "\n=== STEP 2: CREAZIONE CONTESTO DI SICUREZZA ===\n";

    $body_data = [
        'sistemaUUID' => $config['sistemaUUID'],
        'enteAderenteUUID' => $config['enteAderenteUUIDs'][$index],
        'tenancyUUID' => $config['tenancyUUIDs'][$index],
        'labelDescrittivaUtente' => $config['utente']['labelDescrittivaUtente'],
        'idUtente' => $config['utente']['idUtente'],
        'codiceRuolo' => $config['utente']['codiceRuolo']
    ];

    echo "Request body:\n" . json_encode($body_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";

    $ch = curl_init($config['cap_endpoint']);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($body_data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $accessToken",
            "Content-Type: application/json"
        ],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 15,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "HTTP Code: $httpCode\n";
    echo "Response:\n$response\n";

    if ($httpCode === 200 || $httpCode === 201) {
        echo "✅ Contesto creato con successo!\n";
    } else {
        echo "⚠️ Errore nella creazione del contesto\n";
    }
}

// ============================================================================
// ESECUZIONE
// ============================================================================

$accessToken = getAccessToken($config);
if (!$accessToken) exit(1);

// Se vuoi creare contesti per tutti gli UUID disponibili
foreach ($config['tenancyUUIDs'] as $i => $tenancy) {
    createSecurityContext($config, $accessToken, $i);
}
