# SIWES Reporting System - Local Installation Guide

This guide provides step-by-step instructions for setting up the SIWES Reporting System on your local machine.

## System Requirements

- PHP 8.1 or higher
- MySQL 8.0 or higher
- Apache/Nginx web server
- Composer (optional, for dependency management)

## Installation Steps

### 1. Set Up Web Server Environment

#### Option A: Using XAMPP (Recommended for beginners)

1. Download and install XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Start the Apache and MySQL services from the XAMPP Control Panel

#### Option B: Using separate components

1. Install Apache or Nginx web server
2. Install PHP 8.1 or higher
3. Install MySQL 8.0 or higher

### 2. Database Setup

1. Create a new database for the SIWES system:
   - Open phpMyAdmin (http://localhost/phpmyadmin if using XAMPP)
   - Click on "New" in the left sidebar
   - Enter "siwes_db" as the database name
   - Click "Create"

2. Import the database schema:
   - In phpMyAdmin, select the "siwes_db" database
   - Click on the "Import" tab
   - Click "Choose File" and select the `sql/schema.sql` file from the SIWES system files
   - Click "Go" to import the schema

3. Create a database user (optional but recommended):
   - Click on "User accounts" tab
   - Click "Add user account"
   - Enter username: "siwes_user"
   - Select "Local" for host
   - Enter a strong password
   - Under "Database for user account", select "Grant all privileges on database siwes_db"
   - Click "Go" to create the user

### 3. Application Setup

1. Extract the SIWES system zip file to your web server's document root:
   - For XAMPP: Extract to `C:\xampp\htdocs\siwes` (Windows) or `/Applications/XAMPP/htdocs/siwes` (Mac)
   - For Apache: Extract to your configured DocumentRoot
   - For Nginx: Extract to your configured root directory

2. Configure the database connection:
   - Open the file `config/Database.php`
   - Update the database credentials:
     ```php
     private $host = 'localhost';
     private $db_name = 'siwes_db';
     private $username = 'siwes_user'; // or 'root' if you didn't create a new user
     private $password = 'your_password_here';
     ```

3. Set proper permissions:
   - Ensure the web server has read access to all files
   - Ensure the web server has write access to the `uploads` directory
   - On Linux/Mac:
     ```bash
     chmod -R 755 /path/to/siwes
     chmod -R 777 /path/to/siwes/uploads
     ```

4. Configure email settings (optional):
   - Open the file `config/email_config.php`
   - Update the SMTP settings with your email provider's details

### 4. Web Server Configuration

#### For Apache (with XAMPP)

1. If using XAMPP with default settings, the application should be accessible at:
   - http://localhost/siwes/public

2. For a custom virtual host (optional):
   - Edit your Apache configuration or create a new virtual host
   - Point the DocumentRoot to the `public` directory of the SIWES system
   - Restart Apache

#### For Nginx

1. Create a new server block in your Nginx configuration:
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

2. Add `siwes.local` to your hosts file:
   - Windows: `C:\Windows\System32\drivers\etc\hosts`
   - Mac/Linux: `/etc/hosts`
   - Add this line: `127.0.0.1 siwes.local`

3. Restart Nginx

### 5. First Run and Testing

1. Open your web browser and navigate to:
   - http://localhost/siwes/public (for XAMPP default setup)
   - http://siwes.local (if you configured a virtual host)

2. You should see the SIWES Reporting System login page

3. Create an initial admin user:
   - In phpMyAdmin, select the "siwes_db" database
   - Click on the "SQL" tab
   - Execute the following SQL query (replace with your desired admin details):
     ```sql
     INSERT INTO Users (email, password, full_name, role_id) 
     VALUES ('admin@example.com', '$2y$10$hashed_password', 'System Administrator', 4);
     ```
   - For security, use a properly hashed password. You can generate one using the following PHP code:
     ```php
     <?php
     echo password_hash('your_password_here', PASSWORD_DEFAULT);
     ?>
     ```

4. Log in with the admin credentials you just created

### 6. Troubleshooting

#### Common Issues

1. **Database Connection Error**
   - Verify your database credentials in `config/Database.php`
   - Ensure MySQL service is running
   - Check that the database and user exist with proper permissions

2. **404 Not Found Error**
   - Ensure the application files are in the correct location
   - Check your web server configuration
   - Verify that mod_rewrite is enabled (for Apache)

3. **Permission Issues**
   - Check that the web server has appropriate permissions to read files
   - Ensure the `uploads` directory is writable

4. **Blank Page or PHP Errors**
   - Check your PHP error log
   - Ensure all required PHP extensions are installed
   - Verify PHP version is 8.1 or higher

## System Usage

After installation, you can use the system with the following roles:

1. **Administrator** (role_id = 4)
   - Manage users and supervisors
   - Access all system features

2. **Student** (role_id = 1)
   - Submit daily log entries
   - View feedback from supervisors

3. **Industry Supervisor** (role_id = 2)
   - Review and approve/reject student logs
   - Provide feedback to students

4. **School Supervisor** (role_id = 3)
   - Review logs approved by industry supervisors
   - Provide final approval and feedback

5. **ITF Supervisor** (role_id = 5)
   - View aggregate statistics
   - Access read-only reports

## Support

For technical support or questions, please refer to the project documentation or contact the system administrator.
