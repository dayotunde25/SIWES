<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Supervisor Dashboard - SIWES Reporting System</title>
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
                        <a class="nav-link active" href="/supervisor/school">Dashboard</a>
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
        <?php if (isset($_SESSION['review_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php 
                    echo $_SESSION['review_success']; 
                    unset($_SESSION['review_success']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">School Supervisor Dashboard</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Welcome to your SIWES Supervision Portal</h6>
                        <p>
                            As a School Supervisor, you are responsible for the final review and approval of student log entries
                            that have already been approved by their Industry Supervisors. Your feedback is crucial for ensuring
                            academic standards and learning outcomes are met.
                        </p>
                        
                        <?php if (empty($students)): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> You currently have no students assigned to you.
                            </div>
                        <?php else: ?>
                            <div class="mt-4">
                                <h6 class="fw-bold">Your Assigned Students</h6>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Matric Number</th>
                                                <th>Department</th>
                                                <th>Industry Supervisor</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($students as $student): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($student['matric_number']); ?></td>
                                                    <td><?php echo htmlspecialchars($student['department_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($student['industry_supervisor_name']); ?></td>
                                                    <td>
                                                        <a href="/supervisor/schoolStudentLogs/<?php echo $student['student_id']; ?>" class="btn btn-sm btn-outline-success">
                                                            <i class="bi bi-journal-text"></i> View Logs
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($pendingLogs)): ?>
                            <div class="mt-4">
                                <h6 class="fw-bold">Pending Log Entries</h6>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Student</th>
                                                <th>Date</th>
                                                <th>Industry Approved</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pendingLogs as $log): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($log['student_name']); ?></td>
                                                    <td><?php echo date('M d, Y', strtotime($log['log_date'])); ?></td>
                                                    <td><?php echo date('M d, Y', strtotime($log['industry_reviewed_at'])); ?></td>
                                                    <td>
                                                        <a href="/supervisor/schoolReviewLog/<?php echo $log['log_entry_id']; ?>" class="btn btn-sm btn-success">
                                                            <i class="bi bi-check-circle"></i> Review
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <?php if (count($pendingLogs) > 5): ?>
                                    <div class="text-end mt-2">
                                        <a href="/supervisor/schoolPendingLogs" class="btn btn-outline-success btn-sm">
                                            View All Pending Logs
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Assigned Students:</span>
                            <span class="fw-bold"><?php echo $stats['total_students']; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Log Entries:</span>
                            <span class="fw-bold"><?php echo $stats['total_logs']; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Pending Reviews:</span>
                            <span class="badge bg-warning text-dark"><?php echo $stats['pending_logs']; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Approved Entries:</span>
                            <span class="badge bg-success"><?php echo $stats['approved_logs']; ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Rejected Entries:</span>
                            <span class="badge bg-danger"><?php echo $stats['rejected_logs']; ?></span>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="/supervisor/schoolPendingLogs" class="btn btn-outline-success">
                                <i class="bi bi-list-check"></i> View All Pending Logs
                            </a>
                        </div>
                    </div>
                </div>
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
