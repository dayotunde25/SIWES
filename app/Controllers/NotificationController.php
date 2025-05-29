<?php
// /home/ubuntu/siwes_system/app/Controllers/NotificationController.php

require_once MODELS_PATH . "/NotificationModel.php";
require_once MODELS_PATH . "/UserModel.php";

class NotificationController {
    private $notificationModel;
    private $userModel;

    public function __construct() {
        $this->notificationModel = new NotificationModel();
        $this->userModel = new UserModel();
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }
    }

    // View all notifications for the current user
    public function index() {
        $userId = $_SESSION['user_id'];
        
        // Get notifications
        $notifications = $this->notificationModel->getByUserId($userId);
        
        // Load notifications view
        require_once VIEWS_PATH . "/notifications/index.php";
    }

    // Mark a notification as read
    public function markAsRead($notificationId) {
        $userId = $_SESSION['user_id'];
        
        // Mark notification as read
        $result = $this->notificationModel->markAsRead($notificationId);
        
        // Redirect back to notifications
        header('Location: /notifications');
        exit;
    }

    // Mark all notifications as read
    public function markAllAsRead() {
        $userId = $_SESSION['user_id'];
        
        // Mark all notifications as read
        $result = $this->notificationModel->markAllAsRead($userId);
        
        // Redirect back to notifications
        header('Location: /notifications');
        exit;
    }

    // Delete a notification
    public function delete($notificationId) {
        $userId = $_SESSION['user_id'];
        
        // Delete notification
        $result = $this->notificationModel->delete($notificationId);
        
        // Redirect back to notifications
        header('Location: /notifications');
        exit;
    }

    // Get unread notifications count (for AJAX requests)
    public function getUnreadCount() {
        $userId = $_SESSION['user_id'];
        
        // Get unread count
        $count = $this->notificationModel->getUnreadCount($userId);
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode(['count' => $count]);
        exit;
    }

    // Get notifications dropdown content (for AJAX requests)
    public function getDropdownContent() {
        $userId = $_SESSION['user_id'];
        
        // Get notifications (limit to 5 for dropdown)
        $notifications = $this->notificationModel->getByUserId($userId, 5);
        
        // Get unread count
        $unreadCount = $this->notificationModel->getUnreadCount($userId);
        
        // Prepare HTML content
        $html = '';
        
        if (empty($notifications)) {
            $html .= '<div class="dropdown-item text-center">No notifications</div>';
        } else {
            foreach ($notifications as $notification) {
                $isRead = $notification['is_read'] ? '' : 'fw-bold bg-light';
                $timeAgo = $this->timeAgo($notification['created_at']);
                
                $html .= '<a class="dropdown-item ' . $isRead . '" href="/notifications/markAsRead/' . $notification['notification_id'] . '">';
                $html .= '<div class="d-flex justify-content-between align-items-center">';
                $html .= '<span>' . htmlspecialchars($notification['message']) . '</span>';
                $html .= '<small class="text-muted ms-2">' . $timeAgo . '</small>';
                $html .= '</div></a>';
            }
            
            $html .= '<div class="dropdown-divider"></div>';
            $html .= '<a class="dropdown-item text-center" href="/notifications">View all notifications</a>';
        }
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode([
            'html' => $html,
            'count' => $unreadCount
        ]);
        exit;
    }

    // Helper function to format time ago
    private function timeAgo($datetime) {
        $now = new DateTime();
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
        
        if ($diff->y > 0) {
            return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
        } elseif ($diff->m > 0) {
            return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
        } elseif ($diff->d > 0) {
            return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
        } elseif ($diff->h > 0) {
            return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
        } elseif ($diff->i > 0) {
            return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
        } else {
            return 'Just now';
        }
    }
}
?>
