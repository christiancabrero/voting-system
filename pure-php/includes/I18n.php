<?php
/**
 * Internationalization (i18n) Engine for Pure PHP Voting System
 * Supports English (primary default), Spanish, and Catalan
 */

class I18n {
    private static ?string $currentLanguage = null;

    private static array $translations = [
        // Voting status & headers
        'title' => [
            'en' => 'Official Voting Competition',
            'es' => 'Concurso Oficial de Votaciones',
            'ca' => 'Concurs Oficial de Votacions',
        ],
        'subtitle' => [
            'en' => 'Cast your vote for your favorite contestant',
            'es' => 'Emite tu voto por tu participante favorito',
            'ca' => 'Emet el teu vot pel teu participant preferit',
        ],
        'voting_open' => [
            'en' => 'Voting is currently OPEN',
            'es' => 'Las votaciones están ABIERTAS',
            'ca' => 'Les votacions estan OBERTES',
        ],
        'voting_closed' => [
            'en' => 'Voting is currently CLOSED',
            'es' => 'Las votaciones están CERRADAS',
            'ca' => 'Les votacions estan TANCADES',
        ],
        'closed_notice' => [
            'en' => 'Voting is currently closed. You can view the participants below.',
            'es' => 'Las votaciones están actualmente cerradas. A continuación puedes ver los participantes.',
            'ca' => 'Les votacions estan actualment tancades. A continuació podeu veure els participants.',
        ],
        'simple_mode_badge' => [
            'en' => 'Simple Mode: 1 vote per voter',
            'es' => 'Modo Simple: 1 voto por votante',
            'ca' => 'Mode Simple: 1 vot per votant',
        ],
        'advanced_mode_badge' => [
            'en' => 'Advanced Mode: Category limits enabled',
            'es' => 'Modo Avanzado: Límites por categoría activos',
            'ca' => 'Mode Avançat: Límits per categoria actius',
        ],

        // Vote counters & buttons
        'votes_available' => [
            'en' => 'You have cast %1$s of %2$s available votes',
            'es' => 'Has votado %1$s de %2$s votos disponibles',
            'ca' => 'Has votat %1$s de %2$s vots disponibles',
        ],
        'votes_remaining' => [
            'en' => '%s votes remaining',
            'es' => 'Te quedan %s votos',
            'ca' => 'Et queden %s vots',
        ],
        'votes_exhausted' => [
            'en' => 'You have used all your available votes.',
            'es' => 'Has agotado todos tus votos disponibles.',
            'ca' => 'Has esgotat tots els teus vots disponibles.',
        ],
        'vote_btn' => [
            'en' => 'Vote',
            'es' => 'Votar',
            'ca' => 'Vota',
        ],
        'voted_btn' => [
            'en' => '✓ Voted',
            'es' => '✓ Votado',
            'ca' => '✓ Votat',
        ],
        'closed_btn' => [
            'en' => 'Voting closed',
            'es' => 'Votaciones cerradas',
            'ca' => 'Votacions tancades',
        ],
        'already_voted_alert' => [
            'en' => 'You have already voted. Results will be available soon.',
            'es' => 'Ya has votado. Los resultados estarán disponibles pronto.',
            'ca' => 'Ja has votat, aviat en coneixeràs els resultats.',
        ],

        // Messages & Queue processing
        'sending_vote' => [
            'en' => 'Submitting your vote...',
            'es' => 'Enviando tu voto...',
            'ca' => 'Enviant el teu vot...',
        ],
        'vote_queued' => [
            'en' => 'Your vote has been queued and is being processed!',
            'es' => '¡Tu voto ha entrado en la cola y se está procesando!',
            'ca' => 'El teu vot ha entrat a la cua i s’està processant!',
        ],
        'vote_success' => [
            'en' => 'Vote successfully recorded! Thank you for participating.',
            'es' => '¡Voto registrado con éxito! Gracias por participar.',
            'ca' => 'Vot registrat amb èxit! Gràcies per participar.',
        ],
        'vote_rejected_duplicate' => [
            'en' => 'This device has already cast all allowed votes.',
            'es' => 'Este dispositivo ya ha emitido todos los votos permitidos.',
            'ca' => 'Aquest dispositiu ja ha emès tots els vots permesos.',
        ],
        'rate_limit_exceeded' => [
            'en' => 'Too many requests. Please wait a moment and try again.',
            'es' => 'Demasiadas solicitudes. Por favor, espera un momento.',
            'ca' => 'Massa sol·licituds. Si us plau, espera un moment.',
        ],
        'invalid_csrf' => [
            'en' => 'Security token expired. Please refresh the page.',
            'es' => 'Token de seguridad expirado. Por favor recarga la página.',
            'ca' => 'Token de seguretat caducat. Si us plau recarrega la pàgina.',
        ],
        'invalid_contestant' => [
            'en' => 'Invalid contestant selected.',
            'es' => 'Participante seleccionado no válido.',
            'ca' => 'Participant seleccionat no vàlid.',
        ],

        // Labels
        'type' => [
            'en' => 'Type',
            'es' => 'Tipo',
            'ca' => 'Tipus',
        ],
        'category' => [
            'en' => 'Category',
            'es' => 'Categoría',
            'ca' => 'Categoria',
        ],
        'results' => [
            'en' => 'Live Results',
            'es' => 'Resultados en Vivo',
            'ca' => 'Resultats en Viu',
        ],
        'total_votes' => [
            'en' => 'Total Votes',
            'es' => 'Total de Votos',
            'ca' => 'Total de Vots',
        ],
        'admin_panel' => [
            'en' => 'Admin Panel',
            'es' => 'Panel de Administración',
            'ca' => 'Panell d’Administració',
        ],
    ];

    /** Detect language based on query string, cookie, HTTP headers, or default to English (en). */
    public static function getLanguage(): string {
        if (self::$currentLanguage !== null) {
            return self::$currentLanguage;
        }

        // 1. Explicit GET param
        if (!empty($_GET['lang'])) {
            $lang = strtolower(substr(trim($_GET['lang']), 0, 2));
            if (in_array($lang, ['en', 'es', 'ca'], true)) {
                setcookie('vs_lang', $lang, time() + 30 * 86400, '/');
                self::$currentLanguage = $lang;
                return $lang;
            }
        }

        // 2. Cookie
        if (!empty($_COOKIE['vs_lang'])) {
            $lang = strtolower(substr(trim($_COOKIE['vs_lang']), 0, 2));
            if (in_array($lang, ['en', 'es', 'ca'], true)) {
                self::$currentLanguage = $lang;
                return $lang;
            }
        }

        // 3. Browser Accept-Language header
        if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $parts = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            foreach ($parts as $part) {
                $sub = explode(';', $part)[0];
                $code = strtolower(substr(trim($sub), 0, 2));
                if (in_array($code, ['en', 'es', 'ca'], true)) {
                    self::$currentLanguage = $code;
                    return $code;
                }
            }
        }

        // 4. Default primary reference language: English
        self::$currentLanguage = 'en';
        return 'en';
    }

    /** Translate key into active language with fallback to EN. */
    public static function t(string $key, ...$args): string {
        $lang = self::getLanguage();
        $text = self::$translations[$key][$lang] ?? self::$translations[$key]['en'] ?? $key;
        if (!empty($args)) {
            return sprintf($text, ...$args);
        }
        return $text;
    }

    /** Get dictionary for client-side JS. */
    public static function getJsDictionary(): array {
        $lang = self::getLanguage();
        $dict = [];
        foreach (self::$translations as $k => $vals) {
            $dict[$k] = $vals[$lang] ?? $vals['en'] ?? $k;
        }
        return $dict;
    }
}
