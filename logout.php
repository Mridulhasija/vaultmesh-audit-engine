<?php
require_once __DIR__ . '/include/header_config.php';
require_once __DIR__ . '/include/auth-session.php';

$csrfToken = ensureCsrfToken();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && hash_equals($csrfToken, (string) ($_POST['csrf_token'] ?? ''))) {
    revokeUserSession($conn);
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => $params['samesite'] ?? 'Strict',
        ]);
    }
    session_destroy();
}
header('Location: ' . SITE_URL . '/');
exit;
