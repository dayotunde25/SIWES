<?php
// /home/ubuntu/siwes_system/config/Database.php

class Database {
    private $host = '127.0.0.1'; // Or your MySQL host
    private $db_name = 'siwes_db'; // Will be created once schema is provided
    private $username = 'siwes_user'; // Placeholder - Need to create this user
    private $password = 'siwes_password'; // Placeholder - Need to set a secure password
    private $conn;
    private $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    // DB Connect
    public function connect() {
        $this->conn = null;

        try {
            // DSN (Data Source Name)
            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=utf8mb4';
            $this->conn = new PDO($dsn, $this->username, $this->password, $this->options);
        } catch(PDOException $e) {
            // Log error or handle appropriately for production
            // For now, just echo the error during development
            echo 'Connection Error: ' . $e->getMessage();
            // In a real app, you might throw an exception or return false
            // exit(); // Stop script execution if DB connection fails
        }

        return $this->conn;
    }

    // Close connection (optional, PDO closes automatically)
    public function close() {
        $this->conn = null;
    }
}
?>
