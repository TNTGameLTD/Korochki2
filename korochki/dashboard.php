<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

require_once 'classes/Application.php';
require_once 'classes/Course.php';

$app = new Application();
$course = new Course();

$applications = $app->getUserApplications($_SESSION['user']['id']);
$courses = $course->getAll();

// Обработка отзыва
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review'])) {
    require_once 'classes/Review.php';
    $review = new Review();
    $review->user_id = $_SESSION['user']['id'];
    $review->course_id = $_POST['course_id'];
    $review->text = $_POST['review_text'];
    $review->save();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои заявки - Корочки.Есть</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container" style="max-width: 500px;">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin: 0;">Мои заявки</h2>
                <div>
                    <a href="new_application.php" class="btn" style="padding: 8px 16px; width: auto;">+ Новая</a>
                    <a href="logout.php" style="margin-left: 10px; color: #f56565;">Выйти</a>
                </div>
            </div>
            
            <p>Добро пожаловать, <?= htmlspecialchars($_SESSION['user']['full_name']) ?>!</p>
            
            <?php if (empty($applications)): ?>
                <p style="text-align: center; color: #888; margin-top: 30px;">У вас пока нет заявок</p>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Курс</th>
                                <th>Дата</th>
                                <th>Статус</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $app): ?>
                            <tr>
                                <td><?= htmlspecialchars($app['course_name']) ?></td>
                                <td><?= date('d.m.Y', strtotime($app['start_date'])) ?></td>
                                <td>
                                    <span style="
                                        padding: 4px 8px;
                                        border-radius: 20px;
                                        font-size: 12px;
                                        background: <?= 
                                            $app['status'] === 'Новая' ? '#fef3c7' : 
                                            ($app['status'] === 'Идет обучение' ? '#dbeafe' : '#d1fae5')
                                        ?>;
                                        color: <?= 
                                            $app['status'] === 'Новая' ? '#92400e' : 
                                            ($app['status'] === 'Идет обучение' ? '#1e40af' : '#065f46')
                                        ?>;
                                    ">
                                        <?= $app['status'] ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Форма отзыва (доступна только после завершения) -->
                <?php 
                $completedCourses = array_filter($applications, fn($a) => $a['status'] === 'Обучение завершено');
                if (!empty($completedCourses)): 
                ?>
                <div style="margin-top: 30px;">
                    <h3>Оставить отзыв</h3>
                    <form method="POST">
                        <div class="form-group">
                            <select name="course_id" required>
                                <option value="">Выберите курс</option>
                                <?php foreach ($completedCourses as $c): ?>
                                <option value="<?= $c['course_id'] ?>"><?= htmlspecialchars($c['course_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <textarea name="review_text" rows="3" placeholder="Ваш отзыв" required></textarea>
                        </div>
                        <button type="submit" name="review" class="btn btn-secondary">Отправить отзыв</button>
                    </form>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>