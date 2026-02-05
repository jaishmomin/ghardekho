<?php
declare(strict_types=1);

namespace App\Services;

use App\Config\Database;
use PDO;

class AuthService
{
    private PDO $db;
    private MailerService $mailer;

    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->mailer = new MailerService();
    }

    public function register(string $name, string $email, string $password): array
    {
        if ($name === '' || $email === '' || $password === '') {
            return ['success' => false, 'message' => 'All fields are required'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email'];
        }

        $check = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetch()) {
            return ['success' => false, 'message' => 'Email already registered'];
        }

        $hash  = password_hash($password, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(32));

        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password_hash, role, is_verified, verify_token)
             VALUES (?, ?, ?, 'user', 0, ?)"
        );

        $stmt->execute([$name, $email, $hash, $token]);

        // Send verification email
        $this->mailer->sendVerificationEmail($email, $name, $token);

        return [
            'success' => true,
            'message' => 'Registered successfully. Please verify your email.'
        ];
    }

    public function login(string $email, string $password): array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Invalid login'];
        }

        if (!$user['is_verified']) {
            return ['success' => false, 'message' => 'Please verify your email first'];
        }

        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role']
        ];

        return [
            'success' => true,
            'message' => 'Login successful',
            'role'    => $user['role']
        ];
    }

    public function verify(string $token): array
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE verify_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => false, 'message' => 'Invalid token'];
        }

        $this->db->prepare(
            "UPDATE users SET is_verified = 1, verify_token = NULL WHERE id = ?"
        )->execute([$user['id']]);

        return ['success' => true, 'message' => 'Account verified'];
    }
    public function getCurrentUser(): ?array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        return $_SESSION['user'] ?? null;
    }
}
