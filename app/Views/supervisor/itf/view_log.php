<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Log Entry - ITF Supervisor - SIWES Reporting System</title>
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
            <div class="col-md-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/itf/students">Students</a></li>
                        <li class="breadcrumb-item"><a href="/itf/studentLogs/<?php echo $student['student_id']; ?>"><?php echo htmlspecialchars($student['full_name']); ?>'s Logs</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Log Entry (<?php echo date('M d, Y', strtotime($log['log_date'])); ?>)</li>
                    </ol>
                </nav>
                <h2>Log Entry Details</h2>
                <p class="text-muted">
                    Student: <?php echo htmlspecialchars($student['full_name']); ?> | 
                    Date: <?php echo date('F d, Y', strtotime($log['log_date'])); ?>
                </p>
            </div>
        </div>

        <!-- Log Entry Card -->
        <div class="card mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Log Entry Information</h5>
                <span class="badge 
                    <?php 
                        if ($log['status'] == 'Approved by School') {
                            echo 'bg-success';
                        } elseif ($log['status'] == 'Approved by Industry') {
                            echo 'bg-info';
                        } elseif ($log['status'] == 'Rejected by Industry' || $log['status'] == 'Rejected by School') {
                            echo 'bg-danger';
                        } else {
                            echo 'bg-warning text-dark';
                        }
                    ?>">
                    <?php echo htmlspecialchars($log['status']); ?>
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Activities Performed</h6>
                        <div class="p-3 bg-light rounded">
                            <?php echo nl2br(htmlspecialchars($log['activities'])); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Learning Outcomes</h6>
                        <div class="p-3 bg-light rounded">
                            <?php echo nl2br(htmlspecialchars($log['learning_outcomes'])); ?>
                        </div>
                    </div>
                </div>

                <?php if (!empty($log['challenges'])): ?>
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h6>Challenges Encountered</h6>
                        <div class="p-3 bg-light rounded">
                            <?php echo nl2br(htmlspecialchars($log['challenges'])); ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($log['media_url'])): ?>
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h6>Attached Media</h6>
                        <div class="p-3 bg-light rounded">
                            <?php 
                                $mediaUrl = htmlspecialchars($log['media_url']);
                                $mediaExt = pathinfo($mediaUrl, PATHINFO_EXTENSION);
                                $isImage = in_array(strtolower($mediaExt), ['jpg', 'jpeg', 'png', 'gif']);
                            ?>
                            
                            <?php if ($isImage): ?>
                                <img src="<?php echo $mediaUrl; ?>" class="img-fluid rounded" style="max-height: 300px;" alt="Attached media">
                            <?php else: ?>
                                <a href="<?php echo $mediaUrl; ?>" class="btn btn-outline-primary" target="_blank">
                                    <i class="bi bi-file-earmark"></i> View Attached File
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($log['student_signature'])): ?>
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h6>Student Signature</h6>
                        <div class="p-3 bg-light rounded">
                            <img src="<?php echo htmlspecialchars($log['student_signature']); ?>" class="img-fluid" style="max-height: 100px;" alt="Student signature">
                            <div class="text-muted mt-2">
                                Signed on: <?php echo date('F d, Y h:i A', strtotime($log['created_at'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Industry Supervisor Review -->
        <?php if (!empty($log['industry_review_date'])): ?>
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Industry Supervisor Review</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Feedback</h6>
                        <div class="p-3 bg-light rounded">
                            <?php echo nl2br(htmlspecialchars($log['industry_feedback'])); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Supervisor Information</h6>
                        <p>
                            <strong>Name:</strong> <?php echo htmlspecialchars($log['industry_supervisor_name']); ?><br>
                            <strong>Review Date:</strong> <?php echo date('F d, Y h:i A', strtotime($log['industry_review_date'])); ?><br>
                            <strong>Status:</strong> 
                            <span class="badge <?php echo (strpos($log['status'], 'Approved by Industry') !== false) ? 'bg-success' : 'bg-danger'; ?>">
                                <?php echo (strpos($log['status'], 'Approved by Industry') !== false) ? 'Approved' : 'Rejected'; ?>
                            </span>
                        </p>
                        
                        <?php if (!empty($log['industry_signature'])): ?>
                        <h6>Signature</h6>
                        <div class="p-3 bg-light rounded">
                            <img src="<?php echo htmlspecialchars($log['industry_signature']); ?>" class="img-fluid" style="max-height: 100px;" alt="Industry supervisor signature">
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- School Supervisor Review -->
        <?php if (!empty($log['school_review_date'])): ?>
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">School Supervisor Review</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Feedback</h6>
                        <div class="p-3 bg-light rounded">
                            <?php echo nl2br(htmlspecialchars($log['school_feedback'])); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Supervisor Information</h6>
                        <p>
                            <strong>Name:</strong> <?php echo htmlspecialchars($log['school_supervisor_name']); ?><br>
                            <strong>Review Date:</strong> <?php echo date('F d, Y h:i A', strtotime($log['school_review_date'])); ?><br>
                            <strong>Status:</strong> 
                            <span class="badge <?php echo (strpos($log['status'], 'Approved by School') !== false) ? 'bg-success' : 'bg-danger'; ?>">
                                <?php echo (strpos($log['status'], 'Approved by School') !== false) ? 'Approved' : 'Rejected'; ?>
                            </span>
                        </p>
                        
                        <?php if (!empty($log['school_signature'])): ?>
                        <h6>Signature</h6>
                        <div class="p-3 bg-light rounded">
                            <img src="<?php echo htmlspecialchars($log['school_signature']); ?>" class="img-fluid" style="max-height: 100px;" alt="School supervisor signature">
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Action Buttons -->
        <div class="row mb-4">
            <div class="col-md-12 text-end">
                <a href="/itf/studentLogs/<?php echo $student['student_id']; ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Logs
                </a>
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
