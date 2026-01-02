<?php
/**
 * Report Controller
 * INSIA Work Monitor (IWM)
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/Response.php';

class ReportController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Daily Report
     */
    public function daily() {
        $date = $_GET['date'] ?? date('Y-m-d');
        $employeeId = $_GET['employee_id'] ?? null;
        
        $whereDevice = $employeeId ? "AND d.employee_id = $employeeId" : "";
        
        // Get total active time
        $activeTime = $this->db->fetchOne(
            "SELECT 
                SUM(duration_seconds) as total_seconds,
                COUNT(*) as total_sessions
             FROM activity_sessions a
             JOIN devices d ON a.device_id = d.id
             WHERE DATE(a.start_time) = ? $whereDevice",
            [$date]
        );
        
        // Get total idle time
        $idleTime = $this->db->fetchOne(
            "SELECT 
                SUM(idle_seconds) as total_seconds,
                COUNT(*) as total_sessions
             FROM idle_logs i
             JOIN devices d ON i.device_id = d.id
             WHERE DATE(i.start_time) = ? $whereDevice",
            [$date]
        );
        
        // Get top apps
        $topApps = $this->db->fetchAll(
            "SELECT 
                app_name,
                SUM(duration_seconds) as total_seconds,
                COUNT(*) as session_count
             FROM activity_sessions a
             JOIN devices d ON a.device_id = d.id
             WHERE DATE(a.start_time) = ? 
             AND a.type = 'APP' 
             AND app_name IS NOT NULL
             $whereDevice
             GROUP BY app_name
             ORDER BY total_seconds DESC
             LIMIT 10",
            [$date]
        );
        
        // Get top domains
        $topDomains = $this->db->fetchAll(
            "SELECT 
                domain,
                SUM(duration_seconds) as total_seconds,
                COUNT(*) as session_count
             FROM activity_sessions a
             JOIN devices d ON a.device_id = d.id
             WHERE DATE(a.start_time) = ? 
             AND a.type = 'WEB' 
             AND domain IS NOT NULL
             $whereDevice
             GROUP BY domain
             ORDER BY total_seconds DESC
             LIMIT 10",
            [$date]
        );
        
        Response::success([
            'date' => $date,
            'active_time' => $activeTime,
            'idle_time' => $idleTime,
            'top_apps' => $topApps,
            'top_domains' => $topDomains
        ]);
    }
    
    /**
     * Employee Report
     */
    public function employee($id) {
        $from = $_GET['from'] ?? date('Y-m-d', strtotime('-7 days'));
        $to = $_GET['to'] ?? date('Y-m-d');
        
        // Check if employee exists
        $employee = $this->db->fetchOne("SELECT * FROM employees WHERE id = ?", [$id]);
        if (!$employee) {
            Response::notFound('Employee not found');
        }
        
        // Get summary
        $summary = $this->db->fetchOne(
            "SELECT 
                SUM(a.duration_seconds) as total_active_seconds,
                COUNT(DISTINCT DATE(a.start_time)) as days_worked,
                COUNT(DISTINCT a.device_id) as devices_used
             FROM activity_sessions a
             JOIN devices d ON a.device_id = d.id
             WHERE d.employee_id = ?
             AND DATE(a.start_time) BETWEEN ? AND ?",
            [$id, $from, $to]
        );
        
        // Get daily breakdown
        $dailyBreakdown = $this->db->fetchAll(
            "SELECT 
                DATE(a.start_time) as date,
                SUM(a.duration_seconds) as active_seconds,
                COUNT(DISTINCT a.id) as session_count
             FROM activity_sessions a
             JOIN devices d ON a.device_id = d.id
             WHERE d.employee_id = ?
             AND DATE(a.start_time) BETWEEN ? AND ?
             GROUP BY DATE(a.start_time)
             ORDER BY date DESC",
            [$id, $from, $to]
        );
        
        Response::success([
            'employee' => $employee,
            'period' => ['from' => $from, 'to' => $to],
            'summary' => $summary,
            'daily_breakdown' => $dailyBreakdown
        ]);
    }
    
    /**
     * Top Apps Report
     */
    public function topApps() {
        $from = $_GET['from'] ?? date('Y-m-d', strtotime('-7 days'));
        $to = $_GET['to'] ?? date('Y-m-d');
        $limit = $_GET['limit'] ?? 20;
        
        $topApps = $this->db->fetchAll(
            "SELECT 
                app_name,
                SUM(duration_seconds) as total_seconds,
                COUNT(*) as session_count,
                COUNT(DISTINCT device_id) as device_count
             FROM activity_sessions
             WHERE DATE(start_time) BETWEEN ? AND ?
             AND type = 'APP'
             AND app_name IS NOT NULL
             GROUP BY app_name
             ORDER BY total_seconds DESC
             LIMIT ?",
            [$from, $to, (int)$limit]
        );
        
        Response::success($topApps);
    }
    
    /**
     * Top Domains Report
     */
    public function topDomains() {
        $from = $_GET['from'] ?? date('Y-m-d', strtotime('-7 days'));
        $to = $_GET['to'] ?? date('Y-m-d');
        $limit = $_GET['limit'] ?? 20;
        
        $topDomains = $this->db->fetchAll(
            "SELECT 
                domain,
                SUM(duration_seconds) as total_seconds,
                COUNT(*) as session_count,
                COUNT(DISTINCT device_id) as device_count
             FROM activity_sessions
             WHERE DATE(start_time) BETWEEN ? AND ?
             AND type = 'WEB'
             AND domain IS NOT NULL
             GROUP BY domain
             ORDER BY total_seconds DESC
             LIMIT ?",
            [$from, $to, (int)$limit]
        );
        
        Response::success($topDomains);
    }
    
    /**
     * Dashboard Statistics
     */
    public function dashboard() {
        $today = date('Y-m-d');
        
        // Total devices online
        $devicesOnline = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM devices WHERE status = 'online'"
        );
        
        // Total employees
        $totalEmployees = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM employees WHERE status = 'active'"
        );
        
        // Today's active time
        $todayActive = $this->db->fetchOne(
            "SELECT SUM(duration_seconds) as total_seconds 
             FROM activity_sessions 
             WHERE DATE(start_time) = ?",
            [$today]
        );
        
        // Today's idle time
        $todayIdle = $this->db->fetchOne(
            "SELECT SUM(idle_seconds) as total_seconds 
             FROM idle_logs 
             WHERE DATE(start_time) = ?",
            [$today]
        );
        
        Response::success([
            'devices_online' => $devicesOnline['count'] ?? 0,
            'total_employees' => $totalEmployees['count'] ?? 0,
            'today_active_seconds' => $todayActive['total_seconds'] ?? 0,
            'today_idle_seconds' => $todayIdle['total_seconds'] ?? 0
        ]);
    }
}
