<?php
/**
 * General Configuration
 * INSIA Work Monitor (IWM)
 */

// Timezone
date_default_timezone_set('Asia/Jakarta');

// JWT Secret Key
define('JWT_SECRET', getenv('JWT_SECRET') ?: 'your-secret-key-change-in-production');
define('JWT_ALGORITHM', 'HS256');
define('JWT_EXPIRATION', 86400); // 24 hours

// API Settings
define('API_VERSION', 'v1');
define('API_RATE_LIMIT', 100); // requests per minute

// File Upload Settings
define('UPLOAD_DIR', __DIR__ . '/../../uploads/');
define('SCREENSHOT_DIR', UPLOAD_DIR . 'screenshots/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

// Create upload directories if they don't exist
if (!file_exists(SCREENSHOT_DIR)) {
    mkdir(SCREENSHOT_DIR, 0755, true);
}

// Error Reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../logs/php-error.log');

// CORS Settings
define('ALLOW_ORIGIN', '*'); // Change to specific domain in production
define('ALLOW_METHODS', 'GET, POST, PUT, DELETE, OPTIONS');
define('ALLOW_HEADERS', 'Content-Type, Authorization, X-Requested-With');

// Application Settings
define('APP_NAME', 'INSIA Work Monitor');
define('APP_VERSION', '1.0.0');
