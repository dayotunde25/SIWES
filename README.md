# SIWES Reporting System

A web-based platform for managing and tracking SIWES (Student Industrial Work Experience Scheme) activities, log entries, and supervision.

---

## Table of Contents

- [Features](#features)
- [System Requirements](#system-requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Troubleshooting](#troubleshooting)
- [Support](#support)

---

## Features

- Student online daily logbook submission
- Supervisor feedback and approval workflow
- Multi-role system: Administrator, Student, Industry Supervisor, School Supervisor, ITF Supervisor
- Secure login and user management
- Aggregate statistics and reporting

---

## System Requirements

- PHP 8.1 or higher
- MySQL 8.0 or higher
- Apache or Nginx web server
- Composer (optional, for dependency management)

---

## Installation

### 1. Set Up Web Server Environment

**Option A: Using XAMPP (Recommended for beginners)**

1. Download and install [XAMPP](https://www.apachefriends.org/)
2. Start Apache and MySQL from the XAMPP Control Panel

**Option B: Manual Installation**

1. Install Apache or Nginx
2. Install PHP 8.1 or higher
3. Install MySQL 8.0 or higher

### 2. Database Setup

1. **Create the database:**
   - Open phpMyAdmin (usually at `http://localhost/phpmyadmin`)
   - Create a new database named `siwes_db`

2. **Import the schema:**
   - In phpMyAdmin, select `siwes_db`
   - Go to the "Import" tab and select `sql/schema.sql` from the project files

3. **(Optional) Create a database user:**
   - Go to "User accounts" > "Add user account"
   - Username: `siwes_user`
   - Host: `Local`
   - Assign a password
   - Grant all privileges on `siwes_db`

### 3. Application Setup

1. **Extract the project files:**
   - For XAMPP: `C:\xampp\htdocs\siwes`
   - For Apache/Nginx: Place in your web server's document root

2. **Configure database connection:**
   - Edit `config/Database.php`:
     ```php
     private $host = 'localhost';
     private $db_name = 'siwes_db';
     private $username = 'siwes_user'; // or 'root'
     private $password = 'your_password_here';
     ```

3. **Set permissions (Linux/Mac):**
   ```bash
   chmod -R 755 /path/to/siwes
   chmod -R 777 /path/to/siwes/uploads
   ```

4. **(Optional) Configure email:**
   - Edit `config/email_config.php` with your SMTP details

### 4. Web Server Configuration

**Apache (XAMPP):**
- Access the app at `http://localhost/siwes/public`
- (Optional) Set up a custom virtual host to point to the `public` directory

**Nginx:**
- Create a server block like:
   ```nginx
   server {
       listen 80;
       server_name siwes.local;
       root /path/to/siwes/public;
       index index.php;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location ~ \.php$ {
           include snippets/fastcgi-php.conf;
           fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
       }
   }
   ```
- Add `127.0.0.1 siwes.local` to your `/etc/hosts` (or Windows hosts file)
- Restart Nginx

### 5. First Run & Testing

1. Open your browser to:
   - `http://localhost/siwes/public` (XAMPP)
   - `http://siwes.local` (custom host)

2. **Create an initial admin user:**
   - In phpMyAdmin, select `siwes_db`
   - Go to "SQL" and run:
     ```sql
     INSERT INTO Users (email, password, full_name, role_id) 
     VALUES ('admin@example.com', '$2y$10$hashed_password', 'System Administrator', 4);
     ```
   - Generate a hashed password in PHP:
     ```php
     <?php
     echo password_hash('your_password_here', PASSWORD_DEFAULT);
     ?>
     ```

3. Log in with the admin credentials

---

## Usage

**Roles:**

- **Administrator** (role_id = 4): Manage users and system
- **Student** (role_id = 1): Submit daily logs, view feedback
- **Industry Supervisor** (role_id = 2): Review, approve/reject student logs, give feedback
- **School Supervisor** (role_id = 3): Review logs approved by industry, final approval/feedback
- **ITF Supervisor** (role_id = 5): View aggregate stats, read-only reports

---

## Troubleshooting

- **Database Connection Error:**  
  Check credentials in `config/Database.php`, ensure MySQL is running, check user permissions.

- **404 Not Found:**  
  Check file locations and web server configuration, ensure mod_rewrite (Apache) is enabled.

- **Permission Issues:**  
  Ensure correct file and directory permissions, especially for `uploads`.

- **Blank Page / PHP Errors:**  
  Check PHP error log, verify required extensions and PHP version.

---

## Support

For technical support or questions, consult the project documentation or contact the system administrator.

---
