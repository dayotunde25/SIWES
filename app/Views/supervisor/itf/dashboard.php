<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITF Supervisor Dashboard - SIWES Reporting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                        <a class="nav-link active" href="/itf/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/itf/students">Students</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/itf/reports">Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/notifications">Notifications</a>
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
        <div class="row mb-4">
            <div class="col-md-12">
                <h2>ITF Supervisor Dashboard</h2>
                <p class="text-muted">Overview of SIWES program statistics and performance metrics</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <h5 class="card-title">Total Students</h5>
                        <h2 class="display-4"><?php echo $stats['total_students']; ?></h2>
                        <p class="card-text">Registered in SIWES program</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <h5 class="card-title">Approved Logs</h5>
                        <h2 class="display-4"><?php echo $stats['approved_logs']; ?></h2>
                        <p class="card-text">Fully approved by supervisors</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark h-100">
                    <div class="card-body">
                        <h5 class="card-title">Pending Logs</h5>
                        <h2 class="display-4"><?php echo $stats['pending_logs']; ?></h2>
                        <p class="card-text">Awaiting supervisor review</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white h-100">
                    <div class="card-body">
                        <h5 class="card-title">Rejected Logs</h5>
                        <h2 class="display-4"><?php echo $stats['rejected_logs']; ?></h2>
                        <p class="card-text">Rejected by supervisors</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Log Entries by Month</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="logsChart" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Log Status Distribution</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Completion Stats -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Student Completion Statistics</h5>
                        <a href="/itf/students" class="btn btn-sm btn-primary">View All Students</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Total Logs</th>
                                        <th>Approved</th>
                                        <th>Rejected</th>
                                        <th>Pending</th>
                                        <th>Completion</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($studentStats as $index => $student): ?>
                                        <?php if ($index < 5): // Show only top 5 ?>
                                            <?php 
                                                $totalLogs = (int)$student['total_logs'];
                                                $approvedLogs = (int)$student['approved_logs'];
                                                $rejectedLogs = (int)$student['rejected_logs'];
                                                $pendingLogs = (int)$student['pending_logs'];
                                                
                                                $completionPercentage = ($totalLogs > 0) ? 
                                                    round(($approvedLogs / $totalLogs) * 100) : 0;
                                                
                                                $progressClass = 'bg-danger';
                                                if ($completionPercentage >= 75) {
                                                    $progressClass = 'bg-success';
                                                } elseif ($completionPercentage >= 50) {
                                                    $progressClass = 'bg-info';
                                                } elseif ($completionPercentage >= 25) {
                                                    $progressClass = 'bg-warning';
                                                }
                                            ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                                <td><?php echo $totalLogs; ?></td>
                                                <td><?php echo $approvedLogs; ?></td>
                                                <td><?php echo $rejectedLogs; ?></td>
                                                <td><?php echo $pendingLogs; ?></td>
                                                <td>
                                                    <div class="progress">
                                                        <div class="progress-bar <?php echo $progressClass; ?>" 
                                                             role="progressbar" 
                                                             style="width: <?php echo $completionPercentage; ?>%"
                                                             aria-valuenow="<?php echo $completionPercentage; ?>" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100">
                                                            <?php echo $completionPercentage; ?>%
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="/itf/studentLogs/<?php echo $student['student_id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-journal-text"></i> View Logs
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Supervisor Performance -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Supervisor Performance</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Supervisor Name</th>
                                        <th>Assigned Students</th>
                                        <th>Total Reviews</th>
                                        <th>Approved</th>
                                        <th>Rejected</th>
                                        <th>Avg. Review Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($supervisorStats as $index => $supervisor): ?>
                                        <?php if ($index < 5): // Show only top 5 ?>
                                            <tr>
                                                <td>
                                                    <?php if ($supervisor['supervisor_type'] == 'Industry'): ?>
                                                        <span class="badge bg-primary">Industry</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-info">School</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($supervisor['full_name']); ?></td>
                                                <td><?php echo $supervisor['assigned_students']; ?></td>
                                                <td><?php echo $supervisor['total_reviews']; ?></td>
                                                <td><?php echo $supervisor['approved_logs']; ?></td>
                                                <td><?php echo $supervisor['rejected_logs']; ?></td>
                                                <td>
                                                    <?php 
                                                        $avgDays = round($supervisor['avg_review_days'], 1);
                                                        echo $avgDays > 0 ? $avgDays . ' days' : 'N/A';
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <a href="/itf/students" class="btn btn-outline-primary w-100 p-3">
                                    <i class="bi bi-people fs-4 d-block mb-2"></i>
                                    View All Students
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="/itf/reports" class="btn btn-outline-success w-100 p-3">
                                    <i class="bi bi-file-earmark-bar-graph fs-4 d-block mb-2"></i>
                                    Generate Reports
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="/itf/exportReport" class="btn btn-outline-info w-100 p-3">
                                    <i class="bi bi-download fs-4 d-block mb-2"></i>
                                    Export Current Month Report
                                </a>
                            </div>
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
    <script>
        // Chart for log entries by month
        const logsCtx = document.getElementById('logsChart').getContext('2d');
        const logsChart = new Chart(logsCtx, {
            type: 'bar',
            data: {
                labels: [
                    <?php 
                        $months = array_keys($stats['logs_by_month']);
                        foreach ($months as $month) {
                            $formattedMonth = date('M Y', strtotime($month . '-01'));
                            echo "'" . $formattedMonth . "', ";
                        }
                    ?>
                ],
                datasets: [{
                    label: 'Log Entries',
                    data: [
                        <?php 
                            foreach ($stats['logs_by_month'] as $count) {
                                echo $count . ", ";
                            }
                        ?>
                    ],
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // Chart for log status distribution
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: ['Approved', 'Pending', 'Rejected'],
                datasets: [{
                    data: [
                        <?php echo $stats['approved_logs']; ?>,
                        <?php echo $stats['pending_logs']; ?>,
                        <?php echo $stats['rejected_logs']; ?>
                    ],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(220, 53, 69, 0.7)'
                    ],
                    borderColor: [
                        'rgba(40, 167, 69, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(220, 53, 69, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>
