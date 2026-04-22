<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'classes/User.php';
    $user = new User();
    
    // Проверка уникальности логина
    if (!$user->isLoginUnique($_POST['login'])) {
        $error = 'Логин уже занят';
    } else {
        $user->login = $_POST['login'];
        $user->password = $_POST['password'];
        $user->full_name = $_POST['full_name'];
        $user->phone = $_POST['phone'];
        $user->email = $_POST['email'];
        
        if ($user->register()) {
            $success = 'Регистрация успешна! <a href="index.php">Войти</a>';
        } else {
            $error = 'Ошибка регистрации';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Регистрация - Корочки.Есть</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Регистрация</h2>
            
            <?php if ($error): ?>
                <div class="error" style="text-align: center; margin-bottom: 15px;"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success" style="text-align: center; margin-bottom: 15px;"><?= $success ?></div>
            <?php endif; ?>
            
            <form method="POST" id="registerForm">
                <div class="form-group">
                    <label>Логин</label>
                    <input type="text" name="login" id="login" required>
                    <span class="error" id="loginError"></span>
                </div>
                
                <div class="form-group">
                    <label>Пароль</label>
                    <input type="password" name="password" id="password" required>
                    <span class="error" id="passError"></span>
                </div>
                
                <div class="form-group">
                    <label>ФИО</label>
                    <input type="text" name="full_name" id="full_name" required>
                    <span class="error" id="nameError"></span>
                </div>
                
                <div class="form-group">
                    <label>Телефон</label>
                    <input type="tel" name="phone" id="phone" placeholder="8(XXX)XXX-XX-XX" required>
                    <span class="error" id="phoneError"></span>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" id="email" required>
                    <span class="error" id="emailError"></span>
                </div>
                
                <button type="submit" class="btn">Зарегистрироваться</button>
            </form>
            
            <div class="link">
                <a href="index.php">Уже есть аккаунт? Войти</a>
            </div>
        </div>
    </div>
    
    <script src="assets/js/validation.js"></script>
    <script>
        const phoneInput = document.getElementById('phone');
        phoneInput.addEventListener('input', function() {
            Validator.formatPhone(this);
        });
        
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const login = document.getElementById('login').value;
            const password = document.getElementById('password').value;
            const fullName = document.getElementById('full_name').value;
            const phone = document.getElementById('phone').value;
            const email = document.getElementById('email').value;
            let isValid = true;
            
            const loginError = Validator.validateLogin(login);
            document.getElementById('loginError').textContent = loginError;
            if (loginError) isValid = false;
            
            const passError = Validator.validatePassword(password);
            document.getElementById('passError').textContent = passError;
            if (passError) isValid = false;
            
            const nameError = Validator.validateFullName(fullName);
            document.getElementById('nameError').textContent = nameError;
            if (nameError) isValid = false;
            
            const phoneError = Validator.validatePhone(phone);
            document.getElementById('phoneError').textContent = phoneError;
            if (phoneError) isValid = false;
            
            const emailError = Validator.validateEmail(email);
            document.getElementById('emailError').textContent = emailError;
            if (emailError) isValid = false;
            
            if (!isValid) e.preventDefault();
        });
    </script>
</body>
</html>