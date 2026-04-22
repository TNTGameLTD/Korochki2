<?php
require_once __DIR__ . '/../config/db.php';

class Application {
    private $conn;
    private $table = 'applications';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Создание заявки
    public function create($user_id, $course_id, $start_date, $payment_method) {
        $query = "INSERT INTO {$this->table} (user_id, course_id, start_date, payment_method) 
                  VALUES (:user_id, :course_id, :start_date, :payment_method)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':payment_method', $payment_method);
        return $stmt->execute();
    }

    // Получить заявки пользователя
    public function getUserApplications($user_id) {
        $query = "SELECT a.*, c.name as course_name 
                  FROM {$this->table} a 
                  JOIN courses c ON a.course_id = c.id 
                  WHERE a.user_id = :user_id 
                  ORDER BY a.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Получить все заявки (для админа)
    public function getAllApplications($status = null) {
        $query = "SELECT a.*, u.full_name, u.email, u.phone, c.name as course_name 
                  FROM {$this->table} a 
                  JOIN users u ON a.user_id = u.id 
                  JOIN courses c ON a.course_id = c.id";
        
        if ($status) {
            $query .= " WHERE a.status = :status";
        }
        $query .= " ORDER BY a.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Обновить статус заявки
    public function updateStatus($id, $status) {
        $query = "UPDATE {$this->table} SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':status', $status);
        return $stmt->execute();
    }

    // Проверить, завершено ли обучение
    public function isCourseCompleted($user_id, $course_id) {
        $query = "SELECT id FROM {$this->table} 
                  WHERE user_id = :user_id AND course_id = :course_id AND status = 'Обучение завершено'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>