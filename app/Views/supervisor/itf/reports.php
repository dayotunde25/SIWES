<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - ITF Supervisor - SIWES Reporting System</title>
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
                        <a class="nav-link" href="/itf/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/itf/students">Students</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/itf/reports">Reports</a>
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
            <div class="col-md-8">
                <h2>SIWES Monthly Reports</h2>
                <p class="text-muted">Generate and export reports for SIWES program activities</p>
            </div>
            <div class="col-md-4 text-end">
                <form action="/itf/reports" method="GET" class="d-flex">
                    <select name="month" class="form-select me-2">
                        <?php foreach ($availableMonths as $availMonth): ?>
                            <?php 
                                $formattedMonth = date('F Y', strtotime($availMonth['month'] . '-01'));
                                $selected = ($availMonth['month'] == $month) ? 'selected' : '';
                            ?>
                            <option value="<?php echo $availMonth['month']; ?>" <?php echo $selected; ?>>
                                <?php echo $formattedMonth; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                </form>
            </div>
        </div>

        <!-- Report Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h3><?php echo $report['period']; ?> Report</h3>
                        <p>
                            Period: <?php echo date('M d, Y', strtotime($report['start_date'])); ?> to 
                            <?php echo date('M d, Y', strtotime($report['end_date'])); ?>
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="/itf/exportReport?month=<?php echo $month; ?>" class="btn btn-success">
                            <i class="bi bi-download"></i> Export as CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <h5 class="card-title">Total Logs</h5>
                        <h2 class="display-4"><?php echo $report['total_logs']; ?></h2>
                        <p class="card-text">Submitted this month</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <h5 class="card-title">Approved Logs</h5>
                        <h2 class="display-4"><?php echo $report['approved_logs']; ?></h2>
                        <p class="card-text">Fully approved by supervisors</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark h-100">
                    <div class="card-body">
                        <h5 class="card-title">Pending Logs</h5>
                        <h2 class="display-4"><?php echo $report['pending_logs']; ?></h2>
                        <p class="card-text">Awaiting supervisor review</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white h-100">
                    <div class="card-body">
                        <h5 class="card-title">Rejected Logs</h5>
                        <h2 class="display-4"><?php echo $report['rejected_logs']; ?></h2>
                        <p class="card-text">Rejected by supervisors</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Log Chart -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Daily Log Submissions</h5>
            </div>
            <div class="card-body">
                <canvas id="dailyLogsChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Student Statistics -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Student Statistics</h5>
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
                                <th>Completion</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($report['student_stats'])): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No student data available for this period</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($report['student_stats'] as $student): ?>
                                    <?php 
                                        $totalLogs = (int)$student['total_logs'];
                                        $approvedLogs = (int)$student['approved_logs'];
                                        $rejectedLogs = (int)$student['rejected_logs'];
                                        
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
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Supervisor Statistics -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Supervisor Statistics</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Supervisor Name</th>
                                <th>Total Reviews</th>
                                <th>Approved</th>
                                <th>Rejected</th>
                                <th>Approval Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($report['supervisor_stats'])): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No supervisor data available for this period</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($report['supervisor_stats'] as $supervisor): ?>
                                    <?php 
                                        $totalReviews = (int)$supervisor['total_reviews'];
                                        $approvedLogs = (int)$supervisor['approved_logs'];
                                        $rejectedLogs = (int)$supervisor['rejected_logs'];
                                        
                                        $approvalRate = ($totalReviews > 0) ? 
                                            round(($approvedLogs / $totalReviews) * 100) : 0;
                                        
                                        $rateClass = 'bg-danger';
                                        if ($approvalRate >= 75) {
                                            $rateClass = 'bg-success';
                                        } elseif ($approvalRate >= 50) {
                                            $rateClass = 'bg-info';
                                        } elseif ($approvalRate >= 25) {
                                            $rateClass = 'bg-warning';
                                        }
                                    ?>
                                    <tr>
                                        <td>
                                            <?php if ($supervisor['supervisor_type'] == 'Industry'): ?>
                                                <span class="badge bg-primary">Industry</span>
                                            <?php else: ?>
                                                <span class="badge bg-info">School</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($supervisor['full_name']); ?></td>
                                        <td><?php echo $totalReviews; ?></td>
                                        <td><?php echo $approvedLogs; ?></td>
                                        <td><?php echo $rejectedLogs; ?></td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar <?php echo $rateClass; ?>" 
                                                     role="progressbar" 
                                                     style="width: <?php echo $approvalRate; ?>%"
                                                     aria-valuenow="<?php echo $approvalRate; ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                    <?php echo $approvalRate; ?>%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
        // Chart for daily log submissions
        const dailyLogsCtx = document.getElementById('dailyLogsChart').getContext('2d');
        const dailyLogsChart = new Chart(dailyLogsCtx, {
            type: 'line',
            data: {
                labels: [
                    <?php 
                        foreach ($report['daily_logs'] as $day) {
                            $formattedDate = date('M d', strtotime($day['date']));
                            echo "'" . $formattedDate . "', ";
                        }
                    ?>
                ],
                datasets: [{
                    label: 'Log Submissions',
                    data: [
                        <?php 
                            foreach ($report['daily_logs'] as $day) {
                                echo $day['count'] . ", ";
                            }
                        ?>
                    ],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    tension: 0.1
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
    </script>
</body>
</html>
