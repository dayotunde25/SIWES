# Deployment Guide for SIWES Reporting System

This document provides instructions for deploying the SIWES Reporting System to a production environment.

## System Requirements

- PHP 8.1 or higher
- MySQL 8.0 or higher
- Apache/Nginx web server
- Composer (for dependency management)
- SSL certificate (recommended for production)

## Pre-Deployment Checklist

1. Ensure all code is committed to version control
2. Run all tests and fix any issues
3. Update configuration files for production
4. Optimize database queries and indexes
5. Set up email configuration for notifications

## Deployment Steps

### 1. Server Setup

1. Set up a server with the required specifications
2. Install and configure Apache/Nginx, PHP 8.1, and MySQL 8.0
3. Install required PHP extensions:
   - pdo_mysql
   - mbstring
   - xml
   - gd
   - curl

### 2. Database Setup

1. Create a production database:
   ```sql
   CREATE DATABASE siwes_db;
   ```

2. Create a dedicated database user with limited privileges:
   ```sql
   CREATE USER 'siwes_user'@'localhost' IDENTIFIED BY 'strong_password_here';
   GRANT SELECT, INSERT, UPDATE, DELETE ON siwes_db.* TO 'siwes_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

3. Import the database schema:
   ```bash
   mysql -u siwes_user -p siwes_db < /path/to/schema.sql
   ```

### 3. Application Deployment

1. Clone or upload the application code to the server:
   ```bash
   git clone https://github.com/your-repo/siwes-system.git /var/www/siwes
   ```

2. Set proper permissions:
   ```bash
   chown -R www-data:www-data /var/www/siwes
   chmod -R 755 /var/www/siwes
   chmod -R 777 /var/www/siwes/uploads
   ```

3. Update configuration files:
   - Copy `/config/Database.php.example` to `/config/Database.php`
   - Update database credentials
   - Copy `/config/email_config.php.example` to `/config/email_config.php`
   - Update email settings

4. Set up web server configuration:
   
   **Apache (example virtual host):**
   ```apache
   <VirtualHost *:80>
       ServerName siwes.example.com
       DocumentRoot /var/www/siwes/public
       
       <Directory /var/www/siwes/public>
           Options -Indexes +FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
       
       ErrorLog ${APACHE_LOG_DIR}/siwes_error.log
       CustomLog ${APACHE_LOG_DIR}/siwes_access.log combined
   </VirtualHost>
   ```

   **Nginx (example configuration):**
   ```nginx
   server {
       listen 80;
       server_name siwes.example.com;
       root /var/www/siwes/public;
       
       index index.php;
       
       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }
       
       location ~ \.php$ {
           include snippets/fastcgi-php.conf;
           fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
       }
       
       location ~ /\.ht {
           deny all;
       }
   }
   ```

5. Enable the site and restart the web server:
   ```bash
   # Apache
   a2ensite siwes.conf
   systemctl restart apache2
   
   # Nginx
   ln -s /etc/nginx/sites-available/siwes.conf /etc/nginx/sites-enabled/
   systemctl restart nginx
   ```

### 4. SSL Configuration (Recommended)

1. Obtain an SSL certificate (Let's Encrypt or commercial)
2. Configure SSL in your web server
3. Set up redirects from HTTP to HTTPS

### 5. Post-Deployment Tasks

1. Create initial admin user:
   ```sql
   INSERT INTO Users (email, password, full_name, role_id) 
   VALUES ('admin@example.com', '$2y$10$hashed_password', 'System Administrator', 4);
   ```

2. Test the application thoroughly:
   - Test login and registration
   - Test student log submission
   - Test supervisor review workflows
   - Test notifications

3. Set up regular backups:
   ```bash
   # Example backup script
   mysqldump -u backup_user -p --databases siwes_db > /backup/siwes_db_$(date +%Y%m%d).sql
   ```

## Security Considerations

1. **File Permissions**: Ensure proper file permissions to prevent unauthorized access
2. **Database Security**: Use a dedicated database user with limited privileges
3. **Input Validation**: All user inputs are validated and sanitized
4. **Password Security**: Passwords are hashed using PHP's password_hash() function
5. **Session Security**: Sessions are managed securely
6. **HTTPS**: Use SSL/TLS for all communications
7. **Error Handling**: Production error messages do not reveal sensitive information

## Monitoring and Maintenance

1. Set up log rotation for application logs
2. Configure server monitoring (CPU, memory, disk usage)
3. Implement regular security updates
4. Schedule regular database backups
5. Plan for periodic code updates and maintenance

## Troubleshooting

### Common Issues

1. **Database Connection Errors**:
   - Check database credentials in configuration
   - Verify MySQL service is running
   - Check network connectivity and firewall settings

2. **Permission Issues**:
   - Ensure web server has appropriate permissions to read/write files
   - Check upload directory permissions

3. **Email Notification Issues**:
   - Verify SMTP settings in email_config.php
   - Check server's ability to send outbound emails
   - Test email functionality with a test script

## Contact and Support

For technical support, please contact:
- Email: support@example.com
- Phone: +123-456-7890
