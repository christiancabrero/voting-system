<?php
/**
 * CLI Queue Worker Daemon / Cron Runner
 *
 * Usage:
 *   php worker.php --daemon    # Runs continuously in background
 *   php worker.php --once      # Drains pending queue once and exits
 *   php worker.php --status    # Prints current queue metrics
 */

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.\n");
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/Storage.php';
require_once __DIR__ . '/includes/Queue.php';

$options = getopt('', ['daemon', 'once', 'status', 'batch:']);
$batchSize = isset($options['batch']) ? intval($options['batch']) : QUEUE_BATCH_SIZE;

if (isset($options['status'])) {
    $m = Queue::getMetrics();
    echo sprintf("Queue Status: Pending=%d | Processed=%d | Rejected=%d | Total=%d\n",
        $m['pending'], $m['processed'], $m['rejected'], $m['total']);
    exit(0);
}

if (isset($options['once'])) {
    echo "Running single batch drain (batch size: $batchSize)...\n";
    $result = Queue::processBatch($batchSize);
    echo sprintf("Result: Processed=%d | Rejected=%d | Remaining Pending=%d\n",
        $result['processed'] ?? 0, $result['rejected'] ?? 0, $result['pending'] ?? 0);
    exit(0);
}

echo "Starting Voting System Queue Worker Daemon (Press Ctrl+C to stop)...\n";
$running = true;

if (function_exists('pcntl_signal')) {
    pcntl_signal(SIGTERM, function() use (&$running) { $running = false; });
    pcntl_signal(SIGINT, function() use (&$running) { $running = false; });
}

while ($running) {
    if (function_exists('pcntl_signal_dispatch')) {
        pcntl_signal_dispatch();
    }

    $result = Queue::processBatch($batchSize);

    if (($result['processed'] ?? 0) > 0 || ($result['rejected'] ?? 0) > 0) {
        echo sprintf("[%s] Processed: %d | Rejected: %d | Pending: %d\n",
            date('Y-m-d H:i:s'),
            $result['processed'],
            $result['rejected'],
            $result['pending']
        );
    } else {
        // No work pending, sleep 100ms
        usleep(100000);
    }
}

echo "Worker stopped gracefully.\n";
