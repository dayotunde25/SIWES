<?php
// /home/ubuntu/siwes_system/app/Controllers/SupervisorController.php - Adding School Supervisor methods

require_once MODELS_PATH . "/IndustrySupervisorModel.php";
require_once MODELS_PATH . "/SchoolSupervisorModel.php";
require_once MODELS_PATH . "/LogEntryModel.php";
require_once MODELS_PATH . "/UserModel.php";

class SupervisorController {
    private $industrySupervisorModel;
    private $schoolSupervisorModel;
    private $logEntryModel;
    private $userModel;

    public function __construct() {
        $this->industrySupervisorModel = new IndustrySupervisorModel();
        $this->schoolSupervisorModel = new SchoolSupervisorModel();
        $this->logEntryModel = new LogEntryModel();
        $this->userModel = new UserModel();
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }
        
        // Check if user is a supervisor (industry or school)
        if ($_SESSION['role_id'] != 2 && $_SESSION['role_id'] != 3) { // 2 = Industry Supervisor, 3 = School Supervisor
            header('Location: /');
            exit;
        }
    }

    // Industry supervisor dashboard
    public function industry() {
        $supervisorId = $_SESSION['user_id'];
        
        // Check if user is an industry supervisor
        if ($_SESSION['role_id'] != 2) {
            header('Location: /');
            exit;
        }
        
        // Get summary statistics
        $stats = $this->industrySupervisorModel->getSummaryStats($supervisorId);
        
        // Get assigned students
        $students = $this->industrySupervisorModel->getAssignedStudents($supervisorId);
        
        // Get pending log entries
        $pendingLogs = $this->industrySupervisorModel->getPendingLogEntries($supervisorId);
        
        // Load dashboard view
        require_once VIEWS_PATH . "/supervisor/industry/dashboard.php";
    }

    // School supervisor dashboard
    public function school() {
        $supervisorId = $_SESSION['user_id'];
        
        // Check if user is a school supervisor
        if ($_SESSION['role_id'] != 3) {
            header('Location: /');
            exit;
        }
        
        // Get summary statistics
        $stats = $this->schoolSupervisorModel->getSummaryStats($supervisorId);
        
        // Get assigned students
        $students = $this->schoolSupervisorModel->getAssignedStudents($supervisorId);
        
        // Get pending log entries (approved by industry, pending school review)
        $pendingLogs = $this->schoolSupervisorModel->getPendingLogEntries($supervisorId);
        
        // Load dashboard view
        require_once VIEWS_PATH . "/supervisor/school/dashboard.php";
    }

    // View all logs for a specific student (industry supervisor)
    public function studentLogs($studentId) {
        $supervisorId = $_SESSION['user_id'];
        
        // Check if user is an industry supervisor
        if ($_SESSION['role_id'] != 2) {
            header('Location: /');
            exit;
        }
        
        // Get student logs
        $logs = $this->industrySupervisorModel->getStudentLogEntries($studentId, $supervisorId);
        
        // Load student logs view
        require_once VIEWS_PATH . "/supervisor/industry/student_logs.php";
    }

    // View all logs for a specific student (school supervisor)
    public function schoolStudentLogs($studentId) {
        $supervisorId = $_SESSION['user_id'];
        
        // Check if user is a school supervisor
        if ($_SESSION['role_id'] != 3) {
            header('Location: /');
            exit;
        }
        
        // Get student logs
        $logs = $this->schoolSupervisorModel->getStudentLogEntries($studentId, $supervisorId);
        
        // Load student logs view
        require_once VIEWS_PATH . "/supervisor/school/student_logs.php";
    }

    // View a specific log entry (industry supervisor)
    public function viewLog($logEntryId) {
        $supervisorId = $_SESSION['user_id'];
        
        // Check if user is an industry supervisor
        if ($_SESSION['role_id'] != 2) {
            header('Location: /');
            exit;
        }
        
        // Get log entry
        $log = $this->industrySupervisorModel->getLogEntryById($logEntryId, $supervisorId);
        
        // Check if log exists and belongs to a student assigned to this supervisor
        if (!$log) {
            header('Location: /supervisor/industry');
            exit;
        }
        
        // Load log view
        require_once VIEWS_PATH . "/supervisor/industry/view_log.php";
    }

    // View a specific log entry (school supervisor)
    public function schoolViewLog($logEntryId) {
        $supervisorId = $_SESSION['user_id'];
        
        // Check if user is a school supervisor
        if ($_SESSION['role_id'] != 3) {
            header('Location: /');
            exit;
        }
        
        // Get log entry
        $log = $this->schoolSupervisorModel->getLogEntryById($logEntryId, $supervisorId);
        
        // Check if log exists and belongs to a student assigned to this supervisor
        if (!$log) {
            header('Location: /supervisor/school');
            exit;
        }
        
        // Load log view
        require_once VIEWS_PATH . "/supervisor/school/view_log.php";
    }

    // Review a log entry (industry supervisor)
    public function reviewLog($logEntryId) {
        $supervisorId = $_SESSION['user_id'];
        
        // Check if user is an industry supervisor
        if ($_SESSION['role_id'] != 2) {
            header('Location: /');
            exit;
        }
        
        // Get log entry
        $log = $this->industrySupervisorModel->getLogEntryById($logEntryId, $supervisorId);
        
        // Check if log exists and belongs to a student assigned to this supervisor
        if (!$log) {
            header('Location: /supervisor/industry');
            exit;
        }
        
        // Check if log is already reviewed
        if ($log['status'] !== 'Pending') {
            header('Location: /supervisor/viewLog/' . $logEntryId);
            exit;
        }
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $feedback = filter_input(INPUT_POST, 'feedback', FILTER_SANITIZE_STRING);
            
            $errors = [];
            
            if (empty($feedback)) {
                $errors['feedback'] = 'Feedback is required';
            }
            
            if (empty($errors)) {
                if ($action === 'approve') {
                    // Handle signature for approval
                    $signature = $_POST['signature'] ?? '';
                    
                    if (empty($signature)) {
                        $errors['signature'] = 'Digital signature is required for approval';
                    } else {
                        // Save signature as image
                        $signature_filename = 'industry_sig_' . $supervisorId . '_' . time() . '.png';
                        $signature_path = '/uploads/signatures/' . $signature_filename;
                        $signature_full_path = BASE_PATH . $signature_path;
                        
                        // Ensure directory exists
                        $signature_dir = dirname($signature_full_path);
                        if (!file_exists($signature_dir)) {
                            mkdir($signature_dir, 0755, true);
                        }
                        
                        // Decode and save signature
                        $signature_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signature));
                        file_put_contents($signature_full_path, $signature_data);
                        
                        // Approve log entry
                        $result = $this->industrySupervisorModel->approveLogEntry($logEntryId, $feedback, $signature_path);
                    }
                } elseif ($action === 'reject') {
                    // Reject log entry
                    $result = $this->industrySupervisorModel->rejectLogEntry($logEntryId, $feedback);
                } else {
                    $errors['action'] = 'Invalid action';
                }
                
                if (isset($result) && $result) {
                    // Set success message and redirect to dashboard
                    $_SESSION['review_success'] = 'Log entry has been ' . ($action === 'approve' ? 'approved' : 'rejected') . ' successfully!';
                    header('Location: /supervisor/industry');
                    exit;
                } else {
                    $errors['review'] = 'Failed to process review. Please try again.';
                }
            }
            
            // If we get here, there were errors
            require_once VIEWS_PATH . "/supervisor/industry/review_log.php";
        } else {
            // Display review form
            require_once VIEWS_PATH . "/supervisor/industry/review_log.php";
        }
    }

    // Review a log entry (school supervisor)
    public function schoolReviewLog($logEntryId) {
        $supervisorId = $_SESSION['user_id'];
        
        // Check if user is a school supervisor
        if ($_SESSION['role_id'] != 3) {
            header('Location: /');
            exit;
        }
        
        // Get log entry
        $log = $this->schoolSupervisorModel->getLogEntryById($logEntryId, $supervisorId);
        
        // Check if log exists and belongs to a student assigned to this supervisor
        if (!$log) {
            header('Location: /supervisor/school');
            exit;
        }
        
        // Check if log is already reviewed by industry and pending school review
        if ($log['status'] !== 'Approved by Industry') {
            header('Location: /supervisor/schoolViewLog/' . $logEntryId);
            exit;
        }
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $feedback = filter_input(INPUT_POST, 'feedback', FILTER_SANITIZE_STRING);
            
            $errors = [];
            
            if (empty($feedback)) {
                $errors['feedback'] = 'Feedback is required';
            }
            
            if (empty($errors)) {
                if ($action === 'approve') {
                    // Handle signature for approval
                    $signature = $_POST['signature'] ?? '';
                    
                    if (empty($signature)) {
                        $errors['signature'] = 'Digital signature is required for approval';
                    } else {
                        // Save signature as image
                        $signature_filename = 'school_sig_' . $supervisorId . '_' . time() . '.png';
                        $signature_path = '/uploads/signatures/' . $signature_filename;
                        $signature_full_path = BASE_PATH . $signature_path;
                        
                        // Ensure directory exists
                        $signature_dir = dirname($signature_full_path);
                        if (!file_exists($signature_dir)) {
                            mkdir($signature_dir, 0755, true);
                        }
                        
                        // Decode and save signature
                        $signature_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signature));
                        file_put_contents($signature_full_path, $signature_data);
                        
                        // Approve log entry
                        $result = $this->schoolSupervisorModel->approveLogEntry($logEntryId, $feedback, $signature_path);
                    }
                } elseif ($action === 'reject') {
                    // Reject log entry
                    $result = $this->schoolSupervisorModel->rejectLogEntry($logEntryId, $feedback);
                } else {
                    $errors['action'] = 'Invalid action';
                }
                
                if (isset($result) && $result) {
                    // Set success message and redirect to dashboard
                    $_SESSION['review_success'] = 'Log entry has been ' . ($action === 'approve' ? 'approved' : 'rejected') . ' successfully!';
                    header('Location: /supervisor/school');
                    exit;
                } else {
                    $errors['review'] = 'Failed to process review. Please try again.';
                }
            }
            
            // If we get here, there were errors
            require_once VIEWS_PATH . "/supervisor/school/review_log.php";
        } else {
            // Display review form
            require_once VIEWS_PATH . "/supervisor/school/review_log.php";
        }
    }

    // View all pending log entries (industry supervisor)
    public function pendingLogs() {
        $supervisorId = $_SESSION['user_id'];
        
        // Check if user is an industry supervisor
        if ($_SESSION['role_id'] != 2) {
            header('Location: /');
            exit;
        }
        
        // Get pending log entries
        $pendingLogs = $this->industrySupervisorModel->getPendingLogEntries($supervisorId);
        
        // Load pending logs view
        require_once VIEWS_PATH . "/supervisor/industry/pending_logs.php";
    }

    // View all pending log entries (school supervisor)
    public function schoolPendingLogs() {
        $supervisorId = $_SESSION['user_id'];
        
        // Check if user is a school supervisor
        if ($_SESSION['role_id'] != 3) {
            header('Location: /');
            exit;
        }
        
        // Get pending log entries (approved by industry, pending school review)
        $pendingLogs = $this->schoolSupervisorModel->getPendingLogEntries($supervisorId);
        
        // Load pending logs view
        require_once VIEWS_PATH . "/supervisor/school/pending_logs.php";
    }
}
?>
