<?php
/**
 * Pure PHP Voting System - Admin Control Panel
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Storage.php';
require_once __DIR__ . '/../includes/Queue.php';
require_once __DIR__ . '/../includes/I18n.php';

$mode = Storage::getVotingMode();
$isOpen = Storage::isVotingOpen();
$showParticipants = Storage::getClosedShowParticipants();
$queueMetrics = Queue::getMetrics();
$results = Storage::getResults();
$totalVotes = Storage::getTotalVotesCount();
$lang = I18n::getLanguage();

$pdo = Database::getConnection();
$recentVotes = $pdo->query("
    SELECT v.id, o.title as contestant_title, v.device_id, v.ip, v.created_at
    FROM votes v
    JOIN options o ON v.option_id = o.id
    ORDER BY v.id DESC LIMIT 15
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting System - Admin Control Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .vs-admin-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        @media(max-width: 800px) { .vs-admin-grid { grid-template-columns: 1fr; } }
        .vs-stat-card { background: #fff; padding: 20px; border-radius: 10px; border: 1px solid var(--border-color); }
        .vs-stat-num { font-size: 28px; font-weight: 700; color: var(--primary-color); }
        .vs-table { width: 100%; border-collapse: collapse; margin-top: 15px; background: #fff; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color); }
        .vs-table th, .vs-table td { padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 14px; }
        .vs-table th { background: #f9fafb; font-weight: 600; color: #4b5563; }
        .vs-btn-sm { padding: 8px 14px; border-radius: 6px; border: none; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; }
        .vs-btn-primary { background: var(--primary-color); color: #fff; }
        .vs-btn-danger { background: #dc2626; color: #fff; }
        .vs-form-group { margin-bottom: 18px; }
        .vs-form-group label { display: block; font-weight: 600; margin-bottom: 6px; }
        .vs-switch-box { display: flex; gap: 12px; }
        .vs-mode-btn { flex: 1; padding: 14px; border: 2px solid var(--border-color); border-radius: 8px; background: #fff; cursor: pointer; text-align: center; }
        .vs-mode-btn.active { border-color: var(--primary-color); background: #fdf2f2; font-weight: 700; }
    </style>
</head>
<body>

<div class="vs-wrapper">
    <div class="vs-top-bar">
        <a href="../" class="vs-admin-link">&larr; Back to Public Voting Page</a>
        <div>
            <span style="font-weight: 600; color: #4b5563;">Standalone Pure PHP Edition v2.0</span>
        </div>
    </div>

    <header class="vs-header" style="text-align: left; margin-bottom: 25px;">
        <h1>Voting System Administration</h1>
        <p>Control competition modes, monitor high-concurrency ingestion queue, and view real-time audit logs.</p>
    </header>

    <!-- Metrics Cards -->
    <div class="vs-admin-grid">
        <div class="vs-stat-card">
            <h3>Total Votes Confirmed</h3>
            <div class="vs-stat-num"><?php echo number_format($totalVotes); ?></div>
            <p style="font-size: 12px; color: #6b7280; margin-top: 4px;">Stored in ACID transaction log</p>
        </div>
        <div class="vs-stat-card">
            <h3>Queue Pending / Processed</h3>
            <div class="vs-stat-num">
                <?php echo $queueMetrics['pending']; ?>
                <span style="font-size: 16px; color: #16a34a; font-weight: 500;">/ <?php echo $queueMetrics['processed']; ?></span>
            </div>
            <p style="font-size: 12px; color: #6b7280; margin-top: 4px;">Rejected / Duplicate attempts: <?php echo $queueMetrics['rejected']; ?></p>
        </div>
        <div class="vs-stat-card">
            <h3>Competition State</h3>
            <div class="vs-stat-num" style="color: <?php echo $isOpen ? '#16a34a' : '#dc2626'; ?>;">
                <?php echo $isOpen ? 'OPEN' : 'CLOSED'; ?>
            </div>
            <p style="font-size: 12px; color: #6b7280; margin-top: 4px;">Mode: <strong><?php echo ucfirst($mode); ?></strong></p>
        </div>
    </div>

    <!-- Quick Settings Controls -->
    <div class="vs-stat-card" style="margin-bottom: 30px;">
        <h3 style="margin-bottom: 16px;">Competition Settings</h3>
        <form method="POST" action="action.php">
            <input type="hidden" name="action" value="update_settings">

            <div class="vs-form-group">
                <label>Voting Mode (Simple vs Advanced):</label>
                <div class="vs-switch-box">
                    <label class="vs-mode-btn <?php echo $mode === 'simple' ? 'active' : ''; ?>">
                        <input type="radio" name="voting_mode" value="simple" <?php echo $mode === 'simple' ? 'checked' : ''; ?> style="display:none;" onchange="this.form.submit()">
                        <strong>Simple Mode</strong>
                        <div style="font-size: 12px; color: #6b7280;">1 vote per device total (nullifies category limits)</div>
                    </label>
                    <label class="vs-mode-btn <?php echo $mode === 'advanced' ? 'active' : ''; ?>">
                        <input type="radio" name="voting_mode" value="advanced" <?php echo $mode === 'advanced' ? 'checked' : ''; ?> style="display:none;" onchange="this.form.submit()">
                        <strong>Advanced Mode</strong>
                        <div style="font-size: 12px; color: #6b7280;">Category and type independent voting quotas</div>
                    </label>
                </div>
            </div>

            <div style="display: flex; gap: 20px; align-items: center; margin-top: 15px;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="voting_open" value="1" <?php echo $isOpen ? 'checked' : ''; ?> onchange="this.form.submit()">
                    <span>Voting is Open</span>
                </label>

                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="closed_show_participants" value="1" <?php echo $showParticipants ? 'checked' : ''; ?> onchange="this.form.submit()">
                    <span>Show contestants when voting is closed</span>
                </label>
            </div>
        </form>

        <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid var(--border-color); display: flex; gap: 12px;">
            <form method="POST" action="action.php" style="display:inline;">
                <input type="hidden" name="action" value="drain_queue">
                <button type="submit" class="vs-btn-sm vs-btn-primary">Drain Queue Worker Batch</button>
            </form>
            <form method="POST" action="action.php" onsubmit="return confirm('Reset all votes and clear queue?');" style="display:inline;">
                <input type="hidden" name="action" value="clear_votes">
                <button type="submit" class="vs-btn-sm vs-btn-danger">Clear All Votes & Reset</button>
            </form>
        </div>
    </div>

    <!-- Live Results Table -->
    <div class="vs-stat-card" style="margin-bottom: 30px;">
        <h3>Contestant Results & Ranking</h3>
        <table class="vs-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Contestant</th>
                    <th>Category</th>
                    <th>Type</th>
                    <th>Total Votes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $i => $row): ?>
                    <tr>
                        <td><strong><?php echo $i + 1; ?></strong></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><span class="vs-card-category" style="margin-bottom: 0;"><?php echo htmlspecialchars($row['category']); ?></span></td>
                        <td><?php echo htmlspecialchars($row['type']); ?></td>
                        <td><strong><?php echo (int) $row['vote_count']; ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Live Audit Log -->
    <div class="vs-stat-card">
        <h3>Recent Audit Log</h3>
        <table class="vs-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Contestant</th>
                    <th>Device ID (Truncated)</th>
                    <th>Client IP</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentVotes)): ?>
                    <tr><td colspan="5" style="text-align: center; color: #9ca3af;">No votes recorded yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($recentVotes as $v): ?>
                        <tr>
                            <td>#<?php echo (int) $v['id']; ?></td>
                            <td><?php echo htmlspecialchars($v['contestant_title']); ?></td>
                            <td><code><?php echo htmlspecialchars(substr($v['device_id'], 0, 16)); ?>...</code></td>
                            <td><?php echo htmlspecialchars($v['ip']); ?></td>
                            <td><?php echo htmlspecialchars($v['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
