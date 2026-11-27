<?php
/**
 * Pure PHP Standalone Voting System - Main Frontend
 * High-Concurrency & Cache-Friendly Architecture
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/Storage.php';
require_once __DIR__ . '/includes/I18n.php';
require_once __DIR__ . '/includes/Voter.php';

$options = Storage::getOptions();
$isOpen = Storage::isVotingOpen();
$mode = Storage::getVotingMode();
$showParticipants = Storage::getClosedShowParticipants();
$lang = I18n::getLanguage();

// Ensure device ID cookie is set on first GET request
$deviceId = Voter::getDeviceId();
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(I18n::t('title')); ?></title>
    <meta name="csrf-token" content="<?php echo htmlspecialchars(Voter::getCsrfToken($deviceId)); ?>">
    <link rel="stylesheet" href="assets/css/style.css?v=2.0">
</head>
<body>

<div class="vs-wrapper">
    <!-- Top Bar: Languages & Admin -->
    <div class="vs-top-bar">
        <div class="vs-lang-switcher">
            <a href="?lang=en" class="vs-lang-btn <?php echo $lang === 'en' ? 'active' : ''; ?>">EN</a>
            <a href="?lang=es" class="vs-lang-btn <?php echo $lang === 'es' ? 'active' : ''; ?>">ES</a>
            <a href="?lang=ca" class="vs-lang-btn <?php echo $lang === 'ca' ? 'active' : ''; ?>">CA</a>
            <a href="?lang=pt" class="vs-lang-btn <?php echo $lang === 'pt' ? 'active' : ''; ?>">PT</a>
            <a href="?lang=fr" class="vs-lang-btn <?php echo $lang === 'fr' ? 'active' : ''; ?>">FR</a>
            <a href="?lang=ja" class="vs-lang-btn <?php echo $lang === 'ja' ? 'active' : ''; ?>">JP</a>
            <a href="?lang=zh" class="vs-lang-btn <?php echo $lang === 'zh' ? 'active' : ''; ?>">CN</a>
        </div>
        <div>
            <span class="vs-card-category" style="margin-bottom: 0;">
                <?php echo htmlspecialchars($mode === 'simple' ? I18n::t('simple_mode_badge') : I18n::t('advanced_mode_badge')); ?>
            </span>
            <a href="admin/" class="vs-admin-link" style="margin-left: 12px;"><?php echo htmlspecialchars(I18n::t('admin_panel')); ?> &rarr;</a>
        </div>
    </div>

    <!-- Main Header -->
    <header class="vs-header">
        <h1><?php echo htmlspecialchars(I18n::t('title')); ?></h1>
        <p><?php echo htmlspecialchars(I18n::t('subtitle')); ?></p>
    </header>

    <!-- Dynamic Status Banner (hydrated via JS or SSR) -->
    <div id="vs-status-banner" class="vs-banner" style="display: <?php echo (!$isOpen && $showParticipants) ? 'block' : 'none'; ?>;">
        <?php if (!$isOpen && $showParticipants): ?>
            <div class="vs-banner-closed"><?php echo htmlspecialchars(I18n::t('closed_notice')); ?></div>
        <?php endif; ?>
    </div>

    <!-- Voter Status & Remaining Count Box -->
    <div id="vs-remaining-box" class="vs-remaining-box">
        <p><?php echo I18n::t('votes_available', '<strong>0</strong>', '<strong>1</strong>'); ?></p>
        <p class="vs-remaining-count"><?php echo I18n::t('votes_remaining', '1'); ?></p>
    </div>

    <!-- Contestants Grid -->
    <?php 
    $blurClass = (!$isOpen && $showParticipants && Storage::getClosedBlurParticipants()) ? ' vs-closed-blurred' : '';
    if ($isOpen || $showParticipants): 
    ?>
        <div class="vs-grid<?php echo $blurClass; ?>">
            <?php foreach ($options as $opt): ?>
                <div class="vs-card" data-id="<?php echo (int) $opt['id']; ?>">
                    <div class="vs-card-content">
                        <div class="vs-img-wrapper">
                            <img src="<?php echo htmlspecialchars($opt['photo_url']); ?>" alt="<?php echo htmlspecialchars($opt['title']); ?>" loading="lazy">
                        </div>
                        <h3><?php echo htmlspecialchars($opt['title']); ?></h3>
                        <?php if (!empty($opt['type'])): ?>
                            <p class="vs-card-type"><?php echo htmlspecialchars($opt['type']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($opt['category'])): ?>
                            <span class="vs-card-category"><?php echo htmlspecialchars($opt['category']); ?></span>
                        <?php endif; ?>
                    </div>
                    <button class="vs-btn-vote" data-id="<?php echo (int) $opt['id']; ?>" <?php echo !$isOpen ? 'disabled' : ''; ?>>
                        <?php echo htmlspecialchars(!$isOpen ? I18n::t('closed_btn') : I18n::t('vote_btn')); ?>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="vs-banner vs-banner-closed">
            <?php echo htmlspecialchars(I18n::t('voting_closed')); ?>
        </div>
    <?php endif; ?>
</div>

<script src="assets/js/app.js?v=2.0"></script>
</body>
</html>
