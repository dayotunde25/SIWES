<?php
// /home/ubuntu/siwes_system/app/Integrations/NotificationIntegration.php

require_once MODELS_PATH . "/NotificationModel.php";
require_once MODELS_PATH . "/UserModel.php";
require_once HELPERS_PATH . "/EmailHelper.php";

class NotificationIntegration {
    private $notificationModel;
    private $userModel;
    
    public function __construct() {
        $this->notificationModel = new NotificationModel();
        $this->userModel = new UserModel();
    }
    
    // Integrate with LogEntryModel for log submission notifications
    public function notifyLogSubmission($logEntryId, $studentId, $logDate) {
        // Get student and supervisor information
        $studentInfo = $this->getStudentInfo($studentId);
        if (!$studentInfo) {
            return false;
        }
        
        // Create in-app notification for industry supervisor
        $result = $this->notificationModel->notifyLogSubmission(
            $logEntryId, 
            $studentId, 
            $studentInfo['student_name']
        );
        
        // Send email notification if supervisor email is available
        if (!empty($studentInfo['supervisor_email'])) {
            $replacements = [
                'supervisor_name' => $studentInfo['supervisor_name'],
                'student_name' => $studentInfo['student_name'],
                'log_date' => date('F d, Y', strtotime($logDate)),
                'login_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/auth/login'
            ];
            
            EmailHelper::sendEmail(
                $studentInfo['supervisor_email'],
                'log_submission',
                $replacements
            );
        }
        
        return $result;
    }
    
    // Integrate with IndustrySupervisorModel for industry review notifications
    public function notifyIndustryReview($logEntryId, $studentId, $status, $feedback) {
        // Get student and supervisor information
        $studentInfo = $this->getStudentInfo($studentId);
        if (!$studentInfo) {
            return false;
        }
        
        // Create in-app notifications
        $result = $this->notificationModel->notifyIndustryReview(
            $logEntryId, 
            $studentId, 
            $status, 
            $studentInfo['supervisor_name']
        );
        
        // Send email notification to student
        if (!empty($studentInfo['student_email'])) {
            $templateName = ($status === 'Approved by Industry') ? 
                            'log_approved_industry' : 
                            'log_rejected_industry';
            
            $replacements = [
                'student_name' => $studentInfo['student_name'],
                'supervisor_name' => $studentInfo['supervisor_name'],
                'log_date' => date('F d, Y', strtotime($studentInfo['log_date'])),
                'feedback' => $feedback,
                'login_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/auth/login'
            ];
            
            EmailHelper::sendEmail(
                $studentInfo['student_email'],
                $templateName,
                $replacements
            );
        }
        
        // If approved, send email to school supervisor
        if ($status === 'Approved by Industry' && 
            !empty($studentInfo['school_supervisor_email'])) {
            
            $replacements = [
                'supervisor_name' => $studentInfo['school_supervisor_name'],
                'student_name' => $studentInfo['student_name'],
                'log_date' => date('F d, Y', strtotime($studentInfo['log_date'])),
                'login_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/auth/login'
            ];
            
            EmailHelper::sendEmail(
                $studentInfo['school_supervisor_email'],
                'school_review_needed',
                $replacements
            );
        }
        
        return $result;
    }
    
    // Integrate with SchoolSupervisorModel for school review notifications
    public function notifySchoolReview($logEntryId, $studentId, $status, $feedback) {
        // Get student and supervisor information
        $studentInfo = $this->getStudentInfo($studentId);
        if (!$studentInfo) {
            return false;
        }
        
        // Create in-app notifications
        $result = $this->notificationModel->notifySchoolReview(
            $logEntryId, 
            $studentId, 
            $status, 
            $studentInfo['school_supervisor_name']
        );
        
        // Send email notification to student
        if (!empty($studentInfo['student_email'])) {
            $templateName = ($status === 'Approved by School') ? 
                            'log_approved_school' : 
                            'log_rejected_school';
            
            $replacements = [
                'student_name' => $studentInfo['student_name'],
                'supervisor_name' => $studentInfo['school_supervisor_name'],
                'log_date' => date('F d, Y', strtotime($studentInfo['log_date'])),
                'feedback' => $feedback,
                'login_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/auth/login'
            ];
            
            EmailHelper::sendEmail(
                $studentInfo['student_email'],
                $templateName,
                $replacements
            );
        }
        
        return $result;
    }
    
    // Helper method to get student and supervisor information
    private function getStudentInfo($studentId) {
        // Get log entry information
        $sql = "SELECT l.log_date, l.log_entry_id,
                       s.student_id, s.industry_supervisor_id, s.school_supervisor_id,
                       u.user_id as student_user_id, u.full_name as student_name, u.email as student_email,
                       i.full_name as supervisor_name, i.email as supervisor_email,
                       sc.full_name as school_supervisor_name, sc.email as school_supervisor_email
                FROM Students s
                JOIN Users u ON s.user_id = u.user_id
                LEFT JOIN Users i ON s.industry_supervisor_id = i.user_id
                LEFT JOIN Users sc ON s.school_supervisor_id = sc.user_id
                LEFT JOIN Log_Entries l ON l.student_id = s.student_id
                WHERE s.student_id = :student_id
                ORDER BY l.created_at DESC
                LIMIT 1";
        
        try {
            $conn = $this->notificationModel->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':student_id', $studentId);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log error
            error_log("NotificationIntegration::getStudentInfo Error: " . $e->getMessage());
            return false;
        }
    }
}
?>
