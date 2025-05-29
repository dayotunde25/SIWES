<?php
// /home/ubuntu/siwes_system/app/Models/LogEntryModel.php

require_once CONFIG_PATH . "/Database.php";

class LogEntryModel {
    private $db;
    private $table = 'Log_Entries';
    private $mediaTable = 'Log_Entry_Media';

    public function __construct() {
        $this->db = new Database();
    }

    // Create a new log entry
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
            error_log("LogEntryModel::create Error: " . $e->getMessage());
            return false;
        }
    }

    // Add media to a log entry
    public function addMedia($logEntryId, $filePath, $fileType) {
        $sql = "INSERT INTO {$this->mediaTable} (log_entry_id, file_path, file_type) 
                VALUES (:log_entry_id, :file_path, :file_type)";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':log_entry_id', $logEntryId);
            $stmt->bindValue(':file_path', $filePath);
            $stmt->bindValue(':file_type', $fileType);
            
            if ($stmt->execute()) {
                return $conn->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            // Log error
            error_log("LogEntryModel::addMedia Error: " . $e->getMessage());
            return false;
        }
    }

    // Get log entry by ID
    public function getById($logEntryId) {
        $sql = "SELECT * FROM {$this->table} WHERE log_entry_id = :log_entry_id LIMIT 1";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':log_entry_id', $logEntryId);
            $stmt->execute();
            
            $logEntry = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($logEntry) {
                // Get media files for this log entry
                $logEntry['media'] = $this->getMediaByLogEntryId($logEntryId);
            }
            
            return $logEntry;
        } catch (PDOException $e) {
            // Log error
            error_log("LogEntryModel::getById Error: " . $e->getMessage());
            return false;
        }
    }

    // Get media files for a log entry
    public function getMediaByLogEntryId($logEntryId) {
        $sql = "SELECT * FROM {$this->mediaTable} WHERE log_entry_id = :log_entry_id";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':log_entry_id', $logEntryId);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log error
            error_log("LogEntryModel::getMediaByLogEntryId Error: " . $e->getMessage());
            return [];
        }
    }

    // Get log entries by student ID
    public function getByStudentId($studentId, $limit = 10, $offset = 0) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE student_id = :student_id 
                ORDER BY log_date DESC 
                LIMIT :limit OFFSET :offset";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':student_id', $studentId);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            $logEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get media for each log entry
            foreach ($logEntries as &$entry) {
                $entry['media'] = $this->getMediaByLogEntryId($entry['log_entry_id']);
            }
            
            return $logEntries;
        } catch (PDOException $e) {
            // Log error
            error_log("LogEntryModel::getByStudentId Error: " . $e->getMessage());
            return [];
        }
    }

    // Check if a log entry exists for a specific date and student
    public function existsForDateAndStudent($studentId, $logDate) {
        $sql = "SELECT COUNT(*) FROM {$this->table} 
                WHERE student_id = :student_id AND log_date = :log_date";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':student_id', $studentId);
            $stmt->bindValue(':log_date', $logDate);
            $stmt->execute();
            
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            // Log error
            error_log("LogEntryModel::existsForDateAndStudent Error: " . $e->getMessage());
            return false;
        }
    }

    // Update log entry
    public function update($logEntryId, $data) {
        // Prepare update statement
        $setClause = '';
        foreach ($data as $key => $value) {
            $setClause .= "{$key} = :{$key}, ";
        }
        $setClause = rtrim($setClause, ', ');
        
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE log_entry_id = :log_entry_id";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            
            // Bind values
            foreach ($data as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->bindValue(':log_entry_id', $logEntryId);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            // Log error
            error_log("LogEntryModel::update Error: " . $e->getMessage());
            return false;
        }
    }

    // Count total log entries for a student
    public function countByStudentId($studentId) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE student_id = :student_id";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':student_id', $studentId);
            $stmt->execute();
            
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            // Log error
            error_log("LogEntryModel::countByStudentId Error: " . $e->getMessage());
            return 0;
        }
    }
}
?>
