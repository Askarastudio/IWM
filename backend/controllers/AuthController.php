<?php
/**
 * Authentication Controller
 * INSIA Work Monitor (IWM)
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/Auth.php';
require_once __DIR__ . '/../utils/Response.php';

class AuthController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Admin Login
     */
    public function login() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['email']) || !isset($data['password'])) {
            Response::error('Email and password are required', 400);
        }
        
        $email = $data['email'];
        $password = $data['password'];
        
        $user = $this->db->fetchOne(
            "SELECT * FROM users WHERE email = ? AND status = 'active'",
            [$email]
        );
        
        if (!$user || !Auth::verifyPassword($password, $user['password_hash'])) {
            Response::error('Invalid credentials', 401);
        }
        
        $token = Auth::generateToken($user['id'], $user['email'], $user['role']);
        
        Response::success([
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ], 'Login successful');
    }
    
    /**
     * Verify Token
     */
    public function verify() {
        $token = Auth::getBearerToken();
        $payload = Auth::verifyToken($token);
        
        if (!$payload) {
            Response::unauthorized('Invalid or expired token');
        }
        
        Response::success($payload, 'Token is valid');
    }
}
