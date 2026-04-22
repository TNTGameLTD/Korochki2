<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

require_once 'classes/Application.php';

$app = new Application();
$status_filter = $_GET['status'] ?? '';
$applications = $app->getAllApplications($status_filter ?: null);

// Обработка смены статуса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['app_id']) && isset($_POST['new_status'])) {
    $app->updateStatus($_POST['app_id'], $_POST['new_status']);
    header('Location: admin.php?status=' . $status_filter);
    exit;
}

// Пагинация
$per_page = 10;
$page = $_GET['page'] ?? 1;
$total = count($applications);
$pages = ceil($total / $per_page);
$offset = ($page - 1) * $per_page;
$applications_page = array_slice($applications, $offset, $per_page);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель администратора - Корочки.Есть</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container" style="max-width: 800px;">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>Панель администратора</h2>
                <a href="logout.php" style="color: #f56565;">Выйти</a>
            </div>
            
            <!-- Фильтр -->
            <div class="filter-bar">
                <select id="statusFilter" onchange="location.href='admin.php?status=' + this.value">
                    <option value="">Все заявки</option>
                    <option value="Новая" <?= $status_filter === 'Новая' ? 'selected' : '' ?>>Новые</option>
                    <option value="Идет обучение" <?= $status_filter === 'Идет обучение' ? 'selected' : '' ?>>Идет обучение</option>
                    <option value="Обучение завершено" <?= $status_filter === 'Обучение завершено' ? 'selected' : '' ?>>Завершено</option>
                </select>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Студент</th>
                            <th>Курс</th>
                            <th>Дата</th>
                            <th>Оплата</th>
                            <th>Статус</th>
                            <th>Действие</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications_page as $app): ?>
                        <tr>
                            <td><?= $app['id'] ?></td>
                            <td><?= htmlspecialchars($app['full_name']) ?></td>
                            <td><?= htmlspecialchars($app['course_name']) ?></td>
                            <td><?= date('d.m.Y', strtotime($app['start_date'])) ?></td>
                            <td>
                                <?= $app['payment_method'] === 'cash' ? 'Наличные' : 'Перевод' ?>
                            </td>
                            <td>
                                <span style="
                                    padding: 4px 8px;
                                    border-radius: 20px;
                                    font-size: 12px;
                                    background: <?= 
                                        $app['status'] === 'Новая' ? '#fef3c7' : 
                                        ($app['status'] === 'Идет обучение' ? '#dbeafe' : '#d1fae5')
                                    ?>;
                                ">
                                    <?= $app['status'] ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($app['status'] !== 'Обучение завершено'): ?>
                                <form method="POST" class="status-form" data-original-status="<?= $app['status'] ?>">
    <input type="hidden" name="app_id" value="<?= $app['id'] ?>">
    <select name="new_status" class="status-select" style="padding: 4px; font-size: 12px;">
        <option value="">Сменить</option>
        <?php if ($app['status'] === 'Новая'): ?>
        <option value="Идет обучение">Начать обучение</option>
        <?php elseif ($app['status'] === 'Идет обучение'): ?>
        <option value="Обучение завершено">Завершить</option>
        <?php endif; ?>
    </select>
</form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Пагинация -->
            <?php if ($pages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                <a href="?page=<?= $i ?>&status=<?= $status_filter ?>" 
                   class="page-btn <?= $page == $i ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    

    <script src="assets/js/admin.js"></script>
    <script>
        // Передаем данные о заявках для статистики
        window.applicationsData = <?= json_encode($applications) ?>;
        
        // Добавляем ID для таблицы
        document.querySelector('table').id = 'applicationsTable';
        
        // Добавляем контейнер для статистики
        const statsContainer = document.createElement('div');
        statsContainer.className = 'stats-container';
        const filterBar = document.querySelector('.filter-bar');
        filterBar.parentNode.insertBefore(statsContainer, filterBar.nextSibling);
    </script>
</body>
</html>
