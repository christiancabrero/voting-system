<?php
/**
 * API: System Statistics & Live Results
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Storage.php';
require_once __DIR__ . '/../includes/Queue.php';

$results = Storage::getResults();
$queueMetrics = Queue::getMetrics();
$totalVotes = Storage::getTotalVotesCount();

echo json_encode([
    'success' => true,
    'total_votes' => $totalVotes,
    'queue' => $queueMetrics,
    'results' => $results
]);
