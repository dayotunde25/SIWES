<?php
// /home/ubuntu/siwes_system/app/Models/ITFSupervisorModel.php

require_once CONFIG_PATH . "/Database.php";

class ITFSupervisorModel {
    private $db;
    private $table = 'ITF_Supervisors';

    public function __construct() {
        $this->db = new Database();
    }

    // Get ITF supervisor by user ID
    public function getByUserId($userId) {
        $sql = "SELECT * FROM ITF_Supervisors WHERE user_id = :user_id";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            
            $stmt->bindValue(':user_id', $userId);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log error
            error_log("ITFSupervisorModel::getByUserId Error: " . $e->getMessage());
            return false;
        }
    }

    // Get all students assigned to ITF supervisor
    public function getAssignedStudents($itfSupervisorId) {
        $sql = "SELECT s.*, u.full_name, u.email, u.phone, 
                       i.full_name as industry_supervisor_name,
                       sc.full_name as school_supervisor_name
                FROM Students s
                JOIN Users u ON s.user_id = u.user_id
                LEFT JOIN Users i ON s.industry_supervisor_id = i.user_id
                LEFT JOIN Users sc ON s.school_supervisor_id = sc.user_id
                WHERE s.itf_supervisor_id = :itf_supervisor_id
                ORDER BY u.full_name ASC";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            
            $stmt->bindValue(':itf_supervisor_id', $itfSupervisorId);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log error
            error_log("ITFSupervisorModel::getAssignedStudents Error: " . $e->getMessage());
            return [];
        }
    }

    // Get all students for ITF supervisor (read-only access to all students)
    public function getAllStudents() {
        $sql = "SELECT s.*, u.full_name, u.email, u.phone, 
                       i.full_name as industry_supervisor_name,
                       sc.full_name as school_supervisor_name
                FROM Students s
                JOIN Users u ON s.user_id = u.user_id
                LEFT JOIN Users i ON s.industry_supervisor_id = i.user_id
                LEFT JOIN Users sc ON s.school_supervisor_id = sc.user_id
                ORDER BY u.full_name ASC";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log error
            error_log("ITFSupervisorModel::getAllStudents Error: " . $e->getMessage());
            return [];
        }
    }

    // Get student log entries for ITF supervisor review (read-only)
    public function getStudentLogs($studentId, $limit = 50) {
        $sql = "SELECT l.*, s.matric_number, u.full_name as student_name,
                       i.full_name as industry_supervisor_name,
                       sc.full_name as school_supervisor_name
                FROM Log_Entries l
                JOIN Students s ON l.student_id = s.student_id
                JOIN Users u ON s.user_id = u.user_id
                LEFT JOIN Users i ON s.industry_supervisor_id = i.user_id
                LEFT JOIN Users sc ON s.school_supervisor_id = sc.user_id
                WHERE l.student_id = :student_id
                ORDER BY l.log_date DESC
                LIMIT :limit";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            
            $stmt->bindValue(':student_id', $studentId);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log error
            error_log("ITFSupervisorModel::getStudentLogs Error: " . $e->getMessage());
            return [];
        }
    }

    // Get specific log entry details
    public function getLogEntry($logEntryId) {
        $sql = "SELECT l.*, s.matric_number, u.full_name as student_name,
                       i.full_name as industry_supervisor_name, i.user_id as industry_supervisor_id,
                       sc.full_name as school_supervisor_name, sc.user_id as school_supervisor_id
                FROM Log_Entries l
                JOIN Students s ON l.student_id = s.student_id
                JOIN Users u ON s.user_id = u.user_id
                LEFT JOIN Users i ON s.industry_supervisor_id = i.user_id
                LEFT JOIN Users sc ON s.school_supervisor_id = sc.user_id
                WHERE l.log_entry_id = :log_entry_id";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            
            $stmt->bindValue(':log_entry_id', $logEntryId);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log error
            error_log("ITFSupervisorModel::getLogEntry Error: " . $e->getMessage());
            return false;
        }
    }

    // Get aggregate statistics for dashboard
    public function getAggregateStatistics() {
        $stats = [
            'total_students' => 0,
            'total_logs' => 0,
            'approved_logs' => 0,
            'pending_logs' => 0,
            'rejected_logs' => 0,
            'logs_by_month' => [],
            'logs_by_status' => []
        ];
        
        try {
            $conn = $this->db->connect();
            
            // Total students
            $sql = "SELECT COUNT(*) as count FROM Students";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_students'] = $result['count'];
            
            // Total logs
            $sql = "SELECT COUNT(*) as count FROM Log_Entries";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_logs'] = $result['count'];
            
            // Logs by status
            $sql = "SELECT status, COUNT(*) as count FROM Log_Entries GROUP BY status";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($results as $row) {
                $stats['logs_by_status'][$row['status']] = $row['count'];
                
                if ($row['status'] == 'Approved by School') {
                    $stats['approved_logs'] += $row['count'];
                } elseif ($row['status'] == 'Pending' || $row['status'] == 'Approved by Industry') {
                    $stats['pending_logs'] += $row['count'];
                } elseif ($row['status'] == 'Rejected by Industry' || $row['status'] == 'Rejected by School') {
                    $stats['rejected_logs'] += $row['count'];
                }
            }
            
            // Logs by month (last 6 months)
            $sql = "SELECT DATE_FORMAT(log_date, '%Y-%m') as month, COUNT(*) as count 
                    FROM Log_Entries 
                    WHERE log_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                    GROUP BY DATE_FORMAT(log_date, '%Y-%m')
                    ORDER BY month ASC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($results as $row) {
                $stats['logs_by_month'][$row['month']] = $row['count'];
            }
            
            return $stats;
        } catch (PDOException $e) {
            // Log error
            error_log("ITFSupervisorModel::getAggregateStatistics Error: " . $e->getMessage());
            return $stats;
        }
    }

    // Get student completion statistics
    public function getStudentCompletionStats() {
        $sql = "SELECT s.student_id, u.full_name, 
                       COUNT(l.log_entry_id) as total_logs,
                       SUM(CASE WHEN l.status = 'Approved by School' THEN 1 ELSE 0 END) as approved_logs,
                       SUM(CASE WHEN l.status IN ('Rejected by Industry', 'Rejected by School') THEN 1 ELSE 0 END) as rejected_logs,
                       SUM(CASE WHEN l.status IN ('Pending', 'Approved by Industry') THEN 1 ELSE 0 END) as pending_logs
                FROM Students s
                JOIN Users u ON s.user_id = u.user_id
                LEFT JOIN Log_Entries l ON s.student_id = l.student_id
                GROUP BY s.student_id, u.full_name
                ORDER BY approved_logs DESC, total_logs DESC";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log error
            error_log("ITFSupervisorModel::getStudentCompletionStats Error: " . $e->getMessage());
            return [];
        }
    }

    // Get supervisor statistics
    public function getSupervisorStats() {
        $sql = "SELECT 'Industry' as supervisor_type, u.user_id, u.full_name,
                       COUNT(DISTINCT s.student_id) as assigned_students,
                       COUNT(l.log_entry_id) as total_reviews,
                       SUM(CASE WHEN l.status = 'Approved by Industry' THEN 1 ELSE 0 END) as approved_logs,
                       SUM(CASE WHEN l.status = 'Rejected by Industry' THEN 1 ELSE 0 END) as rejected_logs,
                       AVG(DATEDIFF(l.industry_review_date, l.created_at)) as avg_review_days
                FROM Users u
                JOIN Students s ON u.user_id = s.industry_supervisor_id
                LEFT JOIN Log_Entries l ON s.student_id = l.student_id AND l.industry_review_date IS NOT NULL
                WHERE u.role_id = 2
                GROUP BY u.user_id, u.full_name
                
                UNION
                
                SELECT 'School' as supervisor_type, u.user_id, u.full_name,
                       COUNT(DISTINCT s.student_id) as assigned_students,
                       COUNT(l.log_entry_id) as total_reviews,
                       SUM(CASE WHEN l.status = 'Approved by School' THEN 1 ELSE 0 END) as approved_logs,
                       SUM(CASE WHEN l.status = 'Rejected by School' THEN 1 ELSE 0 END) as rejected_logs,
                       AVG(DATEDIFF(l.school_review_date, l.industry_review_date)) as avg_review_days
                FROM Users u
                JOIN Students s ON u.user_id = s.school_supervisor_id
                LEFT JOIN Log_Entries l ON s.student_id = l.student_id AND l.school_review_date IS NOT NULL
                WHERE u.role_id = 3
                GROUP BY u.user_id, u.full_name
                
                ORDER BY supervisor_type, assigned_students DESC";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log error
            error_log("ITFSupervisorModel::getSupervisorStats Error: " . $e->getMessage());
            return [];
        }
    }

    // Generate monthly report data
    public function generateMonthlyReport($month = null, $year = null) {
        // If month and year not provided, use current month
        if (!$month || !$year) {
            $month = date('m');
            $year = date('Y');
        }
        
        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate));
        
        $report = [
            'period' => date('F Y', strtotime($startDate)),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_logs' => 0,
            'approved_logs' => 0,
            'rejected_logs' => 0,
            'pending_logs' => 0,
            'student_stats' => [],
            'supervisor_stats' => [],
            'daily_logs' => []
        ];
        
        try {
            $conn = $this->db->connect();
            
            // Total logs for the month
            $sql = "SELECT COUNT(*) as count, 
                           SUM(CASE WHEN status = 'Approved by School' THEN 1 ELSE 0 END) as approved,
                           SUM(CASE WHEN status IN ('Rejected by Industry', 'Rejected by School') THEN 1 ELSE 0 END) as rejected,
                           SUM(CASE WHEN status IN ('Pending', 'Approved by Industry') THEN 1 ELSE 0 END) as pending
                    FROM Log_Entries 
                    WHERE log_date BETWEEN :start_date AND :end_date";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':start_date', $startDate);
            $stmt->bindValue(':end_date', $endDate);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $report['total_logs'] = $result['count'];
            $report['approved_logs'] = $result['approved'];
            $report['rejected_logs'] = $result['rejected'];
            $report['pending_logs'] = $result['pending'];
            
            // Student stats for the month
            $sql = "SELECT s.student_id, u.full_name, 
                           COUNT(l.log_entry_id) as total_logs,
                           SUM(CASE WHEN l.status = 'Approved by School' THEN 1 ELSE 0 END) as approved_logs,
                           SUM(CASE WHEN l.status IN ('Rejected by Industry', 'Rejected by School') THEN 1 ELSE 0 END) as rejected_logs
                    FROM Students s
                    JOIN Users u ON s.user_id = u.user_id
                    LEFT JOIN Log_Entries l ON s.student_id = l.student_id AND l.log_date BETWEEN :start_date AND :end_date
                    GROUP BY s.student_id, u.full_name
                    HAVING total_logs > 0
                    ORDER BY total_logs DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':start_date', $startDate);
            $stmt->bindValue(':end_date', $endDate);
            $stmt->execute();
            $report['student_stats'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Supervisor stats for the month
            $sql = "SELECT 'Industry' as supervisor_type, u.user_id, u.full_name,
                           COUNT(l.log_entry_id) as total_reviews,
                           SUM(CASE WHEN l.status = 'Approved by Industry' THEN 1 ELSE 0 END) as approved_logs,
                           SUM(CASE WHEN l.status = 'Rejected by Industry' THEN 1 ELSE 0 END) as rejected_logs
                    FROM Users u
                    JOIN Students s ON u.user_id = s.industry_supervisor_id
                    LEFT JOIN Log_Entries l ON s.student_id = l.student_id 
                                           AND l.industry_review_date BETWEEN :start_date AND :end_date
                    WHERE u.role_id = 2
                    GROUP BY u.user_id, u.full_name
                    HAVING total_reviews > 0
                    
                    UNION
                    
                    SELECT 'School' as supervisor_type, u.user_id, u.full_name,
                           COUNT(l.log_entry_id) as total_reviews,
                           SUM(CASE WHEN l.status = 'Approved by School' THEN 1 ELSE 0 END) as approved_logs,
                           SUM(CASE WHEN l.status = 'Rejected by School' THEN 1 ELSE 0 END) as rejected_logs
                    FROM Users u
                    JOIN Students s ON u.user_id = s.school_supervisor_id
                    LEFT JOIN Log_Entries l ON s.student_id = l.student_id 
                                           AND l.school_review_date BETWEEN :start_date AND :end_date
                    WHERE u.role_id = 3
                    GROUP BY u.user_id, u.full_name
                    HAVING total_reviews > 0
                    
                    ORDER BY supervisor_type, total_reviews DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':start_date', $startDate);
            $stmt->bindValue(':end_date', $endDate);
            $stmt->execute();
            $report['supervisor_stats'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Daily log counts
            $sql = "SELECT DATE_FORMAT(log_date, '%Y-%m-%d') as date, COUNT(*) as count
                    FROM Log_Entries
                    WHERE log_date BETWEEN :start_date AND :end_date
                    GROUP BY DATE_FORMAT(log_date, '%Y-%m-%d')
                    ORDER BY date ASC";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':start_date', $startDate);
            $stmt->bindValue(':end_date', $endDate);
            $stmt->execute();
            $report['daily_logs'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $report;
        } catch (PDOException $e) {
            // Log error
            error_log("ITFSupervisorModel::generateMonthlyReport Error: " . $e->getMessage());
            return $report;
        }
    }
}
?>
