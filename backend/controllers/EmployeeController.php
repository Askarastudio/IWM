<?php
/**
 * Employee Controller
 * INSIA Work Monitor (IWM)
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/Response.php';

class EmployeeController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all employees
     */
    public function index() {
        $employees = $this->db->fetchAll(
            "SELECT e.*, 
                    COUNT(DISTINCT d.id) as device_count,
                    COUNT(DISTINCT CASE WHEN d.status = 'online' THEN d.id END) as online_devices
             FROM employees e
             LEFT JOIN devices d ON e.id = d.employee_id
             GROUP BY e.id
             ORDER BY e.created_at DESC"
        );
        
        Response::success($employees);
    }
    
    /**
     * Get single employee
     */
    public function show($id) {
        $employee = $this->db->fetchOne(
            "SELECT * FROM employees WHERE id = ?",
            [$id]
        );
        
        if (!$employee) {
            Response::notFound('Employee not found');
        }
        
        $devices = $this->db->fetchAll(
            "SELECT * FROM devices WHERE employee_id = ?",
            [$id]
        );
        
        $employee['devices'] = $devices;
        
        Response::success($employee);
    }
    
    /**
     * Create employee
     */
    public function create() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['name'])) {
            Response::error('Name is required', 400);
        }
        
        $id = $this->db->insert(
            "INSERT INTO employees (name, position, work_start, work_end, status) 
             VALUES (?, ?, ?, ?, ?)",
            [
                $data['name'],
                $data['position'] ?? null,
                $data['work_start'] ?? '09:00:00',
                $data['work_end'] ?? '17:00:00',
                $data['status'] ?? 'active'
            ]
        );
        
        Response::success(['id' => $id], 'Employee created successfully', 201);
    }
    
    /**
     * Update employee
     */
    public function update($id) {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $employee = $this->db->fetchOne("SELECT * FROM employees WHERE id = ?", [$id]);
        if (!$employee) {
            Response::notFound('Employee not found');
        }
        
        $this->db->update(
            "UPDATE employees SET name = ?, position = ?, work_start = ?, work_end = ?, status = ? 
             WHERE id = ?",
            [
                $data['name'] ?? $employee['name'],
                $data['position'] ?? $employee['position'],
                $data['work_start'] ?? $employee['work_start'],
                $data['work_end'] ?? $employee['work_end'],
                $data['status'] ?? $employee['status'],
                $id
            ]
        );
        
        Response::success(['id' => $id], 'Employee updated successfully');
    }
    
    /**
     * Delete employee
     */
    public function delete($id) {
        $employee = $this->db->fetchOne("SELECT * FROM employees WHERE id = ?", [$id]);
        if (!$employee) {
            Response::notFound('Employee not found');
        }
        
        $this->db->delete("DELETE FROM employees WHERE id = ?", [$id]);
        
        Response::success([], 'Employee deleted successfully');
    }
}
