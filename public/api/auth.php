<?php
declare(strict_types=1);

// Show errors while developing
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

// 👇 IMPORTANT: we are in public/api, vendor is two levels up
require __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Services\AuthService;

session_start();

// 👇 .env is in project root (two levels up)
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->safeLoad();

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    $auth = new AuthService();

    if ($action === 'register') {

    $raw = file_get_contents('php://input');
    error_log('RAW INPUT: ' . $raw);

    $payload = json_decode($raw, true);
    error_log('DECODED PAYLOAD: ' . print_r($payload, true));

    if (!is_array($payload)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid JSON received'
        ]);
        exit;
    }

    $name     = trim($payload['name'] ?? '');
    $email    = trim($payload['email'] ?? '');
    $password = $payload['password'] ?? '';

    error_log("FIELDS => name:$name | email:$email | password_len:" . strlen($password));

    $result = $auth->register($name, $email, $password);
    echo json_encode($result);
    exit;
    }

    if ($action === 'login') {
        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true);

        if (!is_array($payload)) {
            $payload = $_POST;
        }

        $email    = trim($payload['email'] ?? '');
        $password = $payload['password'] ?? '';

        $result = $auth->login($email, $password);
        echo json_encode($result);
        exit;
    }

    if ($action === 'verify') {
        $token  = $_GET['token'] ?? '';
        $result = $auth->verify($token);
        echo json_encode($result);
        exit;
    }

    if ($action === 'forgot') {
        $payload = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $email   = trim($payload['email'] ?? '');

        $result = $auth->requestPasswordReset($email);
        echo json_encode($result);
        exit;
    }

    if ($action === 'reset') {
        $payload     = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $token       = $payload['token']    ?? '';
        $newPassword = $payload['password'] ?? '';

        $result = $auth->resetPassword($token, $newPassword);
        echo json_encode($result);
        exit;
    }

    // logout action
    if ($action === 'logout') {
    // ensure session started
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    // Clear session data
    $_SESSION = [];
    session_unset();
    $params = session_get_cookie_params();

    // delete session cookie on client
    if (ini_get('session.use_cookies')) {
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

    // destroy session on server
    session_destroy();

    // respond JSON
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => true, 'message' => 'Logged out']);
    exit;
    }


    echo json_encode(['success' => false, 'message' => 'Unknown action']);
    } catch (Throwable $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
