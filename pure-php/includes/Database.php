<?php
/**
 * Database Engine with High-Concurrency SQLite WAL & MySQL PDO Support
 */

require_once __DIR__ . '/../config.php';

class Database {
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        if (DB_DRIVER === 'sqlite') {
            $dir = dirname(DB_SQLITE_PATH);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $dsn = 'sqlite:' . DB_SQLITE_PATH;
            self::$pdo = new PDO($dsn, null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 5, // Wait up to 5s if locked
            ]);

            // High-concurrency performance pragmas
            self::$pdo->exec("PRAGMA journal_mode = WAL;");
            self::$pdo->exec("PRAGMA busy_timeout = 5000;");
            self::$pdo->exec("PRAGMA synchronous = NORMAL;");
            self::$pdo->exec("PRAGMA cache_size = 10000;");
            self::$pdo->exec("PRAGMA temp_store = MEMORY;");
        } else {
            $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', DB_MYSQL_HOST, DB_MYSQL_PORT, DB_MYSQL_NAME);
            self::$pdo = new PDO($dsn, DB_MYSQL_USER, DB_MYSQL_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_PERSISTENT => true,
            ]);
        }

        self::ensureSchema(self::$pdo);
        return self::$pdo;
    }

    private static function ensureSchema(PDO $pdo): void {
        static $initialized = false;
        if ($initialized) return;

        if (DB_DRIVER === 'sqlite') {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS options (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title TEXT NOT NULL,
                    type TEXT,
                    category TEXT,
                    photo_url TEXT,
                    votes_count INTEGER DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS votes (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    option_id INTEGER NOT NULL,
                    device_id TEXT NOT NULL,
                    ip TEXT,
                    user_agent TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );
                CREATE INDEX IF NOT EXISTS idx_votes_device ON votes (device_id);
                CREATE INDEX IF NOT EXISTS idx_votes_option ON votes (option_id);

                CREATE TABLE IF NOT EXISTS queue (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    option_id INTEGER NOT NULL,
                    device_id TEXT NOT NULL,
                    ip TEXT,
                    user_agent TEXT,
                    status TEXT DEFAULT 'pending',
                    attempt_count INTEGER DEFAULT 0,
                    error_message TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );
                CREATE INDEX IF NOT EXISTS idx_queue_status ON queue (status);
                CREATE INDEX IF NOT EXISTS idx_queue_device ON queue (device_id);

                CREATE TABLE IF NOT EXISTS settings (
                    setting_key TEXT PRIMARY KEY,
                    setting_value TEXT
                );

                CREATE TABLE IF NOT EXISTS rate_limits (
                    ip TEXT PRIMARY KEY,
                    window_start INTEGER,
                    request_count INTEGER
                );
            ");
        } else {
            // MySQL schema
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS options (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    type VARCHAR(100),
                    category VARCHAR(100),
                    photo_url VARCHAR(500),
                    votes_count INT DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

                CREATE TABLE IF NOT EXISTS votes (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    option_id INT NOT NULL,
                    device_id VARCHAR(255) NOT NULL,
                    ip VARCHAR(45),
                    user_agent TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_device (device_id),
                    INDEX idx_option (option_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

                CREATE TABLE IF NOT EXISTS queue (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    option_id INT NOT NULL,
                    device_id VARCHAR(255) NOT NULL,
                    ip VARCHAR(45),
                    user_agent TEXT,
                    status VARCHAR(20) DEFAULT 'pending',
                    attempt_count INT DEFAULT 0,
                    error_message TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_status (status),
                    INDEX idx_device (device_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

                CREATE TABLE IF NOT EXISTS settings (
                    setting_key VARCHAR(100) PRIMARY KEY,
                    setting_value TEXT
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

                CREATE TABLE IF NOT EXISTS rate_limits (
                    ip VARCHAR(45) PRIMARY KEY,
                    window_start INT,
                    request_count INT
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }

        // Seed default settings if not exists
        $stmt = $pdo->query("SELECT COUNT(*) FROM settings");
        if ($stmt->fetchColumn() == 0) {
            $defaultSettings = [
                'voting_mode' => 'simple',
                'voting_open' => '1',
                'closed_show_participants' => '1',
                'closed_blur_participants' => '0',
                'require_device_cookie' => '1',
            ];
            $insert = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
            foreach ($defaultSettings as $k => $v) {
                $insert->execute([$k, $v]);
            }
        }

        // Seed sample demo contestants if empty
        $stmt = $pdo->query("SELECT COUNT(*) FROM options");
        if ($stmt->fetchColumn() == 0) {
            $demoOptions = [
                ['Bart', 'Family', 'Simpson', 'assets/images/bart.png'],
                ['Homer', 'Family', 'Simpson', 'assets/images/homer.png'],
                ['Lenny', 'Others', 'Simpson', 'assets/images/lenny.png'],
                ['Marge', 'Family', 'Simpson', 'assets/images/marge.png'],
            ];
            $insert = $pdo->prepare("INSERT INTO options (title, type, category, photo_url) VALUES (?, ?, ?, ?)");
            foreach ($demoOptions as $opt) {
                $insert->execute($opt);
            }
        }

        $initialized = true;
    }
}
