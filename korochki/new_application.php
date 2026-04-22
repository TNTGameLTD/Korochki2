<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

require_once 'classes/Course.php';
require_once 'classes/Application.php';

$course = new Course();
$courses = $course->getAll();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $app = new Application();
    $course_id = $_POST['course_id'];
    $start_date = $_POST['start_date'];
    $payment_method = $_POST['payment_method'];
    
    if ($app->create($_SESSION['user']['id'], $course_id, $start_date, $payment_method)) {
        header('Location: dashboard.php?success=1');
        exit;
    } else {
        $error = 'Ошибка при создании заявки';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новая заявка - Корочки.Есть</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Новая заявка</h2>
            
            <?php if ($error): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Курс</label>
                    <select name="course_id" required>
                        <option value="">Выберите курс</option>
                        <?php foreach ($courses as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Дата начала (ДД.ММ.ГГГГ)</label>
                    <input type="date" name="start_date" id="start_date" required>
                </div>
                
                <div class="form-group">
                    <label>Способ оплаты</label>
                    <select name="payment_method" required>
                        <option value="cash">Наличными</option>
                        <option value="phone">Перевод по номеру телефона</option>
                    </select>
                </div>
                
                <button type="submit" class="btn">Отправить заявку</button>
                <a href="dashboard.php" class="btn" style="background: #a0aec0; display: block; text-align: center; text-decoration: none; margin-top: 10px;">Назад</a>
            </form>
        </div>
    </div>
    
    <script>
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('start_date').min = today;
    </script>
</body>
</html>