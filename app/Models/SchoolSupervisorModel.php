<?php
// /home/ubuntu/siwes_system/app/Models/SchoolSupervisorModel.php

require_once CONFIG_PATH . "/Database.php";

class SchoolSupervisorModel {
    private $db;
    private $table = 'Users';

    public function __construct() {
        $this->db = new Database();
    }

    // Get all students assigned to a school supervisor
    public function getAssignedStudents($supervisorId) {
        $sql = "SELECT s.*, u.full_name, u.email, u.phone_number, 
                       d.name as department_name, 
                       i.name as institution_name,
                       iu.full_name as industry_supervisor_name
                FROM Students s
                JOIN Users u ON s.user_id = u.user_id
                LEFT JOIN Departments d ON s.department_id = d.department_id
                LEFT JOIN Institutions i ON d.institution_id = i.institution_id
                LEFT JOIN Users iu ON s.industry_supervisor_id = iu.user_id
                WHERE s.school_supervisor_id = :supervisor_id
                ORDER BY u.full_name";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':supervisor_id', $supervisorId);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log error
            error_log("SchoolSupervisorModel::getAssignedStudents Error: " . $e->getMessage());
            return [];
        }
    }

    // Get log entries approved by industry supervisor but pending school supervisor review
    public function getPendingLogEntries($supervisorId) {
        $sql = "SELECT l.*, u.full_name as student_name, s.matric_number,
                       iu.full_name as industry_supervisor_name
                FROM Log_Entries l
                JOIN Students s ON l.student_id = s.student_id
                JOIN Users u ON s.user_id = u.user_id
                LEFT JOIN Users iu ON s.industry_supervisor_id = iu.user_id
                WHERE s.school_supervisor_id = :supervisor_id
                AND l.status = 'Approved by Industry'
                ORDER BY l.log_date DESC";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':supervisor_id', $supervisorId);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log error
            error_log("SchoolSupervisorModel::getPendingLogEntries Error: " . $e->getMessage());
            return [];
        }
    }

    // Get all log entries for a specific student
    public function getStudentLogEntries($studentId, $supervisorId) {
        $sql = "SELECT l.*, u.full_name as student_name, s.matric_number,
                       iu.full_name as industry_supervisor_name
                FROM Log_Entries l
                JOIN Students s ON l.student_id = s.student_id
                JOIN Users u ON s.user_id = u.user_id
                LEFT JOIN Users iu ON s.industry_supervisor_id = iu.user_id
                WHERE l.student_id = :student_id
                AND s.school_supervisor_id = :supervisor_id
                ORDER BY l.log_date DESC";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':student_id', $studentId);
            $stmt->bindValue(':supervisor_id', $supervisorId);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log error
            error_log("SchoolSupervisorModel::getStudentLogEntries Error: " . $e->getMessage());
            return [];
        }
    }

    // Approve a log entry
    public function approveLogEntry($logEntryId, $feedback, $signaturePath) {
        $sql = "UPDATE Log_Entries 
                SET status = 'Approved by School', 
                    school_supervisor_feedback = :feedback,
                    school_supervisor_signature_path = :signature_path,
                    school_reviewed_at = NOW()
                WHERE log_entry_id = :log_entry_id
                AND status = 'Approved by Industry'";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':feedback', $feedback);
            $stmt->bindValue(':signature_path', $signaturePath);
            $stmt->bindValue(':log_entry_id', $logEntryId);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            // Log error
            error_log("SchoolSupervisorModel::approveLogEntry Error: " . $e->getMessage());
            return false;
        }
    }

    // Reject a log entry
    public function rejectLogEntry($logEntryId, $feedback) {
        $sql = "UPDATE Log_Entries 
                SET status = 'Rejected by School', 
                    school_supervisor_feedback = :feedback,
                    school_reviewed_at = NOW()
                WHERE log_entry_id = :log_entry_id
                AND status = 'Approved by Industry'";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':feedback', $feedback);
            $stmt->bindValue(':log_entry_id', $logEntryId);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            // Log error
            error_log("SchoolSupervisorModel::rejectLogEntry Error: " . $e->getMessage());
            return false;
        }
    }

    // Get summary statistics for a supervisor
    public function getSummaryStats($supervisorId) {
        $sql = "SELECT 
                    COUNT(DISTINCT s.student_id) as total_students,
                    COUNT(l.log_entry_id) as total_logs,
                    COUNT(CASE WHEN l.status = 'Approved by Industry' THEN 1 END) as pending_logs,
                    COUNT(CASE WHEN l.status = 'Approved by School' THEN 1 END) as approved_logs,
                    COUNT(CASE WHEN l.status = 'Rejected by School' THEN 1 END) as rejected_logs
                FROM Students s
                LEFT JOIN Log_Entries l ON s.student_id = l.student_id
                WHERE s.school_supervisor_id = :supervisor_id";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':supervisor_id', $supervisorId);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log error
            error_log("SchoolSupervisorModel::getSummaryStats Error: " . $e->getMessage());
            return [
                'total_students' => 0,
                'total_logs' => 0,
                'pending_logs' => 0,
                'approved_logs' => 0,
                'rejected_logs' => 0
            ];
        }
    }

    // Get log entry by ID (with verification that it belongs to a student assigned to this supervisor)
    public function getLogEntryById($logEntryId, $supervisorId) {
        $sql = "SELECT l.*, u.full_name as student_name, s.matric_number, 
                       s.siwes_organization_name, s.siwes_organization_address,
                       d.name as department_name, i.name as institution_name,
                       iu.full_name as industry_supervisor_name
                FROM Log_Entries l
                JOIN Students s ON l.student_id = s.student_id
                JOIN Users u ON s.user_id = u.user_id
                LEFT JOIN Departments d ON s.department_id = d.department_id
                LEFT JOIN Institutions i ON d.institution_id = i.institution_id
                LEFT JOIN Users iu ON s.industry_supervisor_id = iu.user_id
                WHERE l.log_entry_id = :log_entry_id
                AND s.school_supervisor_id = :supervisor_id
                LIMIT 1";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':log_entry_id', $logEntryId);
            $stmt->bindValue(':supervisor_id', $supervisorId);
            $stmt->execute();
            
            $logEntry = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($logEntry) {
                // Get media files for this log entry
                $mediaQuery = "SELECT * FROM Log_Entry_Media WHERE log_entry_id = :log_entry_id";
                $mediaStmt = $conn->prepare($mediaQuery);
                $mediaStmt->bindValue(':log_entry_id', $logEntryId);
                $mediaStmt->execute();
                
                $logEntry['media'] = $mediaStmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            return $logEntry;
        } catch (PDOException $e) {
            // Log error
            error_log("SchoolSupervisorModel::getLogEntryById Error: " . $e->getMessage());
            return false;
        }
    }
}
?>
