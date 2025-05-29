<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Log Reviews - SIWES Reporting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="/">SIWES Reporting System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/supervisor/school">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/supervisor/schoolPendingLogs">Pending Reviews</a>
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
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Pending Log Reviews</h5>
                <a href="/supervisor/school" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($pendingLogs)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> You have no pending log entries to review.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Matric Number</th>
                                    <th>Log Date</th>
                                    <th>Industry Approved</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingLogs as $log): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($log['student_name']); ?></td>
                                        <td><?php echo htmlspecialchars($log['matric_number']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($log['log_date'])); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($log['industry_reviewed_at'])); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="/supervisor/schoolViewLog/<?php echo $log['log_entry_id']; ?>" class="btn btn-sm btn-outline-success">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                                <a href="/supervisor/schoolReviewLog/<?php echo $log['log_entry_id']; ?>" class="btn btn-sm btn-success">
                                                    <i class="bi bi-check-circle"></i> Review
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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
