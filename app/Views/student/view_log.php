<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Log Entry - SIWES Reporting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .signature-image {
            max-width: 100%;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
            background-color: #fff;
        }
        .media-image {
            max-width: 100%;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
        }
    </style>
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
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Log Entry Details</h5>
                <a href="/student/logs" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-left"></i> Back to Logs
                </a>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Date</h6>
                        <p><?php echo date('F d, Y', strtotime($log['log_date'])); ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold">Status</h6>
                        <p>
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
                        </p>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold">Activities Performed</h6>
                    <p><?php echo nl2br(htmlspecialchars($log['activities_performed'])); ?></p>
                </div>

                <?php if (!empty($log['key_learnings'])): ?>
                    <div class="mb-4">
                        <h6 class="fw-bold">Key Learnings</h6>
                        <p><?php echo nl2br(htmlspecialchars($log['key_learnings'])); ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($log['media'])): ?>
                    <div class="mb-4">
                        <h6 class="fw-bold">Media Attachments</h6>
                        <div class="row">
                            <?php foreach ($log['media'] as $media): ?>
                                <div class="col-md-4 mb-3">
                                    <img src="<?php echo $media['file_path']; ?>" alt="Log Media" class="media-image">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mb-4">
                    <h6 class="fw-bold">Student Digital Signature</h6>
                    <div class="col-md-6">
                        <img src="<?php echo $log['student_digital_signature_path']; ?>" alt="Student Signature" class="signature-image">
                    </div>
                </div>

                <?php if (!empty($log['geolocation_data'])): ?>
                    <div class="mb-4">
                        <h6 class="fw-bold">Location Information</h6>
                        <?php 
                            $geo = json_decode($log['geolocation_data'], true);
                            if ($geo): 
                        ?>
                            <p>
                                <i class="bi bi-geo-alt-fill"></i> 
                                Latitude: <?php echo $geo['latitude']; ?>, 
                                Longitude: <?php echo $geo['longitude']; ?>
                                <?php if (isset($geo['accuracy'])): ?>
                                    (Accuracy: <?php echo round($geo['accuracy']); ?> meters)
                                <?php endif; ?>
                            </p>
                            <div id="map" style="height: 300px; width: 100%;"></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($log['industry_supervisor_feedback']) || !empty($log['industry_supervisor_signature_path'])): ?>
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">Industry Supervisor Feedback</h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($log['industry_supervisor_feedback'])): ?>
                                <p><?php echo nl2br(htmlspecialchars($log['industry_supervisor_feedback'])); ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($log['industry_supervisor_signature_path'])): ?>
                                <div class="mt-3">
                                    <h6 class="fw-bold">Industry Supervisor Signature</h6>
                                    <div class="col-md-6">
                                        <img src="<?php echo $log['industry_supervisor_signature_path']; ?>" alt="Industry Supervisor Signature" class="signature-image">
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($log['school_supervisor_feedback']) || !empty($log['school_supervisor_signature_path'])): ?>
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">School Supervisor Feedback</h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($log['school_supervisor_feedback'])): ?>
                                <p><?php echo nl2br(htmlspecialchars($log['school_supervisor_feedback'])); ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($log['school_supervisor_signature_path'])): ?>
                                <div class="mt-3">
                                    <h6 class="fw-bold">School Supervisor Signature</h6>
                                    <div class="col-md-6">
                                        <img src="<?php echo $log['school_supervisor_signature_path']; ?>" alt="School Supervisor Signature" class="signature-image">
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between">
                    <a href="/student/logs" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Logs
                    </a>
                    
                    <?php if ($log['status'] === 'Rejected by Industry' || $log['status'] === 'Rejected by School'): ?>
                        <a href="/student/logEntry" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Submit New Log
                        </a>
                    <?php endif; ?>
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
    
    <?php if (!empty($log['geolocation_data']) && $geo): ?>
    <script>
        // This would be replaced with an actual map implementation (Google Maps, Leaflet, etc.)
        function initMap() {
            // Map initialization code would go here
            console.log("Map would be initialized with coordinates: <?php echo $geo['latitude']; ?>, <?php echo $geo['longitude']; ?>");
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            // Simulate map loading
            const mapDiv = document.getElementById('map');
            mapDiv.innerHTML = '<div class="alert alert-info">Map would display location at Latitude: <?php echo $geo['latitude']; ?>, Longitude: <?php echo $geo['longitude']; ?></div>';
        });
    </script>
    <?php endif; ?>
</body>
</html>
