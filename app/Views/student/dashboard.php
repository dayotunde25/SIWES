<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - SIWES Reporting System</title>
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
                        <a class="nav-link active" href="/student/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/student/logEntry">New Log Entry</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/student/logs">View Logs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/student/profile">Profile</a>
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
        <?php if (isset($_SESSION['log_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php 
                    echo $_SESSION['log_success']; 
                    unset($_SESSION['log_success']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">SIWES Progress</h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-4 text-center">
                                <div class="progress-circle position-relative" style="width: 150px; height: 150px; margin: 0 auto;">
                                    <svg viewBox="0 0 36 36" class="position-absolute top-0 start-0" width="150" height="150">
                                        <path class="circle-bg"
                                            d="M18 2.0845
                                            a 15.9155 15.9155 0 0 1 0 31.831
                                            a 15.9155 15.9155 0 0 1 0 -31.831"
                                            fill="none"
                                            stroke="#eee"
                                            stroke-width="3"
                                            stroke-dasharray="100, 100"
                                        />
                                        <path class="circle"
                                            d="M18 2.0845
                                            a 15.9155 15.9155 0 0 1 0 31.831
                                            a 15.9155 15.9155 0 0 1 0 -31.831"
                                            fill="none"
                                            stroke="#4e73df"
                                            stroke-width="3"
                                            stroke-dasharray="<?php echo $progress; ?>, 100"
                                        />
                                    </svg>
                                    <div class="position-absolute top-50 start-50 translate-middle">
                                        <h2 class="mb-0"><?php echo $progress; ?>%</h2>
                                        <p class="mb-0">Complete</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <p><strong>SIWES Period:</strong> 
                                        <?php echo date('M d, Y', strtotime($student['siwes_start_date'])); ?> - 
                                        <?php echo date('M d, Y', strtotime($student['siwes_end_date'])); ?>
                                    </p>
                                    <p><strong>Organization:</strong> <?php echo htmlspecialchars($student['siwes_organization_name']); ?></p>
                                    <p><strong>Department:</strong> <?php echo htmlspecialchars($student['department_name']); ?></p>
                                    <p><strong>Institution:</strong> <?php echo htmlspecialchars($student['institution_name']); ?></p>
                                </div>
                                <a href="/student/logEntry" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Add New Log Entry
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Recent Log Entries</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recentLogs)): ?>
                            <div class="alert alert-info">
                                You haven't submitted any log entries yet. 
                                <a href="/student/logEntry" class="alert-link">Create your first log entry</a>.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Activities</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentLogs as $log): ?>
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
                                                                echo '<span class="badge bg-warning text-dark">Pending</span>';
                                                                break;
                                                            case 'Approved by Industry':
                                                                echo '<span class="badge bg-info">Industry Approved</span>';
                                                                break;
                                                            case 'Approved by School':
                                                                echo '<span class="badge bg-success">School Approved</span>';
                                                                break;
                                                            case 'Rejected by Industry':
                                                                echo '<span class="badge bg-danger">Industry Rejected</span>';
                                                                break;
                                                            case 'Rejected by School':
                                                                echo '<span class="badge bg-danger">School Rejected</span>';
                                                                break;
                                                        }
                                                    ?>
                                                </td>
                                                <td>
                                                    <a href="/student/viewLog/<?php echo $log['log_entry_id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-end">
                                <a href="/student/logs" class="btn btn-outline-primary btn-sm">View All Logs</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Supervisors</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6>Industry Supervisor</h6>
                            <?php if ($student['industry_supervisor_name']): ?>
                                <p><?php echo htmlspecialchars($student['industry_supervisor_name']); ?></p>
                            <?php else: ?>
                                <p class="text-muted">Not assigned yet</p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h6>School Supervisor</h6>
                            <?php if ($student['school_supervisor_name']): ?>
                                <p><?php echo htmlspecialchars($student['school_supervisor_name']); ?></p>
                            <?php else: ?>
                                <p class="text-muted">Not assigned yet</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Log Entry Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Entries:</span>
                            <span class="fw-bold"><?php echo $logCounts['total_count']; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Pending:</span>
                            <span class="badge bg-warning text-dark"><?php echo $logCounts['pending_count']; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Industry Approved:</span>
                            <span class="badge bg-info"><?php echo $logCounts['industry_approved_count']; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>School Approved:</span>
                            <span class="badge bg-success"><?php echo $logCounts['school_approved_count']; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Industry Rejected:</span>
                            <span class="badge bg-danger"><?php echo $logCounts['industry_rejected_count']; ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>School Rejected:</span>
                            <span class="badge bg-danger"><?php echo $logCounts['school_rejected_count']; ?></span>
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
