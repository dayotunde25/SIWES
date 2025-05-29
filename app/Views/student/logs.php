<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Logs - SIWES Reporting System</title>
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
                        <a class="nav-link" href="/student/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/student/logEntry">New Log Entry</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/student/logs">View Logs</a>
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
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">My Log Entries</h5>
                <a href="/student/logEntry" class="btn btn-light btn-sm">
                    <i class="bi bi-plus-circle"></i> New Log Entry
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($logs)): ?>
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
                                    <th>Feedback</th>
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
                                            <?php if (!empty($log['industry_supervisor_feedback'])): ?>
                                                <span class="badge bg-secondary" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars($log['industry_supervisor_feedback']); ?>">
                                                    Industry Feedback
                                                </span>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($log['school_supervisor_feedback'])): ?>
                                                <span class="badge bg-secondary" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars($log['school_supervisor_feedback']); ?>">
                                                    School Feedback
                                                </span>
                                            <?php endif; ?>
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
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Log entries pagination">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
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
