<?php
/**
 * Voter Identification, Security, Rate Limiting & Anti-Fraud
 */

require_once __DIR__ . '/Database.php';

class Voter {
    private static ?string $deviceId = null;

    public static function getClientIp(): string {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return filter_var($_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP) ?: '0.0.0.0';
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $first = trim($parts[0]);
            if (filter_var($first, FILTER_VALIDATE_IP)) {
                return $first;
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public static function getUserAgent(): string {
        return substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 255);
    }

    /**
     * Get or set durable HttpOnly device identifier cookie
     */
    public static function getDeviceId(): string {
        if (self::$deviceId !== null) {
            return self::$deviceId;
        }

        if (!empty($_COOKIE['vs_device_id'])) {
            $candidate = preg_replace('/[^a-f0-9]/', '', $_COOKIE['vs_device_id']);
            if (strlen($candidate) === 64) {
                self::$deviceId = $candidate;
                return $candidate;
            }
        }

        // Generate high-entropy 64-char fingerprint device ID
        $entropy = random_bytes(32) . self::getClientIp() . self::getUserAgent() . microtime(true);
        $newId = hash('sha256', $entropy);

        if (!headers_sent()) {
            setcookie('vs_device_id', $newId, [
                'expires' => time() + (COOKIE_EXPIRE_DAYS * 86400),
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }
        $_COOKIE['vs_device_id'] = $newId;
        self::$deviceId = $newId;
        return $newId;
    }

    /**
     * Stateless, High-Concurrency CSRF Token tied to Device ID & HMAC
     * Eliminates PHP session file locking contention under 1,000+ concurrent requests
     */
    public static function getCsrfToken(string $deviceId): string {
        return hash_hmac('sha256', $deviceId . date('YmdH'), CSRF_SECRET);
    }

    public static function verifyCsrfToken(?string $token, string $deviceId): bool {
        if (empty($token) || empty($deviceId)) return false;
        $validCurrent = hash_hmac('sha256', $deviceId . date('YmdH'), CSRF_SECRET);
        $validPrev = hash_hmac('sha256', $deviceId . date('YmdH', time() - 3600), CSRF_SECRET);
        return hash_equals($validCurrent, $token) || hash_equals($validPrev, $token);
    }

    /**
     * IP-based rate limiting to protect against DDoS & spam
     */
    public static function checkRateLimit(string $ip): bool {
        if (!RATE_LIMIT_ENABLED) return true;

        $pdo = Database::getConnection();
        $now = time();
        $windowSize = 60; // 1 minute window

        $stmt = $pdo->prepare("SELECT window_start, request_count FROM rate_limits WHERE ip = ?");
        $stmt->execute([$ip]);
        $row = $stmt->fetch();

        if (!$row) {
            $insert = $pdo->prepare("INSERT INTO rate_limits (ip, window_start, request_count) VALUES (?, ?, 1)");
            $insert->execute([$ip, $now]);
            return true;
        }

        if ($now - $row['window_start'] > $windowSize) {
            // Reset window
            $update = $pdo->prepare("UPDATE rate_limits SET window_start = ?, request_count = 1 WHERE ip = ?");
            $update->execute([$now, $ip]);
            return true;
        }

        if ($row['request_count'] >= RATE_LIMIT_MAX_PER_MINUTE) {
            return false; // Limit exceeded
        }

        $update = $pdo->prepare("UPDATE rate_limits SET request_count = request_count + 1 WHERE ip = ?");
        $update->execute([$ip]);
        return true;
    }
}
