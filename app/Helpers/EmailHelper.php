<?php
// /home/ubuntu/siwes_system/app/Helpers/EmailHelper.php

require_once CONFIG_PATH . "/email_config.php";

class EmailHelper {
    // Send email notification using PHPMailer
    public static function sendEmail($to, $templateName, $replacements = []) {
        // Check if PHPMailer is available
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            // Try to include PHPMailer via Composer autoload
            $autoloadPath = BASE_PATH . '/vendor/autoload.php';
            if (file_exists($autoloadPath)) {
                require_once $autoloadPath;
            } else {
                // Log error
                error_log("EmailHelper::sendEmail Error: PHPMailer not available");
                return false;
            }
        }
        
        // Get email template
        $templates = EMAIL_TEMPLATES;
        if (!isset($templates[$templateName])) {
            error_log("EmailHelper::sendEmail Error: Template '$templateName' not found");
            return false;
        }
        
        $template = $templates[$templateName];
        $subject = $template['subject'];
        $body = $template['body'];
        
        // Replace placeholders in subject and body
        foreach ($replacements as $key => $value) {
            $subject = str_replace('{{' . $key . '}}', $value, $subject);
            $body = str_replace('{{' . $key . '}}', $value, $body);
        }
        
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            
            // Server settings
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = SMTP_PORT;
            
            // Recipients
            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress($to);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            
            return $mail->send();
        } catch (Exception $e) {
            // Log error
            error_log("EmailHelper::sendEmail Error: " . $e->getMessage());
            return false;
        }
    }
    
    // Process email template with replacements
    public static function processTemplate($templateName, $replacements = []) {
        // Get email template
        $templates = EMAIL_TEMPLATES;
        if (!isset($templates[$templateName])) {
            return false;
        }
        
        $template = $templates[$templateName];
        $subject = $template['subject'];
        $body = $template['body'];
        
        // Replace placeholders in subject and body
        foreach ($replacements as $key => $value) {
            $subject = str_replace('{{' . $key . '}}', $value, $subject);
            $body = str_replace('{{' . $key . '}}', $value, $body);
        }
        
        return [
            'subject' => $subject,
            'body' => $body
        ];
    }
}
?>
