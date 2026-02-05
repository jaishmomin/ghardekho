<?php
declare(strict_types=1);

session_start();

// Clear all session data
$_SESSION = [];
session_unset();

// Destroy session cookie
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'] ?? '/',
        $params['domain'] ?? '',
        $params['secure'] ?? false,
        $params['httponly'] ?? true
    );
}

// Destroy session
session_destroy();

// Redirect to home page
header('Location: /');
exit;
