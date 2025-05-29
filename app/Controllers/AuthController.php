<?php
// /home/ubuntu/siwes_system/app/Controllers/AuthController.php

require_once MODELS_PATH . "/UserModel.php";
require_once MODELS_PATH . "/RoleModel.php";

class AuthController {
    private $userModel;
    private $roleModel;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
    }

    // Display login form
    public function login() {
        // Check if already logged in
        if (isset($_SESSION['user_id'])) {
            $this->redirectBasedOnRole();
            exit;
        }

        // Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            
            // Validate input
            $errors = [];
            if (empty($email)) {
                $errors['email'] = 'Email is required';
            }
            if (empty($password)) {
                $errors['password'] = 'Password is required';
            }
            
            if (empty($errors)) {
                // Attempt to verify user
                $user = $this->userModel->verifyPassword($email, $password);
                
                if ($user) {
                    // Check if user is active
                    if (!$user['is_active']) {
                        $errors['login'] = 'Your account is deactivated. Please contact an administrator.';
                    } else {
                        // Set session variables
                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['role_id'] = $user['role_id'];
                        $_SESSION['full_name'] = $user['full_name'];
                        $_SESSION['email'] = $user['email'];
                        
                        // Redirect based on role
                        $this->redirectBasedOnRole();
                        exit;
                    }
                } else {
                    $errors['login'] = 'Invalid email or password';
                }
            }
            
            // If we get here, there were errors
            require_once VIEWS_PATH . "/auth/login.php";
        } else {
            // Display login form
            require_once VIEWS_PATH . "/auth/login.php";
        }
    }

    // Display registration form
    public function register() {
        // Check if already logged in
        if (isset($_SESSION['user_id'])) {
            $this->redirectBasedOnRole();
            exit;
        }
        
        // Get all roles for the form
        $roles = $this->roleModel->getAll();
        
        // Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize and validate input
            $full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $phone_number = filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_STRING);
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $role_id = filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT);
            
            // Validate input
            $errors = [];
            if (empty($full_name)) {
                $errors['full_name'] = 'Full name is required';
            }
            if (empty($email)) {
                $errors['email'] = 'Email is required';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Invalid email format';
            } elseif ($this->userModel->findByEmail($email)) {
                $errors['email'] = 'Email already exists';
            }
            if (empty($password)) {
                $errors['password'] = 'Password is required';
            } elseif (strlen($password) < 8) {
                $errors['password'] = 'Password must be at least 8 characters';
            }
            if ($password !== $confirm_password) {
                $errors['confirm_password'] = 'Passwords do not match';
            }
            if (empty($role_id)) {
                $errors['role_id'] = 'Role is required';
            }
            
            if (empty($errors)) {
                // Create user
                $userData = [
                    'full_name' => $full_name,
                    'email' => $email,
                    'phone_number' => $phone_number,
                    'password' => $password, // Will be hashed in the model
                    'role_id' => $role_id,
                    'is_active' => 1 // Active by default
                ];
                
                $user_id = $this->userModel->create($userData);
                
                if ($user_id) {
                    // Set success message and redirect to login
                    $_SESSION['registration_success'] = 'Registration successful! You can now log in.';
                    header('Location: /auth/login');
                    exit;
                } else {
                    $errors['register'] = 'Registration failed. Please try again.';
                }
            }
            
            // If we get here, there were errors
            require_once VIEWS_PATH . "/auth/register.php";
        } else {
            // Display registration form
            require_once VIEWS_PATH . "/auth/register.php";
        }
    }

    // Logout user
    public function logout() {
        // Unset all session variables
        $_SESSION = [];
        
        // Destroy the session
        session_destroy();
        
        // Redirect to login page
        header('Location: /auth/login');
        exit;
    }

    // Redirect based on user role
    private function redirectBasedOnRole() {
        switch ($_SESSION['role_id']) {
            case 1: // Student
                header('Location: /student/dashboard');
                break;
            case 2: // Industry Supervisor
                header('Location: /supervisor/industry/dashboard');
                break;
            case 3: // School Supervisor
                header('Location: /supervisor/school/dashboard');
                break;
            case 4: // ITF Supervisor
                header('Location: /itf/dashboard');
                break;
            case 5: // Admin
                header('Location: /admin/dashboard');
                break;
            default:
                header('Location: /');
                break;
        }
    }
}
?>
