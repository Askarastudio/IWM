<?php
/**
 * Activity Controller
 * INSIA Work Monitor (IWM)
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/Auth.php';
require_once __DIR__ . '/../utils/Response.php';

class ActivityController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Record activity logs (from agent)
     */
    public function logActivity() {
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? null;
        if (!$apiKey) {
            Response::unauthorized('API key required');
        }
        
        $apiKeyHash = Auth::hashApiKey($apiKey);
        $device = $this->db->fetchOne(
            "SELECT * FROM devices WHERE api_key_hash = ?",
            [$apiKeyHash]
        );
        
        if (!$device) {
            Response::unauthorized('Invalid API key');
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['activities']) || !is_array($data['activities'])) {
            Response::error('Activities array is required', 400);
        }
        
        $conn = $this->db->getConnection();
        $conn->beginTransaction();
        
        try {
            $stmt = $conn->prepare(
                "INSERT INTO activity_sessions 
                (device_id, type, app_name, window_title, domain, start_time, end_time, duration_seconds, is_work_hours) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            
            foreach ($data['activities'] as $activity) {
                $stmt->execute([
                    $device['id'],
                    $activity['type'] ?? 'APP',
                    $activity['app_name'] ?? null,
                    $activity['window_title'] ?? null,
                    $activity['domain'] ?? null,
                    $activity['start_time'],
                    $activity['end_time'] ?? null,
                    $activity['duration_seconds'] ?? 0,
                    $activity['is_work_hours'] ?? false
                ]);
            }
            
            $conn->commit();
            Response::success([], 'Activities logged successfully');
        } catch (Exception $e) {
            $conn->rollBack();
            Response::serverError('Failed to log activities: ' . $e->getMessage());
        }
    }
    
    /**
     * Record idle logs (from agent)
     */
    public function logIdle() {
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? null;
        if (!$apiKey) {
            Response::unauthorized('API key required');
        }
        
        $apiKeyHash = Auth::hashApiKey($apiKey);
        $device = $this->db->fetchOne(
            "SELECT * FROM devices WHERE api_key_hash = ?",
            [$apiKeyHash]
        );
        
        if (!$device) {
            Response::unauthorized('Invalid API key');
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['idle_logs']) || !is_array($data['idle_logs'])) {
            Response::error('Idle logs array is required', 400);
        }
        
        $conn = $this->db->getConnection();
        $conn->beginTransaction();
        
        try {
            $stmt = $conn->prepare(
                "INSERT INTO idle_logs (device_id, start_time, end_time, idle_seconds) 
                VALUES (?, ?, ?, ?)"
            );
            
            foreach ($data['idle_logs'] as $log) {
                $stmt->execute([
                    $device['id'],
                    $log['start_time'],
                    $log['end_time'] ?? null,
                    $log['idle_seconds'] ?? 0
                ]);
            }
            
            $conn->commit();
            Response::success([], 'Idle logs recorded successfully');
        } catch (Exception $e) {
            $conn->rollBack();
            Response::serverError('Failed to log idle time: ' . $e->getMessage());
        }
    }
    
    /**
     * Record USB logs (from agent)
     */
    public function logUsb() {
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? null;
        if (!$apiKey) {
            Response::unauthorized('API key required');
        }
        
        $apiKeyHash = Auth::hashApiKey($apiKey);
        $device = $this->db->fetchOne(
            "SELECT * FROM devices WHERE api_key_hash = ?",
            [$apiKeyHash]
        );
        
        if (!$device) {
            Response::unauthorized('Invalid API key');
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['usb_logs']) || !is_array($data['usb_logs'])) {
            Response::error('USB logs array is required', 400);
        }
        
        $conn = $this->db->getConnection();
        $conn->beginTransaction();
        
        try {
            $stmt = $conn->prepare(
                "INSERT INTO usb_logs (device_id, action, vendor_id, product_id, serial, device_info) 
                VALUES (?, ?, ?, ?, ?, ?)"
            );
            
            foreach ($data['usb_logs'] as $log) {
                $stmt->execute([
                    $device['id'],
                    $log['action'],
                    $log['vendor_id'] ?? null,
                    $log['product_id'] ?? null,
                    $log['serial'] ?? null,
                    $log['device_info'] ?? null
                ]);
            }
            
            $conn->commit();
            Response::success([], 'USB logs recorded successfully');
        } catch (Exception $e) {
            $conn->rollBack();
            Response::serverError('Failed to log USB activity: ' . $e->getMessage());
        }
    }
    
    /**
     * Get activities for device/employee
     */
    public function getActivities() {
        $deviceId = $_GET['device_id'] ?? null;
        $employeeId = $_GET['employee_id'] ?? null;
        $date = $_GET['date'] ?? date('Y-m-d');
        $type = $_GET['type'] ?? null;
        
        $where = ["DATE(start_time) = ?"];
        $params = [$date];
        
        if ($deviceId) {
            $where[] = "device_id = ?";
            $params[] = $deviceId;
        } elseif ($employeeId) {
            $where[] = "device_id IN (SELECT id FROM devices WHERE employee_id = ?)";
            $params[] = $employeeId;
        }
        
        if ($type) {
            $where[] = "type = ?";
            $params[] = $type;
        }
        
        $whereClause = implode(' AND ', $where);
        
        $activities = $this->db->fetchAll(
            "SELECT * FROM activity_sessions WHERE $whereClause ORDER BY start_time DESC",
            $params
        );
        
        Response::success($activities);
    }
}
