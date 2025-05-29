<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile - SIWES Reporting System</title>
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
                        <a class="nav-link" href="/student/logs">View Logs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/student/profile">Profile</a>
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
                <h5 class="mb-0">Student Profile</h5>
            </div>
            <div class="card-body">
                <?php if (isset($errors['profile'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $errors['profile']; ?>
                    </div>
                <?php endif; ?>

                <form action="/student/profile" method="post">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="matric_number" class="form-label">Matriculation Number</label>
                            <input type="text" class="form-control <?php echo isset($errors['matric_number']) ? 'is-invalid' : ''; ?>" 
                                   id="matric_number" name="matric_number" 
                                   value="<?php echo isset($_POST['matric_number']) ? htmlspecialchars($_POST['matric_number']) : (isset($student['matric_number']) ? htmlspecialchars($student['matric_number']) : ''); ?>" required>
                            <?php if (isset($errors['matric_number'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $errors['matric_number']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label for="department_id" class="form-label">Department</label>
                            <select class="form-select <?php echo isset($errors['department_id']) ? 'is-invalid' : ''; ?>" 
                                    id="department_id" name="department_id" required>
                                <option value="">Select Department</option>
                                <!-- This would normally be populated from the database -->
                                <option value="1" <?php echo (isset($_POST['department_id']) && $_POST['department_id'] == 1) || (isset($student['department_id']) && $student['department_id'] == 1) ? 'selected' : ''; ?>>Computer Science</option>
                                <option value="2" <?php echo (isset($_POST['department_id']) && $_POST['department_id'] == 2) || (isset($student['department_id']) && $student['department_id'] == 2) ? 'selected' : ''; ?>>Electrical Engineering</option>
                                <option value="3" <?php echo (isset($_POST['department_id']) && $_POST['department_id'] == 3) || (isset($student['department_id']) && $student['department_id'] == 3) ? 'selected' : ''; ?>>Mechanical Engineering</option>
                                <option value="4" <?php echo (isset($_POST['department_id']) && $_POST['department_id'] == 4) || (isset($student['department_id']) && $student['department_id'] == 4) ? 'selected' : ''; ?>>Civil Engineering</option>
                            </select>
                            <?php if (isset($errors['department_id'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $errors['department_id']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="organization_name" class="form-label">Organization Name</label>
                        <input type="text" class="form-control <?php echo isset($errors['organization_name']) ? 'is-invalid' : ''; ?>" 
                               id="organization_name" name="organization_name" 
                               value="<?php echo isset($_POST['organization_name']) ? htmlspecialchars($_POST['organization_name']) : (isset($student['siwes_organization_name']) ? htmlspecialchars($student['siwes_organization_name']) : ''); ?>" required>
                        <?php if (isset($errors['organization_name'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $errors['organization_name']; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="organization_address" class="form-label">Organization Address</label>
                        <textarea class="form-control <?php echo isset($errors['organization_address']) ? 'is-invalid' : ''; ?>" 
                                  id="organization_address" name="organization_address" rows="2" required><?php echo isset($_POST['organization_address']) ? htmlspecialchars($_POST['organization_address']) : (isset($student['siwes_organization_address']) ? htmlspecialchars($student['siwes_organization_address']) : ''); ?></textarea>
                        <?php if (isset($errors['organization_address'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $errors['organization_address']; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">SIWES Start Date</label>
                            <input type="date" class="form-control <?php echo isset($errors['start_date']) ? 'is-invalid' : ''; ?>" 
                                   id="start_date" name="start_date" 
                                   value="<?php echo isset($_POST['start_date']) ? $_POST['start_date'] : (isset($student['siwes_start_date']) ? $student['siwes_start_date'] : ''); ?>" required>
                            <?php if (isset($errors['start_date'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $errors['start_date']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label">SIWES End Date</label>
                            <input type="date" class="form-control <?php echo isset($errors['end_date']) ? 'is-invalid' : ''; ?>" 
                                   id="end_date" name="end_date" 
                                   value="<?php echo isset($_POST['end_date']) ? $_POST['end_date'] : (isset($student['siwes_end_date']) ? $student['siwes_end_date'] : ''); ?>" required>
                            <?php if (isset($errors['end_date'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $errors['end_date']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Save Profile</button>
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
</body>
</html>
