<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - SIWES Reporting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">SIWES Reporting System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <?php if ($_SESSION['role_id'] == 1): // Student ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/student">Dashboard</a>
                        </li>
                    <?php elseif ($_SESSION['role_id'] == 2): // Industry Supervisor ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/supervisor/industry">Dashboard</a>
                        </li>
                    <?php elseif ($_SESSION['role_id'] == 3): // School Supervisor ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/supervisor/school">Dashboard</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link active" href="/notifications">Notifications</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <span class="navbar-text me-3">
                        Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>
                    </span>
                    <a href="/auth/logout" class="btn btn-outline-light btn-sm">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Notifications</h5>
                <?php if (!empty($notifications)): ?>
                    <a href="/notifications/markAllAsRead" class="btn btn-light btn-sm">
                        Mark All as Read
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (empty($notifications)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> You have no notifications.
                    </div>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($notifications as $notification): ?>
                            <div class="list-group-item list-group-item-action <?php echo $notification['is_read'] ? '' : 'bg-light'; ?>">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1 <?php echo $notification['is_read'] ? '' : 'fw-bold'; ?>">
                                        <?php 
                                            $icon = '';
                                            switch ($notification['type']) {
                                                case 'log_submission':
                                                    $icon = '<i class="bi bi-journal-plus text-primary me-2"></i>';
                                                    break;
                                                case 'log_review':
                                                    $icon = '<i class="bi bi-check-circle text-success me-2"></i>';
                                                    break;
                                                case 'log_approval':
                                                    $icon = '<i class="bi bi-check-all text-success me-2"></i>';
                                                    break;
                                                default:
                                                    $icon = '<i class="bi bi-bell text-secondary me-2"></i>';
                                            }
                                            echo $icon . htmlspecialchars($notification['message']);
                                        ?>
                                    </h6>
                                    <small class="text-muted">
                                        <?php 
                                            $now = new DateTime();
                                            $ago = new DateTime($notification['created_at']);
                                            $diff = $now->diff($ago);
                                            
                                            if ($diff->y > 0) {
                                                echo $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
                                            } elseif ($diff->m > 0) {
                                                echo $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
                                            } elseif ($diff->d > 0) {
                                                echo $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
                                            } elseif ($diff->h > 0) {
                                                echo $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
                                            } elseif ($diff->i > 0) {
                                                echo $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
                                            } else {
                                                echo 'Just now';
                                            }
                                        ?>
                                    </small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <?php if (!empty($notification['related_id'])): ?>
                                        <?php 
                                            $viewUrl = '';
                                            switch ($notification['type']) {
                                                case 'log_submission':
                                                case 'log_review':
                                                case 'log_approval':
                                                    if ($_SESSION['role_id'] == 1) { // Student
                                                        $viewUrl = '/student/viewLog/' . $notification['related_id'];
                                                    } elseif ($_SESSION['role_id'] == 2) { // Industry Supervisor
                                                        $viewUrl = '/supervisor/viewLog/' . $notification['related_id'];
                                                    } elseif ($_SESSION['role_id'] == 3) { // School Supervisor
                                                        $viewUrl = '/supervisor/schoolViewLog/' . $notification['related_id'];
                                                    }
                                                    break;
                                            }
                                            
                                            if (!empty($viewUrl)):
                                        ?>
                                            <a href="<?php echo $viewUrl; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> View Details
                                            </a>
                                        <?php else: ?>
                                            <div></div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div></div>
                                    <?php endif; ?>
                                    
                                    <div>
                                        <?php if (!$notification['is_read']): ?>
                                            <a href="/notifications/markAsRead/<?php echo $notification['notification_id']; ?>" class="btn btn-sm btn-outline-secondary me-1">
                                                <i class="bi bi-check"></i> Mark as Read
                                            </a>
                                        <?php endif; ?>
                                        <a href="/notifications/delete/<?php echo $notification['notification_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this notification?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light py-3 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> SIWES Reporting System</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
