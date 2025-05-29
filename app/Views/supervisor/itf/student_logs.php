<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Logs - ITF Supervisor - SIWES Reporting System</title>
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
                        <a class="nav-link" href="/itf/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/itf/students">Students</a>
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
            <div class="col-md-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/itf/students">Students</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($student['full_name']); ?>'s Logs</li>
                    </ol>
                </nav>
                <h2><?php echo htmlspecialchars($student['full_name']); ?>'s SIWES Logs</h2>
                <p class="text-muted">
                    Matric Number: <?php echo htmlspecialchars($student['matric_number']); ?> | 
                    Institution: <?php echo htmlspecialchars($student['institution']); ?> |
                    Industry: <?php echo htmlspecialchars($student['industry_name']); ?>
                </p>
            </div>
            <div class="col-md-4 text-end">
                <div class="input-group">
                    <input type="text" id="logSearch" class="form-control" placeholder="Search logs...">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Student Info Card -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h5>Supervisors</h5>
                                <p>
                                    <strong>Industry Supervisor:</strong> 
                                    <?php echo !empty($student['industry_supervisor_name']) ? 
                                        htmlspecialchars($student['industry_supervisor_name']) : 
                                        '<span class="text-warning">Not Assigned</span>'; ?>
                                </p>
                                <p>
                                    <strong>School Supervisor:</strong> 
                                    <?php echo !empty($student['school_supervisor_name']) ? 
                                        htmlspecialchars($student['school_supervisor_name']) : 
                                        '<span class="text-warning">Not Assigned</span>'; ?>
                                </p>
                            </div>
                            <div class="col-md-4">
                                <h5>Contact Information</h5>
                                <p>
                                    <strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?>
                                </p>
                                <p>
                                    <strong>Phone:</strong> <?php echo !empty($student['phone']) ? 
                                        htmlspecialchars($student['phone']) : 'N/A'; ?>
                                </p>
                            </div>
                            <div class="col-md-4">
                                <h5>SIWES Progress</h5>
                                <?php
                                    // Calculate log statistics
                                    $totalLogs = count($logs);
                                    $approvedLogs = 0;
                                    $pendingLogs = 0;
                                    $rejectedLogs = 0;
                                    
                                    foreach ($logs as $log) {
                                        if ($log['status'] == 'Approved by School') {
                                            $approvedLogs++;
                                        } elseif ($log['status'] == 'Pending' || $log['status'] == 'Approved by Industry') {
                                            $pendingLogs++;
                                        } elseif ($log['status'] == 'Rejected by Industry' || $log['status'] == 'Rejected by School') {
                                            $rejectedLogs++;
                                        }
                                    }
                                    
                                    $progressPercentage = ($totalLogs > 0) ? round(($approvedLogs / $totalLogs) * 100) : 0;
                                ?>
                                <div class="progress mb-2">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $progressPercentage; ?>%">
                                        <?php echo $progressPercentage; ?>%
                                    </div>
                                </div>
                                <p class="mb-0">
                                    <span class="badge bg-success"><?php echo $approvedLogs; ?> Approved</span>
                                    <span class="badge bg-warning"><?php echo $pendingLogs; ?> Pending</span>
                                    <span class="badge bg-danger"><?php echo $rejectedLogs; ?> Rejected</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logs Table -->
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Log Entries</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="logsTable">
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
                            <?php if (empty($logs)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No log entries found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y', strtotime($log['log_date'])); ?></td>
                                        <td>
                                            <?php 
                                                $activities = htmlspecialchars($log['activities']);
                                                echo (strlen($activities) > 100) ? substr($activities, 0, 100) . '...' : $activities;
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                                $statusClass = 'bg-secondary';
                                                if ($log['status'] == 'Approved by School') {
                                                    $statusClass = 'bg-success';
                                                } elseif ($log['status'] == 'Approved by Industry') {
                                                    $statusClass = 'bg-info';
                                                } elseif ($log['status'] == 'Rejected by Industry' || $log['status'] == 'Rejected by School') {
                                                    $statusClass = 'bg-danger';
                                                } elseif ($log['status'] == 'Pending') {
                                                    $statusClass = 'bg-warning text-dark';
                                                }
                                            ?>
                                            <span class="badge <?php echo $statusClass; ?>">
                                                <?php echo htmlspecialchars($log['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($log['industry_review_date'])): ?>
                                                <small>
                                                    <?php echo date('M d, Y', strtotime($log['industry_review_date'])); ?>
                                                    by <?php echo htmlspecialchars($log['industry_supervisor_name']); ?>
                                                </small>
                                            <?php else: ?>
                                                <span class="text-muted">Not reviewed</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($log['school_review_date'])): ?>
                                                <small>
                                                    <?php echo date('M d, Y', strtotime($log['school_review_date'])); ?>
                                                    by <?php echo htmlspecialchars($log['school_supervisor_name']); ?>
                                                </small>
                                            <?php else: ?>
                                                <span class="text-muted">Not reviewed</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="/itf/viewLog/<?php echo $log['log_entry_id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> View
                                            </a>
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
        // Simple search functionality
        document.getElementById('logSearch').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const table = document.getElementById('logsTable');
            const rows = table.getElementsByTagName('tr');
            
            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                let found = false;
                
                for (let j = 0; j < cells.length; j++) {
                    const cellText = cells[j].textContent.toLowerCase();
                    if (cellText.indexOf(searchValue) > -1) {
                        found = true;
                        break;
                    }
                }
                
                if (found) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    </script>
</body>
</html>
