<?php
/**
 * Device Controller
 * INSIA Work Monitor (IWM)
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/Auth.php';
require_once __DIR__ . '/../utils/Response.php';

class DeviceController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all devices
     */
    public function index() {
        $devices = $this->db->fetchAll(
            "SELECT d.*, e.name as employee_name, e.position as employee_position
             FROM devices d
             LEFT JOIN employees e ON d.employee_id = e.id
             ORDER BY d.last_seen_at DESC"
        );
        
        Response::success($devices);
    }
    
    /**
     * Get single device
     */
    public function show($id) {
        $device = $this->db->fetchOne(
            "SELECT d.*, e.name as employee_name, e.position as employee_position
             FROM devices d
             LEFT JOIN employees e ON d.employee_id = e.id
             WHERE d.id = ?",
            [$id]
        );
        
        if (!$device) {
            Response::notFound('Device not found');
        }
        
        Response::success($device);
    }
    
    /**
     * Register/Create device
     */
    public function create() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['employee_id']) || !isset($data['device_name'])) {
            Response::error('Employee ID and device name are required', 400);
        }
        
        // Check if employee exists
        $employee = $this->db->fetchOne(
            "SELECT * FROM employees WHERE id = ?",
            [$data['employee_id']]
        );
        
        if (!$employee) {
            Response::error('Employee not found', 404);
        }
        
        // Generate API key
        $apiKey = Auth::generateApiKey();
        $apiKeyHash = Auth::hashApiKey($apiKey);
        
        $id = $this->db->insert(
            "INSERT INTO devices (employee_id, device_name, os_version, agent_version, api_key_hash) 
             VALUES (?, ?, ?, ?, ?)",
            [
                $data['employee_id'],
                $data['device_name'],
                $data['os_version'] ?? null,
                $data['agent_version'] ?? null,
                $apiKeyHash
            ]
        );
        
        Response::success([
            'id' => $id,
            'api_key' => $apiKey  // Only shown once during creation
        ], 'Device registered successfully', 201);
    }
    
    /**
     * Update device
     */
    public function update($id) {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $device = $this->db->fetchOne("SELECT * FROM devices WHERE id = ?", [$id]);
        if (!$device) {
            Response::notFound('Device not found');
        }
        
        $this->db->update(
            "UPDATE devices SET device_name = ?, os_version = ?, agent_version = ? WHERE id = ?",
            [
                $data['device_name'] ?? $device['device_name'],
                $data['os_version'] ?? $device['os_version'],
                $data['agent_version'] ?? $device['agent_version'],
                $id
            ]
        );
        
        Response::success(['id' => $id], 'Device updated successfully');
    }
    
    /**
     * Reset API key
     */
    public function resetApiKey($id) {
        $device = $this->db->fetchOne("SELECT * FROM devices WHERE id = ?", [$id]);
        if (!$device) {
            Response::notFound('Device not found');
        }
        
        $apiKey = Auth::generateApiKey();
        $apiKeyHash = Auth::hashApiKey($apiKey);
        
        $this->db->update(
            "UPDATE devices SET api_key_hash = ? WHERE id = ?",
            [$apiKeyHash, $id]
        );
        
        Response::success([
            'id' => $id,
            'api_key' => $apiKey  // Only shown during reset
        ], 'API key reset successfully');
    }
    
    /**
     * Delete device
     */
    public function delete($id) {
        $device = $this->db->fetchOne("SELECT * FROM devices WHERE id = ?", [$id]);
        if (!$device) {
            Response::notFound('Device not found');
        }
        
        $this->db->delete("DELETE FROM devices WHERE id = ?", [$id]);
        
        Response::success([], 'Device deleted successfully');
    }
    
    /**
     * Device Heartbeat (from agent)
     */
    public function heartbeat() {
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
        
        // Insert heartbeat
        $this->db->insert(
            "INSERT INTO heartbeats (device_id, ip_address, cpu_usage, ram_usage) 
             VALUES (?, ?, ?, ?)",
            [
                $device['id'],
                $_SERVER['REMOTE_ADDR'] ?? null,
                $data['cpu_usage'] ?? null,
                $data['ram_usage'] ?? null
            ]
        );
        
        // Update device status and last seen
        $this->db->update(
            "UPDATE devices SET status = 'online', last_seen_at = NOW() WHERE id = ?",
            [$device['id']]
        );
        
        Response::success([], 'Heartbeat recorded');
    }
}
