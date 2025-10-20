<?php
// === Inspira Auto-Deploy Script for STAGE Branch ===
// File: /home/u296731902/domains/inspira-zentrum.de/public_html/stage/deploy-stage.php

$secret = 'inspira_@2025_autodeploy!'; // same shared secret as main deploy
$logFile = '/home/u296731902/domains/inspira-zentrum.de/public_html/stage/logs/deploy-stage.log';

function logLine($message) {
    global $logFile;
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . $message . "\n", FILE_APPEND);
}

// --- Validate request ---
$input = file_get_contents('php://input');
$headers = getallheaders();
$signature = $headers['X-Hub-Signature-256'] ?? '';

logLine('=== Incoming webhook (STAGE) ===');
logLine('Headers: ' . json_encode($headers));
logLine('Raw input length: ' . strlen($input));

if (!$signature) {
    http_response_code(400);
    logLine('❌ Missing signature header');
    echo "No signature header";
    exit;
}

$hash = 'sha256=' . hash_hmac('sha256', $input, $secret);
if (!hash_equals($hash, $signature)) {
    http_response_code(403);
    logLine('❌ Invalid signature');
    echo "Invalid signature";
    exit;
}

// --- Decode payload to check branch ---
$payload = json_decode($input, true);
$branch = $payload['ref'] ?? '';

if ($branch !== 'refs/heads/stage') {
    logLine("⚠️ Ignored push to non-stage branch: $branch");
    http_response_code(200);
    echo "Ignored non-stage branch push.";
    exit;
}

// --- Perform Git deployment ---
chdir('/home/u296731902/domains/inspira-zentrum.de/public_html/stage');
logLine('✅ Valid signature – starting STAGE deploy');

exec('git fetch origin stage 2>&1', $out1, $ret1);
exec('git reset --hard origin/stage 2>&1', $out2, $ret2);

logLine("Fetch exit code: $ret1");
logLine("Reset exit code: $ret2");
logLine("Output:\n" . implode("\n", array_merge($out1, $out2)));

http_response_code(200);
logLine('✅ Stage deployment completed successfully');
echo "Stage deploy completed.";
?>
