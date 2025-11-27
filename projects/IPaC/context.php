<?php
/**
 * create_context.php - ENDPOINT PER LIBRERIA TECA H2W
 */

// ============================================================================
// CONFIGURAZIONE
// ============================================================================
$config = [
    'client_id' => 'I2ywPWn7zxQd2FBMTuu1Ogm0Ygoa',
    'client_secret' => 'i34lXT3mtNGvJBjBOyLLXxrYuwIa',
    'iam_url' => 'https://identity-collaudo.cloud.sbn.it/oauth2/token',
    'cap_endpoint' => 'https://cap-apicast-preprod.prod.os01.ocp.cineca.it/capautorizzazionesoggettosistema/api/v1/cap/autorizzazionesoggettosistema/predisponeAutenticazione',
    'sistemaUUID' => '89CB662C0CE44126A31E6FD4D37AC8C4', // BDL [attached_file:1]
    'tenancyUUIDs' => [
        'FB5F15FBE0904D0CAB75298F4D687640', // BIBLIOGRAFICO - Regione Liguria [attached_file:1]
        '2D86DFEBF5614DB6A07F7F667E902F9F', // ARCHIVISTICO [attached_file:1]
        'BB77E9C87564459691116893C645E179', // MULTIMEDIALE [attached_file:1]
        'FA4BED808FB041ED91598ECA263C09D4'  // BIBLIOGRAFICO - Berio [attached_file:1]
    ],
    'enteAderenteUUIDs' => [
        'DFDA683E0AF14D7894596DEFEF908BE7', // Regione Liguria [attached_file:1]
        'DFDA683E0AF14D7894596DEFEF908BE7',
        'DFDA683E0AF14D7894596DEFEF908BE7',
        '17DD47EA7F5C92EAE0630204FE0AF650'  // Biblioteca civica Berio [attached_file:1]
    ],
    'utente' => [
        'labelDescrittivaUtente' => 'TEST',
        'idUtente' => '12345',
        'codiceRuolo' => 'Amministratore'   // da schema_pagina [attached_file:1]
    ]
];

// ============================================================================
// FUNZIONI
// ============================================================================
function getAccessToken($config)
{
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
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        return null;
    }

    $json = json_decode($response, true);
    return $json['access_token'] ?? null;
}

function createSecurityContext($config, $accessToken, $index = 0)
{
    $body_data = [
        'sistemaUUID' => $config['sistemaUUID'],
        'enteAderenteUUID' => $config['enteAderenteUUIDs'][$index],
        'tenancyUUID' => $config['tenancyUUIDs'][$index],
        'labelDescrittivaUtente' => $config['utente']['labelDescrittivaUtente'],
        'idUtente' => $config['utente']['idUtente'],
        'codiceRuolo' => $config['utente']['codiceRuolo']
    ];

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

    return $httpCode === 200 || $httpCode === 201;
}

// ============================================================================
// ENDPOINT JSON
// ============================================================================
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Metodo non consentito, usare POST']);
    exit;
}

// tenancyIndex passato dal frontend (0..3)
$tenancyIndex = isset($_POST['tenancy']) ? (int) $_POST['tenancy'] : 0;
if ($tenancyIndex < 0 || $tenancyIndex > 3) {
    $tenancyIndex = 0;
}

$accessToken = getAccessToken($config);
if (!$accessToken) {
    http_response_code(500);
    echo json_encode(['error' => 'Token IAM fallito']);
    exit;
}

if (!createSecurityContext($config, $accessToken, $tenancyIndex)) {
    http_response_code(500);
    echo json_encode(['error' => 'Contesto sicurezza fallito']);
    exit;
}

// Formato richiesto dalla libreria Teca: access_token, scope, token_type, expires_in [attached_file:2]
echo json_encode([
    'access_token' => $accessToken,
    'scope' => 'am_application_scope default',
    'token_type' => 'Bearer',
    'expires_in' => 3600
]);
