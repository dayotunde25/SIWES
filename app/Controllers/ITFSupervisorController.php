<?php
// /home/ubuntu/siwes_system/app/Controllers/ITFSupervisorController.php

require_once MODELS_PATH . "/ITFSupervisorModel.php";
require_once MODELS_PATH . "/UserModel.php";
require_once MODELS_PATH . "/StudentModel.php";
require_once MODELS_PATH . "/LogEntryModel.php";

class ITFSupervisorController {
    private $itfSupervisorModel;
    private $userModel;
    private $studentModel;
    private $logEntryModel;
    private $itfSupervisorId;

    public function __construct() {
        $this->itfSupervisorModel = new ITFSupervisorModel();
        $this->userModel = new UserModel();
        $this->studentModel = new StudentModel();
        $this->logEntryModel = new LogEntryModel();
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }
        
        // Check if user is an ITF supervisor
        if ($_SESSION['role_id'] != 5) {
            header('Location: /');
            exit;
        }
        
        // Get ITF supervisor ID
        $itfSupervisor = $this->itfSupervisorModel->getByUserId($_SESSION['user_id']);
        if ($itfSupervisor) {
            $this->itfSupervisorId = $itfSupervisor['itf_supervisor_id'];
        }
    }

    // Dashboard
    public function dashboard() {
        // Get aggregate statistics
        $stats = $this->itfSupervisorModel->getAggregateStatistics();
        
        // Get student completion stats
        $studentStats = $this->itfSupervisorModel->getStudentCompletionStats();
        
        // Get supervisor stats
        $supervisorStats = $this->itfSupervisorModel->getSupervisorStats();
        
        // Load dashboard view
        require_once VIEWS_PATH . "/supervisor/itf/dashboard.php";
    }

    // View all students
    public function students() {
        // Get all students
        $students = $this->itfSupervisorModel->getAllStudents();
        
        // Load students view
        require_once VIEWS_PATH . "/supervisor/itf/students.php";
    }

    // View student logs
    public function studentLogs($studentId) {
        // Get student details
        $student = $this->studentModel->getById($studentId);
        if (!$student) {
            header('Location: /itf/students');
            exit;
        }
        
        // Get student logs
        $logs = $this->itfSupervisorModel->getStudentLogs($studentId);
        
        // Load student logs view
        require_once VIEWS_PATH . "/supervisor/itf/student_logs.php";
    }

    // View specific log entry
    public function viewLog($logEntryId) {
        // Get log entry details
        $log = $this->itfSupervisorModel->getLogEntry($logEntryId);
        if (!$log) {
            header('Location: /itf/students');
            exit;
        }
        
        // Get student details
        $student = $this->studentModel->getById($log['student_id']);
        
        // Load view log view
        require_once VIEWS_PATH . "/supervisor/itf/view_log.php";
    }

    // Generate reports
    public function reports() {
        // Get available months for reports
        $sql = "SELECT DISTINCT DATE_FORMAT(log_date, '%Y-%m') as month 
                FROM Log_Entries 
                ORDER BY month DESC";
        
        try {
            $conn = $this->itfSupervisorModel->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $availableMonths = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $availableMonths = [];
        }
        
        // Get current month report by default
        $month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
        list($year, $monthNum) = explode('-', $month);
        
        $report = $this->itfSupervisorModel->generateMonthlyReport($monthNum, $year);
        
        // Load reports view
        require_once VIEWS_PATH . "/supervisor/itf/reports.php";
    }

    // Export report as CSV
    public function exportReport() {
        // Get month and year from request
        $month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
        list($year, $monthNum) = explode('-', $month);
        
        // Generate report
        $report = $this->itfSupervisorModel->generateMonthlyReport($monthNum, $year);
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="siwes_report_' . $month . '.csv"');
        
        // Create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');
        
        // Output report header
        fputcsv($output, ['SIWES Monthly Report', $report['period']]);
        fputcsv($output, ['']);
        
        // Output summary statistics
        fputcsv($output, ['Summary Statistics']);
        fputcsv($output, ['Total Logs', $report['total_logs']]);
        fputcsv($output, ['Approved Logs', $report['approved_logs']]);
        fputcsv($output, ['Rejected Logs', $report['rejected_logs']]);
        fputcsv($output, ['Pending Logs', $report['pending_logs']]);
        fputcsv($output, ['']);
        
        // Output student statistics
        fputcsv($output, ['Student Statistics']);
        fputcsv($output, ['Student Name', 'Total Logs', 'Approved Logs', 'Rejected Logs']);
        
        foreach ($report['student_stats'] as $student) {
            fputcsv($output, [
                $student['full_name'],
                $student['total_logs'],
                $student['approved_logs'],
                $student['rejected_logs']
            ]);
        }
        
        fputcsv($output, ['']);
        
        // Output supervisor statistics
        fputcsv($output, ['Supervisor Statistics']);
        fputcsv($output, ['Type', 'Supervisor Name', 'Total Reviews', 'Approved Logs', 'Rejected Logs']);
        
        foreach ($report['supervisor_stats'] as $supervisor) {
            fputcsv($output, [
                $supervisor['supervisor_type'],
                $supervisor['full_name'],
                $supervisor['total_reviews'],
                $supervisor['approved_logs'],
                $supervisor['rejected_logs']
            ]);
        }
        
        fputcsv($output, ['']);
        
        // Output daily log counts
        fputcsv($output, ['Daily Log Counts']);
        fputcsv($output, ['Date', 'Count']);
        
        foreach ($report['daily_logs'] as $day) {
            fputcsv($output, [
                $day['date'],
                $day['count']
            ]);
        }
        
        // Close the file pointer
        fclose($output);
        exit;
    }
}
?>
