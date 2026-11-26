<?php
/**
 * Plugin Name: Voting System
 * Description: Sistema de votaciones y resultados con categorías, apertura, cierre, limitaciones y opciones de seguridad. ES/CA/EN
 * Version: 3.6.6
 * Author: Christian Cabrero
 * Plugin uri: https://welovewebs.com/
 * Author uri: https://cabrero.ch/
 */

if (!defined('ABSPATH')) exit;

define('VS_URL', plugin_dir_url(__FILE__));
define('VS_PATH', plugin_dir_path(__FILE__));

/** Detect preferred language from the browser's HTTP_ACCEPT_LANGUAGE header. */
function vs_detect_browser_language() {
    if (empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        return '';
    }
    $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
    foreach ($langs as $part) {
        $sub = explode(';', $part)[0];
        $code = strtolower(substr(trim($sub), 0, 2));
        if (in_array($code, ['en', 'es', 'ca'], true)) {
            return $code;
        }
    }
    return '';
}

/** Return the active public language, prioritizing EN as default and auto-detecting via WP locale or Browser. */
function vs_front_language() {
    // 1. Explicit GET or COOKIE override
    if (!empty($_GET['lang'])) {
        $get_lang = strtolower(substr(trim($_GET['lang']), 0, 2));
        if (in_array($get_lang, ['en', 'es', 'ca'], true)) {
            return $get_lang;
        }
    }
    if (!empty($_COOKIE['vs_lang'])) {
        $cookie_lang = strtolower(substr(trim($_COOKIE['vs_lang']), 0, 2));
        if (in_array($cookie_lang, ['en', 'es', 'ca'], true)) {
            return $cookie_lang;
        }
    }

    // 2. Multilingual plugins (Polylang, WPML)
    if (function_exists('pll_current_language')) {
        $pll = (string) pll_current_language('slug');
        $code = strtolower(substr($pll, 0, 2));
        if (in_array($code, ['en', 'es', 'ca'], true)) {
            return $code;
        }
    }
    if (has_filter('wpml_current_language')) {
        $wpml = (string) apply_filters('wpml_current_language', null);
        $code = strtolower(substr($wpml, 0, 2));
        if (in_array($code, ['en', 'es', 'ca'], true)) {
            return $code;
        }
    }

    // 3. WordPress Locale if set to Spanish or Catalan
    if (function_exists('determine_locale') || function_exists('get_locale')) {
        $locale = function_exists('determine_locale') ? determine_locale() : get_locale();
        $wp_code = strtolower(substr((string) $locale, 0, 2));
        if (in_array($wp_code, ['es', 'ca'], true)) {
            return $wp_code;
        }
    }

    // 4. Browser Accept-Language header
    $browser_lang = vs_detect_browser_language();
    if ($browser_lang !== '') {
        return $browser_lang;
    }

    // 5. Primary default language is English (en)
    return 'en';
}

/** Translate plugin-owned public UI strings with English as primary reference. */
function vs_front_text($key) {
    static $translations = [
        'closed' => ['en' => 'Voting is closed', 'es' => 'Las votaciones están cerradas', 'ca' => 'Les votacions estan tancades'],
        'starts' => ['en' => 'Voting will start on %s', 'es' => 'Las votaciones comenzarán el %s', 'ca' => 'Les votacions començaran el %s'],
        'ended' => ['en' => 'Voting ended on %s', 'es' => 'Las votaciones finalizaron el %s', 'ca' => 'Les votacions van finalitzar el %s'],
        'unauthorized' => ['en' => 'Unauthorized access. Invalid voting token.', 'es' => 'Acceso no autorizado. Token de votación inválido.', 'ca' => 'Accés no autoritzat. El token de votació no és vàlid.'],
        'ip_denied' => ['en' => 'Your IP address is not authorized to vote.', 'es' => 'Tu dirección IP no está autorizada para votar.', 'ca' => 'La teva adreça IP no està autoritzada per votar.'],
        'login_required' => ['en' => 'You must log in to vote.', 'es' => 'Debes iniciar sesión para votar.', 'ca' => 'Has d’iniciar sessió per votar.'],
        'login' => ['en' => 'Log in', 'es' => 'Iniciar sesión', 'ca' => 'Inicia sessió'],
        'voter_limit' => ['en' => 'The limit of %d voters has been reached', 'es' => 'Se ha alcanzado el límite de %d votantes', 'ca' => 'S’ha assolit el límit de %d votants'],
        'no_options' => ['en' => 'There are no options available to vote for', 'es' => 'No hay opciones disponibles para votar', 'ca' => 'No hi ha opcions disponibles per votar'],
        'already_voted' => ['en' => 'You have already voted. Results will be available soon.', 'es' => 'Ya has votado. Los resultados estarán disponibles pronto.', 'ca' => 'Ja has votat, aviat en coneixeràs els resultats.'],
        'voter_number' => ['en' => 'Voter', 'es' => 'Votante', 'ca' => 'Votant'],
        'of' => ['en' => 'of', 'es' => 'de', 'ca' => 'de'],
        'votes_available' => ['en' => 'You have cast %1$s of %2$s available votes', 'es' => 'Has votado %1$s de %2$s votos disponibles', 'ca' => 'Has votat %1$s de %2$s vots disponibles'],
        'votes_left' => ['en' => '%s votes remaining', 'es' => 'Te quedan %s votos', 'ca' => 'Et queden %s vots'],
        'votes_used' => ['en' => 'You have used all your votes%s.', 'es' => 'Has agotado todos tus votos%s.', 'ca' => 'Has esgotat tots els teus vots%s.'],
        'vote' => ['en' => 'Vote', 'es' => 'Votar', 'ca' => 'Vota'],
        'voted' => ['en' => 'Voted', 'es' => 'Votado', 'ca' => 'Ja has votat'],
        'vote_badge' => ['en' => '%d votes', 'es' => '%d votos', 'ca' => '%d vots'],
        'vote_badge_one' => ['en' => '1 vote', 'es' => '1 voto', 'ca' => '1 vot'],
        'no_votes_yet' => ['en' => 'No votes have been recorded yet.', 'es' => 'No hay votos registrados todavía.', 'ca' => 'Encara no hi ha vots registrats.'],
        'results' => ['en' => 'Results', 'es' => 'Resultados', 'ca' => 'Resultats'],
        'total_votes' => ['en' => 'Total votes: %d', 'es' => 'Total de votos: %d', 'ca' => 'Total de vots: %d'],
        'unique_voters' => ['en' => 'Unique voters: %d', 'es' => 'Votantes únicos: %d', 'ca' => 'Votants únics: %d'],
        'votes' => ['en' => '%d votes', 'es' => '%d votos', 'ca' => '%d vots'],
        'type' => ['en' => 'Type: %s', 'es' => 'Tipo: %s', 'ca' => 'Tipus: %s'],
        'category' => ['en' => 'Category: %s', 'es' => 'Categoría: %s', 'ca' => 'Categoria: %s'],
        'voting_closed_btn' => ['en' => 'Voting closed', 'es' => 'Votaciones cerradas', 'ca' => 'Votacions tancades'],
        'voting_closed_notice' => ['en' => 'Voting is currently closed. You can view the participants below.', 'es' => 'Las votaciones están actualmente cerradas. A continuación puedes ver los participantes.', 'ca' => 'Les votacions estan actualment tancades. A continuació podeu veure els participants.'],
        'simple_mode_notice' => ['en' => 'Simple mode active: 1 vote per voter.', 'es' => 'Modo simple activo: 1 voto por votante.', 'ca' => 'Mode simple actiu: 1 vot per votant.'],
        'sending_vote' => ['en' => 'Sending vote...', 'es' => 'Enviando voto...', 'ca' => 'Enviant vot...'],
        'vote_success' => ['en' => 'Vote registered successfully!', 'es' => '¡Voto registrado correctamente!', 'ca' => 'Vot registrat correctament!'],
        'vote_error' => ['en' => 'Error processing vote', 'es' => 'Error al procesar el voto', 'ca' => 'Error en processar el vot'],
    ];

    $language = vs_front_language();
    return isset($translations[$key][$language]) ? $translations[$key][$language] : (isset($translations[$key]['en']) ? $translations[$key]['en'] : (isset($translations[$key]['es']) ? $translations[$key]['es'] : $key));
}

// ============================================
// 1. ACTIVACIÓN - Crear tablas
// ============================================
register_activation_hook(__FILE__, 'vs_install');
function vs_install() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Tabla de opciones (participantes)
    $table = $wpdb->prefix . 'vs_options';
    $sql = "CREATE TABLE $table (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        type VARCHAR(100),
        photo_url VARCHAR(500),
        category VARCHAR(100),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";

    // Tabla de VOTOS Y AUDITORÍA UNIFICADA
    $table_votes = $wpdb->prefix . 'vs_votes';
    $sql .= "CREATE TABLE $table_votes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        option_id INT NOT NULL,
        device_id VARCHAR(255) NOT NULL,
        ip VARCHAR(45),
        user_agent TEXT,
        voter_session VARCHAR(255),
        token VARCHAR(255),
        user_id INT DEFAULT 0,
        is_valid BOOLEAN DEFAULT TRUE,
        fraud_attempts INT DEFAULT 0,
        fraud_reason VARCHAR(255),
        voted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX (option_id),
        INDEX (device_id),
        INDEX (voter_session),
        INDEX (token),
        INDEX (user_id),
        INDEX (is_valid)
    ) $charset_collate;";

    // Tabla de tokens de votación (SESIONES ACTIVAS)
	$table_tokens = $wpdb->prefix . 'vs_tokens';
	$sql .= "CREATE TABLE $table_tokens (
		id INT AUTO_INCREMENT PRIMARY KEY,
		token VARCHAR(255) NOT NULL UNIQUE,
		device_id VARCHAR(255) NOT NULL,
		ip VARCHAR(45),
		user_agent TEXT,
		is_used BOOLEAN DEFAULT FALSE,
		is_blocked BOOLEAN DEFAULT FALSE,
		blocked_reason VARCHAR(255),
		used_at DATETIME DEFAULT NULL,
		created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
		expires_at DATETIME,
		INDEX (token),
		INDEX (device_id),
		INDEX (is_blocked)
	) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Crear rol de usuario "Votante"
    add_role('votante', 'Votante', [
        'read' => true,
        'level_0' => true
    ]);

    // Opciones por defecto
    add_option('vs_voting_open', false);
    add_option('vs_closed_show_participants', false);
    add_option('vs_voting_mode', 'advanced');
    add_option('vs_show_breakdown', true);
    add_option('vs_voting_start', '');
    add_option('vs_voting_end', '');
    add_option('vs_voting_auto_open', false);
    add_option('vs_voting_auto_close', false);
    add_option('vs_votes_per_device', 1);
    add_option('vs_votes_per_category', '');
    add_option('vs_votes_per_type', '');
    add_option('vs_results_default_view', 'detailed');
    add_option('vs_results_show_percentage', true);
    add_option('vs_results_show_numbers', true);
    add_option('vs_security_method', 'cookie');
    add_option('vs_allow_multiple_per_option', false);
    add_option('vs_max_voters', 0);
    add_option('vs_enable_device_fingerprint', false);
    add_option('vs_enable_chrome_user_check', false);
    add_option('vs_refresh_after_vote', true);
    add_option('vs_enable_token_security', false);
    add_option('vs_token_param_name', 'token-votacion');
    add_option('vs_token_expiry_hours', 24);
    add_option('vs_results_show_images', true);
    add_option('vs_enable_ip_restriction', false);
    add_option('vs_allowed_ips', '');
    add_option('vs_require_registered_user', false);
    add_option('vs_token_single_use', true);
	add_option('vs_max_token_attempts', 3);
}

// ============================================
// 2. FUNCIONES DE SESIÓN
// ============================================

function vs_start_session() {
    if (!session_id() && !headers_sent()) {
        @session_start();
    }
}

// ============================================
// 3. INICIALIZACIÓN TEMPRANA (CORREGIDO - Usar wp en lugar de init)
// ============================================
add_action('wp', 'vs_init_early');
function vs_init_early() {
    if (!headers_sent() && !is_admin()) {
        // Verificar si estamos en una página de votación
        $voting_pages = get_option('vs_voting_pages', []);
        $current_page_id = get_the_ID();
        
        // Si hay páginas seleccionadas y NO estamos en una de ellas, NO procesar tokens
        if (!empty($voting_pages) && !in_array($current_page_id, $voting_pages)) {
            return; // No estamos en una página de votación
        }
        
        // Estamos en una página de votación (o no hay páginas seleccionadas = todas)
        vs_start_session();
        $token_handled = vs_handle_token();
        if (!$token_handled) {
            vs_get_device_id();
        }
    }
}

// ============================================
// 4. SISTEMA DE TOKENS MEJORADO (CORREGIDO)
// ============================================

function vs_handle_token() {
    global $wpdb;
    
    $enable_token = get_option('vs_enable_token_security', false);
    if (!$enable_token) {
        return false;
    }
    
    if (is_admin() || vs_is_login_page()) {
        return false;
    }
    
    $voting_pages = get_option('vs_voting_pages', []);
    if (!empty($voting_pages)) {
        $current_page_id = get_the_ID();
        if (!$current_page_id) {
            return false;
        }
        if (!in_array($current_page_id, $voting_pages)) {
            return false;
        }
    }
    
    $param_name = get_option('vs_token_param_name', 'token-votacion');
    $token = isset($_GET[$param_name]) ? sanitize_text_field($_GET[$param_name]) : '';
    $single_use = get_option('vs_token_single_use', true);
    $current_device_id = vs_get_device_id();
    
    // ============================================
    // VERIFICAR SI EL DISPOSITIVO ESTÁ BLOQUEADO
    // ============================================
    $device_blocked = $wpdb->get_var($wpdb->prepare(
        "SELECT is_blocked FROM {$wpdb->prefix}vs_tokens 
        WHERE device_id = %s AND is_blocked = 1 
        LIMIT 1",
        $current_device_id
    ));
    
    if ($device_blocked) {
        vs_log_vote_attempt($token, 'DEVICE_BLOCKED');
        wp_die('Este dispositivo ha sido bloqueado por actividad sospechosa. Se han impedido más votos por parte de este dispositivo.');
    }
    
    // Si no hay token, generar uno NUEVO
    if (empty($token)) {
        $device_id = $current_device_id;
        
        // Buscar token activo existente
        $existing_token = $wpdb->get_row($wpdb->prepare(
            "SELECT token FROM {$wpdb->prefix}vs_tokens 
            WHERE device_id = %s AND is_used = 0 AND is_blocked = 0 AND (expires_at IS NULL OR expires_at > NOW())
            ORDER BY created_at DESC LIMIT 1",
            $device_id
        ));
        
        if ($existing_token) {
            $new_token = $existing_token->token;
        } else {
            $new_token = vs_create_token_for_device($device_id);
        }
        
        $current_url = vs_get_current_url();
        $current_url = remove_query_arg($param_name, $current_url);
        $redirect_url = add_query_arg($param_name, $new_token, $current_url);
        wp_redirect($redirect_url);
        exit;
    }
    
    // ============================================
    // VERIFICAR TOKEN EN DB
    // ============================================
    $token_data = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}vs_tokens WHERE token = %s",
        $token
    ));
    
    if (!$token_data) {
        vs_log_vote_attempt($token, 'INVALID_TOKEN');
        wp_die('Token de votación inválido.');
    }
    
    // ============================================
    // VERIFICAR SI EL TOKEN ESTÁ BLOQUEADO
    // ============================================
    if ($token_data->is_blocked) {
        vs_log_vote_attempt($token, 'TOKEN_BLOCKED');
        wp_die('Este token de votación ya ha sido utilizado. Se han impedido más votos por parte de este dispositivo.');
    }
    
    // Verificar expiración
    if (!empty($token_data->expires_at) && current_time('timestamp') > strtotime($token_data->expires_at)) {
        vs_log_vote_attempt($token, 'TOKEN_EXPIRED');
        wp_die('El token de votación ha expirado. Se han impedido más votos por parte de este dispositivo.');
    }
    
    // Verificar si ya fue usado
    if ($single_use && $token_data->is_used) {
        vs_log_vote_attempt($token, 'TOKEN_ALREADY_USED');
        wp_die('Este token de votación ya ha sido utilizado. Se han impedido más votos por parte de este dispositivo.');
    }
    
    // ============================================
    // VERIFICAR DISPOSITIVO Y BLOQUEAR SI NO COINCIDE
    // ============================================
    if ($token_data->device_id !== $current_device_id) {
        // REGISTRAR INTENTO FALLIDO
        vs_log_vote_attempt($token, 'DEVICE_MISMATCH', $current_device_id);
        
        // BLOQUEAR EL TOKEN PERMANENTEMENTE
        $wpdb->update(
            $wpdb->prefix . 'vs_tokens',
            [
                'is_blocked' => 1,
                'blocked_reason' => 'Dispositivo no autorizado intentó usar este token'
            ],
            ['token' => $token]
        );
        
        // BLOQUEAR EL DISPOSITIVO QUE INTENTÓ USARLO
        $wpdb->update(
            $wpdb->prefix . 'vs_tokens',
            [
                'is_blocked' => 1,
                'blocked_reason' => 'Intentó usar token de otro dispositivo'
            ],
            ['device_id' => $current_device_id]
        );
        
        wp_die('Este token de votación ya ha sido utilizado. Se han impedido más votos por parte de este dispositivo.');
    }
    
    // Verificar IP
    if (get_option('vs_enable_ip_restriction', false)) {
        $allowed_ips = array_map('trim', explode(',', get_option('vs_allowed_ips', '')));
        $current_ip = $_SERVER['REMOTE_ADDR'];
        if (!empty($allowed_ips) && !in_array($current_ip, $allowed_ips)) {
            vs_log_vote_attempt($token, 'IP_RESTRICTED', $current_ip);
            wp_die('Tu dirección IP no está autorizada para votar.');
        }
    }
    
    // Verificar usuario registrado
    if (get_option('vs_require_registered_user', false) && !is_user_logged_in()) {
        vs_log_vote_attempt($token, 'USER_NOT_LOGGED_IN');
        wp_die('Debes iniciar sesión para votar.');
    }
    
    // Token válido
    return true;
}

// Función auxiliar para verificar login
function vs_is_login_page() {
    return in_array($GLOBALS['pagenow'], ['wp-login.php', 'wp-register.php']);
}

// Función para regenerar token manualmente (para casos de error)
function vs_regenerate_token() {
    global $wpdb;
    $device_id = vs_get_device_id();
    $wpdb->delete($wpdb->prefix . 'vs_tokens', ['device_id' => $device_id]);
    $new_token = vs_create_token_for_device($device_id);
    $param_name = get_option('vs_token_param_name', 'token-votacion');
    $redirect_url = add_query_arg($param_name, $new_token, remove_query_arg($param_name));
    wp_redirect($redirect_url);
    exit;
}

function vs_create_token_for_device($device_id) {
    global $wpdb;
    
    // Verificar si el dispositivo está bloqueado
    $is_blocked = $wpdb->get_var($wpdb->prepare(
        "SELECT is_blocked FROM {$wpdb->prefix}vs_tokens WHERE device_id = %s AND is_blocked = 1 LIMIT 1",
        $device_id
    ));
    
    if ($is_blocked) {
        wp_die('Se han impedido más votos por parte de este dispositivo.');
    }
    
    // Eliminar tokens antiguos del mismo dispositivo (que no estén bloqueados)
    $wpdb->delete($wpdb->prefix . 'vs_tokens', [
        'device_id' => $device_id,
        'is_blocked' => 0,
        'is_used' => 0
    ]);
    
    $token = bin2hex(random_bytes(32));
    $expiry_hours = intval(get_option('vs_token_expiry_hours', 24));
    $expires_at = date('Y-m-d H:i:s', current_time('timestamp') + ($expiry_hours * 3600));
    
    $wpdb->insert(
        $wpdb->prefix . 'vs_tokens',
        [
            'token' => $token,
            'device_id' => $device_id,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'expires_at' => $expires_at,
            'is_used' => 0,
            'is_blocked' => 0
        ]
    );
    
    return $token;
}

function vs_generate_voting_token() {
    $device_id = vs_get_device_id();
    return vs_create_token_for_device($device_id);
}

function vs_log_vote_attempt($token, $reason, $device_id = '') {
    global $wpdb;
    
    if (empty($device_id)) {
        $device_id = vs_get_device_id();
    }
    
    // Buscar si ya existe un intento para este token/device
    $existing = $wpdb->get_row($wpdb->prepare(
        "SELECT id, fraud_attempts FROM {$wpdb->prefix}vs_votes 
        WHERE token = %s AND device_id = %s AND is_valid = 0",
        $token, $device_id
    ));
    
    if ($existing) {
        // Actualizar contador de intentos
        $wpdb->update(
            $wpdb->prefix . 'vs_votes',
            [
                'fraud_attempts' => $existing->fraud_attempts + 1,
                'fraud_reason' => $reason
            ],
            ['id' => $existing->id]
        );
    } else {
        // Crear nuevo registro de intento
        $wpdb->insert(
            $wpdb->prefix . 'vs_votes',
            [
                'option_id' => 0,
                'device_id' => $device_id,
                'ip' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'voter_session' => vs_get_voter_session(),
                'token' => $token,
                'user_id' => get_current_user_id(),
                'is_valid' => 0,
                'fraud_attempts' => 1,
                'fraud_reason' => $reason,
                'voted_at' => current_time('mysql')
            ]
        );
    }
}

function vs_get_current_url() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

function vs_validate_token_access() {
    global $wpdb;
    
    $enable_token = get_option('vs_enable_token_security', false);
    if (!$enable_token) {
        return true;
    }
    
    // Si estamos en admin o login, permitir acceso
    if (is_admin() || vs_is_login_page()) {
        return true;
    }
    
    // Verificar si estamos en una página de votación
    $voting_pages = get_option('vs_voting_pages', []);
    if (!empty($voting_pages)) {
        $current_page_id = get_the_ID();
        if (!$current_page_id) {
            return true;
        }
        if (!in_array($current_page_id, $voting_pages)) {
            return true;
        }
    }
    
    $param_name = get_option('vs_token_param_name', 'token-votacion');
    $token = isset($_GET[$param_name]) ? sanitize_text_field($_GET[$param_name]) : '';
    
    // Si no hay token en la URL, verificar si hay token en sesión/cookie
    if (empty($token)) {
        $device_id = vs_get_device_id();
        $token_data = $wpdb->get_row($wpdb->prepare(
            "SELECT token FROM {$wpdb->prefix}vs_tokens 
            WHERE device_id = %s AND is_used = 0 AND (expires_at IS NULL OR expires_at > NOW()) 
            ORDER BY created_at DESC LIMIT 1",
            $device_id
        ));
        if ($token_data) {
            $redirect_url = add_query_arg($param_name, $token_data->token, vs_get_current_url());
            wp_redirect($redirect_url);
            exit;
        }
        return false;
    }
    
    // Verificar token en la base de datos
    $token_data = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}vs_tokens WHERE token = %s",
        $token
    ));
    
    if (!$token_data) {
        return false;
    }
    
    // Verificar expiración
    if (!empty($token_data->expires_at) && current_time('timestamp') > strtotime($token_data->expires_at)) {
        return false;
    }
    
    // Verificar si el token ya fue usado (single use)
    // SOLO verificar, NO marcar como usado aquí
    if (get_option('vs_token_single_use', true) && $token_data->is_used) {
        return false;
    }
    
    // Verificar que el dispositivo coincide
    $device_id = vs_get_device_id();
    if ($token_data->device_id !== $device_id) {
        vs_log_vote_attempt($token, 'DEVICE_MISMATCH_ATTEMPT', $device_id);
        return false;
    }
    
    return true;
}

// Ejecutar al guardar la configuración de seguridad
add_action('update_option_vs_voting_pages', 'vs_clean_tokens_on_page_change', 10, 2);
function vs_clean_tokens_on_page_change($old_value, $new_value) {
    global $wpdb;
    // Si se desactivaron páginas, limpiar tokens antiguos
    if (!empty($old_value) && empty($new_value)) {
        $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}vs_tokens");
    }
}

// ============================================
// 5. ASSETS
// ============================================
add_action('wp_enqueue_scripts', 'vs_enqueue_assets');
function vs_enqueue_assets() {
    $css_file = VS_URL . 'assets/css/frontend.css';
    wp_enqueue_style('vs-frontend', $css_file, [], '3.4');
    wp_enqueue_script('vs-frontend', VS_URL . 'assets/js/frontend.js', ['jquery'], '3.4', true);
    wp_localize_script('vs-frontend', 'vs_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('vs_vote_nonce'),
        'refresh' => get_option('vs_refresh_after_vote', true),
        'lang' => vs_front_language(),
        'i18n' => [
            'sending_vote' => vs_front_text('sending_vote'),
            'vote_success' => vs_front_text('vote_success'),
            'vote_error' => vs_front_text('vote_error'),
            'voted' => vs_front_text('voted'),
            'voting_closed' => vs_front_text('voting_closed_btn'),
        ]
    ]);
}

add_action('admin_enqueue_scripts', 'vs_admin_enqueue_assets');
function vs_admin_enqueue_assets($hook) {
    if (strpos($hook, 'vs-') === false) return;
    wp_enqueue_style('vs-frontend', VS_URL . 'assets/css/frontend.css', [], '3.4');
    wp_enqueue_style('vs-admin', VS_URL . 'assets/css/admin.css', [], '3.4');
    wp_enqueue_media();
    wp_enqueue_script('vs-admin', VS_URL . 'assets/js/admin.js', ['jquery', 'media-upload'], '3.4', true);
    
    // Para ocultar/mostrar columnas
    wp_enqueue_script('wp-list-revisions');
}

// ============================================
// 6. FUNCIONES DE SEGURIDAD
// ============================================

function vs_get_device_id() {
    global $wpdb;
    static $cached_device_id = null;
    if ($cached_device_id !== null) {
        return $cached_device_id;
    }

    $security_method = get_option('vs_security_method', 'cookie');
    
    if ($security_method == 'session' || $security_method == 'both') {
        if (session_status() == PHP_SESSION_ACTIVE && isset($_SESSION['vs_device_id'])) {
            $device_id = $_SESSION['vs_device_id'];
            // Verificar si el dispositivo está bloqueado
            $is_blocked = $wpdb->get_var($wpdb->prepare(
                "SELECT is_blocked FROM {$wpdb->prefix}vs_tokens WHERE device_id = %s AND is_blocked = 1 LIMIT 1",
                $device_id
            ));
            if ($is_blocked) {
                wp_die('Se han impedido más votos por parte de este dispositivo.');
            }
            $cached_device_id = $device_id;
            return $device_id;
        }
    }
    
    if (isset($_COOKIE['vs_device_id']) && !empty($_COOKIE['vs_device_id'])) {
        $device_id = sanitize_text_field($_COOKIE['vs_device_id']);
        // Verificar si el dispositivo está bloqueado
        $is_blocked = $wpdb->get_var($wpdb->prepare(
            "SELECT is_blocked FROM {$wpdb->prefix}vs_tokens WHERE device_id = %s AND is_blocked = 1 LIMIT 1",
            $device_id
        ));
        if ($is_blocked) {
            wp_die('Se han impedido más votos por parte de este dispositivo.');
        }
        $cached_device_id = $device_id;
        return $device_id;
    }
    
    $fingerprint = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '') . '|' . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
    $device_id = hash('sha256', $fingerprint . uniqid(mt_rand(), true));
    
    // Guardar en superglobal y caché estática para evitar generar múltiples IDs en la misma petición
    $_COOKIE['vs_device_id'] = $device_id;
    $cached_device_id = $device_id;
    
    if (!headers_sent() && (!is_admin() || (function_exists('wp_doing_ajax') && wp_doing_ajax()))) {
        setcookie('vs_device_id', $device_id, time() + 365 * DAY_IN_SECONDS, '/', '', false, true);
        if ($security_method == 'session' || $security_method == 'both') {
            if (session_status() == PHP_SESSION_ACTIVE) {
                $_SESSION['vs_device_id'] = $device_id;
            }
        }
    }
    
    return $device_id;
}

function vs_get_voter_session() {
    $security_method = get_option('vs_security_method', 'cookie');
    $session_id = '';
    if ($security_method == 'session' || $security_method == 'both') {
        if (session_status() == PHP_SESSION_ACTIVE) {
            $session_id = session_id();
        }
    }
    return $session_id;
}

function vs_check_voter_limit() {
    global $wpdb;
    $max_voters = intval(get_option('vs_max_voters', 0));
    if ($max_voters <= 0) return true;
    $voter_count = $wpdb->get_var("SELECT COUNT(DISTINCT device_id) FROM {$wpdb->prefix}vs_votes WHERE is_valid = 1");
    return $voter_count < $max_voters;
}

function vs_get_voter_position() {
    global $wpdb;
    $device_id = vs_get_device_id();
    $voters = $wpdb->get_col("SELECT device_id FROM {$wpdb->prefix}vs_votes WHERE is_valid = 1 GROUP BY device_id ORDER BY MIN(voted_at) ASC");
    $position = array_search($device_id, $voters);
    if ($position === false) {
        return $wpdb->get_var("SELECT COUNT(DISTINCT device_id) FROM {$wpdb->prefix}vs_votes WHERE is_valid = 1") + 1;
    }
    return $position + 1;
}

function vs_can_vote_for_option($option_id, $device_id) {
    global $wpdb;
    $allow_multiple = get_option('vs_allow_multiple_per_option', false);
    
    // Si permite múltiples, siempre puede votar (los límites se controlan por categoría/tipo)
    if ($allow_multiple) {
        return true;
    }
    
    if (strpos($device_id, 'admin_') === 0) {
        return true;
    }

    $user_id = get_current_user_id();
    if ($user_id > 0) {
        $already_voted = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}vs_votes 
            WHERE (device_id = %s OR (user_id > 0 AND user_id = %d)) AND option_id = %d AND is_valid = 1",
            $device_id, $user_id, $option_id
        ));
    } else {
        $already_voted = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}vs_votes WHERE device_id = %s AND option_id = %d AND is_valid = 1",
            $device_id, $option_id
        ));
    }
    
    return !$already_voted;
}

function vs_is_ip_allowed() {
    if (!get_option('vs_enable_ip_restriction', false)) return true;
    $allowed_ips = array_map('trim', explode(',', get_option('vs_allowed_ips', '')));
    return empty($allowed_ips) || in_array($_SERVER['REMOTE_ADDR'], $allowed_ips);
}

// ============================================
// 7. FUNCIONES DE LÍMITES
// ============================================

function vs_get_effective_limit($atts = []) {
    global $wpdb;
    
    // Si está en modo simple: siempre 1 voto global
    if (get_option('vs_voting_mode', 'advanced') === 'simple') {
        return 1;
    }
    
    // Si hay un límite específico en el shortcode, usarlo
    if (!empty($atts['limit']) && is_numeric($atts['limit']) && intval($atts['limit']) > 0) {
        return intval($atts['limit']);
    }
    
    $type = isset($atts['type']) ? $atts['type'] : '';
    $category = isset($atts['category']) ? $atts['category'] : '';
    
    // Verificar si hay límite por tipo (cuando se filtra por tipo)
    if (!empty($type)) {
        $type_limit = get_option('vs_votes_per_type', '');
        if (!empty($type_limit) && is_numeric($type_limit) && intval($type_limit) > 0) {
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}vs_options WHERE type = %s",
                $type
            ));
            if ($exists > 0) {
                return intval($type_limit);
            }
        }
    }
    
    // Verificar si hay límite por categoría (cuando se filtra por categoría)
    if (!empty($category)) {
        $category_limit = get_option('vs_votes_per_category', '');
        if (!empty($category_limit) && is_numeric($category_limit) && intval($category_limit) > 0) {
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}vs_options WHERE category = %s",
                $category
            ));
            if ($exists > 0) {
                return intval($category_limit);
            }
        }
    }
    
    // Límite global por dispositivo (NUNCA 999 si está configurado)
    $global_limit = intval(get_option('vs_votes_per_device', 1));
    return $global_limit > 0 ? $global_limit : 999;
}

function vs_get_remaining_votes($device_id, $atts = []) {
    global $wpdb;
    
    if (strpos($device_id, 'admin_') === 0) {
        return 99;
    }
    
    $max_votes = vs_get_effective_limit($atts);
    $voted_count = vs_get_voted_count($device_id, $atts);
    $remaining = max(0, $max_votes - $voted_count);
    
    return $remaining;
}

// ============================================
// NUEVA FUNCIÓN: Obtener el límite y el ámbito
// ============================================
function vs_get_limit_info($atts = []) {
    global $wpdb;
    
    // Modo simple: 1 voto por dispositivo indiferente de categorías o tipos
    if (get_option('vs_voting_mode', 'advanced') === 'simple') {
        return [
            'limit' => 1,
            'scope' => 'device'
        ];
    }
    
    // Si hay un límite específico en el shortcode, usarlo (ámbito: shortcode)
    if (!empty($atts['limit']) && is_numeric($atts['limit']) && intval($atts['limit']) > 0) {
        return [
            'limit' => intval($atts['limit']),
            'scope' => 'shortcode'
        ];
    }
    
    $type = isset($atts['type']) ? $atts['type'] : '';
    $category = isset($atts['category']) ? $atts['category'] : '';
    
    // Verificar si hay límite por tipo (ámbito: tipo)
    if (!empty($type)) {
        $type_limit = get_option('vs_votes_per_type', '');
        if (!empty($type_limit) && is_numeric($type_limit) && intval($type_limit) > 0) {
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}vs_options WHERE type = %s",
                $type
            ));
            if ($exists > 0) {
                return [
                    'limit' => intval($type_limit),
                    'scope' => 'type',
                    'scope_value' => $type
                ];
            }
        }
    }
    
    // Verificar si hay límite por categoría (ámbito: categoría)
    if (!empty($category)) {
        $category_limit = get_option('vs_votes_per_category', '');
        if (!empty($category_limit) && is_numeric($category_limit) && intval($category_limit) > 0) {
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}vs_options WHERE category = %s",
                $category
            ));
            if ($exists > 0) {
                return [
                    'limit' => intval($category_limit),
                    'scope' => 'category',
                    'scope_value' => $category
                ];
            }
        }
    }
    
    // Límite global (ámbito: dispositivo)
    return [
        'limit' => intval(get_option('vs_votes_per_device', 1)),
        'scope' => 'device'
    ];
}

// ============================================
// NUEVA FUNCIÓN: Contar votos según el ámbito
// ============================================
function vs_get_voted_count_by_scope($device_id, $scope, $scope_value = '') {
    global $wpdb;
    
    if (strpos($device_id, 'admin_') === 0) {
        return 0;
    }
    
    $user_id = get_current_user_id();
    
    switch ($scope) {
        case 'device':
            // Contar TODOS los votos del dispositivo o usuario
            if ($user_id > 0) {
                return intval($wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->prefix}vs_votes 
                    WHERE (device_id = %s OR (user_id > 0 AND user_id = %d)) AND is_valid = 1",
                    $device_id, $user_id
                )));
            }
            return intval($wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}vs_votes 
                WHERE device_id = %s AND is_valid = 1",
                $device_id
            )));
            
        case 'category':
            // Contar votos en esa categoría
            if ($user_id > 0) {
                return intval($wpdb->get_var($wpdb->prepare("
                    SELECT COUNT(*) 
                    FROM {$wpdb->prefix}vs_votes v
                    JOIN {$wpdb->prefix}vs_options o ON v.option_id = o.id
                    WHERE (v.device_id = %s OR (v.user_id > 0 AND v.user_id = %d)) AND v.is_valid = 1 AND o.category = %s
                ", $device_id, $user_id, $scope_value)));
            }
            return intval($wpdb->get_var($wpdb->prepare("
                SELECT COUNT(*) 
                FROM {$wpdb->prefix}vs_votes v
                JOIN {$wpdb->prefix}vs_options o ON v.option_id = o.id
                WHERE v.device_id = %s AND v.is_valid = 1 AND o.category = %s
            ", $device_id, $scope_value)));
            
        case 'type':
            // Contar votos en ese tipo
            if ($user_id > 0) {
                return intval($wpdb->get_var($wpdb->prepare("
                    SELECT COUNT(*) 
                    FROM {$wpdb->prefix}vs_votes v
                    JOIN {$wpdb->prefix}vs_options o ON v.option_id = o.id
                    WHERE (v.device_id = %s OR (v.user_id > 0 AND v.user_id = %d)) AND v.is_valid = 1 AND o.type = %s
                ", $device_id, $user_id, $scope_value)));
            }
            return intval($wpdb->get_var($wpdb->prepare("
                SELECT COUNT(*) 
                FROM {$wpdb->prefix}vs_votes v
                JOIN {$wpdb->prefix}vs_options o ON v.option_id = o.id
                WHERE v.device_id = %s AND v.is_valid = 1 AND o.type = %s
            ", $device_id, $scope_value)));
            
        default:
            return 0;
    }
}

function vs_get_voted_count($device_id, $atts = []) {
    global $wpdb;
    
    if (strpos($device_id, 'admin_') === 0) {
        return 0;
    }
    
    $user_id = get_current_user_id();
    $params = [];
    if ($user_id > 0) {
        $where = ["(v.device_id = %s OR (v.user_id > 0 AND v.user_id = %d))", "v.is_valid = 1"];
        $params[] = $device_id;
        $params[] = $user_id;
    } else {
        $where = ["v.device_id = %s", "v.is_valid = 1"];
        $params[] = $device_id;
    }
    
    if (!empty($atts['type'])) {
        $where[] = "o.type = %s";
        $params[] = $atts['type'];
    }
    
    if (!empty($atts['category'])) {
        $where[] = "o.category = %s";
        $params[] = $atts['category'];
    }
    
    $where_clause = "AND " . implode(" AND ", $where);
    $query = $wpdb->prepare("
        SELECT COUNT(*) 
        FROM {$wpdb->prefix}vs_votes v
        JOIN {$wpdb->prefix}vs_options o ON v.option_id = o.id
        WHERE 1=1 $where_clause
    ", $params);
    
    return intval($wpdb->get_var($query));
}

// ============================================
// NUEVA FUNCIÓN: Contar TODOS los votos del dispositivo
// ============================================
function vs_get_total_voted_count($device_id) {
    global $wpdb;
    
    if (strpos($device_id, 'admin_') === 0) {
        return 0;
    }
    
    $user_id = get_current_user_id();
    if ($user_id > 0) {
        return intval($wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}vs_votes 
            WHERE (device_id = %s OR (user_id > 0 AND user_id = %d)) AND is_valid = 1",
            $device_id, $user_id
        )));
    }

    return intval($wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}vs_votes 
        WHERE device_id = %s AND is_valid = 1",
        $device_id
    )));
}

function vs_get_filter_info($atts = []) {
    $info = [];
    if (!empty($atts['type'])) $info[] = 'tipo "' . esc_html($atts['type']) . '"';
    if (!empty($atts['category'])) $info[] = 'categoría "' . esc_html($atts['category']) . '"';
    return empty($info) ? 'generales' : implode(' y ', $info);
}

// ============================================
// 8. SHORTCODES (voting_form y voting_results)
// ============================================

add_shortcode('voting_form', 'vs_voting_form');
function vs_voting_form($atts = []) {
    global $wpdb;
    
    if (get_option('vs_enable_token_security', false) && !vs_validate_token_access()) {
        return '<div class="vs-closed">' . esc_html(vs_front_text('unauthorized')) . '</div>';
    }
    
    if (!vs_is_ip_allowed()) {
        return '<div class="vs-closed">' . esc_html(vs_front_text('ip_denied')) . '</div>';
    }
    
    if (get_option('vs_require_registered_user', false) && !is_user_logged_in()) {
        return '<div class="vs-closed">' . esc_html(vs_front_text('login_required')) . ' <a href="' . esc_url(wp_login_url(get_permalink())) . '">' . esc_html(vs_front_text('login')) . '</a></div>';
    }
    
    $atts = shortcode_atts(['type' => '', 'category' => '', 'limit' => 0], $atts);
    $is_open = vs_check_voting_status();
    $closed_show_participants = (bool) get_option('vs_closed_show_participants', false);
    
    // Si está cerrado y no se deben mostrar participantes
    if (!$is_open && !$closed_show_participants) {
        return '<div class="vs-closed">' . vs_get_voting_status_message() . '</div>';
    }
    
    if (!vs_check_voter_limit()) return '<div class="vs-closed">' . esc_html(sprintf(vs_front_text('voter_limit'), (int) get_option('vs_max_voters', 0))) . '</div>';

    $where = [];
    $params = [];
    if (!empty($atts['type'])) { $where[] = "type = %s"; $params[] = $atts['type']; }
    if (!empty($atts['category'])) { $where[] = "category = %s"; $params[] = $atts['category']; }
    $where_clause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
    $query = "SELECT * FROM {$wpdb->prefix}vs_options $where_clause ORDER BY id ASC";
    if (!empty($params)) $query = $wpdb->prepare($query, $params);
    $options = $wpdb->get_results($query);
    if (empty($options)) return '<p>' . esc_html(vs_front_text('no_options')) . '</p>';

    $device_id = is_admin() ? 'admin_preview' : vs_get_device_id();
	$limit_info = vs_get_limit_info($atts);
	$effective_limit = $limit_info['limit'];
	$voted_count = vs_get_voted_count_by_scope($device_id, $limit_info['scope'], $limit_info['scope_value'] ?? '');
	$remaining_votes = max(0, $effective_limit - $voted_count);
    
    $voting_mode = get_option('vs_voting_mode', 'advanced');

    // Si no hay votos restantes y no estamos en admin y la votación está abierta
    if ($remaining_votes <= 0 && !is_admin() && $is_open) {
        $filter_text = '';
        if ($voting_mode !== 'simple') {
            if (!empty($atts['type']) && !empty($atts['category'])) {
                $filter_text = ' para tipo "' . esc_html($atts['type']) . '" y categoría "' . esc_html($atts['category']) . '"';
            } elseif (!empty($atts['type'])) {
                $filter_text = ' para tipo "' . esc_html($atts['type']) . '"';
            } elseif (!empty($atts['category'])) {
                $filter_text = ' para categoría "' . esc_html($atts['category']) . '"';
            }
        }
		return '<div class="vs-closed">' . esc_html(vs_front_text('already_voted')) . '</div>';
    }

    $voter_position = vs_get_voter_position();
    $max_voters = get_option('vs_max_voters', 0);
    $allow_multiple = ($voting_mode === 'simple') ? false : get_option('vs_allow_multiple_per_option', false);
    
    // Construir mensajes claros según el contexto
    $filter_text = '';
    if ($voting_mode !== 'simple') {
        if (!empty($atts['type']) && !empty($atts['category'])) {
            $filter_text = ' para tipo "' . esc_html($atts['type']) . '" y categoría "' . esc_html($atts['category']) . '"';
        } elseif (!empty($atts['type'])) {
            $filter_text = ' para tipo "' . esc_html($atts['type']) . '"';
        } elseif (!empty($atts['category'])) {
            $filter_text = ' para categoría "' . esc_html($atts['category']) . '"';
        }
    }
    
    $header_message = sprintf(vs_front_text('votes_available'), '<strong>' . (int) $voted_count . '</strong>', '<strong>' . (int) $effective_limit . '</strong>') . $filter_text;
    
    if (!$is_open) {
        $remaining_message = '<br><span class="vs-remaining-count" style="color: #6c757d;">' . esc_html(vs_front_text('closed')) . '</span>';
    } elseif ($remaining_votes > 0) {
        $remaining_message = '<br><span class="vs-remaining-count">' . sprintf(vs_front_text('votes_left'), '<strong>' . (int) $remaining_votes . '</strong>') . '</span>';
    } else {
        $remaining_message = '<br><span class="vs-remaining-count" style="color: #dc3545;">' . sprintf(vs_front_text('votes_used'), $filter_text) . '</span>';
    }

    // ID único para el mensaje de este shortcode
    $message_id = 'vs-message-' . uniqid();
    
    ob_start();
    ?>
    <div class="vs-container" data-limit="<?php echo $remaining_votes; ?>" data-max="<?php echo $effective_limit; ?>" data-message-id="<?php echo $message_id; ?>" data-is-open="<?php echo $is_open ? '1' : '0'; ?>" data-show-closed="<?php echo $closed_show_participants ? '1' : '0'; ?>" data-voting-mode="<?php echo esc_attr($voting_mode); ?>">
        <?php if (!$is_open && $closed_show_participants): ?>
            <div class="vs-closed vs-closed-banner" style="margin-bottom: 20px;">
                <strong><?php echo esc_html(vs_front_text('closed')); ?>:</strong> <?php echo vs_get_voting_status_message(); ?>
            </div>
        <?php endif; ?>
        <div class="vs-header">
            <?php if ($max_voters > 0): ?>
                <p class="vs-voter-info"><?php echo esc_html(vs_front_text('voter_number')); ?> #<?php echo (int) $voter_position; ?> <?php echo esc_html(vs_front_text('of')); ?> <?php echo (int) $max_voters; ?></p>
            <?php endif; ?>
            <p class="vs-remaining">
                <?php echo $header_message; ?>
                <?php echo $remaining_message; ?>
            </p>
        </div>
        <div class="vs-grid">
            <?php foreach ($options as $opt): 
                // Verificar si puede votar por esta opción
                $can_vote = vs_can_vote_for_option($opt->id, $device_id);
                
                // Si permite múltiples votos, obtener el contador
                $user_votes_for_option = 0;
                if ($allow_multiple) {
                    $curr_user_id = get_current_user_id();
                    if ($curr_user_id > 0) {
                        $user_votes_for_option = $wpdb->get_var($wpdb->prepare(
                            "SELECT COUNT(*) FROM {$wpdb->prefix}vs_votes 
                            WHERE (device_id = %s OR (user_id > 0 AND user_id = %d)) AND option_id = %d AND is_valid = 1",
                            $device_id, $curr_user_id, $opt->id
                        ));
                    } else {
                        $user_votes_for_option = $wpdb->get_var($wpdb->prepare(
                            "SELECT COUNT(*) FROM {$wpdb->prefix}vs_votes 
                            WHERE device_id = %s AND option_id = %d AND is_valid = 1",
                            $device_id, $opt->id
                        ));
                    }
                }
                
                // Determinar si el botón debe estar deshabilitado
                $is_disabled = false;
                $button_text = vs_front_text('vote');
                $badge_text = '';
                
                if (!$is_open) {
                    $is_disabled = true;
                    $button_text = vs_front_text('voting_closed_btn');
                } elseif ($allow_multiple) {
                    // Con múltiples votos permitidos, solo se deshabilita si no quedan votos
                    $is_disabled = ($remaining_votes <= 0);
                    if ($user_votes_for_option > 0) {
                        $badge_text = sprintf(vs_front_text('vote_badge'), (int) $user_votes_for_option);
                    }
                } else {
                    // Sin múltiples votos, deshabilitar si ya votó o no quedan votos
                    $has_voted = !$can_vote;
                    $is_disabled = ($has_voted || $remaining_votes <= 0);
                    $button_text = $has_voted ? vs_front_text('voted') : vs_front_text('vote');
                    if ($has_voted) {
                        $badge_text = vs_front_text('vote_badge_one');
                    }
                }
            ?>
                <div class="vs-item" data-id="<?php echo $opt->id; ?>">
                    <?php if ($opt->photo_url): ?>
                        <img src="<?php echo esc_url($opt->photo_url); ?>" alt="<?php echo esc_attr($opt->title); ?>">
                    <?php endif; ?>
                    <h3><?php echo esc_html($opt->title); ?></h3>
                    <?php if ($opt->type): ?>
                        <p class="vs-type"><?php echo esc_html($opt->type); ?></p>
                    <?php endif; ?>
                    <?php if ($opt->category): ?>
                        <span class="vs-category"><?php echo esc_html($opt->category); ?></span>
                    <?php endif; ?>
                    <button class="vs-vote-btn" data-id="<?php echo $opt->id; ?>" <?php echo $is_disabled ? 'disabled' : ''; ?>>
                        <?php echo esc_html($button_text); ?>
                    </button>
                    <?php if (!empty($badge_text)): ?>
                        <span class="vs-voted-badge"><?php echo esc_html($badge_text); ?></span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div id="<?php echo $message_id; ?>" class="vs-message"></div>
    </div>
    <?php
    return ob_get_clean();
}

add_shortcode('voting_results', 'vs_voting_results');
function vs_voting_results($atts = []) {
    global $wpdb;
    
    if (get_option('vs_enable_token_security', false) && !vs_validate_token_access()) {
        return '<div class="vs-closed">' . esc_html(vs_front_text('unauthorized')) . '</div>';
    }
    
    $atts = shortcode_atts([
        'type' => '',
        'category' => '',
        'view' => get_option('vs_results_default_view', 'detailed'),
        'show_percentage' => get_option('vs_results_show_percentage', true),
        'show_numbers' => get_option('vs_results_show_numbers', true),
        'show_images' => get_option('vs_results_show_images', true),
        'limit' => 0
    ], $atts);
    
    $atts['show_percentage'] = filter_var($atts['show_percentage'], FILTER_VALIDATE_BOOLEAN);
    $atts['show_numbers'] = filter_var($atts['show_numbers'], FILTER_VALIDATE_BOOLEAN);
    $atts['show_images'] = filter_var($atts['show_images'], FILTER_VALIDATE_BOOLEAN);
    
    $where = [];
    $params = [];
    if (!empty($atts['type'])) { $where[] = "o.type = %s"; $params[] = $atts['type']; }
    if (!empty($atts['category'])) { $where[] = "o.category = %s"; $params[] = $atts['category']; }
    $where_clause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
    $query = "
        SELECT o.id, o.title, o.type, o.category, o.photo_url, COUNT(v.id) as votes 
        FROM {$wpdb->prefix}vs_options o
        LEFT JOIN {$wpdb->prefix}vs_votes v ON o.id = v.option_id AND v.is_valid = 1
        $where_clause
        GROUP BY o.id
        ORDER BY votes DESC
    ";
    if (!empty($params)) $query = $wpdb->prepare($query, $params);
    if (!empty($atts['limit']) && is_numeric($atts['limit']) && $atts['limit'] > 0) {
        $query .= " LIMIT " . intval($atts['limit']);
    }
    $results = $wpdb->get_results($query);
    $total_votes = array_sum(array_column($results, 'votes'));
    if (empty($results) || $total_votes == 0) return '<p class="sin-votos-todavia">' . esc_html(vs_front_text('no_votes_yet')) . '</p>';

    ob_start();
    ?>
    <div class="vs-results">
        <h3><?php echo esc_html(vs_front_text('results')); ?></h3>
        <p><?php echo esc_html(sprintf(vs_front_text('total_votes'), (int) $total_votes)); ?></p>
        <p class="votantes-unicos"><?php echo esc_html(sprintf(vs_front_text('unique_voters'), (int) $wpdb->get_var("SELECT COUNT(DISTINCT device_id) FROM {$wpdb->prefix}vs_votes WHERE is_valid = 1"))); ?></p>
        <ul class="vs-results-list">
            <?php foreach ($results as $r): 
                $percentage = ($total_votes > 0) ? ($r->votes / $total_votes * 100) : 0;
            ?>
                <li>
                    <?php if ($atts['show_images'] && !empty($r->photo_url)): ?>
                        <div class="vs-result-image">
                            <img src="<?php echo esc_url($r->photo_url); ?>" alt="<?php echo esc_attr($r->title); ?>">
                        </div>
                    <?php endif; ?>
                    <div class="vs-result-content">
                        <strong><?php echo esc_html($r->title); ?></strong>
                        <div class="vs-result-meta">
                            <?php if ($atts['show_numbers']): ?>
                                <span class="vs-vote-count"><?php echo esc_html(sprintf(vs_front_text('votes'), (int) $r->votes)); ?></span>
                            <?php endif; ?>
                            <?php if ($atts['show_percentage']): ?>
                                <span class="vs-percentage">(<?php echo round($percentage, 1); ?>%)</span>
                            <?php endif; ?>
                        </div>
                        <div class="vs-bar">
                            <div class="vs-bar-fill" style="width: <?php echo $percentage; ?>%"></div>
                        </div>
                        <?php if ($atts['view'] == 'detailed'): ?>
                            <div class="vs-meta">
                                <?php if ($r->type): ?>
                                    <small><?php echo esc_html(sprintf(vs_front_text('type'), $r->type)); ?></small>
                                <?php endif; ?>
                                <?php if ($r->category): ?>
                                    <small><?php echo esc_html(sprintf(vs_front_text('category'), $r->category)); ?></small>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
    return ob_get_clean();
}

// ============================================
// 9. FUNCIONES DE VOTACIÓN
// ============================================

function vs_check_voting_status() {
    $is_open = get_option('vs_voting_open', false);
    if (get_option('vs_voting_auto_open', false)) {
        $start = get_option('vs_voting_start', '');
        if (!empty($start) && current_time('timestamp') >= strtotime($start)) {
            $is_open = true;
            update_option('vs_voting_open', true);
        }
    }
    if (get_option('vs_voting_auto_close', false)) {
        $end = get_option('vs_voting_end', '');
        if (!empty($end) && current_time('timestamp') >= strtotime($end)) {
            $is_open = false;
            update_option('vs_voting_open', false);
        }
    }
    return (bool) $is_open;
}

function vs_get_voting_status_message() {
    $end = get_option('vs_voting_end', '');
    $start = get_option('vs_voting_start', '');
    $current_time = current_time('timestamp');
    if (!empty($start) && $current_time < strtotime($start)) {
        return esc_html(sprintf(vs_front_text('starts'), date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($start))));
    }
    if (!empty($end) && $current_time >= strtotime($end)) {
        return esc_html(sprintf(vs_front_text('ended'), date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($end))));
    }
    return esc_html(vs_front_text('closed'));
}

// ============================================
// 10. AJAX - Procesar voto (CORREGIDO - Sin refresco automático que anule token)
// ============================================
add_action('wp_ajax_vs_vote', 'vs_process_vote');
add_action('wp_ajax_nopriv_vs_vote', 'vs_process_vote');
function vs_process_vote() {
    if (!wp_verify_nonce($_POST['nonce'], 'vs_vote_nonce')) {
        wp_die('Error de seguridad');
    }

    global $wpdb;
    $option_id = intval($_POST['option_id']);
    
    if (!vs_is_ip_allowed()) {
        wp_send_json_error('Tu dirección IP no está autorizada para votar.');
    }
    
    if (get_option('vs_require_registered_user', false) && !is_user_logged_in()) {
        wp_send_json_error('Debes iniciar sesión para votar.');
    }
    
    $option = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}vs_options WHERE id = %d",
        $option_id
    ));
    if (!$option) wp_send_json_error('Opción no válida');
    if (!vs_check_voting_status()) wp_send_json_error(vs_front_text('closed'));
    if (!vs_check_voter_limit()) wp_send_json_error('Se ha alcanzado el límite de votantes');

    $device_id = vs_get_device_id();

    $voting_mode = get_option('vs_voting_mode', 'advanced');
    if ($voting_mode === 'simple') {
        // En modo simple: estrictamente 1 voto por dispositivo/usuario
        $global_voted = vs_get_voted_count_by_scope($device_id, 'device');
        if ($global_voted >= 1) {
            wp_send_json_error(vs_front_text('already_voted'));
        }
        if (!vs_can_vote_for_option($option_id, $device_id)) {
            wp_send_json_error('Ya has votado por esta opción');
        }
    } else {
        // ============================================
        // VERIFICAR LÍMITE GLOBAL POR DISPOSITIVO (MODO AVANZADO)
        // ============================================
        $global_limit = intval(get_option('vs_votes_per_device', 1));
        if ($global_limit > 0) {
            $global_voted = vs_get_voted_count_by_scope($device_id, 'device');
            if ($global_voted >= $global_limit) {
                wp_send_json_error('Has agotado tus ' . $global_limit . ' votos totales permitidos.');
            }
        }

        // ============================================
        // VERIFICAR SI YA VOTÓ POR ESTA OPCIÓN
        // ============================================
        if (!vs_can_vote_for_option($option_id, $device_id)) {
            wp_send_json_error('Ya has votado por esta opción');
        }

        // ============================================
        // VERIFICAR LÍMITE POR TIPO
        // ============================================
        if (!empty($option->type)) {
            $type_limit = get_option('vs_votes_per_type', '');
            if (!empty($type_limit) && is_numeric($type_limit) && intval($type_limit) > 0) {
                $type_voted = vs_get_voted_count_by_scope($device_id, 'type', $option->type);
                if ($type_voted >= intval($type_limit)) {
                    wp_send_json_error('Has agotado tus ' . $type_limit . ' votos para tipo "' . esc_html($option->type) . '"');
                }
            }
        }

        // ============================================
        // VERIFICAR LÍMITE POR CATEGORÍA
        // ============================================
        if (!empty($option->category)) {
            $category_limit = get_option('vs_votes_per_category', '');
            if (!empty($category_limit) && is_numeric($category_limit) && intval($category_limit) > 0) {
                $category_voted = vs_get_voted_count_by_scope($device_id, 'category', $option->category);
                if ($category_voted >= intval($category_limit)) {
                    wp_send_json_error('Has agotado tus ' . $category_limit . ' votos para categoría "' . esc_html($option->category) . '"');
                }
            }
        }
    }
    
    // ============================================
    // INSERTAR VOTO
    // ============================================
    $param_name = get_option('vs_token_param_name', 'token-votacion');
    $token = isset($_GET[$param_name]) ? sanitize_text_field($_GET[$param_name]) : '';

    $wpdb->insert(
        $wpdb->prefix . 'vs_votes',
        [
            'option_id' => $option_id,
            'device_id' => $device_id,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'voter_session' => vs_get_voter_session(),
            'token' => $token,
            'user_id' => get_current_user_id(),
            'is_valid' => 1,
            'voted_at' => current_time('mysql')
        ]
    );

    // Marcar token como usado
    $new_token = '';
    if (get_option('vs_token_single_use', true) && !empty($token)) {
        $wpdb->update(
            $wpdb->prefix . 'vs_tokens',
            ['is_used' => 1, 'used_at' => current_time('mysql')],
            ['token' => $token]
        );
        
        // Verificar si quedan votos
        $global_voted_after = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}vs_votes 
            WHERE device_id = %s AND is_valid = 1",
            $device_id
        ));
        $remaining_global = $global_limit > 0 ? max(0, $global_limit - intval($global_voted_after)) : 999;
        
        if ($remaining_global > 0) {
            $new_token = vs_create_token_for_device($device_id);
        }
    }

    $refresh = get_option('vs_refresh_after_vote', true);
    
    $response_data = [
        'message' => 'Vot registrat correctament!',
        'refresh' => $refresh
    ];
    
    if (!empty($new_token)) {
        $response_data['new_token'] = $new_token;
        $response_data['token_param'] = $param_name;
    }
    
	// Verificar todos los votos de este dispositivo
	$todos_los_votos = $wpdb->get_results($wpdb->prepare(
		"SELECT id, option_id, device_id, ip, is_valid, voted_at 
		 FROM {$wpdb->prefix}vs_votes 
		 WHERE ip = %s OR device_id = %s
		 ORDER BY voted_at DESC",
		$_SERVER['REMOTE_ADDR'], $device_id
	));
	error_log('Todos los votos del dispositivo/IP: ' . print_r($todos_los_votos, true));
	
    wp_send_json_success($response_data);
}

// ============================================
// 10.1 AJAX - Estado dinámico del votante (Compatibilidad con plugins de Caché)
// ============================================
add_action('wp_ajax_vs_get_voter_state', 'vs_ajax_get_voter_state');
add_action('wp_ajax_nopriv_vs_get_voter_state', 'vs_ajax_get_voter_state');
function vs_ajax_get_voter_state() {
    global $wpdb;
    $device_id = vs_get_device_id();
    $is_open = vs_check_voting_status();
    $closed_show_participants = (bool) get_option('vs_closed_show_participants', false);
    $voting_mode = get_option('vs_voting_mode', 'advanced');
    
    $atts = [
        'type' => isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '',
        'category' => isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '',
        'limit' => isset($_POST['limit']) ? intval($_POST['limit']) : 0,
    ];
    $limit_info = vs_get_limit_info($atts);
    $effective_limit = $limit_info['limit'];
    $voted_count = vs_get_voted_count_by_scope($device_id, $limit_info['scope'], $limit_info['scope_value'] ?? '');
    $remaining_votes = max(0, $effective_limit - $voted_count);
    
    $allow_multiple = ($voting_mode === 'simple') ? false : get_option('vs_allow_multiple_per_option', false);
    
    // Obtener los votos ya emitidos por este votante
    $user_id = get_current_user_id();
    if ($user_id > 0) {
        $voted_rows = $wpdb->get_results($wpdb->prepare(
            "SELECT option_id, COUNT(*) as count FROM {$wpdb->prefix}vs_votes 
            WHERE (device_id = %s OR (user_id > 0 AND user_id = %d)) AND is_valid = 1 
            GROUP BY option_id",
            $device_id, $user_id
        ));
    } else {
        $voted_rows = $wpdb->get_results($wpdb->prepare(
            "SELECT option_id, COUNT(*) as count FROM {$wpdb->prefix}vs_votes 
            WHERE device_id = %s AND is_valid = 1 
            GROUP BY option_id",
            $device_id
        ));
    }
    
    $voted_option_ids = [];
    $votes_by_option = [];
    foreach ($voted_rows as $row) {
        $voted_option_ids[] = intval($row->option_id);
        $votes_by_option[intval($row->option_id)] = intval($row->count);
    }
    
    $filter_text = '';
    if ($voting_mode !== 'simple') {
        if (!empty($atts['type']) && !empty($atts['category'])) {
            $filter_text = ' para tipo "' . esc_html($atts['type']) . '" y categoría "' . esc_html($atts['category']) . '"';
        } elseif (!empty($atts['type'])) {
            $filter_text = ' para tipo "' . esc_html($atts['type']) . '"';
        } elseif (!empty($atts['category'])) {
            $filter_text = ' para categoría "' . esc_html($atts['category']) . '"';
        }
    }
    
    $header_message = sprintf(vs_front_text('votes_available'), '<strong>' . (int) $voted_count . '</strong>', '<strong>' . (int) $effective_limit . '</strong>') . $filter_text;
    
    if (!$is_open) {
        $remaining_message = '<br><span class="vs-remaining-count" style="color: #6c757d;">' . esc_html(vs_front_text('closed')) . '</span>';
    } elseif ($remaining_votes > 0) {
        $remaining_message = '<br><span class="vs-remaining-count">' . sprintf(vs_front_text('votes_left'), '<strong>' . (int) $remaining_votes . '</strong>') . '</span>';
    } else {
        $remaining_message = '<br><span class="vs-remaining-count" style="color: #dc3545;">' . sprintf(vs_front_text('votes_used'), $filter_text) . '</span>';
    }
    
    wp_send_json_success([
        'fresh_nonce' => wp_create_nonce('vs_vote_nonce'),
        'is_open' => $is_open,
        'closed_show_participants' => $closed_show_participants,
        'closed_status_message' => vs_get_voting_status_message(),
        'closed_btn_text' => vs_front_text('voting_closed_btn'),
        'voting_mode' => $voting_mode,
        'effective_limit' => $effective_limit,
        'voted_count' => $voted_count,
        'remaining_votes' => $remaining_votes,
        'voted_option_ids' => $voted_option_ids,
        'votes_by_option' => $votes_by_option,
        'allow_multiple' => $allow_multiple,
        'header_message' => $header_message,
        'remaining_message' => $remaining_message,
        'already_voted_message' => vs_front_text('already_voted'),
        'voted_btn_text' => vs_front_text('voted'),
        'vote_badge_text' => vs_front_text('vote_badge_one'),
        'is_admin' => is_admin() || current_user_can('manage_options')
    ]);
}

// ============================================
// 11. ADMINISTRACIÓN - MENÚS
// ============================================
add_action('admin_menu', 'vs_admin_menu');
function vs_admin_menu() {
    add_menu_page(
        'Votaciones',
        'Votaciones',
        'manage_options',
        'vs-voting',
        'vs_admin_page',
        'dashicons-chart-bar',
        30
    );
    add_submenu_page(
        'vs-voting',
        'Añadir Opción',
        'Añadir Opción',
        'manage_options',
        'vs-add-option',
        'vs_add_option_page'
    );
    add_submenu_page(
        'vs-voting',
        'Configuración',
        'Configuración',
        'manage_options',
        'vs-settings',
        'vs_settings_page'
    );
    add_submenu_page(
        'vs-voting',
        'Seguridad',
        'Seguridad',
        'manage_options',
        'vs-security',
        'vs_security_page'
    );
    add_submenu_page(
        'vs-voting',
        'Auditoría',
        'Auditoría',
        'manage_options',
        'vs-audit',
        'vs_audit_page'
    );
    add_submenu_page(
        'vs-voting',
        'Vista Previa',
        'Vista Previa',
        'manage_options',
        'vs-preview',
        'vs_preview_page'
    );
    add_submenu_page(
        'vs-voting',
        'Resetear Tokens',
        'Resetear Tokens',
        'manage_options',
        'vs-reset-tokens',
        'vs_reset_tokens_page'
    );
}

// ============================================
// 12. ADMIN - PÁGINA PRINCIPAL
// ============================================
function vs_admin_page() {
    global $wpdb;
    
    if (isset($_POST['vs_delete_all_votes'])) {
        $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}vs_votes");
        echo '<div class="notice notice-success"><p>Todos los votos eliminados</p></div>';
    }
    if (isset($_POST['vs_delete_all_options'])) {
        $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}vs_options");
        $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}vs_votes");
        echo '<div class="notice notice-success"><p>Todas las opciones y votos eliminados</p></div>';
    }
    if (isset($_POST['vs_delete_option']) && isset($_POST['option_id'])) {
        $id = intval($_POST['option_id']);
        $wpdb->delete($wpdb->prefix . 'vs_options', ['id' => $id]);
        $wpdb->delete($wpdb->prefix . 'vs_votes', ['option_id' => $id]);
        echo '<div class="notice notice-success"><p>Opción eliminada</p></div>';
    }

    $options = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}vs_options ORDER BY id ASC");
    $votes = $wpdb->get_results("
        SELECT v.*, o.title as option_title, o.type, o.category
        FROM {$wpdb->prefix}vs_votes v
        LEFT JOIN {$wpdb->prefix}vs_options o ON v.option_id = o.id
        ORDER BY v.voted_at DESC
    ");
    $unique_voters = $wpdb->get_var("SELECT COUNT(DISTINCT device_id) FROM {$wpdb->prefix}vs_votes");
    ?>
    <div class="wrap">
        <h1>Gestión de Votaciones</h1>
        
        <div class="vs-admin-box vs-shortcodes-info">
            <h2>Shortcodes disponibles</h2>
            <div class="vs-shortcode-grid">
                <div class="vs-shortcode-item">
                    <code>[voting_form]</code>
                    <span>Formulario de votación</span>
                    <span class="vs-shortcode-attrs">Atributos: type, category, limit (límite de votos)</span>
                </div>
                <div class="vs-shortcode-item">
                    <code>[voting_results]</code>
                    <span>Resultados de la votación</span>
                    <span class="vs-shortcode-attrs">Atributos: type, category, view, show_percentage, show_numbers, show_images, limit</span>
                </div>
            </div>
            <p class="vs-shortcode-tip">Ejemplo: <code>[voting_form type="Cantante" category="Instrumental" limit="3"]</code></p>
        </div>
        
        <div class="vs-admin-box">
            <h2>Estadísticas</h2>
            <ul>
                <li><strong>Votos totales:</strong> <?php echo count($votes); ?></li>
                <li><strong>Votantes únicos:</strong> <?php echo $unique_voters; ?></li>
                <li><strong>Opciones:</strong> <?php echo count($options); ?></li>
            </ul>
        </div>
        
        <div class="vs-admin-box">
            <h2>Opciones disponibles (<?php echo count($options); ?>)</h2>
            <form method="post" style="margin-bottom: 15px;">
                <button type="submit" name="vs_delete_all_options" class="button button-danger" onclick="return confirm('¿Eliminar TODAS las opciones y votos?')">
                    Eliminar todas las opciones
                </button>
            </form>
            
            <table class="wp-list-table widefat fixed vs-sortable-table">
                <thead>
                    <tr>
                        <th class="sortable" data-sort="id">ID <span class="sort-indicator">↕</span></th>
                        <th>Imagen</th>
                        <th class="sortable" data-sort="title">Título <span class="sort-indicator">↕</span></th>
                        <th class="sortable" data-sort="type">Tipo <span class="sort-indicator">↕</span></th>
                        <th class="sortable" data-sort="category">Categoría <span class="sort-indicator">↕</span></th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($options as $opt): ?>
                    <tr data-id="<?php echo $opt->id; ?>" data-title="<?php echo esc_attr($opt->title); ?>" data-type="<?php echo esc_attr($opt->type); ?>" data-category="<?php echo esc_attr($opt->category); ?>">
                        <td><?php echo $opt->id; ?></td>
                        <td>
                            <?php if (!empty($opt->photo_url)): ?>
                                <img src="<?php echo esc_url($opt->photo_url); ?>" alt="<?php echo esc_attr($opt->title); ?>" style="width:50px; height:50px; border-radius:50%; object-fit:cover; border:2px solid #f0f0f0;">
                            <?php else: ?>
                                <span style="display:inline-block; width:50px; height:50px; border-radius:50%; background:#f0f0f0; text-align:center; line-height:50px; color:#999; font-size:12px;">Sin foto</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html($opt->title); ?></td>
                        <td><?php echo esc_html($opt->type); ?></td>
                        <td><?php echo esc_html($opt->category); ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="option_id" value="<?php echo $opt->id; ?>">
                                <button type="submit" name="vs_delete_option" class="button button-small" onclick="return confirm('¿Eliminar esta opción?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p class="description">Haz clic en los encabezados de las columnas para ordenar</p>
        </div>
    </div>
    <?php
    // Sortable tables script
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.vs-sortable-table').forEach(function(table) {
            const headers = table.querySelectorAll('th.sortable');
            let sortDirection = {};
            headers.forEach(function(header) {
                header.style.cursor = 'pointer';
                header.addEventListener('click', function() {
                    const column = this.dataset.sort;
                    const tbody = table.querySelector('tbody');
                    const rows = Array.from(tbody.querySelectorAll('tr'));
                    if (!sortDirection[column]) sortDirection[column] = 'asc';
                    else if (sortDirection[column] === 'asc') sortDirection[column] = 'desc';
                    else sortDirection[column] = 'asc';
                    const direction = sortDirection[column];
                    headers.forEach(function(h) {
                        h.querySelector('.sort-indicator').textContent = '↕';
                    });
                    this.querySelector('.sort-indicator').textContent = direction === 'asc' ? '↑' : '↓';
                    rows.sort(function(a, b) {
                        let valA = a.dataset[column] || a.querySelector('td:first-child')?.textContent || '';
                        let valB = b.dataset[column] || b.querySelector('td:first-child')?.textContent || '';
                        if (column === 'id') {
                            valA = parseInt(valA); valB = parseInt(valB);
                            return direction === 'asc' ? valA - valB : valB - valA;
                        }
                        valA = valA.toString().toLowerCase();
                        valB = valB.toString().toLowerCase();
                        return direction === 'asc' ? valA.localeCompare(valB) : valB.localeCompare(valA);
                    });
                    rows.forEach(function(row) { tbody.appendChild(row); });
                });
            });
        });
    });
    </script>
    <?php
}

// ============================================
// 13. ADMIN - CONFIGURACIÓN
// ============================================
function vs_settings_page() {
    global $wpdb;
    
    if (isset($_POST['vs_save_settings'])) {
        update_option('vs_voting_open', isset($_POST['vs_voting_open']));
        update_option('vs_closed_show_participants', isset($_POST['vs_closed_show_participants']));
        update_option('vs_show_breakdown', isset($_POST['vs_show_breakdown']));
        update_option('vs_voting_auto_open', isset($_POST['vs_voting_auto_open']));
        update_option('vs_voting_auto_close', isset($_POST['vs_voting_auto_close']));
        update_option('vs_voting_start', sanitize_text_field($_POST['vs_voting_start']));
        update_option('vs_voting_end', sanitize_text_field($_POST['vs_voting_end']));
        
        $voting_mode = (isset($_POST['vs_voting_mode']) && $_POST['vs_voting_mode'] === 'simple') ? 'simple' : 'advanced';
        update_option('vs_voting_mode', $voting_mode);
        if ($voting_mode === 'simple') {
            update_option('vs_votes_per_device', 1);
            update_option('vs_votes_per_category', '');
            update_option('vs_votes_per_type', '');
            update_option('vs_allow_multiple_per_option', false);
        } else {
            update_option('vs_votes_per_device', intval($_POST['vs_votes_per_device']));
            update_option('vs_votes_per_category', sanitize_text_field($_POST['vs_votes_per_category']));
            update_option('vs_votes_per_type', sanitize_text_field($_POST['vs_votes_per_type']));
            update_option('vs_allow_multiple_per_option', isset($_POST['vs_allow_multiple_per_option']));
        }
        
        update_option('vs_results_default_view', sanitize_text_field($_POST['vs_results_default_view']));
        update_option('vs_results_show_percentage', isset($_POST['vs_results_show_percentage']));
        update_option('vs_results_show_numbers', isset($_POST['vs_results_show_numbers']));
        update_option('vs_refresh_after_vote', isset($_POST['vs_refresh_after_vote']));
        update_option('vs_max_voters', intval($_POST['vs_max_voters']));
        update_option('vs_results_show_images', isset($_POST['vs_results_show_images']));
        update_option('vs_require_registered_user', isset($_POST['vs_require_registered_user']));
        echo '<div class="notice notice-success"><p>Configuración guardada</p></div>';
    }
    
    $is_open = get_option('vs_voting_open', false);
    $closed_show_participants = get_option('vs_closed_show_participants', false);
    $voting_mode = get_option('vs_voting_mode', 'advanced');
    $show_breakdown = get_option('vs_show_breakdown', true);
    $auto_open = get_option('vs_voting_auto_open', false);
    $auto_close = get_option('vs_voting_auto_close', false);
    $start = get_option('vs_voting_start', '');
    $end = get_option('vs_voting_end', '');
    $votes_per_device = get_option('vs_votes_per_device', 1);
    $votes_per_category = get_option('vs_votes_per_category', '');
    $votes_per_type = get_option('vs_votes_per_type', '');
    $default_view = get_option('vs_results_default_view', 'detailed');
    $show_percentage = get_option('vs_results_show_percentage', true);
    $show_numbers = get_option('vs_results_show_numbers', true);
    $refresh = get_option('vs_refresh_after_vote', true);
    $allow_multiple = get_option('vs_allow_multiple_per_option', false);
    $max_voters = get_option('vs_max_voters', 0);
    $show_images = get_option('vs_results_show_images', true);
    $require_registered = get_option('vs_require_registered_user', false);
    
    $types = $wpdb->get_col("SELECT DISTINCT type FROM {$wpdb->prefix}vs_options WHERE type != '' ORDER BY type ASC");
    $categories = $wpdb->get_col("SELECT DISTINCT category FROM {$wpdb->prefix}vs_options WHERE category != '' ORDER BY category ASC");
    $unique_voters = $wpdb->get_var("SELECT COUNT(DISTINCT device_id) FROM {$wpdb->prefix}vs_votes");
    ?>
    <div class="wrap">
        <h1>Configuración del Sistema de Votaciones</h1>
        
        <form method="post">
            <div class="vs-admin-box">
                <h2>Control de Votaciones</h2>
                <table class="form-table">
                    <tr>
                        <th>Estado actual</th>
                        <td>
                            <label>
                                <input type="checkbox" name="vs_voting_open" <?php checked($is_open); ?>>
                                Votaciones abiertas
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th>Participantes al cerrar</th>
                        <td>
                            <label>
                                <input type="checkbox" name="vs_closed_show_participants" <?php checked($closed_show_participants); ?>>
                                Mostrar participantes cuando las votaciones estén cerradas
                            </label>
                            <p class="description">Mantiene visible la lista de participantes con un aviso de cierre y los botones deshabilitados ("Votaciones cerradas").</p>
                        </td>
                    </tr>
                    <tr>
                        <th>Apertura automática</th>
                        <td>
                            <label>
                                <input type="checkbox" name="vs_voting_auto_open" <?php checked($auto_open); ?>>
                                Abrir automáticamente
                            </label>
                            <input type="datetime-local" name="vs_voting_start" value="<?php echo esc_attr($start); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>Cierre automático</th>
                        <td>
                            <label>
                                <input type="checkbox" name="vs_voting_auto_close" <?php checked($auto_close); ?>>
                                Cerrar automáticamente
                            </label>
                            <input type="datetime-local" name="vs_voting_end" value="<?php echo esc_attr($end); ?>">
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="vs-admin-box">
                <h2>Modo de Votación y Límites</h2>
                <table class="form-table">
                    <tr>
                        <th>Modo de votación</th>
                        <td>
                            <div class="vs-mode-switch-wrapper">
                                <label class="vs-mode-card <?php echo $voting_mode === 'simple' ? 'active' : ''; ?>">
                                    <input type="radio" name="vs_voting_mode" value="simple" <?php checked($voting_mode, 'simple'); ?> class="vs-mode-radio">
                                    <span class="vs-mode-card-title">⚡ Modo Simple</span>
                                    <span class="vs-mode-card-desc">1 voto por dispositivo indiferente de categorías o tipos. Las opciones por categoría y tipo se anulan y se ocultan.</span>
                                </label>
                                <label class="vs-mode-card <?php echo $voting_mode === 'advanced' ? 'active' : ''; ?>">
                                    <input type="radio" name="vs_voting_mode" value="advanced" <?php checked($voting_mode, 'advanced'); ?> class="vs-mode-radio">
                                    <span class="vs-mode-card-title">⚙️ Modo Avanzado</span>
                                    <span class="vs-mode-card-desc">Permite configurar límites de votos por categoría, por tipo, y votos múltiples por opción.</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr class="vs-simple-only-notice" <?php echo $voting_mode === 'simple' ? '' : 'style="display:none;"'; ?>>
                        <th>Reglas del Modo Simple</th>
                        <td>
                            <div class="notice notice-info inline" style="margin: 0; padding: 12px 16px;">
                                <p style="margin: 0;"><strong>ℹ️ Modo Simple activo:</strong> Cada votante dispone de exactamente <strong>1 voto total</strong> para todo el concurso, sin distinción de categorías o tipos. Las configuraciones avanzadas de votos por categoría o tipo están anuladas y ocultas para simplificar la gestión.</p>
                            </div>
                        </td>
                    </tr>
                    <tr class="vs-advanced-field" <?php echo $voting_mode === 'simple' ? 'style="display:none;"' : ''; ?>>
                        <th>Votos por dispositivo</th>
                        <td>
                            <input type="number" name="vs_votes_per_device" value="<?php echo esc_attr($votes_per_device); ?>" min="0" max="100">
                            <p class="description">Número total de votos permitidos por dispositivo (0 = sin límite)</p>
                        </td>
                    </tr>
                    <tr class="vs-advanced-field" <?php echo $voting_mode === 'simple' ? 'style="display:none;"' : ''; ?>>
                        <th>Límite por categoría</th>
                        <td>
                            <input type="number" name="vs_votes_per_category" value="<?php echo esc_attr($votes_per_category); ?>" min="0" max="100" placeholder="Dejar vacío para sin límite">
                            <p class="description">Votos máximos por categoría (por dispositivo)</p>
                            <?php if (!empty($categories)): ?>
                                <p class="description">Categorías existentes: <?php echo implode(', ', array_map('esc_html', $categories)); ?></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr class="vs-advanced-field" <?php echo $voting_mode === 'simple' ? 'style="display:none;"' : ''; ?>>
                        <th>Límite por tipo</th>
                        <td>
                            <input type="number" name="vs_votes_per_type" value="<?php echo esc_attr($votes_per_type); ?>" min="0" max="100" placeholder="Dejar vacío para sin límite">
                            <p class="description">Votos máximos por tipo (por dispositivo)</p>
                            <?php if (!empty($types)): ?>
                                <p class="description">Tipos existentes: <?php echo implode(', ', array_map('esc_html', $types)); ?></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr class="vs-advanced-field" <?php echo $voting_mode === 'simple' ? 'style="display:none;"' : ''; ?>>
                        <th>Múltiples votos por opción</th>
                        <td>
                            <label>
                                <input type="checkbox" name="vs_allow_multiple_per_option" <?php checked($allow_multiple); ?>>
                                Permitir votar múltiples veces a la misma opción
                            </label>
                            <p class="description">Si está desactivado, solo se puede votar una vez por opción</p>
                        </td>
                    </tr>
                    <tr>
                        <th>Límite de votantes</th>
                        <td>
                            <input type="number" name="vs_max_voters" value="<?php echo esc_attr($max_voters); ?>" min="0" max="99999">
                            <p class="description">Número máximo de votantes únicos (0 = sin límite)</p>
                            <?php if ($unique_voters > 0): ?>
                                <p class="description"><strong>Votantes actuales:</strong> <?php echo $unique_voters; ?></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Requerir usuario registrado</th>
                        <td>
                            <label>
                                <input type="checkbox" name="vs_require_registered_user" <?php checked($require_registered); ?>>
                                Solo usuarios registrados pueden votar
                            </label>
                            <p class="description">Los votantes deben iniciar sesión en WordPress para votar</p>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="vs-admin-box vs-cache-guide-box">
                <h2>⚡ Rendimiento y Compatibilidad con Plugins de Caché</h2>
                <p>El plugin está optimizado para funcionar con plugins de caché de página como <strong>WP Rocket, WP Super Cache, LiteSpeed Cache, W3 Total Cache, FastCGI Cache</strong> y CDNs (Cloudflare).</p>
                <div class="vs-cache-columns">
                    <div class="vs-cache-col">
                        <h3>¿Cómo funciona con la caché?</h3>
                        <ul>
                            <li><strong>Caché estática de participantes:</strong> Los nombres, fotos, tipos y categorías de los concursantes se entregan desde la caché estática HTML para lograr tiempos de respuesta ultrarrápidos.</li>
                            <li><strong>Hidratación dinámica por AJAX:</strong> El estado personalizado de cada usuario (votos restantes, opciones ya votadas, avisos y botones deshabilitados) se consulta dinámicamente en segundo plano. Así ningún votante ve el estado en caché de otro.</li>
                            <li><strong>Renovación automática de Nonce:</strong> La llamada AJAX devuelve un token de seguridad renovado para que las páginas cacheadas durante días nunca fallen con errores de seguridad al votar.</li>
                        </ul>
                    </div>
                    <div class="vs-cache-col">
                        <h3>Recomendaciones de configuración</h3>
                        <ul>
                            <li><strong>admin-ajax.php:</strong> Asegúrate de que las llamadas a <code>/wp-admin/admin-ajax.php</code> no sean cacheadas como HTML (la gran mayoría de plugins ya las excluyen por defecto).</li>
                            <li><strong>Vaciado de caché:</strong> Si añades, editas o eliminas un participante, vacía la caché de la página donde esté el shortcode <code>[voting_form]</code> para que se actualicen las fotos y textos.</li>
                            <li><strong>Minificación JS:</strong> Puedes minificar los scripts de forma segura. Si usas "Delay JS Execution" (retardo de JavaScript), excluye <code>frontend.js</code> para que el estado de los votos se hidrate al instante.</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="vs-admin-box">
                <h2>Configuración de Resultados</h2>
                <table class="form-table">
                    <tr>
                        <th>Vista por defecto</th>
                        <td>
                            <select name="vs_results_default_view">
                                <option value="simple" <?php selected($default_view, 'simple'); ?>>Simple</option>
                                <option value="detailed" <?php selected($default_view, 'detailed'); ?>>Detallada</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Mostrar imágenes</th>
                        <td>
                            <label>
                                <input type="checkbox" name="vs_results_show_images" <?php checked($show_images); ?>>
                                Mostrar imágenes de las opciones en los resultados
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th>Mostrar porcentajes</th>
                        <td>
                            <label>
                                <input type="checkbox" name="vs_results_show_percentage" <?php checked($show_percentage); ?>>
                                Mostrar porcentajes en los resultados
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th>Mostrar números</th>
                        <td>
                            <label>
                                <input type="checkbox" name="vs_results_show_numbers" <?php checked($show_numbers); ?>>
                                Mostrar número de votos en los resultados
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th>Desglose por opciones</th>
                        <td>
                            <label>
                                <input type="checkbox" name="vs_show_breakdown" <?php checked($show_breakdown); ?>>
                                Mostrar desglose detallado
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th>Recargar después de votar</th>
                        <td>
                            <label>
                                <input type="checkbox" name="vs_refresh_after_vote" <?php checked($refresh); ?>>
                                Recargar la página después de votar
                            </label>
                        </td>
                    </tr>
                </table>
            </div>
            
            <p class="submit">
                <button type="submit" name="vs_save_settings" class="button button-primary">Guardar configuración</button>
            </p>
        </form>
    </div>
    <?php
}

// ============================================
// 14. ADMIN - SEGURIDAD (CORREGIDO)
// ============================================
function vs_security_page() {
    global $wpdb;
    
    if (isset($_POST['vs_save_security'])) {
        update_option('vs_security_method', sanitize_text_field($_POST['vs_security_method']));
        update_option('vs_enable_device_fingerprint', isset($_POST['vs_enable_device_fingerprint']));
        update_option('vs_enable_chrome_user_check', isset($_POST['vs_enable_chrome_user_check']));
        update_option('vs_enable_token_security', isset($_POST['vs_enable_token_security']));
        update_option('vs_token_param_name', sanitize_text_field($_POST['vs_token_param_name']));
        update_option('vs_token_expiry_hours', intval($_POST['vs_token_expiry_hours']));
        update_option('vs_token_single_use', isset($_POST['vs_token_single_use']));
		update_option('vs_max_token_attempts', intval($_POST['vs_max_token_attempts']));
        update_option('vs_enable_ip_restriction', isset($_POST['vs_enable_ip_restriction']));
        update_option('vs_allowed_ips', sanitize_text_field($_POST['vs_allowed_ips']));
        
        // Guardar páginas de votación
        $voting_pages = isset($_POST['vs_voting_pages']) ? array_map('intval', $_POST['vs_voting_pages']) : [];
        update_option('vs_voting_pages', $voting_pages);
        
        echo '<div class="notice notice-success"><p>Configuración de seguridad guardada</p></div>';
    }
    
    $security_method = get_option('vs_security_method', 'cookie');
    $enable_fingerprint = get_option('vs_enable_device_fingerprint', false);
    $enable_chrome = get_option('vs_enable_chrome_user_check', false);
    $enable_token = get_option('vs_enable_token_security', false);
    $token_param = get_option('vs_token_param_name', 'token-votacion');
    $token_expiry = get_option('vs_token_expiry_hours', 24);
    $token_single_use = get_option('vs_token_single_use', true);
    $enable_ip_restriction = get_option('vs_enable_ip_restriction', false);
    $allowed_ips = get_option('vs_allowed_ips', '');
    $voting_pages = get_option('vs_voting_pages', []);
    
    // Obtener todas las páginas
    $pages = get_pages([
        'sort_column' => 'post_title',
        'sort_order' => 'ASC'
    ]);
    ?>
    <div class="wrap">
        <h1>Configuración de Seguridad</h1>
        
        <form method="post">
            <div class="vs-admin-box">
                <h2>📍 Páginas de Votación</h2>
                <table class="form-table">
                    <tr>
                        <th>Páginas con shortcodes</th>
                        <td>
                            <select name="vs_voting_pages[]" multiple style="width:100%; max-width:500px; height:150px; padding:8px;">
                                <?php foreach ($pages as $page): 
                                    $selected = in_array($page->ID, $voting_pages) ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $page->ID; ?>" <?php echo $selected; ?>>
                                        <?php echo esc_html($page->post_title); ?> (ID: <?php echo $page->ID; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description">
                                <strong>Importante:</strong> Selecciona las páginas que contienen shortcodes de votación. 
                                Los tokens SOLO se generarán en estas páginas. 
                                Mantén presionado <strong>Ctrl</strong> (Windows) o <strong>Cmd</strong> (Mac) para seleccionar múltiples.
                            </p>
                            <p class="description" style="color: #856404; margin-top: 5px;">
                                ⚠️ Si no seleccionas ninguna página, los tokens se aplicarán en TODO el sitio.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th>Páginas actualmente seleccionadas</th>
                        <td>
                            <?php if (empty($voting_pages)): ?>
                                <span style="color: #856404;">⚠️ Ninguna página seleccionada - Los tokens se aplican en todo el sitio</span>
                            <?php else: ?>
                                <ul style="margin: 0; padding-left: 20px;">
                                    <?php foreach ($voting_pages as $page_id): 
                                        $page = get_post($page_id);
                                        if ($page):
                                    ?>
                                        <li>📄 <?php echo esc_html($page->post_title); ?> (ID: <?php echo $page_id; ?>)</li>
                                    <?php endif; endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="vs-admin-box">
                <h2>🔐 Sistema de Tokens de Votación</h2>
                <table class="form-table">
                    <tr>
                        <th>Activar tokens</th>
                        <td>
                            <label>
                                <input type="checkbox" name="vs_enable_token_security" <?php checked($enable_token); ?>>
                                Activar sistema de tokens de votación
                            </label>
                            <p class="description">Cada dispositivo recibe un token único en la URL. Si se copia el enlace a otro dispositivo, no podrá votar.</p>
                        </td>
                    </tr>
                    <tr>
                        <th>Token de uso único</th>
                        <td>
                            <label>
                                <input type="checkbox" name="vs_token_single_use" <?php checked($token_single_use); ?>>
                                Tokens de un solo uso
                            </label>
                            <p class="description">El token solo puede ser usado una vez. Después de votar, el token queda invalidado.</p>
                        </td>
                    </tr>
                    <tr>
                        <th>Parámetro URL</th>
                        <td>
                            <input type="text" name="vs_token_param_name" value="<?php echo esc_attr($token_param); ?>" class="regular-text">
                            <p class="description">Nombre del parámetro en la URL (ej: ?token-votacion=XXXX)</p>
                        </td>
                    </tr>
					<tr>
					<th>Máximos intentos de token</th>
					<td>
						<input type="number" name="vs_max_token_attempts" value="<?php echo esc_attr(get_option('vs_max_token_attempts', 3)); ?>" min="1" max="10">
						<p class="description">
							Número máximo de intentos permitidos con el mismo token antes de marcarlo como sospechoso.
							Si un token se usa más de X veces sin votar, se considera posible fraude.
						</p>
					</td>
				</tr>
                    <tr>
                        <th>Expiración del token</th>
                        <td>
                            <input type="number" name="vs_token_expiry_hours" value="<?php echo esc_attr($token_expiry); ?>" min="1" max="720">
                            <span>horas</span>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="vs-admin-box">
                <h2>🌐 Restricción por IP</h2>
                <table class="form-table">
                    <tr>
                        <th>Activar restricción IP</th>
                        <td>
                            <label>
                                <input type="checkbox" name="vs_enable_ip_restriction" <?php checked($enable_ip_restriction); ?>>
                                Limitar votos a IPs específicas
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th>IPs permitidas</th>
                        <td>
                            <textarea name="vs_allowed_ips" rows="4" cols="50" class="regular-text" placeholder="Ej: 192.168.1.1, 10.0.0.1"><?php echo esc_textarea($allowed_ips); ?></textarea>
                            <p class="description">IPs separadas por comas. Solo estas direcciones podrán votar.</p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="vs-admin-box">
                <h2>🔒 Métodos de Identificación</h2>
                <table class="form-table">
                    <tr>
                        <th>Método principal</th>
                        <td>
                            <select name="vs_security_method">
                                <option value="cookie" <?php selected($security_method, 'cookie'); ?>>Solo Cookie</option>
                                <option value="session" <?php selected($security_method, 'session'); ?>>Solo Sesión</option>
                                <option value="both" <?php selected($security_method, 'both'); ?>>Cookie + Sesión</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Fingerprint del dispositivo</th>
                        <td>
                            <label>
                                <input type="checkbox" name="vs_enable_device_fingerprint" <?php checked($enable_fingerprint); ?>>
                                Usar fingerprint del dispositivo
                            </label>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="vs-admin-box">
                <h2>📋 Resumen de Seguridad</h2>
                <ul>
                    <li><strong>Tokens activos:</strong> <?php echo $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}vs_tokens WHERE is_used = 0 AND (expires_at IS NULL OR expires_at > NOW())"); ?></li>
                    <li><strong>Tokens usados:</strong> <?php echo $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}vs_tokens WHERE is_used = 1"); ?></li>
                    <li><strong>Intentos de fraude:</strong> <?php echo $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}vs_votes WHERE is_valid = 0"); ?></li>
                    <?php if (!empty($voting_pages)): ?>
                        <li><strong>Páginas con tokens:</strong> <?php echo count($voting_pages); ?> página(s) seleccionada(s)</li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <p class="submit">
                <button type="submit" name="vs_save_security" class="button button-primary">Guardar configuración de seguridad</button>
            </p>
        </form>
    </div>
    <?php
}

// ============================================
// 15. ADMIN - AUDITORÍA CON DETECCIÓN DE FRAUDE
// ============================================
function vs_audit_page() {
    global $wpdb;
    
    // Eliminar todos los votos
    if (isset($_POST['vs_delete_all_votes'])) {
        $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}vs_votes");
        echo '<div class="notice notice-success"><p>Todos los votos eliminados</p></div>';
    }
    
    // Anular voto individual
    if (isset($_POST['vs_void_vote']) && isset($_POST['vote_id'])) {
        $vote_id = intval($_POST['vote_id']);
        $wpdb->update(
            $wpdb->prefix . 'vs_votes',
            ['is_valid' => 0, 'fraud_reason' => 'Anulado manualmente'],
            ['id' => $vote_id]
        );
        echo '<div class="notice notice-success"><p>Voto #' . $vote_id . ' ha sido anulado.</p></div>';
    }
    
    // Reactivar voto
    if (isset($_POST['vs_restore_vote']) && isset($_POST['vote_id'])) {
        $vote_id = intval($_POST['vote_id']);
        $wpdb->update(
            $wpdb->prefix . 'vs_votes',
            ['is_valid' => 1, 'fraud_reason' => ''],
            ['id' => $vote_id]
        );
        echo '<div class="notice notice-success"><p>Voto #' . $vote_id . ' ha sido restaurado.</p></div>';
    }

    // Anular votos por fraude detectado
    if (isset($_POST['vs_void_fraud_votes']) && isset($_POST['fraud_vote_ids'])) {
        $fraud_ids = array_map('intval', explode(',', $_POST['fraud_vote_ids']));
        foreach ($fraud_ids as $id) {
            if ($id > 0) {
                $wpdb->update(
                    $wpdb->prefix . 'vs_votes',
                    ['is_valid' => 0, 'fraud_reason' => 'Fraude detectado automáticamente'],
                    ['id' => $id]
                );
            }
        }
        echo '<div class="notice notice-success"><p>' . count($fraud_ids) . ' votos sospechosos han sido anulados.</p></div>';
    }
    
    // ============================================
    // DETECCIÓN DE FRAUDE AUTOMÁTICA
    // ============================================
    $max_attempts = intval(get_option('vs_max_token_attempts', 3));
    $fraud_detected = false;
    
    // 1. Detectar tokens con múltiples intentos (sin votar)
    $suspicious_tokens = $wpdb->get_results($wpdb->prepare("
        SELECT 
            t.token,
            t.device_id,
            t.ip,
            t.user_agent,
            COUNT(v.id) as total_attempts,
            GROUP_CONCAT(v.id) as attempt_ids
        FROM {$wpdb->prefix}vs_tokens t
        LEFT JOIN {$wpdb->prefix}vs_votes v ON v.token = t.token AND v.is_valid = 0
        WHERE t.is_used = 0 
            AND (t.expires_at IS NULL OR t.expires_at > NOW())
            AND v.id IS NOT NULL
        GROUP BY t.token
        HAVING total_attempts >= %d
    ", $max_attempts));
    
    if (!empty($suspicious_tokens)) $fraud_detected = true;
    
    // 2. Detectar mismo dispositivo con múltiples tokens
    $suspicious_devices = $wpdb->get_results("
        SELECT 
            device_id,
            COUNT(DISTINCT token) as total_tokens,
            COUNT(DISTINCT ip) as unique_ips,
            GROUP_CONCAT(DISTINCT token) as tokens
        FROM {$wpdb->prefix}vs_tokens
        WHERE is_used = 0 
            AND (expires_at IS NULL OR expires_at > NOW())
        GROUP BY device_id
        HAVING total_tokens > 2
    ");
    
    if (!empty($suspicious_devices)) $fraud_detected = true;
    
    // 3. Detectar misma IP con múltiples dispositivos
    $suspicious_ips = $wpdb->get_results("
        SELECT 
            ip,
            COUNT(DISTINCT device_id) as total_devices,
            COUNT(DISTINCT token) as total_tokens,
            GROUP_CONCAT(DISTINCT device_id) as device_ids
        FROM {$wpdb->prefix}vs_tokens
        WHERE is_used = 0 
            AND (expires_at IS NULL OR expires_at > NOW())
            AND ip != ''
        GROUP BY ip
        HAVING total_devices > 3
    ");
    
    if (!empty($suspicious_ips)) $fraud_detected = true;

	// 4. Detectar dispositivos bloqueados
	$blocked_devices = $wpdb->get_results("
		SELECT 
			device_id,
			token,
			blocked_reason,
			created_at
		FROM {$wpdb->prefix}vs_tokens
		WHERE is_blocked = 1
		ORDER BY created_at DESC
		LIMIT 50
	");

	if (!empty($blocked_devices)) $fraud_detected = true;
    
    // Obtener todos los votos
    $valid_votes = $wpdb->get_results("
        SELECT v.*, o.title as option_title, o.type, o.category, u.display_name as user_name
        FROM {$wpdb->prefix}vs_votes v
        LEFT JOIN {$wpdb->prefix}vs_options o ON v.option_id = o.id
        LEFT JOIN {$wpdb->prefix}users u ON v.user_id = u.ID
        ORDER BY v.voted_at DESC
    ");
    
    $total_valid = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}vs_votes WHERE is_valid = 1");
    $total_invalid = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}vs_votes WHERE is_valid = 0");
    $total_fraud_attempts = $wpdb->get_var("SELECT SUM(fraud_attempts) FROM {$wpdb->prefix}vs_votes WHERE is_valid = 0");
    ?>
    <div class="wrap">
        <h1>Auditoría de Votaciones</h1>
        
        <!-- RESUMEN -->
        <div class="vs-admin-box">
            <h2>📊 Resumen</h2>
            <ul>
                <li><strong>Votos válidos:</strong> <?php echo $total_valid; ?></li>
                <li><strong>Votos anulados:</strong> <?php echo $total_invalid; ?></li>
                <li><strong>Intentos de fraude:</strong> <?php echo $total_fraud_attempts ?: 0; ?></li>
                <li><strong>Total de registros:</strong> <?php echo count($valid_votes); ?></li>
            </ul>
            <form method="post" style="margin-top: 10px;">
                <button type="submit" name="vs_delete_all_votes" class="button button-danger" onclick="return confirm('¿Eliminar TODOS los votos? Esta acción no se puede deshacer.')">
                    Eliminar todos los votos
                </button>
            </form>
        </div>
        
        <!-- TABLA DE FRAUDE DETECTADO -->
        <?php if ($fraud_detected): ?>
        <div class="vs-admin-box" style="border-left: 4px solid #dc3545;">
            <h2>🚨 Posibles Fraudes Detectados</h2>
            
            <?php if (!empty($suspicious_tokens)): ?>
                <h3>🔴 Tokens con múltiples intentos (> <?php echo $max_attempts; ?>)</h3>
                <form method="post">
                    <table class="wp-list-table widefat fixed">
                        <thead>
                            <tr>
                                <th>Token</th>
                                <th>Dispositivo</th>
                                <th>IP</th>
                                <th>Intentos</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($suspicious_tokens as $s): ?>
                            <tr>
                                <td><code><?php echo substr($s->token, 0, 20) . '...'; ?></code></td>
                                <td><code><?php echo substr($s->device_id, 0, 20) . '...'; ?></code></td>
                                <td><?php echo $s->ip; ?></td>
                                <td><span class="badge badge-danger"><?php echo $s->total_attempts; ?></span></td>
                                <td>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="fraud_vote_ids" value="<?php echo $s->attempt_ids; ?>">
                                        <button type="submit" name="vs_void_fraud_votes" class="button button-small button-danger" onclick="return confirm('¿Anular todos los votos sospechosos de este token?')">
                                            Anular todos
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <p class="description">Tokens que han tenido múltiples intentos fallidos sin votar.</p>
                </form>
            <?php endif; ?>
            
            <?php if (!empty($suspicious_devices)): ?>
                <h3>🟡 Mismo dispositivo con múltiples tokens</h3>
                <table class="wp-list-table widefat fixed">
                    <thead>
                        <tr>
                            <th>Dispositivo</th>
                            <th>Tokens</th>
                            <th>IPs únicas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($suspicious_devices as $s): ?>
                        <tr>
                            <td><code><?php echo substr($s->device_id, 0, 20) . '...'; ?></code></td>
                            <td><?php echo $s->total_tokens; ?></td>
                            <td><?php echo $s->unique_ips; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="description">Dispositivos que han generado múltiples tokens sin usarlos.</p>
            <?php endif; ?>
            
            <?php if (!empty($suspicious_ips)): ?>
                <h3>🟠 Misma IP con múltiples dispositivos</h3>
                <table class="wp-list-table widefat fixed">
                    <thead>
                        <tr>
                            <th>IP</th>
                            <th>Dispositivos</th>
                            <th>Tokens</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($suspicious_ips as $s): ?>
                        <tr>
                            <td><?php echo $s->ip; ?></td>
                            <td><?php echo $s->total_devices; ?></td>
                            <td><?php echo $s->total_tokens; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="description">Direcciones IP desde las que se han generado múltiples tokens desde diferentes dispositivos.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
		
		<!-- TABLA DISPOSITIVOS BLOQUEADOS -->
		<?php if (!empty($blocked_devices)): ?>
		<h3>⛔ Dispositivos Bloqueados</h3>
		<table class="wp-list-table widefat fixed">
			<thead>
				<tr>
					<th>Dispositivo</th>
					<th>Token</th>
					<th>Razón</th>
					<th>Fecha</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($blocked_devices as $b): ?>
				<tr>
					<td><code><?php echo substr($b->device_id, 0, 20) . '...'; ?></code></td>
					<td><code><?php echo substr($b->token, 0, 20) . '...'; ?></code></td>
					<td><?php echo esc_html($b->blocked_reason); ?></td>
					<td><?php echo $b->created_at; ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<p class="description">Dispositivos bloqueados permanentemente por actividad sospechosa.</p>
	<?php endif; ?>
        
        <!-- REGISTRO COMPLETO -->
        <div class="vs-admin-box">
            <h2>📋 Registro Completo de Votos</h2>
            <table class="wp-list-table widefat fixed vs-sortable-table">
                <thead>
                    <tr>
                        <th class="sortable" data-sort="id">ID</th>
                        <th class="sortable" data-sort="option">Opción</th>
                        <th class="sortable" data-sort="type">Tipo</th>
                        <th class="sortable" data-sort="category">Categoría</th>
                        <th class="sortable" data-sort="user">Usuario</th>
                        <th>Dispositivo</th>
                        <th>IP</th>
                        <th>Sesión</th>
                        <th>Token</th>
                        <th class="sortable" data-sort="date">Fecha</th>
                        <th>Estado</th>
                        <th>Intentos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($valid_votes as $v): ?>
                    <tr>
                        <td><?php echo $v->id; ?></td>
                        <td><?php echo esc_html($v->option_title ?: 'N/A'); ?></td>
                        <td><?php echo esc_html($v->type); ?></td>
                        <td><?php echo esc_html($v->category); ?></td>
                        <td><?php echo esc_html($v->user_name ?: 'Anónimo'); ?></td>
                        <td><?php echo substr($v->device_id, 0, 20) . '...'; ?></td>
                        <td><?php echo $v->ip; ?></td>
                        <td><?php echo substr($v->voter_session, 0, 20) . '...'; ?></td>
                        <td><code><?php echo !empty($v->token) ? substr($v->token, 0, 20) . '...' : 'N/A'; ?></code></td>
                        <td><?php echo $v->voted_at; ?></td>
                        <td>
                            <span class="badge <?php echo $v->is_valid ? 'badge-success' : 'badge-danger'; ?>">
                                <?php echo $v->is_valid ? '✓ Válido' : '✗ Anulado'; ?>
                            </span>
                        </td>
                        <td><?php echo $v->fraud_attempts ?: 0; ?></td>
                        <td>
                            <?php if ($v->is_valid): ?>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="vote_id" value="<?php echo $v->id; ?>">
                                    <button type="submit" name="vs_void_vote" class="button button-small" onclick="return confirm('¿Anular este voto?')">Anular</button>
                                </form>
                            <?php else: ?>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="vote_id" value="<?php echo $v->id; ?>">
                                    <button type="submit" name="vs_restore_vote" class="button button-small" onclick="return confirm('¿Restaurar este voto?')">Restaurar</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p class="description">Haz clic en los encabezados para ordenar. Los votos anulados no se cuentan en los resultados.</p>
        </div>
    </div>
    
    <style>
    .badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-success {
        background: #d4edda;
        color: #155724;
    }
    .badge-danger {
        background: #f8d7da;
        color: #721c24;
    }
    .button-danger {
        background: #dc3545 !important;
        color: white !important;
        border-color: #dc3545 !important;
    }
    .button-danger:hover {
        background: #c82333 !important;
        border-color: #bd2130 !important;
    }
    .vs-admin-box {
        background: #fff;
        padding: 20px;
        margin: 20px 0;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .vs-admin-box h2 {
        margin-top: 0;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 10px;
    }
    .vs-admin-box h3 {
        margin: 15px 0 10px;
        color: #333;
    }
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.vs-sortable-table').forEach(function(table) {
            const headers = table.querySelectorAll('th.sortable');
            let sortDirection = {};
            headers.forEach(function(header) {
                header.style.cursor = 'pointer';
                header.addEventListener('click', function() {
                    const column = this.dataset.sort;
                    const tbody = table.querySelector('tbody');
                    const rows = Array.from(tbody.querySelectorAll('tr'));
                    if (!sortDirection[column]) sortDirection[column] = 'asc';
                    else if (sortDirection[column] === 'asc') sortDirection[column] = 'desc';
                    else sortDirection[column] = 'asc';
                    const direction = sortDirection[column];
                    headers.forEach(function(h) {
                        h.querySelector('.sort-indicator').textContent = '↕';
                    });
                    this.querySelector('.sort-indicator').textContent = direction === 'asc' ? '↑' : '↓';
                    rows.sort(function(a, b) {
                        let valA = a.querySelector('td:nth-child(' + (Array.from(header.parentElement.children).indexOf(header) + 1) + ')')?.textContent || '';
                        let valB = b.querySelector('td:nth-child(' + (Array.from(header.parentElement.children).indexOf(header) + 1) + ')')?.textContent || '';
                        if (column === 'id') {
                            valA = parseInt(valA); valB = parseInt(valB);
                            return direction === 'asc' ? valA - valB : valB - valA;
                        }
                        if (column === 'date') {
                            valA = new Date(valA); valB = new Date(valB);
                            return direction === 'asc' ? valA - valB : valB - valA;
                        }
                        valA = valA.toString().toLowerCase();
                        valB = valB.toString().toLowerCase();
                        return direction === 'asc' ? valA.localeCompare(valB) : valB.localeCompare(valA);
                    });
                    rows.forEach(function(row) { tbody.appendChild(row); });
                });
            });
        });
    });
    </script>
    <?php
}

// ============================================
// 16. ADMIN - RESET DE TOKENS
// ============================================
function vs_reset_tokens() {
    global $wpdb;
    if (!current_user_can('manage_options')) wp_die('No tienes permisos.');
    if (!isset($_POST['vs_reset_nonce']) || !wp_verify_nonce($_POST['vs_reset_nonce'], 'vs_reset_tokens')) {
        wp_die('Error de seguridad.');
    }
    
    $action = sanitize_text_field($_POST['vs_reset_action']);
    $message = '';
    $message_type = 'success';
    
    switch ($action) {
        case 'delete_all_tokens':
            $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}vs_tokens");
            if (!headers_sent() && isset($_COOKIE['vs_device_id'])) {
                setcookie('vs_device_id', '', time() - 3600, '/', '', false, true);
            }
            $message = '✅ Todos los tokens han sido eliminados.';
            break;
        case 'delete_inactive_tokens':
            $wpdb->query("DELETE FROM {$wpdb->prefix}vs_tokens WHERE is_used = 1 OR expires_at < NOW()");
            $message = '✅ Tokens inactivos y expirados eliminados.';
            break;
        case 'regenerate_current_token':
            $device_id = vs_get_device_id();
            if (!empty($device_id)) {
                $wpdb->delete($wpdb->prefix . 'vs_tokens', ['device_id' => $device_id]);
                $new_token = vs_generate_voting_token();
                $redirect_url = add_query_arg(
                    get_option('vs_token_param_name', 'token-votacion'),
                    $new_token,
                    remove_query_arg(get_option('vs_token_param_name', 'token-votacion'))
                );
                if (!headers_sent()) {
                    wp_redirect($redirect_url);
                    exit;
                }
                $message = '<a href="' . esc_url($redirect_url) . '">Haz clic aquí para continuar con tu nuevo token</a>';
                $message_type = 'warning';
            } else {
                $message = '⚠️ No se pudo encontrar tu dispositivo.';
                $message_type = 'warning';
            }
            break;
        default:
            $message = '❌ Acción no válida.';
            $message_type = 'error';
    }
    
    if (!empty($message) && $action !== 'regenerate_current_token') {
        add_action('admin_notices', function() use ($message, $message_type) {
            $class = $message_type === 'error' ? 'notice-error' : ($message_type === 'warning' ? 'notice-warning' : 'notice-success');
            echo '<div class="notice ' . $class . ' is-dismissible"><p>' . $message . '</p></div>';
        });
    }
}

function vs_reset_tokens_page() {
    if (isset($_POST['vs_reset_action'])) vs_reset_tokens();
    
    global $wpdb;
    $total_tokens = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}vs_tokens");
    $used_tokens = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}vs_tokens WHERE is_used = 1");
    $active_tokens = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}vs_tokens WHERE is_used = 0 AND (expires_at IS NULL OR expires_at > NOW())");
    ?>
    <div class="wrap">
        <h1>🔄 Resetear Tokens de Votación</h1>
        
        <div class="vs-admin-box">
            <h2>📊 Estadísticas de Tokens</h2>
            <ul>
                <li><strong>Total de tokens:</strong> <?php echo $total_tokens; ?></li>
                <li><strong>Tokens activos:</strong> <?php echo $active_tokens; ?></li>
                <li><strong>Tokens usados:</strong> <?php echo $used_tokens; ?></li>
            </ul>
        </div>
        
        <div class="vs-admin-box">
            <h2>⚙️ Opciones de Reseteo</h2>
            
            <form method="post" style="margin-bottom: 20px;">
                <?php wp_nonce_field('vs_reset_tokens', 'vs_reset_nonce'); ?>
                <input type="hidden" name="vs_reset_action" value="delete_all_tokens">
                <p><strong>Eliminar TODOS los tokens</strong></p>
                <button type="submit" class="button button-danger" onclick="return confirm('¿Eliminar TODOS los tokens?')">
                    🗑️ Eliminar todos los tokens
                </button>
            </form>
            
            <form method="post" style="border-top: 1px solid #ddd; padding-top: 20px;">
                <?php wp_nonce_field('vs_reset_tokens', 'vs_reset_nonce'); ?>
                <input type="hidden" name="vs_reset_action" value="delete_inactive_tokens">
                <p><strong>Limpiar tokens inactivos</strong></p>
                <button type="submit" class="button" onclick="return confirm('¿Eliminar tokens inactivos?')">
                    🧹 Limpiar tokens inactivos
                </button>
            </form>
            
            <form method="post" style="border-top: 1px solid #ddd; padding-top: 20px;">
                <?php wp_nonce_field('vs_reset_tokens', 'vs_reset_nonce'); ?>
                <input type="hidden" name="vs_reset_action" value="regenerate_current_token">
                <p><strong>Regenerar tu token actual</strong></p>
                <button type="submit" class="button button-primary">
                    🔄 Regenerar mi token
                </button>
            </form>
        </div>
    </div>
    <?php
}

// ============================================
// 17. ADMIN - AÑADIR OPCIÓN
// ============================================
function vs_add_option_page() {
    global $wpdb;
    
    $categories = $wpdb->get_col("SELECT DISTINCT category FROM {$wpdb->prefix}vs_options WHERE category != '' ORDER BY category ASC");
    $types = $wpdb->get_col("SELECT DISTINCT type FROM {$wpdb->prefix}vs_options WHERE type != '' ORDER BY type ASC");
    
    if (isset($_POST['vs_add_option'])) {
        $wpdb->insert(
            $wpdb->prefix . 'vs_options',
            [
                'title' => sanitize_text_field($_POST['title']),
                'type' => sanitize_text_field($_POST['type']),
                'photo_url' => esc_url_raw($_POST['photo_url']),
                'category' => sanitize_text_field($_POST['category'])
            ]
        );
        echo '<div class="notice notice-success"><p>Opción añadida correctamente</p></div>';
    }
    ?>
    <div class="wrap">
        <h1>Añadir nueva opción</h1>
        <form method="post" id="vs-add-option-form">
            <table class="form-table">
                <tr>
                    <th><label for="title">Título *</label></th>
                    <td><input type="text" id="title" name="title" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="type">Tipo</label></th>
                    <td>
                        <input type="text" id="type" name="type" class="regular-text" placeholder="Escribe un tipo" list="type-list">
                        <datalist id="type-list">
                            <?php foreach ($types as $t): ?>
                                <option value="<?php echo esc_attr($t); ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </td>
                </tr>
                <tr>
                    <th><label for="category">Categoría</label></th>
                    <td>
                        <input type="text" id="category" name="category" class="regular-text" placeholder="Escribe una categoría" list="category-list">
                        <datalist id="category-list">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo esc_attr($cat); ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </td>
                </tr>
                <tr>
                    <th><label for="photo_url">Foto</label></th>
                    <td>
                        <div class="vs-media-uploader">
                            <input type="url" id="photo_url" name="photo_url" class="regular-text" placeholder="URL de la imagen">
                            <button type="button" class="button vs-upload-btn">Seleccionar imagen</button>
                            <div id="vs-photo-preview" style="margin-top: 10px;"></div>
                        </div>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <button type="submit" name="vs_add_option" class="button button-primary">Añadir opción</button>
            </p>
        </form>
    </div>
    <?php
}

// ============================================
// 18. ADMIN - VISTA PREVIA
// ============================================
function vs_preview_page() {
    ?>
    <div class="wrap">
        <h1>Vista Previa de Shortcodes</h1>
        <p>Así se verán los shortcodes en el frontend con tus opciones actuales.</p>
        
        <div class="vs-preview-section">
            <h2>Formulario de votación</h2>
            <div class="notice notice-info inline" style="margin: 0 0 15px 0; padding: 8px 12px;">
                <p><strong>ℹ️ Modo Vista Previa:</strong> Aquí los votos no se bloquean ni descuentan para que puedas probar el diseño del formulario.</p>
            </div>
            <div class="vs-preview-box">
                <?php echo vs_voting_form(); ?>
            </div>
        </div>
        
        <div class="vs-preview-section">
            <h2>Resultados</h2>
            <div class="vs-preview-box">
                <?php echo vs_voting_results(); ?>
            </div>
        </div>
        
        <div class="vs-preview-section">
            <h2>Shortcodes para copiar</h2>
            <div class="vs-shortcode-grid">
                <div class="vs-shortcode-item">
                    <code>[voting_form]</code>
                    <span>Formulario de votación</span>
                    <button class="button button-small vs-copy-btn" data-shortcode="[voting_form]">Copiar</button>
                </div>
                <div class="vs-shortcode-item">
                    <code>[voting_results]</code>
                    <span>Resultados de la votación</span>
                    <button class="button button-small vs-copy-btn" data-shortcode="[voting_results]">Copiar</button>
                </div>
            </div>
            <div style="margin-top: 15px;">
                <h3>Ejemplos con filtros:</h3>
                <ul>
                    <li><code>[voting_form type="Cantante" category="Instrumental"]</code> - Filtrar por tipo y categoría</li>
                    <li><code>[voting_form type="Cantante" limit="3"]</code> - Solo 3 votos para el tipo "Cantante"</li>
                    <li><code>[voting_results category="Canto" view="simple" limit="5"]</code> - Resultados simples, limitados a 5</li>
                    <li><code>[voting_results show_images="false"]</code> - Resultados sin imágenes</li>
                </ul>
            </div>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('.vs-copy-btn').on('click', function() {
            var shortcode = $(this).data('shortcode');
            var temp = $('<input>');
            $('body').append(temp);
            temp.val(shortcode).select();
            document.execCommand('copy');
            temp.remove();
            $(this).text('¡Copiado!');
            setTimeout(() => $(this).text('Copiar'), 2000);
        });
    });
    </script>
    <?php
}
