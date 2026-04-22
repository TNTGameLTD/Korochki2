<?php
require_once __DIR__ . '/../config/db.php';

class Course {  // ← ДОЛЖНО БЫТЬ Course, а НЕ Application!
    private $conn;
    private $table = 'courses';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getAll() {
        $query = "SELECT * FROM {$this->table} ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }
}
?>