<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Log Entry - SIWES Reporting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        #signature-pad {
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 100%;
            height: 200px;
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
                <h5 class="mb-0">Review Log Entry</h5>
                <a href="/supervisor/school" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
            <div class="card-body">
                <?php if (isset($errors['review'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $errors['review']; ?>
                    </div>
                <?php endif; ?>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Student</h6>
                        <p><?php echo htmlspecialchars($log['student_name']); ?> (<?php echo htmlspecialchars($log['matric_number']); ?>)</p>
                        <p><small class="text-muted"><?php echo htmlspecialchars($log['department_name']); ?>, <?php echo htmlspecialchars($log['institution_name']); ?></small></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold">Log Date</h6>
                        <p><?php echo date('F d, Y', strtotime($log['log_date'])); ?></p>
                        <p><small class="text-muted">Submitted: <?php echo date('F d, Y, g:i A', strtotime($log['created_at'])); ?></small></p>
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
                        <img src="<?php echo $log['student_digital_signature_path']; ?>" alt="Student Signature" class="media-image">
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">Industry Supervisor Review</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="fw-bold">Supervisor</h6>
                            <p><?php echo htmlspecialchars($log['industry_supervisor_name']); ?></p>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="fw-bold">Feedback</h6>
                            <p><?php echo nl2br(htmlspecialchars($log['industry_supervisor_feedback'])); ?></p>
                        </div>
                        
                        <div>
                            <h6 class="fw-bold">Industry Supervisor Signature</h6>
                            <div class="col-md-6">
                                <img src="<?php echo $log['industry_supervisor_signature_path']; ?>" alt="Industry Supervisor Signature" class="media-image">
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <form action="/supervisor/schoolReviewLog/<?php echo $log['log_entry_id']; ?>" method="post">
                    <div class="mb-3">
                        <label for="feedback" class="form-label">Your Feedback</label>
                        <textarea class="form-control <?php echo isset($errors['feedback']) ? 'is-invalid' : ''; ?>" 
                                  id="feedback" name="feedback" rows="4" required><?php echo $_POST['feedback'] ?? ''; ?></textarea>
                        <?php if (isset($errors['feedback'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $errors['feedback']; ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-text">Provide academic feedback on the student's activities and learnings.</div>
                    </div>

                    <div class="mb-4" id="signature-section" style="display: none;">
                        <label class="form-label">Your Digital Signature</label>
                        <div class="mb-2">
                            <canvas id="signature-pad"></canvas>
                            <input type="hidden" name="signature" id="signature-data">
                        </div>
                        <?php if (isset($errors['signature'])): ?>
                            <div class="text-danger mb-2">
                                <?php echo $errors['signature']; ?>
                            </div>
                        <?php endif; ?>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="clear-signature">
                            <i class="bi bi-eraser"></i> Clear Signature
                        </button>
                        <div class="form-text">Please sign using your mouse or finger (on touch devices).</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-danger" name="action" value="reject" id="reject-btn">
                            <i class="bi bi-x-circle"></i> Reject Entry
                        </button>
                        <button type="submit" class="btn btn-success" name="action" value="approve" id="approve-btn">
                            <i class="bi bi-check-circle"></i> Approve Entry
                        </button>
                    </div>
                </form>
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
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        // Signature Pad
        const canvas = document.getElementById('signature-pad');
        const signatureData = document.getElementById('signature-data');
        const clearButton = document.getElementById('clear-signature');
        const approveBtn = document.getElementById('approve-btn');
        const rejectBtn = document.getElementById('reject-btn');
        const signatureSection = document.getElementById('signature-section');
        
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)'
        });

        // Resize canvas
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear(); // Clear the canvas
        }

        window.addEventListener("resize", resizeCanvas);
        resizeCanvas();

        // Clear signature
        clearButton.addEventListener('click', function() {
            signaturePad.clear();
            signatureData.value = '';
        });

        // Show signature pad only when approving
        approveBtn.addEventListener('click', function(e) {
            if (signaturePad.isEmpty()) {
                e.preventDefault();
                alert('Please provide your signature for approval');
                return false;
            }
            
            signatureData.value = signaturePad.toDataURL();
            return true;
        });

        // Toggle signature section based on action
        document.addEventListener('DOMContentLoaded', function() {
            // Show signature section by default since it's needed for approval
            signatureSection.style.display = 'block';
            
            // Add event listeners to buttons
            approveBtn.addEventListener('mouseenter', function() {
                signatureSection.style.display = 'block';
            });
            
            rejectBtn.addEventListener('mouseenter', function() {
                signatureSection.style.display = 'none';
            });
        });
    </script>
</body>
</html>
