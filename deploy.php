<?php
/**
 * === Inspira Auto-Deploy Script (MAIN-DESK / Live) ===
 * File: /home/u296731902/domains/inspira-zentrum.de/public_html/deploy.php
 */

$secret = 'inspira_@2025_autodeploy!'; // must match GitHub webhook secret
$logFile = __DIR__ . '/logs/deploy-main.log';

/**
 * Log helper
 */
function logLine($message)
{
    global $logFile;
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
}

// --- Read incoming data ---
$input = file_get_contents('php://input');
$headers = getallheaders();
$signature = $headers['X-Hub-Signature-256'] ?? '';

logLine("=== Incoming webhook (MAIN-DESK) ===");
logLine("Headers: " . json_encode($headers));
logLine("Payload length: " . strlen($input));

// --- Validate signature ---
if (!$signature) {
    http_response_code(400);
    logLine("❌ No signature header");
    echo "No signature header";
    exit;
}

$expected = 'sha256=' . hash_hmac('sha256', $input, $secret);
if (!hash_equals($expected, $signature)) {
    http_response_code(403);
    logLine("❌ Invalid signature");
    echo "Invalid signature";
    exit;
}

// --- Decode payload ---
$payload = json_decode($input, true);
$branch = $payload['ref'] ?? '';

logLine("Payload branch: " . $branch);

// --- Only deploy main-desk branch ---
if ($branch !== 'refs/heads/main-desk') {
    logLine("⚠️ Ignored push to non-main-desk branch: " . $branch);
    http_response_code(200);
    echo "Ignored push to non-main branch";
    exit;
}

// --- Run Git commands ---
chdir(__DIR__);
logLine("✅ Valid signature – deploying MAIN-DESK...");

exec('git fetch origin main-desk 2>&1', $fetchOutput, $fetchCode);
exec('git reset --hard origin/main-desk 2>&1', $resetOutput, $resetCode);

logLine("Fetch exit code: $fetchCode");
logLine("Reset exit code: $resetCode");
logLine("Output:\n" . implode("\n", array_merge($fetchOutput, $resetOutput)));

http_response_code(200);
logLine("✅ MAIN-DESK deploy completed successfully");
echo "MAIN-DESK deploy completed successfully";
?>
