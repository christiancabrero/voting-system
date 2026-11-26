<?php
/**
 * API: High-Concurrency Vote Ingestion Endpoint
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Storage.php';
require_once __DIR__ . '/../includes/Voter.php';
require_once __DIR__ . '/../includes/Queue.php';
require_once __DIR__ . '/../includes/I18n.php';

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed']);
    exit;
}

$ip = Voter::getClientIp();

// 1. IP Rate Limiting Check
if (!Voter::checkRateLimit($ip)) {
    http_response_code(429);
    echo json_encode([
        'success' => false,
        'error' => I18n::t('rate_limit_exceeded')
    ]);
    exit;
}

// 2. Parse input JSON or POST parameters
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$optionId = intval($input['option_id'] ?? 0);
$csrfToken = $input['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

// 3. Get Device ID and CSRF Verification
$deviceId = Voter::getDeviceId();
$userAgent = Voter::getUserAgent();

if (!Voter::verifyCsrfToken($csrfToken, $deviceId)) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'error' => I18n::t('invalid_csrf')
    ]);
    exit;
}

if ($optionId <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => I18n::t('invalid_contestant')
    ]);
    exit;
}

// 4. Ingest into Queue
$result = Queue::enqueueVote($optionId, $deviceId, $ip, $userAgent);

if ($result['success']) {
    // Check if it was immediately drained into confirmed votes
    $votedCount = Storage::getDeviceVoteCount($deviceId);
    $mode = Storage::getVotingMode();
    $limit = ($mode === 'simple') ? 1 : 1;
    $remaining = max(0, $limit - $votedCount);

    echo json_encode([
        'success' => true,
        'status' => $result['status'],
        'queue_id' => $result['queue_id'],
        'message' => I18n::t($result['message_key']),
        'remaining_votes' => $remaining,
        'voted_count' => $votedCount
    ]);
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'status' => $result['status'],
        'error' => I18n::t($result['message_key'])
    ]);
}
