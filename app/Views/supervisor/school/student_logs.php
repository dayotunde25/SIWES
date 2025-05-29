<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Logs - SIWES Reporting System</title>
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
                        <a class="nav-link" href="/supervisor/schoolPendingLogs">Pending Reviews</a>
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
                <h5 class="mb-0">Student Log Entries</h5>
                <a href="/supervisor/school" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($logs)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> This student has not submitted any log entries yet.
                    </div>
                <?php else: ?>
                    <div class="mb-4">
                        <h6 class="fw-bold">Student: <?php echo htmlspecialchars($logs[0]['student_name']); ?></h6>
                        <p>Matric Number: <?php echo htmlspecialchars($logs[0]['matric_number']); ?></p>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Activities</th>
                                    <th>Status</th>
                                    <th>Industry Review</th>
                                    <th>School Review</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y', strtotime($log['log_date'])); ?></td>
                                        <td>
                                            <?php 
                                                $activities = htmlspecialchars($log['activities_performed']);
                                                echo (strlen($activities) > 50) ? substr($activities, 0, 50) . '...' : $activities;
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                                switch ($log['status']) {
                                                    case 'Pending':
                                                        echo '<span class="badge bg-warning text-dark">Pending Industry</span>';
                                                        break;
                                                    case 'Approved by Industry':
                                                        echo '<span class="badge bg-info">Pending School</span>';
                                                        break;
                                                    case 'Rejected by Industry':
                                                        echo '<span class="badge bg-danger">Industry Rejected</span>';
                                                        break;
                                                    case 'Approved by School':
                                                        echo '<span class="badge bg-success">Fully Approved</span>';
                                                        break;
                                                    case 'Rejected by School':
                                                        echo '<span class="badge bg-danger">School Rejected</span>';
                                                        break;
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($log['industry_supervisor_feedback'])): ?>
                                                <span class="badge bg-secondary" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars($log['industry_supervisor_feedback']); ?>">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-dash-circle"></i>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($log['school_supervisor_feedback'])): ?>
                                                <span class="badge bg-secondary" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars($log['school_supervisor_feedback']); ?>">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-dash-circle"></i>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="/supervisor/schoolViewLog/<?php echo $log['log_entry_id']; ?>" class="btn btn-sm btn-outline-success">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                                <?php if ($log['status'] === 'Approved by Industry'): ?>
                                                    <a href="/supervisor/schoolReviewLog/<?php echo $log['log_entry_id']; ?>" class="btn btn-sm btn-success">
                                                        <i class="bi bi-check-circle"></i> Review
                                                    </a>
                                                <?php endif; ?>
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
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
</body>
</html>
