<?php
/**
 * API Router
 * INSIA Work Monitor (IWM)
 */

// CORS Headers
header('Access-Control-Allow-Origin: ' . ALLOW_ORIGIN);
header('Access-Control-Allow-Methods: ' . ALLOW_METHODS);
header('Access-Control-Allow-Headers: ' . ALLOW_HEADERS);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/utils/Auth.php';
require_once __DIR__ . '/utils/Response.php';
require_once __DIR__ . '/middleware/AuthMiddleware.php';

// Get request URI and method
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Remove query string
$requestUri = strtok($requestUri, '?');

// Remove base path if exists
$basePath = '/backend';
if (strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
}

// Parse route
$route = trim($requestUri, '/');
$parts = explode('/', $route);

// Router
try {
    // Public routes (no auth required)
    if ($route === 'api/auth/login' && $requestMethod === 'POST') {
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->login();
    }
    
    // Device API routes (API key auth)
    elseif ($route === 'api/device/heartbeat' && $requestMethod === 'POST') {
        require_once __DIR__ . '/controllers/DeviceController.php';
        $controller = new DeviceController();
        $controller->heartbeat();
    }
    
    elseif ($route === 'api/device/logs/activity' && $requestMethod === 'POST') {
        require_once __DIR__ . '/controllers/ActivityController.php';
        $controller = new ActivityController();
        $controller->logActivity();
    }
    
    elseif ($route === 'api/device/logs/idle' && $requestMethod === 'POST') {
        require_once __DIR__ . '/controllers/ActivityController.php';
        $controller = new ActivityController();
        $controller->logIdle();
    }
    
    elseif ($route === 'api/device/logs/usb' && $requestMethod === 'POST') {
        require_once __DIR__ . '/controllers/ActivityController.php';
        $controller = new ActivityController();
        $controller->logUsb();
    }
    
    // Admin routes (JWT auth required)
    else {
        // Verify JWT token
        AuthMiddleware::verifyToken();
        
        // Employees
        if ($route === 'api/admin/employees' && $requestMethod === 'GET') {
            require_once __DIR__ . '/controllers/EmployeeController.php';
            $controller = new EmployeeController();
            $controller->index();
        }
        
        elseif (preg_match('/^api\/admin\/employees\/(\d+)$/', $route, $matches) && $requestMethod === 'GET') {
            require_once __DIR__ . '/controllers/EmployeeController.php';
            $controller = new EmployeeController();
            $controller->show($matches[1]);
        }
        
        elseif ($route === 'api/admin/employees' && $requestMethod === 'POST') {
            require_once __DIR__ . '/controllers/EmployeeController.php';
            $controller = new EmployeeController();
            $controller->create();
        }
        
        elseif (preg_match('/^api\/admin\/employees\/(\d+)$/', $route, $matches) && $requestMethod === 'PUT') {
            require_once __DIR__ . '/controllers/EmployeeController.php';
            $controller = new EmployeeController();
            $controller->update($matches[1]);
        }
        
        elseif (preg_match('/^api\/admin\/employees\/(\d+)$/', $route, $matches) && $requestMethod === 'DELETE') {
            require_once __DIR__ . '/controllers/EmployeeController.php';
            $controller = new EmployeeController();
            $controller->delete($matches[1]);
        }
        
        // Devices
        elseif ($route === 'api/admin/devices' && $requestMethod === 'GET') {
            require_once __DIR__ . '/controllers/DeviceController.php';
            $controller = new DeviceController();
            $controller->index();
        }
        
        elseif (preg_match('/^api\/admin\/devices\/(\d+)$/', $route, $matches) && $requestMethod === 'GET') {
            require_once __DIR__ . '/controllers/DeviceController.php';
            $controller = new DeviceController();
            $controller->show($matches[1]);
        }
        
        elseif ($route === 'api/admin/devices' && $requestMethod === 'POST') {
            require_once __DIR__ . '/controllers/DeviceController.php';
            $controller = new DeviceController();
            $controller->create();
        }
        
        elseif (preg_match('/^api\/admin\/devices\/(\d+)$/', $route, $matches) && $requestMethod === 'PUT') {
            require_once __DIR__ . '/controllers/DeviceController.php';
            $controller = new DeviceController();
            $controller->update($matches[1]);
        }
        
        elseif (preg_match('/^api\/admin\/devices\/(\d+)\/reset-api-key$/', $route, $matches) && $requestMethod === 'POST') {
            require_once __DIR__ . '/controllers/DeviceController.php';
            $controller = new DeviceController();
            $controller->resetApiKey($matches[1]);
        }
        
        elseif (preg_match('/^api\/admin\/devices\/(\d+)$/', $route, $matches) && $requestMethod === 'DELETE') {
            require_once __DIR__ . '/controllers/DeviceController.php';
            $controller = new DeviceController();
            $controller->delete($matches[1]);
        }
        
        // Activities
        elseif ($route === 'api/admin/activities' && $requestMethod === 'GET') {
            require_once __DIR__ . '/controllers/ActivityController.php';
            $controller = new ActivityController();
            $controller->getActivities();
        }
        
        // Reports
        elseif ($route === 'api/admin/reports/dashboard' && $requestMethod === 'GET') {
            require_once __DIR__ . '/controllers/ReportController.php';
            $controller = new ReportController();
            $controller->dashboard();
        }
        
        elseif ($route === 'api/admin/reports/daily' && $requestMethod === 'GET') {
            require_once __DIR__ . '/controllers/ReportController.php';
            $controller = new ReportController();
            $controller->daily();
        }
        
        elseif (preg_match('/^api\/admin\/reports\/employee\/(\d+)$/', $route, $matches) && $requestMethod === 'GET') {
            require_once __DIR__ . '/controllers/ReportController.php';
            $controller = new ReportController();
            $controller->employee($matches[1]);
        }
        
        elseif ($route === 'api/admin/reports/top-apps' && $requestMethod === 'GET') {
            require_once __DIR__ . '/controllers/ReportController.php';
            $controller = new ReportController();
            $controller->topApps();
        }
        
        elseif ($route === 'api/admin/reports/top-domains' && $requestMethod === 'GET') {
            require_once __DIR__ . '/controllers/ReportController.php';
            $controller = new ReportController();
            $controller->topDomains();
        }
        
        else {
            Response::notFound('Endpoint not found');
        }
    }
} catch (Exception $e) {
    Response::serverError('Server error: ' . $e->getMessage());
}
