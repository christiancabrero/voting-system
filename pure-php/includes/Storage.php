<?php
/**
 * Storage and Settings Management for Pure PHP Voting System
 */

require_once __DIR__ . '/Database.php';

class Storage {
    private static array $cachedSettings = [];

    public static function getSetting(string $key, string $default = ''): string {
        if (isset(self::$cachedSettings[$key])) {
            return self::$cachedSettings[$key];
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();

        if ($val === false) {
            self::$cachedSettings[$key] = $default;
            return $default;
        }

        self::$cachedSettings[$key] = (string) $val;
        return (string) $val;
    }

    public static function setSetting(string $key, string $value): void {
        $pdo = Database::getConnection();
        if (DB_DRIVER === 'sqlite') {
            $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON CONFLICT(setting_key) DO UPDATE SET setting_value = excluded.setting_value");
        } else {
            $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        }
        $stmt->execute([$key, $value]);
        self::$cachedSettings[$key] = $value;
    }

    public static function isVotingOpen(): bool {
        return self::getSetting('voting_open', '1') === '1';
    }

    public static function getVotingMode(): string {
        return self::getSetting('voting_mode', 'simple');
    }

    public static function getClosedShowParticipants(): bool {
        return self::getSetting('closed_show_participants', '1') === '1';
    }

    public static function getClosedBlurParticipants(): bool {
        return self::getSetting('closed_blur_participants', '0') === '1';
    }

    /** Get all contestants/options */
    public static function getOptions(): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM options ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    public static function getOptionById(int $id): ?array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM options WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Number of votes cast by device in total */
    public static function getDeviceVoteCount(string $deviceId): int {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM votes WHERE device_id = ?");
        $stmt->execute([$deviceId]);
        return (int) $stmt->fetchColumn();
    }

    /** Array of option IDs voted by device */
    public static function getDeviceVotedOptionIds(string $deviceId): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT DISTINCT option_id FROM votes WHERE device_id = ?");
        $stmt->execute([$deviceId]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    /** Total votes cast in entire system */
    public static function getTotalVotesCount(): int {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT COUNT(*) FROM votes");
        return (int) $stmt->fetchColumn();
    }

    /** Results per contestant */
    public static function getResults(): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("
            SELECT o.id, o.title, o.type, o.category, o.photo_url, COUNT(v.id) as vote_count
            FROM options o
            LEFT JOIN votes v ON o.id = v.option_id
            GROUP BY o.id, o.title, o.type, o.category, o.photo_url
            ORDER BY vote_count DESC, o.id ASC
        ");
        return $stmt->fetchAll();
    }

    public static function clearAllVotes(): void {
        $pdo = Database::getConnection();
        $pdo->exec("DELETE FROM votes; DELETE FROM queue;");
        $pdo->exec("UPDATE options SET votes_count = 0;");
    }
}
