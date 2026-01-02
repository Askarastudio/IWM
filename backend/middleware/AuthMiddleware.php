<?php
/**
 * Authentication Middleware
 * INSIA Work Monitor (IWM)
 */

require_once __DIR__ . '/../utils/Auth.php';
require_once __DIR__ . '/../utils/Response.php';

class AuthMiddleware {
    public static function verifyToken() {
        $token = Auth::getBearerToken();
        
        if (!$token) {
            Response::unauthorized('Authorization token required');
        }
        
        $payload = Auth::verifyToken($token);
        
        if (!$payload) {
            Response::unauthorized('Invalid or expired token');
        }
        
        // Store user data in global scope for use in controllers
        $GLOBALS['current_user'] = $payload;
        
        return $payload;
    }
}
