<?php
// /home/ubuntu/siwes_system/app/Models/UserModel.php

require_once CONFIG_PATH . "/Database.php";

class UserModel {
    private $db;
    private $table = 'Users';

    public function __construct() {
        $this->db = new Database();
    }

    // Create a new user
    public function create($data) {
        // Hash the password
        $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        unset($data['password']); // Remove plain text password
        
        // Prepare SQL statement
        $fields = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ({$fields}) VALUES ({$placeholders})";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            
            // Bind values
            foreach ($data as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            
            if ($stmt->execute()) {
                return $conn->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            // Log error
            error_log("UserModel::create Error: " . $e->getMessage());
            return false;
        }
    }

    // Find user by email
    public function findByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':email', $email);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log error
            error_log("UserModel::findByEmail Error: " . $e->getMessage());
            return false;
        }
    }

    // Find user by ID
    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :id LIMIT 1";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log error
            error_log("UserModel::findById Error: " . $e->getMessage());
            return false;
        }
    }

    // Update user
    public function update($id, $data) {
        // If password is being updated, hash it
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']); // Remove plain text password
        }
        
        // Prepare update statement
        $setClause = '';
        foreach ($data as $key => $value) {
            $setClause .= "{$key} = :{$key}, ";
        }
        $setClause = rtrim($setClause, ', ');
        
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE user_id = :id";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            
            // Bind values
            foreach ($data as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->bindValue(':id', $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            // Log error
            error_log("UserModel::update Error: " . $e->getMessage());
            return false;
        }
    }

    // Verify password
    public function verifyPassword($email, $password) {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        
        return false;
    }

    // Get all users (with optional role filter)
    public function getAll($role_id = null) {
        $sql = "SELECT u.*, r.role_name FROM {$this->table} u 
                JOIN Roles r ON u.role_id = r.role_id";
        
        if ($role_id !== null) {
            $sql .= " WHERE u.role_id = :role_id";
        }
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            
            if ($role_id !== null) {
                $stmt->bindValue(':role_id', $role_id);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log error
            error_log("UserModel::getAll Error: " . $e->getMessage());
            return false;
        }
    }

    // Deactivate user
    public function deactivate($id) {
        $sql = "UPDATE {$this->table} SET is_active = 0 WHERE user_id = :id";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            // Log error
            error_log("UserModel::deactivate Error: " . $e->getMessage());
            return false;
        }
    }

    // Activate user
    public function activate($id) {
        $sql = "UPDATE {$this->table} SET is_active = 1 WHERE user_id = :id";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            // Log error
            error_log("UserModel::activate Error: " . $e->getMessage());
            return false;
        }
    }
}
?>
