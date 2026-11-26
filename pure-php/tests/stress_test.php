<?php
/**
 * Concurrency & Stress Testing Suite
 * Validates high-concurrency ingestion and race condition immunity (100 to 1,000 requests)
 *
 * Usage:
 *   php tests/stress_test.php [concurrency=200] [target_url=http://localhost:10038/voting-system-pure-php]
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Storage.php';
require_once __DIR__ . '/../includes/Queue.php';
require_once __DIR__ . '/../includes/Voter.php';

$totalRequests = isset($argv[1]) ? intval($argv[1]) : 200;
$baseUrl = isset($argv[2]) ? rtrim($argv[2], '/') : 'http://localhost:10038/voting-system-pure-php';

echo "=====================================================\n";
echo "  VOTING SYSTEM CONCURRENCY & STRESS TEST BENCHMARK\n";
echo "=====================================================\n";
echo "Base URL:       $baseUrl\n";
echo "Concurrency:    $totalRequests simultaneous requests\n\n";

// Reset DB for clean test run
Storage::clearAllVotes();
Storage::setSetting('voting_mode', 'simple');
Storage::setSetting('voting_open', '1');

echo "Starting TEST 1: High Concurrency Distinct Ingestion ($totalRequests votes)...\n";

$startTime = microtime(true);

$mh = curl_multi_init();
$handles = [];

for ($i = 0; $i < $totalRequests; $i++) {
    $ch = curl_init($baseUrl . '/api/vote.php');
    $deviceId = hash('sha256', "stress_device_unique_$i");
    $csrfToken = Voter::getCsrfToken($deviceId);
    $optionId = ($i % 8) + 1; // Distribute across 8 contestants

    $payload = json_encode([
        'option_id' => $optionId,
        'csrf_token' => $csrfToken
    ]);

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "X-CSRF-Token: $csrfToken",
            "Cookie: vs_device_id=$deviceId",
            "X-Forwarded-For: 10." . intval($i / 65000) . "." . intval(($i % 65000) / 255) . "." . ($i % 255)
        ],
        CURLOPT_TIMEOUT => 15,
    ]);

    curl_multi_add_handle($mh, $ch);
    $handles[$i] = $ch;
}

// Execute parallel requests simultaneously
$running = null;
do {
    curl_multi_exec($mh, $running);
    curl_multi_select($mh, 0.02);
} while ($running > 0);

$endTime = microtime(true);
$duration = max(0.0001, $endTime - $startTime);

// Analyze responses
$successCount = 0;
$failCount = 0;

foreach ($handles as $ch) {
    $response = curl_multi_getcontent($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $data = json_decode($response, true);

    if ($httpCode === 200 && !empty($data['success'])) {
        $successCount++;
    } else {
        $failCount++;
    }
    curl_multi_remove_handle($mh, $ch);
}
curl_multi_close($mh);

$rps = round($totalRequests / $duration, 2);

echo "TEST 1 Completed in " . round($duration, 3) . "s\n";
echo "Throughput:      $rps requests/sec\n";
echo "Ingested Votes:  $successCount / $totalRequests (" . round(($successCount / $totalRequests) * 100, 1) . "%)\n";
echo "Failed / Limit:  $failCount\n\n";

// Drain any remaining items in queue
Queue::processBatch($totalRequests);
$finalConfirmed = Storage::getTotalVotesCount();
echo "Database Verified Confirmed Votes: $finalConfirmed / $totalRequests\n\n";

// -----------------------------------------------------------------
echo "Starting TEST 2: RACE CONDITION ATTACK TEST\n";
echo "Sending 50 SIMULTANEOUS votes from THE EXACT SAME DEVICE at millisecond 0...\n";

$singleDeviceId = hash('sha256', "single_race_condition_attacker_device");
$raceCsrf = Voter::getCsrfToken($singleDeviceId);
$raceRequests = 50;
$mh2 = curl_multi_init();
$handles2 = [];

$startTime2 = microtime(true);
for ($i = 0; $i < $raceRequests; $i++) {
    $ch = curl_init($baseUrl . '/api/vote.php');
    $payload = json_encode([
        'option_id' => 1,
        'csrf_token' => $raceCsrf
    ]);

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "X-CSRF-Token: $raceCsrf",
            "Cookie: vs_device_id=$singleDeviceId",
            "X-Forwarded-For: 192.168.1.100"
        ],
        CURLOPT_TIMEOUT => 10,
    ]);

    curl_multi_add_handle($mh2, $ch);
    $handles2[$i] = $ch;
}

$running2 = null;
do {
    curl_multi_exec($mh2, $running2);
    curl_multi_select($mh2, 0.02);
} while ($running2 > 0);

$raceSuccess = 0;
$raceRejected = 0;

foreach ($handles2 as $ch) {
    $response = curl_multi_getcontent($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $data = json_decode($response, true);

    if ($httpCode === 200 && !empty($data['success'])) {
        $raceSuccess++;
    } else {
        $raceRejected++;
    }
    curl_multi_remove_handle($mh2, $ch);
}
curl_multi_close($mh2);

Queue::processBatch(100);
$actualRecordedForDevice = Storage::getDeviceVoteCount($singleDeviceId);

echo "Race Attack Results:\n";
echo "Accepted by Ingestion:  $raceSuccess\n";
echo "Rejected / Deduped:     $raceRejected\n";
echo "ACTUAL VOTES RECORDED:  $actualRecordedForDevice (Strictly 1 allowed in Simple Mode)\n";

if ($actualRecordedForDevice === 1) {
    echo "\n>>> RACE CONDITION TEST PASSED! 100% IDEMPOTENT & THREAD-SAFE! <<<\n";
} else {
    echo "\n>>> RACE CONDITION TEST FAILED! Expected 1 vote, got $actualRecordedForDevice <<<\n";
}
echo "=====================================================\n";
