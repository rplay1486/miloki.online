<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

/*
 * ClearKey HEX -> Base64 JSON
 *
 * Ejemplo:
 * /api/results.php?keyid=001122...&key=aabbcc...
 */

$keyid = isset($_GET['keyid']) ? trim($_GET['keyid']) : '';
$key   = isset($_GET['key'])   ? trim($_GET['key'])   : '';

if ($keyid === '' || $key === '') {
    http_response_code(400);

    echo json_encode([
        'error' => 'Faltan parámetros',
        'usage' => '/api/results.php?keyid=KID_HEX&key=KEY_HEX'
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

    exit;
}

/*
 * Solo permitir HEX.
 */
if (!ctype_xdigit($keyid) || !ctype_xdigit($key)) {
    http_response_code(400);

    echo json_encode([
        'error' => 'keyid y key deben contener solamente caracteres HEX'
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

    exit;
}

/*
 * ClearKey utiliza KID y KEY de 16 bytes normalmente
 * = 32 caracteres HEX.
 */
if (strlen($keyid) !== 32 || strlen($key) !== 32) {
    http_response_code(400);

    echo json_encode([
        'error' => 'keyid y key deben tener exactamente 32 caracteres HEX (16 bytes)'
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

    exit;
}

/*
 * HEX -> bytes -> Base64URL sin padding.
 */
function hexToBase64Url($hex)
{
    $binary = hex2bin($hex);

    if ($binary === false) {
        return false;
    }

    return rtrim(
        strtr(
            base64_encode($binary),
            '+/',
            '-_'
        ),
        '='
    );
}

$kidBase64 = hexToBase64Url($keyid);
$keyBase64 = hexToBase64Url($key);

if ($kidBase64 === false || $keyBase64 === false) {
    http_response_code(400);

    echo json_encode([
        'error' => 'No se pudo convertir HEX a Base64URL'
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

    exit;
}

/*
 * Respuesta ClearKey.
 */
$response = [
    'keys' => [
        [
            'kty' => 'oct',
            'k'   => $keyBase64,
            'kid' => $kidBase64
        ]
    ],
    'type' => 'temporary'
];

echo json_encode(
    $response,
    JSON_UNESCAPED_SLASHES
);
