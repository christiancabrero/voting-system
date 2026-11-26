<?php
/**
 * Admin Action Handler
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Storage.php';
require_once __DIR__ . '/../includes/Queue.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'update_settings':
        if (isset($_POST['voting_mode'])) {
            $mode = in_array($_POST['voting_mode'], ['simple', 'advanced'], true) ? $_POST['voting_mode'] : 'simple';
            Storage::setSetting('voting_mode', $mode);
        }
        $isOpen = !empty($_POST['voting_open']) ? '1' : '0';
        Storage::setSetting('voting_open', $isOpen);

        $showClosed = !empty($_POST['closed_show_participants']) ? '1' : '0';
        Storage::setSetting('closed_show_participants', $showClosed);
        break;

    case 'drain_queue':
        Queue::processBatch(250);
        break;

    case 'clear_votes':
        Storage::clearAllVotes();
        break;
}

header('Location: index.php');
exit;
