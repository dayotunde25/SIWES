<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Entry Form - SIWES Reporting System</title>
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
        .media-preview {
            max-width: 150px;
            max-height: 150px;
            margin: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
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
                        <a class="nav-link active" href="/student/logEntry">New Log Entry</a>
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
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Daily Log Entry</h5>
            </div>
            <div class="card-body">
                <?php if (isset($errors['log'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $errors['log']; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($logExists) && $logExists): ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> You have already submitted a log entry for today. 
                        You can view your logs <a href="/student/logs" class="alert-link">here</a>.
                    </div>
                <?php endif; ?>

                <form action="/student/logEntry" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="log_date" class="form-label">Date</label>
                        <input type="date" class="form-control <?php echo isset($errors['log_date']) ? 'is-invalid' : ''; ?>" 
                               id="log_date" name="log_date" 
                               value="<?php echo $_POST['log_date'] ?? $today; ?>"
                               max="<?php echo $today; ?>">
                        <?php if (isset($errors['log_date'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $errors['log_date']; ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-text">You can log entries for today or up to 24 hours back.</div>
                    </div>

                    <div class="mb-3">
                        <label for="activities" class="form-label">Activities Performed</label>
                        <textarea class="form-control <?php echo isset($errors['activities']) ? 'is-invalid' : ''; ?>" 
                                  id="activities" name="activities" rows="5" required><?php echo $_POST['activities'] ?? ''; ?></textarea>
                        <?php if (isset($errors['activities'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $errors['activities']; ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-text">Describe in detail the activities you performed during this day.</div>
                    </div>

                    <div class="mb-3">
                        <label for="learnings" class="form-label">Key Learnings</label>
                        <textarea class="form-control" id="learnings" name="learnings" rows="3"><?php echo $_POST['learnings'] ?? ''; ?></textarea>
                        <div class="form-text">What did you learn from today's activities? (Optional)</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Media Upload (Optional)</label>
                        <input type="file" class="form-control" name="media[]" id="media" multiple accept="image/jpeg,image/jpg,image/png">
                        <div class="form-text">You can upload up to 3 images (PNG, JPG, JPEG) with a maximum size of 2MB each.</div>
                        <div id="media-preview-container" class="d-flex flex-wrap mt-2"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Digital Signature</label>
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

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="geolocation-check">
                            <label class="form-check-label" for="geolocation-check">
                                Include my current location
                            </label>
                        </div>
                        <div id="geolocation-status" class="form-text"></div>
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">
                        <input type="hidden" name="accuracy" id="accuracy">
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Submit Log Entry</button>
                        <a href="/student/dashboard" class="btn btn-outline-secondary">Cancel</a>
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

        // Save signature data on form submit
        document.querySelector('form').addEventListener('submit', function(e) {
            if (signaturePad.isEmpty()) {
                e.preventDefault();
                alert('Please provide your signature');
                return false;
            }
            
            signatureData.value = signaturePad.toDataURL();
            return true;
        });

        // Media preview
        document.getElementById('media').addEventListener('change', function(e) {
            const previewContainer = document.getElementById('media-preview-container');
            previewContainer.innerHTML = '';
            
            const files = e.target.files;
            
            for (let i = 0; i < files.length; i++) {
                if (i >= 3) break; // Limit to 3 previews
                
                const file = files[i];
                if (!file.type.match('image.*')) continue;
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'media-preview';
                    previewContainer.appendChild(img);
                }
                reader.readAsDataURL(file);
            }
        });

        // Geolocation
        const geoCheck = document.getElementById('geolocation-check');
        const geoStatus = document.getElementById('geolocation-status');
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const accInput = document.getElementById('accuracy');

        geoCheck.addEventListener('change', function() {
            if (this.checked) {
                if (navigator.geolocation) {
                    geoStatus.textContent = 'Fetching your location...';
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            latInput.value = position.coords.latitude;
                            lngInput.value = position.coords.longitude;
                            accInput.value = position.coords.accuracy;
                            geoStatus.innerHTML = '<i class="bi bi-geo-alt-fill text-success"></i> Location captured successfully';
                        },
                        function(error) {
                            geoCheck.checked = false;
                            switch(error.code) {
                                case error.PERMISSION_DENIED:
                                    geoStatus.textContent = 'Location permission denied.';
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    geoStatus.textContent = 'Location information unavailable.';
                                    break;
                                case error.TIMEOUT:
                                    geoStatus.textContent = 'Location request timed out.';
                                    break;
                                default:
                                    geoStatus.textContent = 'Unknown error occurred.';
                                    break;
                            }
                        }
                    );
                } else {
                    geoStatus.textContent = 'Geolocation is not supported by your browser.';
                    geoCheck.checked = false;
                }
            } else {
                latInput.value = '';
                lngInput.value = '';
                accInput.value = '';
                geoStatus.textContent = '';
            }
        });
    </script>
</body>
</html>
