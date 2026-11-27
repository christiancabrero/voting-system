<?php
/**
 * Internationalization (i18n) Engine for WordPress Voting System Plugin
 * Supports: English (en), Spanish (es), Catalan (ca), Portuguese (pt), French (fr), Japanese (ja/jp), Chinese (zh/cn)
 */

if (!defined('ABSPATH')) {
    exit;
}

function vs_supported_languages() {
    return ['en', 'es', 'ca', 'pt', 'fr', 'ja', 'zh'];
}

function vs_normalize_lang_code($code) {
    $code = strtolower(substr(trim((string)$code), 0, 2));
    if ($code === 'jp') {
        return 'ja';
    }
    if ($code === 'cn') {
        return 'zh';
    }
    if (in_array($code, vs_supported_languages(), true)) {
        return $code;
    }
    return '';
}

function vs_detect_browser_language() {
    if (empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        return '';
    }
    $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
    foreach ($langs as $part) {
        $sub = explode(';', $part)[0];
        $normalized = vs_normalize_lang_code($sub);
        if ($normalized !== '') {
            return $normalized;
        }
    }
    return '';
}

function vs_get_active_language() {
    // 1. Explicit GET override (?lang=)
    if (!empty($_GET['lang'])) {
        $code = vs_normalize_lang_code($_GET['lang']);
        if ($code !== '') {
            return $code;
        }
    }

    // 2. Cookie override (vs_lang)
    if (!empty($_COOKIE['vs_lang'])) {
        $code = vs_normalize_lang_code($_COOKIE['vs_lang']);
        if ($code !== '') {
            return $code;
        }
    }

    // 3. Admin user locale
    if (function_exists('is_admin') && is_admin() && function_exists('get_user_locale')) {
        $user_locale = get_user_locale();
        $code = vs_normalize_lang_code($user_locale);
        if ($code !== '') {
            return $code;
        }
    }

    // 4. Multilingual plugins (Polylang, WPML)
    if (function_exists('pll_current_language')) {
        $pll = (string) pll_current_language('slug');
        $code = vs_normalize_lang_code($pll);
        if ($code !== '') {
            return $code;
        }
    }
    if (function_exists('has_filter') && has_filter('wpml_current_language')) {
        $wpml = (string) apply_filters('wpml_current_language', null);
        $code = vs_normalize_lang_code($wpml);
        if ($code !== '') {
            return $code;
        }
    }

    // 5. WordPress Site Locale
    if (function_exists('determine_locale') || function_exists('get_locale')) {
        $locale = function_exists('determine_locale') ? determine_locale() : get_locale();
        $code = vs_normalize_lang_code($locale);
        if ($code !== '') {
            return $code;
        }
    }

    // 6. Browser Accept-Language header
    $browser_lang = vs_detect_browser_language();
    if ($browser_lang !== '') {
        return $browser_lang;
    }

    // 7. Default to English
    return 'en';
}

function vs_front_language() {
    return vs_get_active_language();
}

function vs_get_dictionary() {
    static $dict = null;
    if ($dict !== null) {
        return $dict;
    }
    $file = __DIR__ . "/dictionary.php";
    if (file_exists($file)) {
        $dict = require $file;
    } else {
        $dict = [];
    }
    return is_array($dict) ? $dict : [];
}

function vs_get_scope_filter_text($type = '', $category = '') {
    $type = trim((string)$type);
    $category = trim((string)$category);
    if (!empty($type) && !empty($category)) {
        return vs_t('filter_type_category', esc_html($type), esc_html($category));
    }
    if (!empty($type)) {
        return vs_t('filter_type', esc_html($type));
    }
    if (!empty($category)) {
        return vs_t('filter_category', esc_html($category));
    }
    return '';
}

function vs_t($key, ...$args) {
    $dict = vs_get_dictionary();
    $lang = vs_get_active_language();

    $text = '';
    if (isset($dict[$key][$lang])) {
        $text = $dict[$key][$lang];
    } elseif (isset($dict[$key]['en'])) {
        $text = $dict[$key]['en'];
    } elseif (isset($dict[$key]['es'])) {
        $text = $dict[$key]['es'];
    } else {
        $text = $key;
    }

    if (!empty($args)) {
        return vsprintf($text, $args);
    }
    return $text;
}

function vs_e($key, ...$args) {
    echo esc_html(vs_t($key, ...$args));
}

function vs_front_text($key) {
    return vs_t($key);
}
