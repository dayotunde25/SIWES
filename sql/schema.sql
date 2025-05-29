-- /home/ubuntu/siwes_system/sql/schema.sql
-- Designed based on requirements in SWes.txt as Appendix B was missing.

CREATE DATABASE IF NOT EXISTS siwes_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE siwes_db;

-- Roles Table: Defines user types
CREATE TABLE Roles (
    role_id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE COMMENT 'E.g., Student, Industry Supervisor, School Supervisor, ITF Supervisor, Admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Initial Roles Data (as mentioned in SWes.txt)
INSERT INTO Roles (role_name) VALUES ('Student'), ('Industry Supervisor'), ('School Supervisor'), ('ITF Supervisor'), ('Admin');

-- Users Table: Stores login and basic info for all user types
CREATE TABLE Users (
    user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL COMMENT 'Store hashed passwords only',
    full_name VARCHAR(150) NOT NULL,
    phone_number VARCHAR(20),
    role_id TINYINT UNSIGNED NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES Roles(role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Institutions Table: Schools/Universities
CREATE TABLE Institutions (
    institution_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Departments Table: Departments within institutions
CREATE TABLE Departments (
    department_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    institution_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (institution_id) REFERENCES Institutions(institution_id) ON DELETE CASCADE,
    UNIQUE KEY inst_dept_unique (institution_id, name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- School Supervisors Table: Links User to a department and tracks student load
CREATE TABLE School_Supervisors (
    supervisor_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL UNIQUE COMMENT 'Links to the Users table',
    department_id INT UNSIGNED NOT NULL,
    staff_id VARCHAR(50) UNIQUE,
    current_student_count INT UNSIGNED DEFAULT 0 COMMENT 'Used for least-loaded algorithm',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES Departments(department_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Students Table: Specific details for students
CREATE TABLE Students (
    student_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL UNIQUE COMMENT 'Links to the Users table',
    matric_number VARCHAR(50) NOT NULL UNIQUE,
    department_id INT UNSIGNED NOT NULL,
    school_supervisor_id INT UNSIGNED NULL COMMENT 'Assigned school supervisor (references School_Supervisors.supervisor_id)',
    industry_supervisor_id INT UNSIGNED NULL COMMENT 'Assigned industry supervisor (references Users.user_id)',
    siwes_organization_name VARCHAR(255),
    siwes_organization_address TEXT,
    siwes_start_date DATE,
    siwes_end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES Departments(department_id),
    FOREIGN KEY (school_supervisor_id) REFERENCES School_Supervisors(supervisor_id) ON DELETE SET NULL,
    FOREIGN KEY (industry_supervisor_id) REFERENCES Users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Log Entries Table: Stores daily SIWES logs
CREATE TABLE Log_Entries (
    log_entry_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    log_date DATE NOT NULL,
    activities_performed TEXT NOT NULL,
    key_learnings TEXT,
    student_digital_signature_path VARCHAR(255) COMMENT 'Path to student signature image file',
    geolocation_data JSON COMMENT 'Optional GeoJSON data',
    status ENUM('Pending', 'Approved by Industry', 'Approved by School', 'Rejected by Industry', 'Rejected by School') DEFAULT 'Pending',
    industry_supervisor_id INT UNSIGNED NULL COMMENT 'User ID of industry supervisor who reviewed',
    industry_review_comments TEXT,
    industry_signature_path VARCHAR(255) COMMENT 'Path to industry supervisor signature image file',
    industry_review_date TIMESTAMP NULL,
    school_supervisor_id INT UNSIGNED NULL COMMENT 'User ID of school supervisor who reviewed',
    school_review_comments TEXT,
    school_review_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES Students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (industry_supervisor_id) REFERENCES Users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (school_supervisor_id) REFERENCES Users(user_id) ON DELETE SET NULL,
    UNIQUE KEY student_log_date_unique (student_id, log_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Log Entry Media Table: Stores paths to media uploaded with log entries
CREATE TABLE Log_Entry_Media (
    media_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    log_entry_id BIGINT UNSIGNED NOT NULL,
    file_path VARCHAR(255) NOT NULL COMMENT 'Path to uploaded media file (image)',
    file_type VARCHAR(50) COMMENT 'e.g., image/png, image/jpeg',
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (log_entry_id) REFERENCES Log_Entries(log_entry_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notifications Table: Records system notifications (e.g., email alerts)
CREATE TABLE Notifications (
    notification_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    recipient_user_id INT UNSIGNED NOT NULL,
    sender_user_id INT UNSIGNED NULL COMMENT 'User who triggered the notification, if applicable',
    log_entry_id BIGINT UNSIGNED NULL COMMENT 'Related log entry, if applicable',
    notification_type VARCHAR(100) NOT NULL COMMENT 'e.g., NewLogEntry, LogApproved, LogRejected',
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (recipient_user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (sender_user_id) REFERENCES Users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (log_entry_id) REFERENCES Log_Entries(log_entry_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin Configuration Table (Optional, for system-wide settings like SIWES duration)
CREATE TABLE Admin_Config (
    config_key VARCHAR(100) PRIMARY KEY,
    config_value TEXT,
    description VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Example Config Data (Uncomment to use)
-- INSERT INTO Admin_Config (config_key, config_value, description) VALUES
-- ('global_siwes_start_date', '2024-06-01', 'Default SIWES start date for the current cycle'),
-- ('global_siwes_end_date', '2024-11-30', 'Default SIWES end date for the current cycle');


