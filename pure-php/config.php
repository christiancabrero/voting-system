<?php
/**
 * Voting System - Pure PHP High Concurrency Edition
 * Configuration File
 */

// Application root path
define('VS_ROOT', __DIR__);

// Database Configuration
// Options: 'sqlite' (recommended for zero-config high performance) or 'mysql'
define('DB_DRIVER', 'sqlite');

// SQLite settings
define('DB_SQLITE_PATH', VS_ROOT . '/data/voting_system.sqlite');

// MySQL settings (if DB_DRIVER === 'mysql')
define('DB_MYSQL_HOST', '127.0.0.1');
define('DB_MYSQL_PORT', 3306);
define('DB_MYSQL_NAME', 'voting_system');
define('DB_MYSQL_USER', 'root');
define('DB_MYSQL_PASS', '');

// Queue Configuration
define('QUEUE_DRIVER', 'sqlite'); // 'sqlite', 'redis', or 'file'
define('QUEUE_AUTO_DRAIN', true);  // Automatically process micro-batches on request if daemon is not running
define('QUEUE_BATCH_SIZE', 100);   // Number of votes processed per worker batch

// Security & Anti-Fraud
define('RATE_LIMIT_ENABLED', true);
define('RATE_LIMIT_MAX_PER_MINUTE', 60); // Max voting attempts per IP per minute
define('COOKIE_EXPIRE_DAYS', 365);
define('CSRF_SECRET', 'vs_pure_php_secret_key_change_me_in_production_2026');

// Session configuration
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}
