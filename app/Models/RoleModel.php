<?php
// /home/ubuntu/siwes_system/app/Models/RoleModel.php

require_once CONFIG_PATH . "/Database.php";

class RoleModel {
    private $db;
    private $table = 'Roles';

    public function __construct() {
        $this->db = new Database();
    }

    // Get all roles
    public function getAll() {
        $sql = "SELECT * FROM {$this->table} ORDER BY role_id";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log error
            error_log("RoleModel::getAll Error: " . $e->getMessage());
            return false;
        }
    }

    // Get role by ID
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE role_id = :id LIMIT 1";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log error
            error_log("RoleModel::getById Error: " . $e->getMessage());
            return false;
        }
    }

    // Get role by name
    public function getByName($name) {
        $sql = "SELECT * FROM {$this->table} WHERE role_name = :name LIMIT 1";
        
        try {
            $conn = $this->db->connect();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':name', $name);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log error
            error_log("RoleModel::getByName Error: " . $e->getMessage());
            return false;
        }
    }
}
?>
