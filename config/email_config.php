<?php
// /home/ubuntu/siwes_system/config/email_config.php

// Email configuration
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'siwes@example.com');
define('SMTP_PASSWORD', 'your_password_here');
define('SMTP_FROM_EMAIL', 'siwes@example.com');
define('SMTP_FROM_NAME', 'SIWES Reporting System');

// Email templates
define('EMAIL_TEMPLATES', [
    'log_submission' => [
        'subject' => 'New Log Entry Submitted',
        'body' => '
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #007bff; color: white; padding: 10px; text-align: center; }
                    .content { padding: 20px; border: 1px solid #ddd; }
                    .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h2>SIWES Reporting System</h2>
                    </div>
                    <div class="content">
                        <p>Dear {{supervisor_name}},</p>
                        <p>A new log entry has been submitted by <strong>{{student_name}}</strong> and requires your review.</p>
                        <p>Log Date: <strong>{{log_date}}</strong></p>
                        <p>Please log in to the SIWES Reporting System to review this entry.</p>
                        <p><a href="{{login_url}}">Click here to login</a></p>
                    </div>
                    <div class="footer">
                        <p>This is an automated message from the SIWES Reporting System. Please do not reply to this email.</p>
                    </div>
                </div>
            </body>
            </html>
        '
    ],
    'log_approved_industry' => [
        'subject' => 'Log Entry Approved by Industry Supervisor',
        'body' => '
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #28a745; color: white; padding: 10px; text-align: center; }
                    .content { padding: 20px; border: 1px solid #ddd; }
                    .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h2>SIWES Reporting System</h2>
                    </div>
                    <div class="content">
                        <p>Dear {{student_name}},</p>
                        <p>Your log entry dated <strong>{{log_date}}</strong> has been <strong>approved</strong> by your Industry Supervisor, <strong>{{supervisor_name}}</strong>.</p>
                        <p>Feedback: {{feedback}}</p>
                        <p>Your log entry will now be reviewed by your School Supervisor.</p>
                        <p><a href="{{login_url}}">Click here to login</a> to view the details.</p>
                    </div>
                    <div class="footer">
                        <p>This is an automated message from the SIWES Reporting System. Please do not reply to this email.</p>
                    </div>
                </div>
            </body>
            </html>
        '
    ],
    'log_rejected_industry' => [
        'subject' => 'Log Entry Rejected by Industry Supervisor',
        'body' => '
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #dc3545; color: white; padding: 10px; text-align: center; }
                    .content { padding: 20px; border: 1px solid #ddd; }
                    .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h2>SIWES Reporting System</h2>
                    </div>
                    <div class="content">
                        <p>Dear {{student_name}},</p>
                        <p>Your log entry dated <strong>{{log_date}}</strong> has been <strong>rejected</strong> by your Industry Supervisor, <strong>{{supervisor_name}}</strong>.</p>
                        <p>Feedback: {{feedback}}</p>
                        <p>Please review the feedback and submit a revised log entry.</p>
                        <p><a href="{{login_url}}">Click here to login</a> to view the details and make corrections.</p>
                    </div>
                    <div class="footer">
                        <p>This is an automated message from the SIWES Reporting System. Please do not reply to this email.</p>
                    </div>
                </div>
            </body>
            </html>
        '
    ],
    'log_approved_school' => [
        'subject' => 'Log Entry Fully Approved',
        'body' => '
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #28a745; color: white; padding: 10px; text-align: center; }
                    .content { padding: 20px; border: 1px solid #ddd; }
                    .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h2>SIWES Reporting System</h2>
                    </div>
                    <div class="content">
                        <p>Dear {{student_name}},</p>
                        <p>Your log entry dated <strong>{{log_date}}</strong> has been <strong>fully approved</strong> by your School Supervisor, <strong>{{supervisor_name}}</strong>.</p>
                        <p>Feedback: {{feedback}}</p>
                        <p>Congratulations! This log entry is now complete and has been added to your SIWES record.</p>
                        <p><a href="{{login_url}}">Click here to login</a> to view the details.</p>
                    </div>
                    <div class="footer">
                        <p>This is an automated message from the SIWES Reporting System. Please do not reply to this email.</p>
                    </div>
                </div>
            </body>
            </html>
        '
    ],
    'log_rejected_school' => [
        'subject' => 'Log Entry Rejected by School Supervisor',
        'body' => '
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #dc3545; color: white; padding: 10px; text-align: center; }
                    .content { padding: 20px; border: 1px solid #ddd; }
                    .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h2>SIWES Reporting System</h2>
                    </div>
                    <div class="content">
                        <p>Dear {{student_name}},</p>
                        <p>Your log entry dated <strong>{{log_date}}</strong> has been <strong>rejected</strong> by your School Supervisor, <strong>{{supervisor_name}}</strong>.</p>
                        <p>Feedback: {{feedback}}</p>
                        <p>Please review the feedback and submit a revised log entry.</p>
                        <p><a href="{{login_url}}">Click here to login</a> to view the details and make corrections.</p>
                    </div>
                    <div class="footer">
                        <p>This is an automated message from the SIWES Reporting System. Please do not reply to this email.</p>
                    </div>
                </div>
            </body>
            </html>
        '
    ],
    'school_review_needed' => [
        'subject' => 'Log Entry Requires School Supervisor Review',
        'body' => '
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #17a2b8; color: white; padding: 10px; text-align: center; }
                    .content { padding: 20px; border: 1px solid #ddd; }
                    .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h2>SIWES Reporting System</h2>
                    </div>
                    <div class="content">
                        <p>Dear {{supervisor_name}},</p>
                        <p>A log entry by <strong>{{student_name}}</strong> dated <strong>{{log_date}}</strong> has been approved by the Industry Supervisor and now requires your review.</p>
                        <p>Please log in to the SIWES Reporting System to review this entry.</p>
                        <p><a href="{{login_url}}">Click here to login</a></p>
                    </div>
                    <div class="footer">
                        <p>This is an automated message from the SIWES Reporting System. Please do not reply to this email.</p>
                    </div>
                </div>
            </body>
            </html>
        '
    ]
]);
?>
