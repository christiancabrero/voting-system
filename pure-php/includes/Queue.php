<?php
/**
 * High-Concurrency Queue Manager (Producer-Consumer Pattern)
 * Designed to handle 100 to 1,000+ simultaneous requests without lock contention
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Storage.php';

class Queue {

    /**
     * Fast non-blocking ingestion of a vote into the queue
     */
    public static function enqueueVote(int $optionId, string $deviceId, string $ip, string $userAgent): array {
        $pdo = Database::getConnection();

        // 1. Fast Pre-Check: Is voting open?
        if (!Storage::isVotingOpen()) {
            return [
                'success' => false,
                'status' => 'rejected',
                'message_key' => 'voting_closed'
            ];
        }

        // 2. Fast Pre-Check: Verify contestant exists
        $option = Storage::getOptionById($optionId);
        if (!$option) {
            return [
                'success' => false,
                'status' => 'rejected',
                'message_key' => 'invalid_contestant'
            ];
        }

        // 3. Fast In-Memory / Deduplication Check:
        // Has this device already reached the vote limit in confirmed votes?
        $mode = Storage::getVotingMode();
        $existingCount = Storage::getDeviceVoteCount($deviceId);
        if ($mode === 'simple' && $existingCount >= 1) {
            return [
                'success' => false,
                'status' => 'rejected',
                'message_key' => 'vote_rejected_duplicate'
            ];
        }

        // Check if there is already a pending item in queue for this device
        $pendingCheck = $pdo->prepare("SELECT COUNT(*) FROM queue WHERE device_id = ? AND status = 'pending'");
        $pendingCheck->execute([$deviceId]);
        if ($mode === 'simple' && ((int) $pendingCheck->fetchColumn()) > 0) {
            return [
                'success' => false,
                'status' => 'rejected',
                'message_key' => 'vote_rejected_duplicate'
            ];
        }

        // 4. Ingest into queue (Ultra-fast append-only operation)
        $stmt = $pdo->prepare("
            INSERT INTO queue (option_id, device_id, ip, user_agent, status, created_at)
            VALUES (?, ?, ?, ?, 'pending', CURRENT_TIMESTAMP)
        ");
        $stmt->execute([$optionId, $deviceId, $ip, $userAgent]);
        $queueId = (int) $pdo->lastInsertId();

        // 5. If AUTO_DRAIN is enabled (no CLI daemon required), process immediate micro-batch
        if (QUEUE_AUTO_DRAIN) {
            self::processBatch(25);
        }

        return [
            'success' => true,
            'status' => 'queued',
            'queue_id' => $queueId,
            'message_key' => 'vote_queued'
        ];
    }

    /**
     * Batch Worker Processor
     * Processes pending votes in serialized atomic ACID transactions
     */
    public static function processBatch(int $batchSize = QUEUE_BATCH_SIZE): array {
        $pdo = Database::getConnection();

        $processed = 0;
        $rejected = 0;

        // Atomically fetch pending queue items
        $stmt = $pdo->prepare("SELECT * FROM queue WHERE status = 'pending' ORDER BY id ASC LIMIT ?");
        $stmt->bindValue(1, $batchSize, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();

        if (empty($items)) {
            return ['processed' => 0, 'rejected' => 0, 'pending' => 0];
        }

        $mode = Storage::getVotingMode();
        $isOpen = Storage::isVotingOpen();

        $pdo->beginTransaction();
        try {
            $insertVote = $pdo->prepare("
                INSERT INTO votes (option_id, device_id, ip, user_agent, created_at)
                VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)
            ");
            $updateOption = $pdo->prepare("UPDATE options SET votes_count = votes_count + 1 WHERE id = ?");
            $updateQueue = $pdo->prepare("UPDATE queue SET status = ?, error_message = ? WHERE id = ?");

            $checkVotes = $pdo->prepare("SELECT COUNT(*) FROM votes WHERE device_id = ?");

            foreach ($items as $item) {
                $qId = (int) $item['id'];
                $optId = (int) $item['option_id'];
                $devId = $item['device_id'];

                if (!$isOpen) {
                    $updateQueue->execute(['rejected', 'Voting closed', $qId]);
                    $rejected++;
                    continue;
                }

                // Strict limit validation in transaction
                $checkVotes->execute([$devId]);
                $currentVotes = (int) $checkVotes->fetchColumn();

                if ($mode === 'simple' && $currentVotes >= 1) {
                    $updateQueue->execute(['rejected', 'Device vote limit reached', $qId]);
                    $rejected++;
                    continue;
                }

                // Record vote
                $insertVote->execute([$optId, $devId, $item['ip'], $item['user_agent']]);
                $updateOption->execute([$optId]);
                $updateQueue->execute(['processed', null, $qId]);
                $processed++;
            }

            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            error_log("Queue batch error: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }

        $pendingRemaining = (int) $pdo->query("SELECT COUNT(*) FROM queue WHERE status = 'pending'")->fetchColumn();

        return [
            'processed' => $processed,
            'rejected' => $rejected,
            'pending' => $pendingRemaining
        ];
    }

    /** Queue status metrics */
    public static function getMetrics(): array {
        $pdo = Database::getConnection();
        $pending = (int) $pdo->query("SELECT COUNT(*) FROM queue WHERE status = 'pending'")->fetchColumn();
        $processed = (int) $pdo->query("SELECT COUNT(*) FROM queue WHERE status = 'processed'")->fetchColumn();
        $rejected = (int) $pdo->query("SELECT COUNT(*) FROM queue WHERE status = 'rejected'")->fetchColumn();
        return [
            'pending' => $pending,
            'processed' => $processed,
            'rejected' => $rejected,
            'total' => $pending + $processed + $rejected
        ];
    }
}
