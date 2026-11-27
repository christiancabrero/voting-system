<?php
/**
 * Internationalization (i18n) Engine for Pure PHP Voting System
 * Easily translatable through separate language files in the /languages directory:
 * en.php, es.php, ca.php, pt.php, fr.php, ja.php, zh.php
 */

class I18n {
    private static ?string $currentLanguage = null;
    private static array $loadedTranslations = [];
    private static array $fallbackTranslations = [];

    public static function getSupportedLanguages(): array {
        return ['en', 'es', 'ca', 'pt', 'fr', 'ja', 'zh'];
    }

    private static function normalizeCode(string $code): string {
        $code = strtolower(substr(trim($code), 0, 2));
        if ($code === 'jp') {
            return 'ja';
        }
        if ($code === 'cn') {
            return 'zh';
        }
        if (in_array($code, self::getSupportedLanguages(), true)) {
            return $code;
        }
        return '';
    }

    /** Detect language based on query string, cookie, HTTP headers, or default to English (en). */
    public static function getLanguage(): string {
        if (self::$currentLanguage !== null) {
            return self::$currentLanguage;
        }

        // 1. Explicit GET param
        if (!empty($_GET['lang'])) {
            $code = self::normalizeCode($_GET['lang']);
            if ($code !== '') {
                if (!headers_sent()) {
                    setcookie('vs_lang', $code, time() + 30 * 86400, '/');
                }
                self::$currentLanguage = $code;
                return $code;
            }
        }

        // 2. Cookie
        if (!empty($_COOKIE['vs_lang'])) {
            $code = self::normalizeCode($_COOKIE['vs_lang']);
            if ($code !== '') {
                self::$currentLanguage = $code;
                return $code;
            }
        }

        // 3. Browser Accept-Language header
        if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $parts = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            foreach ($parts as $part) {
                $sub = explode(';', $part)[0];
                $code = self::normalizeCode($sub);
                if ($code !== '') {
                    self::$currentLanguage = $code;
                    return $code;
                }
            }
        }

        // 4. Default primary reference language: English
        self::$currentLanguage = 'en';
        return 'en';
    }

    public static function setLanguage(string $lang): void {
        $code = self::normalizeCode($lang);
        if ($code !== '') {
            self::$currentLanguage = $code;
        }
    }

    private static function loadLanguage(string $lang): array {
        $file = __DIR__ . '/../languages/' . $lang . '.php';
        if (file_exists($file)) {
            $arr = include $file;
            if (is_array($arr)) {
                return $arr;
            }
        }
        return [];
    }

    /** Translate key into active language with fallback to EN. */
    public static function t(string $key, ...$args): string {
        $lang = self::getLanguage();

        if (empty(self::$fallbackTranslations)) {
            self::$fallbackTranslations = self::loadLanguage('en');
        }

        if (!isset(self::$loadedTranslations[$lang])) {
            self::$loadedTranslations[$lang] = self::loadLanguage($lang);
        }

        $text = self::$loadedTranslations[$lang][$key] 
             ?? self::$fallbackTranslations[$key] 
             ?? $key;

        if (!empty($args)) {
            return sprintf($text, ...$args);
        }
        return $text;
    }

    public static function e(string $key, ...$args): void {
        echo htmlspecialchars(self::t($key, ...$args), ENT_QUOTES, 'UTF-8');
    }

    /** Get dictionary for client-side JS. */
    public static function getJsDictionary(): array {
        $lang = self::getLanguage();
        if (empty(self::$fallbackTranslations)) {
            self::$fallbackTranslations = self::loadLanguage('en');
        }
        if (!isset(self::$loadedTranslations[$lang])) {
            self::$loadedTranslations[$lang] = self::loadLanguage($lang);
        }
        return array_merge(self::$fallbackTranslations, self::$loadedTranslations[$lang]);
    }
}
