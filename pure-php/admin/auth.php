<?php
/**
 * Admin Authentication Gate for Pure PHP Voting System
 * Credentials configured in config.php (ADMIN_USER, ADMIN_PASS)
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/I18n.php';

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

// Handle Logout
if (isset($_GET['logout'])) {
    unset($_SESSION['vs_admin_logged_in']);
    session_destroy();
    header('Location: index.php');
    exit;
}

// Handle Login Form Submission
$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vs_admin_login'])) {
    $user = trim($_POST['username'] ?? '');
    $pass = trim($_POST['password'] ?? '');

    $validUser = defined('ADMIN_USER') ? ADMIN_USER : 'admin';
    $validPass = defined('ADMIN_PASS') ? ADMIN_PASS : 'admin123';

    if ($user === $validUser && $pass === $validPass) {
        $_SESSION['vs_admin_logged_in'] = true;
        header('Location: index.php');
        exit;
    } else {
        $loginError = 'Invalid credentials. Please verify username and password.';
    }
}

// If already authenticated, proceed
if (!empty($_SESSION['vs_admin_logged_in'])) {
    return;
}

// Render Login Page
$lang = I18n::getLanguage();
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting System - Admin Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .vs-login-box {
            max-width: 440px;
            margin: 60px auto;
            background: #fff;
            padding: 35px 30px;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: 1px solid var(--border-color);
            text-align: center;
        }
        .vs-login-box h2 {
            margin-bottom: 8px;
            color: var(--primary-color);
        }
        .vs-login-box p.subtitle {
            color: var(--text-muted);
            font-size: 14px;
            margin-bottom: 25px;
        }
        .vs-input-group {
            text-align: left;
            margin-bottom: 18px;
        }
        .vs-input-group label {
            display: block;
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 6px;
            color: var(--text-color);
        }
        .vs-input-group input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }
        .vs-input-group input:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        .vs-btn-submit {
            width: 100%;
            padding: 13px;
            background: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 10px;
        }
        .vs-btn-submit:hover {
            background: var(--hover-color);
        }
        .vs-error-msg {
            background: #fee2e2;
            color: #dc2626;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 18px;
            font-size: 14px;
        }
        .vs-recovery-info {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
            font-size: 12px;
            color: #6b7280;
            line-height: 1.5;
            text-align: left;
        }
        .vs-recovery-info code {
            background: #f3f4f6;
            padding: 2px 6px;
            border-radius: 4px;
            color: #1f2937;
        }
    </style>
</head>
<body>
<div class="vs-wrapper">
    <div class="vs-top-bar" style="justify-content: flex-start;">
        <a href="../" class="vs-admin-link">&larr; Back to Public Voting Page</a>
    </div>

    <div class="vs-login-box">
        <h2>Voting System Administration</h2>
        <p class="subtitle">Enter your credentials to access the console</p>

        <?php if (!empty($loginError)): ?>
            <div class="vs-error-msg"><?php echo htmlspecialchars($loginError); ?></div>
        <?php endif; ?>

        <form method="post" action="index.php">
            <input type="hidden" name="vs_admin_login" value="1">
            <div class="vs-input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autofocus placeholder="admin">
            </div>
            <div class="vs-input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>
            <button type="submit" class="vs-btn-submit">Sign In &rarr;</button>
        </form>

        <div class="vs-recovery-info">
            <strong>Access & Credential Recovery:</strong><br>
            • Default login: <code>admin</code> / <code>admin123</code><br>
            • To configure credentials or recover access, edit <code>ADMIN_USER</code> and <code>ADMIN_PASS</code> in <code>pure-php/config.php</code>.
        </div>
    </div>
</div>
</body>
</html>
<?php
exit;
