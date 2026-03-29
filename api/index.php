<?php
/**
 *  Name: Yaygara Telemetry
 *  Author: Mert S. Kaplan, mail@mertskaplan.com
 *  Licence: GNU GPLv3
 *  Source: https://github.com/mertskaplan/yaygara-telemetry
 **/

// CONFIGURATION (CORS)
// Define all allowed domains that can send telemetry data to this API.
// Include 'http://localhost:8080' or similar for local testing if needed. (Just for testing, do not use in production)
$allowedOrigins = [
    'https://yaygara.mertskaplan.com'
];
// =========================================================================

$requestOrigin = isset($_SERVER['HTTP_ORIGIN']) ? rtrim($_SERVER['HTTP_ORIGIN'], '/') : '';

// Check if request origin is in the allowed list
if (in_array($requestOrigin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $requestOrigin");
} else if (!empty($allowedOrigins)) {
    // Fallback: If no direct match (e.g. server-to-server or unauthorized), default to primary. 
    // Unauthorized browsers will still block the request.
    header("Access-Control-Allow-Origin: {$allowedOrigins[0]}");
}

header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Preflight request handling
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Allow only POST requests (and OPTIONS already handled)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

// Origin check
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

// For local testing, we might need to be flexible, but the production requirement is strict.
// We'll also check Referer if Origin is missing.
$referer = $_SERVER['HTTP_REFERER'] ?? '';

if (strpos($origin, 'yaygara.mertskaplan.com') === false && strpos($referer, 'yaygara.mertskaplan.com') === false) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden: Origin not allowed']);
    exit;
}

// Get raw POST data
$rawData = file_get_contents('php://input');
$decodedData = json_decode($rawData, true);

if (!$decodedData) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

// Security: No DB, write to file in NDJSON format
$storageFile = __DIR__ . '/../telemetry.json';

// Append data as a single line with a newline
// Using lock to prevent concurrency issues
$jsonData = json_encode($decodedData) . "\n";
file_put_contents($storageFile, $jsonData, FILE_APPEND | LOCK_EX);

http_response_code(202);
echo json_encode(['message' => 'Telemetry data accepted']);
