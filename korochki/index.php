<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: ' . ($_SESSION['user']['role'] === 'admin' ? 'admin.php' : 'dashboard.php'));
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'classes/User.php';
    $user = new User();
    $user->login = $_POST['login'] ?? '';
    $user->password = $_POST['password'] ?? '';
    
    if ($user->login()) {
        $_SESSION['user'] = [
            'id' => $user->id,
            'login' => $user->login,
            'full_name' => $user->full_name,
            'role' => $user->role
        ];
        
        if ($user->login === 'Admin' && $_POST['password'] === 'KorokNET') {
            $_SESSION['user']['role'] = 'admin';
        }
        
        header('Location: ' . ($_SESSION['user']['role'] === 'admin' ? 'admin.php' : 'dashboard.php'));
        exit;
    } else {
        $error = 'Неверный логин или пароль';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Корочки.Есть - Вход</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Корочки.Есть</h2>
            
            <!-- Слайдер -->
            <div class="slider-container">
                <div class="slider">
                    <div class="slide"><img src="assets/images/image15.jpg" alt="Slide 1"></div>
                    <div class="slide"><img src="assets/images/image16.jpg" alt="Slide 2"></div>
                    <div class="slide"><img src="assets/images/image17.jpeg" alt="Slide 3"></div>
                    <div class="slide"><img src="assets/images/image18.webp" alt="Slide 4"></div>
                </div>
                <button class="slider-btn prev">❮</button>
                <button class="slider-btn next">❯</button>
                <div class="slider-dots"></div>
            </div>
            
            <?php if ($error): ?>
                <div class="error" style="text-align: center; margin-bottom: 15px;"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST" id="loginForm">
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
                
                <button type="submit" class="btn">Войти</button>
            </form>
            
            <div class="link">
                <a href="register.php">Еще не зарегистрированы? Регистрация</a>
            </div>
        </div>
    </div>
    
    <script src="assets/js/slider.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const login = document.getElementById('login').value;
            const password = document.getElementById('password').value;
            let isValid = true;
            
            const loginError = Validator.validateLogin(login);
            if (loginError) {
                document.getElementById('loginError').textContent = loginError;
                isValid = false;
            } else {
                document.getElementById('loginError').textContent = '';
            }
            
            const passError = Validator.validatePassword(password);
            if (passError) {
                document.getElementById('passError').textContent = passError;
                isValid = false;
            } else {
                document.getElementById('passError').textContent = '';
            }
            
            if (!isValid) e.preventDefault();
        });
    </script>
    <script src="assets/js/validation.js"></script>
</body>
</html>