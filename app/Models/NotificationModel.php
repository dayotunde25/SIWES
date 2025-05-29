<?php
// /home/ubuntu/siwes_system/app/Models/NotificationModel.php

require_once CONFIG_PATH . "/Database.php";

class NotificationModel {
    private $db;
    private $table = 'Notifications';

    public function __construct() {
        $this->db = new Database();
    }

    // Create a new notification
    public function create($userId, $type, $message, $relatedId = null, $isRead = 0) {
        $sql = "INSERT INTO Notifications (user_id, type, message, related_id, is_read, created_at) 
                VALUES (:user_id, :type, :message, :related_id, :is_read, NOW())";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            
            $stmt->bindValue(':user_id', $userId);
            $stmt->bindValue(':type', $type);
            $stmt->bindValue(':message', $message);
            $stmt->bindValue(':related_id', $relatedId);
            $stmt->bindValue(':is_read', $isRead);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            // Log error
            error_log("NotificationModel::create Error: " . $e->getMessage());
            return false;
        }
    }

    // Get all notifications for a user
    public function getByUserId($userId, $limit = 20) {
        $sql = "SELECT * FROM Notifications 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC 
                LIMIT :limit";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            
            $stmt->bindValue(':user_id', $userId);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log error
            error_log("NotificationModel::getByUserId Error: " . $e->getMessage());
            return [];
        }
    }

    // Get unread notifications count for a user
    public function getUnreadCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM Notifications 
                WHERE user_id = :user_id AND is_read = 0";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            
            $stmt->bindValue(':user_id', $userId);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            // Log error
            error_log("NotificationModel::getUnreadCount Error: " . $e->getMessage());
            return 0;
        }
    }

    // Mark a notification as read
    public function markAsRead($notificationId) {
        $sql = "UPDATE Notifications 
                SET is_read = 1 
                WHERE notification_id = :notification_id";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            
            $stmt->bindValue(':notification_id', $notificationId);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            // Log error
            error_log("NotificationModel::markAsRead Error: " . $e->getMessage());
            return false;
        }
    }

    // Mark all notifications as read for a user
    public function markAllAsRead($userId) {
        $sql = "UPDATE Notifications 
                SET is_read = 1 
                WHERE user_id = :user_id AND is_read = 0";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            
            $stmt->bindValue(':user_id', $userId);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            // Log error
            error_log("NotificationModel::markAllAsRead Error: " . $e->getMessage());
            return false;
        }
    }

    // Delete a notification
    public function delete($notificationId) {
        $sql = "DELETE FROM Notifications 
                WHERE notification_id = :notification_id";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            
            $stmt->bindValue(':notification_id', $notificationId);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            // Log error
            error_log("NotificationModel::delete Error: " . $e->getMessage());
            return false;
        }
    }

    // Create notification for log submission
    public function notifyLogSubmission($logEntryId, $studentId, $studentName) {
        // Get industry supervisor ID for this student
        $sql = "SELECT industry_supervisor_id FROM Students WHERE student_id = :student_id";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':student_id', $studentId);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && $result['industry_supervisor_id']) {
                $supervisorId = $result['industry_supervisor_id'];
                $message = "New log entry submitted by $studentName requires your review.";
                
                return $this->create($supervisorId, 'log_submission', $message, $logEntryId);
            }
            
            return false;
        } catch (PDOException $e) {
            // Log error
            error_log("NotificationModel::notifyLogSubmission Error: " . $e->getMessage());
            return false;
        }
    }

    // Create notification for industry supervisor review
    public function notifyIndustryReview($logEntryId, $studentId, $status, $supervisorName) {
        // Get student user ID
        $sql = "SELECT s.school_supervisor_id, u.user_id, u.full_name 
                FROM Students s
                JOIN Users u ON s.user_id = u.user_id
                WHERE s.student_id = :student_id";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':student_id', $studentId);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $studentUserId = $result['user_id'];
                $studentName = $result['full_name'];
                $schoolSupervisorId = $result['school_supervisor_id'];
                
                // Notify student
                $studentMessage = "Your log entry has been " . 
                                 ($status === 'Approved by Industry' ? 'approved' : 'rejected') . 
                                 " by $supervisorName.";
                $this->create($studentUserId, 'log_review', $studentMessage, $logEntryId);
                
                // If approved, notify school supervisor
                if ($status === 'Approved by Industry' && $schoolSupervisorId) {
                    $schoolMessage = "Log entry by $studentName approved by industry supervisor $supervisorName requires your review.";
                    $this->create($schoolSupervisorId, 'log_approval', $schoolMessage, $logEntryId);
                }
                
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            // Log error
            error_log("NotificationModel::notifyIndustryReview Error: " . $e->getMessage());
            return false;
        }
    }

    // Create notification for school supervisor review
    public function notifySchoolReview($logEntryId, $studentId, $status, $supervisorName) {
        // Get student user ID and industry supervisor ID
        $sql = "SELECT s.industry_supervisor_id, u.user_id, u.full_name 
                FROM Students s
                JOIN Users u ON s.user_id = u.user_id
                WHERE s.student_id = :student_id";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':student_id', $studentId);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $studentUserId = $result['user_id'];
                $studentName = $result['full_name'];
                $industrySupervisorId = $result['industry_supervisor_id'];
                
                // Notify student
                $studentMessage = "Your log entry has been " . 
                                 ($status === 'Approved by School' ? 'approved' : 'rejected') . 
                                 " by school supervisor $supervisorName.";
                $this->create($studentUserId, 'log_review', $studentMessage, $logEntryId);
                
                // Notify industry supervisor
                if ($industrySupervisorId) {
                    $industryMessage = "Log entry by $studentName has been " . 
                                      ($status === 'Approved by School' ? 'approved' : 'rejected') . 
                                      " by school supervisor $supervisorName.";
                    $this->create($industrySupervisorId, 'log_review', $industryMessage, $logEntryId);
                }
                
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            // Log error
            error_log("NotificationModel::notifySchoolReview Error: " . $e->getMessage());
            return false;
        }
    }

    // Send email notification
    public function sendEmailNotification($to, $subject, $message) {
        // Check if PHPMailer is available
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            // Log error
            error_log("NotificationModel::sendEmailNotification Error: PHPMailer not available");
            return false;
        }
        
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            
            // Server settings
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = SMTP_PORT;
            
            // Recipients
            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress($to);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;
            
            return $mail->send();
        } catch (Exception $e) {
            // Log error
            error_log("NotificationModel::sendEmailNotification Error: " . $e->getMessage());
            return false;
        }
    }
}
?>
