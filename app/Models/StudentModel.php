<?php
// /home/ubuntu/siwes_system/app/Models/StudentModel.php

require_once CONFIG_PATH . "/Database.php";

class StudentModel {
    private $db;
    private $table = 'Students';

    public function __construct() {
        $this->db = new Database();
    }

    // Create a new student record
    public function create($data) {
        // Prepare SQL statement
        $fields = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ({$fields}) VALUES ({$placeholders})";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            
            // Bind values
            foreach ($data as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            
            if ($stmt->execute()) {
                return $conn->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            // Log error
            error_log("StudentModel::create Error: " . $e->getMessage());
            return false;
        }
    }

    // Get student by user ID
    public function getByUserId($userId) {
        $sql = "SELECT s.*, 
                       d.name as department_name, 
                       i.name as institution_name,
                       u_industry.full_name as industry_supervisor_name,
                       u_school.full_name as school_supervisor_name
                FROM {$this->table} s
                LEFT JOIN Departments d ON s.department_id = d.department_id
                LEFT JOIN Institutions i ON d.institution_id = i.institution_id
                LEFT JOIN Users u_industry ON s.industry_supervisor_id = u_industry.user_id
                LEFT JOIN School_Supervisors ss ON s.school_supervisor_id = ss.supervisor_id
                LEFT JOIN Users u_school ON ss.user_id = u_school.user_id
                WHERE s.user_id = :user_id
                LIMIT 1";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':user_id', $userId);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log error
            error_log("StudentModel::getByUserId Error: " . $e->getMessage());
            return false;
        }
    }

    // Get student by ID
    public function getById($studentId) {
        $sql = "SELECT s.*, 
                       d.name as department_name, 
                       i.name as institution_name,
                       u_industry.full_name as industry_supervisor_name,
                       u_school.full_name as school_supervisor_name
                FROM {$this->table} s
                LEFT JOIN Departments d ON s.department_id = d.department_id
                LEFT JOIN Institutions i ON d.institution_id = i.institution_id
                LEFT JOIN Users u_industry ON s.industry_supervisor_id = u_industry.user_id
                LEFT JOIN School_Supervisors ss ON s.school_supervisor_id = ss.supervisor_id
                LEFT JOIN Users u_school ON ss.user_id = u_school.user_id
                WHERE s.student_id = :student_id
                LIMIT 1";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':student_id', $studentId);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log error
            error_log("StudentModel::getById Error: " . $e->getMessage());
            return false;
        }
    }

    // Update student record
    public function update($studentId, $data) {
        // Prepare update statement
        $setClause = '';
        foreach ($data as $key => $value) {
            $setClause .= "{$key} = :{$key}, ";
        }
        $setClause = rtrim($setClause, ', ');
        
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE student_id = :student_id";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            
            // Bind values
            foreach ($data as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->bindValue(':student_id', $studentId);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            // Log error
            error_log("StudentModel::update Error: " . $e->getMessage());
            return false;
        }
    }

    // Calculate SIWES progress percentage
    public function calculateProgress($studentId) {
        $student = $this->getById($studentId);
        
        if (!$student || !$student['siwes_start_date'] || !$student['siwes_end_date']) {
            return 0;
        }
        
        $startDate = new DateTime($student['siwes_start_date']);
        $endDate = new DateTime($student['siwes_end_date']);
        $currentDate = new DateTime();
        
        // If SIWES hasn't started yet
        if ($currentDate < $startDate) {
            return 0;
        }
        
        // If SIWES has ended
        if ($currentDate > $endDate) {
            return 100;
        }
        
        // Calculate progress
        $totalDays = $startDate->diff($endDate)->days;
        $daysElapsed = $startDate->diff($currentDate)->days;
        
        if ($totalDays == 0) {
            return 0;
        }
        
        $progress = ($daysElapsed / $totalDays) * 100;
        return min(round($progress), 100); // Ensure it doesn't exceed 100%
    }

    // Get log entry counts by status
    public function getLogEntryCounts($studentId) {
        $sql = "SELECT 
                    COUNT(CASE WHEN status = 'Pending' THEN 1 END) as pending_count,
                    COUNT(CASE WHEN status = 'Approved by Industry' THEN 1 END) as industry_approved_count,
                    COUNT(CASE WHEN status = 'Approved by School' THEN 1 END) as school_approved_count,
                    COUNT(CASE WHEN status = 'Rejected by Industry' THEN 1 END) as industry_rejected_count,
                    COUNT(CASE WHEN status = 'Rejected by School' THEN 1 END) as school_rejected_count,
                    COUNT(*) as total_count
                FROM Log_Entries
                WHERE student_id = :student_id";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':student_id', $studentId);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log error
            error_log("StudentModel::getLogEntryCounts Error: " . $e->getMessage());
            return [
                'pending_count' => 0,
                'industry_approved_count' => 0,
                'school_approved_count' => 0,
                'industry_rejected_count' => 0,
                'school_rejected_count' => 0,
                'total_count' => 0
            ];
        }
    }
}
?>
