# SIWES Reporting System Project Plan

## Phase 1: Setup & Core Infrastructure (Estimated 2 days)
- [X] **Environment Setup:**
    - [X] Set up local development environment (Apache, MySQL 8.0 installed and running).
    - [X] Install PHP 8.1 and required extensions (Resolved `apt_pkg` error).
    - [X] Initialize Git repository. (`/home/ubuntu/siwes_system`)
    - [X] Create initial MVC directory structure (`public/`, `app/Controllers/`, `app/Models/`, `app/Views/`, `config/`, `uploads/log_media/`, `sql/`).
- [X] **Database Setup:**
    - [X] Design and create SQL Schema (`/home/ubuntu/siwes_system/sql/schema.sql`).
    - [X] Execute SQL Schema to create `siwes_db` and tables (Completed).
    - [X] Insert initial Roles data (Included and verified in schema.sql execution).
    - [X] Implement `Database.php` class using PHP PDO for secure connections and queries.
- [X] **Basic Routing & Authentication:**
    - [X] Implement simple routing mechanism (`index.php` with MVC routing).
    - [X] Develop `Users` and `Roles` models (`UserModel.php`, `RoleModel.php`).
    - [X] Implement secure user registration and login system (`AuthController.php`) with password hashing and session management.
    - [X] Implement basic role-based access control (RBAC) with role-based redirects.

## Phase 2: User Modules - Student & Industry Supervisor (Estimated 5 days)

- [X] **Student Dashboard (FR2):**
    - [X] Implement `StudentController` and `StudentModel`.
    - [X] Design and implement student dashboard view (`student/dashboard.php`).
    - [X] Display dynamic data (SIWES progress, log counts, supervisors).
- [X] **Student Daily Log Entry (FR1):**
    - [X] Develop `LogEntryModel` and integrate into `StudentController`.
    - [X] Implement log entry form (`student/log_entry_form.php`) with text areas, date picker.
    - [X] Implement Media Upload (FR1, NFR2) with validation and secure storage.
    - [X] Implement Digital Signature Capture (FR1) using Signature Pad JS.
    - [X] Implement Geolocation capture (optional, FR1).
    - [X] Implement submission logic.
- [X] **Industry Supervisor Module (FR3):**
    - [X] Implement `IndustrySupervisorModel` and integrate into `SupervisorController`.
    - [X] Develop industry supervisor dashboard view (`supervisor/industry/dashboard.php`).
    - [X] Implement log entry review page (`supervisor/industry/review_log.php`) with approval/rejection, comments, signature.
    - [X] Create pending logs view and detailed log view for supervisors.
- [X] **Notifications (FR8):**
    - [X] Implement `NotificationModel` for in-app notifications.
    - [X] Create `EmailHelper` and email templates.
    - [X] Implement `NotificationIntegration` for log submission, approval, and rejection notifications.
    - [X] Create notification views and UI components.

## Phase 3: Supervisor Assignment & School/ITF Supervisor Modules (Estimated 5 days)

- [ ] **Least-Loaded Assignment Algorithm (FR7):**
    - [ ] Implement `getLeastLoadedSchoolSupervisor()` and `assignStudentToSupervisor()` in `SupervisorModel`.
    - [ ] Integrate into student registration/assignment.
- [X] **School Supervisor Module (FR4):**
    - [X] Implement `SchoolSupervisorModel` and integrate into `SupervisorController`.
    - [X] Develop school supervisor dashboard view (`supervisor/school/dashboard.php`).
    - [X] Implement log entry review page (`supervisor/school/review_log.php`) with approval/rejection, comments, signature.
    - [X] Create pending logs view and detailed log view for school supervisors.
    - [ ] Implement optional messaging system.
- [X] **ITF Supervisor Module (FR5):**
    - [X] Implement `ITFSupervisorModel` and `ITFSupervisorController`.
    - [X] Develop read-only dashboard (`supervisor/itf/dashboard.php`) with aggregate stats.
    - [X] Implement students view and student logs view.
    - [X] Create detailed log view for ITF supervisors.
    - [X] Implement reports generation and export functionality.
    - [X] Integrate with routing system in `index.php`.

## Phase 4: Admin & Reporting (Estimated 4 days)

- [ ] **Administrator Module (FR6):**
    - [ ] Implement `AdminController` and `AdminModel`.
    - [ ] Develop admin dashboard (`admin/dashboard.php`).
    - [ ] Implement user management, institution/department management, supervisor assignment, SIWES duration configuration.
- [ ] **Report Generation (FR9):**
    - [ ] Integrate DOMPDF library.
    - [ ] Implement PDF generation for student logbooks, supervisor performance, aggregate ITF reports.
- [ ] **Refinement & Non-Functional Requirements (NFRs):**
    - [ ] Security (NFR2): Input validation, output sanitization, PDO prepared statements, secure sessions, secure file uploads.
    - [ ] Usability (NFR3): Review UI/UX, ensure consistency (Bootstrap 5.3), clear messages.
    - [ ] Performance (NFR1): Optimize queries, caching, image handling.
    - [ ] Implement error handling and logging.

## Phase 5: Testing, Documentation & Deployment Prep (Estimated 4 days)

- [ ] **Unit Testing:** Write PHPUnit tests for critical backend logic.
- [ ] **Integration Testing:** Perform end-to-end testing of core workflows and RBAC.
- [ ] **User Acceptance Testing (UAT):** Simulate scenarios (Appendix C - **MISSING**), gather feedback.
- [ ] **Code Review & Refactoring:** Review codebase, refactor if needed.
- [ ] **Documentation:** Add inline comments, update README.md, document API/DB schema.
- [ ] **Deployment Preparation:** Externalize configurations, prepare for deployment.

---
**Notes:**
*   Appendix B (SQL Schema) was not provided in the original `SWes.txt`. A custom schema was created based on the requirements.
*   Appendix C (UAT Scenarios) was not provided. User input may be needed for UAT.
*   Environment setup complete with PHP 8.1, Apache, and MySQL 8.0.
