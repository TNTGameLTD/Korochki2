<?php
require_once __DIR__ . '/../config/db.php';

class User {
    private $conn;
    private $table = 'users';

    public $id;
    public $login;
    public $password;
    public $full_name;
    public $phone;
    public $email;
    public $role;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Регистрация
    public function register() {
        $query = "INSERT INTO {$this->table} (login, password, full_name, phone, email) 
                  VALUES (:login, :password, :full_name, :phone, :email)";
        $stmt = $this->conn->prepare($query);
        
        $this->login = htmlspecialchars(strip_tags($this->login));
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->email = htmlspecialchars(strip_tags($this->email));

        $stmt->bindParam(':login', $this->login);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':email', $this->email);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // Авторизация
    public function login() {
        $query = "SELECT * FROM {$this->table} WHERE login = :login LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':login', $this->login);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            if (password_verify($this->password, $row['password'])) {
                $this->id = $row['id'];
                $this->full_name = $row['full_name'];
                $this->role = $row['role'];
                return true;
            }
        }
        return false;
    }

    // Проверка уникальности логина
    public function isLoginUnique($login) {
        $query = "SELECT id FROM {$this->table} WHERE login = :login";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        return $stmt->rowCount() === 0;
    }
}
?>