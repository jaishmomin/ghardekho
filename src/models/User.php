<?php

namespace App\Models;

use App\Services\MailerService;
use PDO;
use PDOException;

class User extends Model {
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name', 'email', 'password_hash', 'role', 'is_verified', 
        'verify_token', 'reset_token', 'reset_token_expires_at',
        'phone', 'profile_image', 'last_login_at'
    ];
    
    protected $hidden = [
        'password_hash', 'verify_token', 'reset_token', 'reset_token_expires_at'
    ];

    // Role constants
    const ROLE_USER = 'user';
    const ROLE_AGENT = 'agent';
    const ROLE_ADMIN = 'admin';

    // Validation rules
    public static $validationRules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'phone' => 'nullable|string|max:20',
        'role' => 'in:user,agent,admin'
    ];

    // Relationships
    public function properties() {
        return (new Property())->where('user_id', $this->id);
    }

    public function inquiries() {
        return (new Inquiry())->where('user_id', $this->id);
    }

    public function favorites() {
        $sql = "SELECT p.* FROM properties p 
                JOIN favorites f ON p.id = f.property_id 
                WHERE f.user_id = :user_id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $this->id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new \Exception("Error fetching favorites: " . $e->getMessage());
        }
    }

    // Authentication methods
    public static function register(array $data) {
        // Validate input
        $errors = self::validate($data);
        if (!empty($errors)) {
            throw new \Exception(implode("\n", $errors));
        }

        // Check if user already exists
        $existingUser = (new self())->where('email', $data['email']);
        if (!empty($existingUser)) {
            throw new \Exception('Email already registered');
        }

        // Create user
        $user = new self();
        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
            'role' => $data['role'] ?? self::ROLE_USER,
            'is_verified' => 0,
            'verify_token' => bin2hex(random_bytes(32)),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Save user
        if ($user->save()) {
            // Send verification email
            $mailer = new MailerService();
            $mailer->sendVerificationEmail($user->email, $user->name, $user->verify_token);
            
            return $user;
        }

        throw new \Exception('Failed to create user');
    }

    public static function verify($token) {
        $user = (new self())->where('verify_token', $token)->first();
        
        if ($user) {
            $user->is_verified = 1;
            $user->verify_token = null;
            $user->email_verified_at = date('Y-m-d H:i:s');
            
            if ($user->save()) {
                return true;
            }
        }
        
        return false;
    }

    public static function attemptLogin($email, $password) {
        $user = (new self())->where('email', $email)->first();
        
        if ($user && password_verify($password, $user->password_hash)) {
            if (!$user->is_verified) {
                throw new \Exception('Please verify your email address first');
            }
            
            // Update last login
            $user->last_login_at = date('Y-m-d H:i:s');
            $user->save();
            
            return $user;
        }
        
        return false;
    }

    public function generateAuthToken() {
        $token = bin2hex(random_bytes(32));
        
        // Store token in database (you might want to use a separate table for this)
        $this->auth_token = $token;
        $this->token_expires_at = date('Y-m-d H:i:s', strtotime('+1 week'));
        $this->save();
        
        return $token;
    }

    public function requestPasswordReset() {
        $this->reset_token = bin2hex(random_bytes(32));
        $this->reset_token_expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        if ($this->save()) {
            // Send password reset email
            $mailer = new MailerService();
            return $mailer->sendPasswordResetEmail(
                $this->email, 
                $this->name, 
                $this->reset_token
            );
        }
        
        return false;
    }

    public function resetPassword($token, $newPassword) {
        if ($this->reset_token !== $token || 
            strtotime($this->reset_token_expires_at) < time()) {
            return false;
        }
        
        $this->password_hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $this->reset_token = null;
        $this->reset_token_expires_at = null;
        
        return $this->save();
    }

    // Helper methods
    public function isAdmin() {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isAgent() {
        return $this->role === self::ROLE_AGENT;
    }

    public function isVerified() {
        return (bool)$this->is_verified;
    }

    // Validation helper
    protected static function validate($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = 'Name is required';
        }
        
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required';
        }
        
        if (empty($data['password']) || strlen($data['password']) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }
        
        if (isset($data['password_confirmation']) && $data['password'] !== $data['password_confirmation']) {
            $errors[] = 'Passwords do not match';
        }
        
        if (isset($data['role']) && !in_array($data['role'], [self::ROLE_USER, self::ROLE_AGENT, self::ROLE_ADMIN])) {
            $errors[] = 'Invalid role specified';
        }
        
        return $errors;
    }
}
