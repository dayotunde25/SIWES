<?php
// Main entry point for the SIWES Reporting System
// Define constants for paths
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONTROLLERS_PATH', APP_PATH . '/Controllers');
define('MODELS_PATH', APP_PATH . '/Models');
define('VIEWS_PATH', APP_PATH . '/Views');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');

// Start session
session_start();

// Include controllers
require_once CONTROLLERS_PATH . '/HomeController.php';
require_once CONTROLLERS_PATH . '/AuthController.php';
require_once CONTROLLERS_PATH . '/StudentController.php';
require_once CONTROLLERS_PATH . '/SupervisorController.php';
require_once CONTROLLERS_PATH . '/NotificationController.php';
require_once CONTROLLERS_PATH . '/ITFSupervisorController.php';

// Simple router
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', trim($uri, '/'));

// Default controller and action
$controller = !empty($uri[0]) ? $uri[0] : 'home';
$action = !empty($uri[1]) ? $uri[1] : 'index';
$param = !empty($uri[2]) ? $uri[2] : null;

// Route to appropriate controller and action
switch ($controller) {
    case 'auth':
        $authController = new AuthController();
        switch ($action) {
            case 'login':
                $authController->login();
                break;
            case 'register':
                $authController->register();
                break;
            case 'logout':
                $authController->logout();
                break;
            case 'processLogin':
                $authController->processLogin();
                break;
            case 'processRegistration':
                $authController->processRegistration();
                break;
            default:
                $authController->login();
                break;
        }
        break;
        
    case 'student':
        $studentController = new StudentController();
        switch ($action) {
            case 'dashboard':
                $studentController->dashboard();
                break;
            case 'profile':
                $studentController->profile();
                break;
            case 'updateProfile':
                $studentController->updateProfile();
                break;
            case 'logs':
                $studentController->logs();
                break;
            case 'logEntry':
                $studentController->logEntry();
                break;
            case 'submitLog':
                $studentController->submitLog();
                break;
            case 'viewLog':
                $studentController->viewLog($param);
                break;
            default:
                $studentController->dashboard();
                break;
        }
        break;
        
    case 'supervisor':
        $supervisorController = new SupervisorController();
        switch ($action) {
            case 'dashboard':
                $supervisorController->dashboard();
                break;
            case 'students':
                $supervisorController->students();
                break;
            case 'studentLogs':
                $supervisorController->studentLogs($param);
                break;
            case 'reviewLog':
                $supervisorController->reviewLog($param);
                break;
            case 'processReview':
                $supervisorController->processReview();
                break;
            case 'pendingLogs':
                $supervisorController->pendingLogs();
                break;
            case 'viewLog':
                $supervisorController->viewLog($param);
                break;
            default:
                $supervisorController->dashboard();
                break;
        }
        break;
        
    case 'notifications':
        $notificationController = new NotificationController();
        switch ($action) {
            case 'index':
                $notificationController->index();
                break;
            case 'markAsRead':
                $notificationController->markAsRead($param);
                break;
            case 'markAllAsRead':
                $notificationController->markAllAsRead();
                break;
            case 'delete':
                $notificationController->delete($param);
                break;
            default:
                $notificationController->index();
                break;
        }
        break;
        
    case 'itf':
        $itfSupervisorController = new ITFSupervisorController();
        switch ($action) {
            case 'dashboard':
                $itfSupervisorController->dashboard();
                break;
            case 'students':
                $itfSupervisorController->students();
                break;
            case 'studentLogs':
                $itfSupervisorController->studentLogs($param);
                break;
            case 'viewLog':
                $itfSupervisorController->viewLog($param);
                break;
            case 'reports':
                $itfSupervisorController->reports();
                break;
            case 'exportReport':
                $itfSupervisorController->exportReport();
                break;
            default:
                $itfSupervisorController->dashboard();
                break;
        }
        break;
        
    case 'home':
    default:
        $homeController = new HomeController();
        $homeController->index();
        break;
}
?>
