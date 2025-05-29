<?php
// /home/ubuntu/siwes_system/app/Controllers/StudentController.php

require_once MODELS_PATH . "/StudentModel.php";
require_once MODELS_PATH . "/LogEntryModel.php";
require_once MODELS_PATH . "/UserModel.php";

class StudentController {
    private $studentModel;
    private $logEntryModel;
    private $userModel;

    public function __construct() {
        $this->studentModel = new StudentModel();
        $this->logEntryModel = new LogEntryModel();
        $this->userModel = new UserModel();
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }
        
        // Check if user is a student
        if ($_SESSION['role_id'] != 1) { // 1 = Student role
            header('Location: /');
            exit;
        }
    }

    // Student dashboard
    public function dashboard() {
        $userId = $_SESSION['user_id'];
        $student = $this->studentModel->getByUserId($userId);
        
        if (!$student) {
            // If student profile doesn't exist yet, redirect to profile creation
            header('Location: /student/profile');
            exit;
        }
        
        // Calculate SIWES progress
        $progress = $this->studentModel->calculateProgress($student['student_id']);
        
        // Get log entry counts
        $logCounts = $this->studentModel->getLogEntryCounts($student['student_id']);
        
        // Get recent log entries
        $recentLogs = $this->logEntryModel->getByStudentId($student['student_id'], 5);
        
        // Load dashboard view
        require_once VIEWS_PATH . "/student/dashboard.php";
    }

    // Student profile
    public function profile() {
        $userId = $_SESSION['user_id'];
        $student = $this->studentModel->getByUserId($userId);
        $user = $this->userModel->findById($userId);
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize and validate input
            $matric_number = filter_input(INPUT_POST, 'matric_number', FILTER_SANITIZE_STRING);
            $department_id = filter_input(INPUT_POST, 'department_id', FILTER_VALIDATE_INT);
            $organization_name = filter_input(INPUT_POST, 'organization_name', FILTER_SANITIZE_STRING);
            $organization_address = filter_input(INPUT_POST, 'organization_address', FILTER_SANITIZE_STRING);
            $start_date = filter_input(INPUT_POST, 'start_date', FILTER_SANITIZE_STRING);
            $end_date = filter_input(INPUT_POST, 'end_date', FILTER_SANITIZE_STRING);
            
            // Validate input
            $errors = [];
            if (empty($matric_number)) {
                $errors['matric_number'] = 'Matriculation number is required';
            }
            if (empty($department_id)) {
                $errors['department_id'] = 'Department is required';
            }
            if (empty($organization_name)) {
                $errors['organization_name'] = 'Organization name is required';
            }
            if (empty($organization_address)) {
                $errors['organization_address'] = 'Organization address is required';
            }
            if (empty($start_date)) {
                $errors['start_date'] = 'SIWES start date is required';
            }
            if (empty($end_date)) {
                $errors['end_date'] = 'SIWES end date is required';
            } elseif ($start_date >= $end_date) {
                $errors['end_date'] = 'End date must be after start date';
            }
            
            if (empty($errors)) {
                $studentData = [
                    'user_id' => $userId,
                    'matric_number' => $matric_number,
                    'department_id' => $department_id,
                    'siwes_organization_name' => $organization_name,
                    'siwes_organization_address' => $organization_address,
                    'siwes_start_date' => $start_date,
                    'siwes_end_date' => $end_date
                ];
                
                if ($student) {
                    // Update existing student profile
                    $result = $this->studentModel->update($student['student_id'], $studentData);
                } else {
                    // Create new student profile
                    $result = $this->studentModel->create($studentData);
                }
                
                if ($result) {
                    // Redirect to dashboard
                    header('Location: /student/dashboard');
                    exit;
                } else {
                    $errors['profile'] = 'Failed to save profile. Please try again.';
                }
            }
            
            // If we get here, there were errors
            require_once VIEWS_PATH . "/student/profile.php";
        } else {
            // Display profile form
            require_once VIEWS_PATH . "/student/profile.php";
        }
    }

    // Log entry form
    public function logEntry() {
        $userId = $_SESSION['user_id'];
        $student = $this->studentModel->getByUserId($userId);
        
        if (!$student) {
            // If student profile doesn't exist yet, redirect to profile creation
            header('Location: /student/profile');
            exit;
        }
        
        // Default to today's date
        $today = date('Y-m-d');
        
        // Check if a log already exists for today
        $logExists = $this->logEntryModel->existsForDateAndStudent($student['student_id'], $today);
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize and validate input
            $log_date = filter_input(INPUT_POST, 'log_date', FILTER_SANITIZE_STRING);
            $activities = filter_input(INPUT_POST, 'activities', FILTER_SANITIZE_STRING);
            $learnings = filter_input(INPUT_POST, 'learnings', FILTER_SANITIZE_STRING);
            $signature = $_POST['signature'] ?? '';
            
            // Get geolocation data if provided
            $geolocation = null;
            if (!empty($_POST['latitude']) && !empty($_POST['longitude'])) {
                $geolocation = json_encode([
                    'latitude' => $_POST['latitude'],
                    'longitude' => $_POST['longitude'],
                    'accuracy' => $_POST['accuracy'] ?? null,
                    'timestamp' => time()
                ]);
            }
            
            // Validate input
            $errors = [];
            if (empty($log_date)) {
                $errors['log_date'] = 'Date is required';
            } elseif ($log_date > $today) {
                $errors['log_date'] = 'Cannot log entries for future dates';
            } elseif ($this->logEntryModel->existsForDateAndStudent($student['student_id'], $log_date) && $log_date != $today) {
                $errors['log_date'] = 'A log entry already exists for this date';
            }
            
            if (empty($activities)) {
                $errors['activities'] = 'Activities performed is required';
            }
            
            if (empty($signature)) {
                $errors['signature'] = 'Digital signature is required';
            }
            
            if (empty($errors)) {
                // Save signature as image
                $signature_filename = 'student_sig_' . $student['student_id'] . '_' . time() . '.png';
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
                
                // Create log entry
                $logData = [
                    'student_id' => $student['student_id'],
                    'log_date' => $log_date,
                    'activities_performed' => $activities,
                    'key_learnings' => $learnings,
                    'student_digital_signature_path' => $signature_path,
                    'geolocation_data' => $geolocation,
                    'status' => 'Pending',
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                $log_entry_id = $this->logEntryModel->create($logData);
                
                if ($log_entry_id) {
                    // Handle media uploads
                    if (!empty($_FILES['media']['name'][0])) {
                        $this->handleMediaUpload($log_entry_id);
                    }
                    
                    // Set success message and redirect to dashboard
                    $_SESSION['log_success'] = 'Log entry submitted successfully!';
                    header('Location: /student/dashboard');
                    exit;
                } else {
                    $errors['log'] = 'Failed to save log entry. Please try again.';
                }
            }
            
            // If we get here, there were errors
            require_once VIEWS_PATH . "/student/log_entry_form.php";
        } else {
            // Display log entry form
            require_once VIEWS_PATH . "/student/log_entry_form.php";
        }
    }

    // View log entries
    public function logs() {
        $userId = $_SESSION['user_id'];
        $student = $this->studentModel->getByUserId($userId);
        
        if (!$student) {
            // If student profile doesn't exist yet, redirect to profile creation
            header('Location: /student/profile');
            exit;
        }
        
        // Pagination
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        // Get log entries
        $logs = $this->logEntryModel->getByStudentId($student['student_id'], $limit, $offset);
        $totalLogs = $this->logEntryModel->countByStudentId($student['student_id']);
        $totalPages = ceil($totalLogs / $limit);
        
        // Load logs view
        require_once VIEWS_PATH . "/student/logs.php";
    }

    // View a specific log entry
    public function viewLog($logId) {
        $userId = $_SESSION['user_id'];
        $student = $this->studentModel->getByUserId($userId);
        
        if (!$student) {
            // If student profile doesn't exist yet, redirect to profile creation
            header('Location: /student/profile');
            exit;
        }
        
        // Get log entry
        $log = $this->logEntryModel->getById($logId);
        
        // Check if log exists and belongs to this student
        if (!$log || $log['student_id'] != $student['student_id']) {
            header('Location: /student/logs');
            exit;
        }
        
        // Load log view
        require_once VIEWS_PATH . "/student/view_log.php";
    }

    // Handle media upload for log entries
    private function handleMediaUpload($logEntryId) {
        // Define allowed file types and max size
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        
        // Get uploaded files
        $files = $_FILES['media'];
        $fileCount = count($files['name']);
        
        // Ensure uploads directory exists
        $uploadDir = BASE_PATH . '/uploads/log_media/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Process each file
        for ($i = 0; $i < $fileCount; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $tmpName = $files['tmp_name'][$i];
                $name = $files['name'][$i];
                $type = $files['type'][$i];
                $size = $files['size'][$i];
                
                // Validate file type and size
                if (!in_array($type, $allowedTypes)) {
                    continue; // Skip invalid file types
                }
                
                if ($size > $maxSize) {
                    continue; // Skip files that are too large
                }
                
                // Generate unique filename
                $extension = pathinfo($name, PATHINFO_EXTENSION);
                $newFilename = uniqid('log_') . '_' . time() . '.' . $extension;
                $filePath = '/uploads/log_media/' . $newFilename;
                $fullPath = $uploadDir . $newFilename;
                
                // Move file to uploads directory
                if (move_uploaded_file($tmpName, $fullPath)) {
                    // Add to database
                    $this->logEntryModel->addMedia($logEntryId, $filePath, $type);
                }
            }
        }
    }
}
?>
