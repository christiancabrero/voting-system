<?php
/**
 * API: Dynamic Voter State Hydration Endpoint
 * Non-cached, personalized response per voter device
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Storage.php';
require_once __DIR__ . '/../includes/Voter.php';
require_once __DIR__ . '/../includes/I18n.php';

$deviceId = Voter::getDeviceId();
$mode = Storage::getVotingMode();
$isOpen = Storage::isVotingOpen();
$showParticipants = Storage::getClosedShowParticipants();

$votedCount = Storage::getDeviceVoteCount($deviceId);
$effectiveLimit = ($mode === 'simple') ? 1 : 1;
$remainingVotes = max(0, $effectiveLimit - $votedCount);
$votedOptionIds = Storage::getDeviceVotedOptionIds($deviceId);

echo json_encode([
    'success' => true,
    'data' => [
        'csrf_token' => Voter::getCsrfToken($deviceId),
        'device_id_truncated' => substr($deviceId, 0, 8) . '...',
        'is_open' => $isOpen,
        'voting_mode' => $mode,
        'closed_show_participants' => $showParticipants,
        'effective_limit' => $effectiveLimit,
        'voted_count' => $votedCount,
        'remaining_votes' => $remainingVotes,
        'voted_option_ids' => $votedOptionIds,
        'lang' => I18n::getLanguage(),
        'i18n' => I18n::getJsDictionary(),
    ]
]);
