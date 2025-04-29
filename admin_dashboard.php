<?php
// admin_dashboard.php - Административная панель статистики
require 'config.php';
session_start();

// Проверка, что пользователь авторизован и является администратором
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

/* 1. Общая статистика по заказам */
$stmt = $pdo->query("SELECT COUNT(*) AS total_orders, IFNULL(SUM(total), 0) AS total_revenue, IFNULL(AVG(total), 0) AS avg_order_value FROM orders");
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

/* Количество пользователей */
$stmt = $pdo->query("SELECT COUNT(*) AS total_users FROM users");
$totalUsers = $stmt->fetchColumn();

/* Количество новых регистраций за последние 7 дней (если есть поле created_at в таблице users) */
$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
$newUsers = $stmt->fetchColumn();

/* 2. Динамика выручки и заказов за последние 7 дней */
$stmt = $pdo->query("SELECT DATE(created_at) AS order_date, SUM(total) AS daily_revenue, COUNT(*) AS daily_orders 
                     FROM orders 
                     GROUP BY DATE(created_at) 
                     ORDER BY order_date DESC 
                     LIMIT 7");
$dailyStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* 3. Популярные товары (топ-5) по количеству заказов */
$sqlPopular = "SELECT oi.product_id, COUNT(*) AS orders_count, p.name 
               FROM order_items oi 
               JOIN products p ON oi.product_id = p.id 
               GROUP BY oi.product_id 
               ORDER BY orders_count DESC 
               LIMIT 5";
$stmt = $pdo->query($sqlPopular);
$popularProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* 4. Статистика отзывов */
// Общий средний рейтинг и общее количество отзывов
$stmt = $pdo->query("SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews FROM reviews");
$reviewStats = $stmt->fetch(PDO::FETCH_ASSOC);

// Распределение отзывов по оценкам (от 5 до 1)
$stmt = $pdo->query("SELECT rating, COUNT(*) AS count FROM reviews GROUP BY rating ORDER BY rating DESC");
$ratingDistribution = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Дополнительная статистика отзывов: список товаров с их средним рейтингом и количеством отзывов
$stmt = $pdo->query("
    SELECT p.id, p.name, IFNULL(AVG(r.rating), 0) AS product_avg_rating, COUNT(r.id) AS product_reviews 
    FROM products p
    LEFT JOIN reviews r ON p.id = r.product_id
    GROUP BY p.id
    ORDER BY product_reviews DESC
    LIMIT 5
");
$productReviewStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* 5. Статистика избранного */
$stmt = $pdo->query("SELECT COUNT(*) AS total_favorites FROM user_favorites");
$totalFavorites = $stmt->fetchColumn();

/* 6. Фильтр заказов по конкретному пользователю */
$filterUserId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$orders = [];
if ($filterUserId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = :uid ORDER BY created_at DESC");
    $stmt->execute([':uid' => $filterUserId]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<?php include 'header.php'; ?>

<div class="container mt-4">
    
    <!-- 1. Общая статистика -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Заказы</div>
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($stats['total_orders']) ?></h5>
                    <p class="card-text">Общее количество заказов</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Выручка</div>
                <div class="card-body">
                    <h5 class="card-title"><?= number_format($stats['total_revenue'], 2) ?> руб.</h5>
                    <p class="card-text">Общая выручка</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-header">Средний чек</div>
                <div class="card-body">
                    <h5 class="card-title"><?= number_format($stats['avg_order_value'], 2) ?> руб.</h5>
                    <p class="card-text">Средняя стоимость заказа</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-secondary mb-3">
                <div class="card-header">Пользователи</div>
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($totalUsers) ?></h5>
                    <p class="card-text">Всего пользователей</p>
                    <p class="card-text">Новых за 7 дней: <?= htmlspecialchars($newUsers) ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 2. Динамика выручки и заказов за последние 7 дней -->
    <div class="card mb-4">
        <div class="card-header">Динамика выручки и заказов (последние 7 дней)</div>
        <div class="card-body">
            <?php if (!empty($dailyStats)): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Выручка, руб.</th>
                        <th>Заказы</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dailyStats as $day): ?>
                        <tr>
                            <td><?= htmlspecialchars($day['order_date']) ?></td>
                            <td><?= number_format($day['daily_revenue'], 2) ?></td>
                            <td><?= htmlspecialchars($day['daily_orders']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p>Нет данных за последние 7 дней.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- 3. Популярные товары (топ-5) -->
    <div class="card mb-4">
        <div class="card-header">Популярные товары (топ-5)</div>
        <div class="card-body">
            <?php if (!empty($popularProducts)): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Товар</th>
                        <th>Заказы</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($popularProducts as $pop): ?>
                        <tr>
                            <td><?= htmlspecialchars($pop['name']) ?></td>
                            <td><?= htmlspecialchars($pop['orders_count']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p>Нет данных о популярных товарах.</p>
            <?php endif; ?>
        </div>
    </div>
    
    
    <!-- 4.1 Статистика отзывов по товарам -->
    <div class="card mb-4">
        <div class="card-header">Статистика отзывов по товарам (топ-5)</div>
        <div class="card-body">
            <?php if (!empty($productReviewStats)): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Товар</th>
                        <th>Средний рейтинг</th>
                        <th>Отзывы</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productReviewStats as $prod): ?>
                        <tr>
                            <td><?= htmlspecialchars($prod['name']) ?></td>
                            <td><?= number_format($prod['product_avg_rating'], 2) ?> ⭐</td>
                            <td><?= htmlspecialchars($prod['product_reviews']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p>Нет данных по отзывам по товарам.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- 5. Статистика избранного -->
    <div class="card mb-4">
        <div class="card-header">Избранное</div>
        <div class="card-body">
            <p><strong>Общее количество добавлений в избранное:</strong> <?= htmlspecialchars($totalFavorites) ?></p>
        </div>
    </div>
    
    <!-- 6. Фильтр для просмотра заказов конкретного пользователя -->
    <div class="card mb-4">
        <div class="card-header">Просмотр заказов по пользователю</div>
        <div class="card-body">
            <form method="get" action="admin_dashboard.php" class="form-inline">
                <label class="mr-2" for="userSelect">Выберите пользователя:</label>
                <select name="user_id" id="userSelect" class="form-control mr-2">
                    <option value="0">-- Все пользователи --</option>
                    <?php 
                    // Получаем список пользователей
                    $stmt = $pdo->query("SELECT id, nickname, full_name FROM users ORDER BY full_name ASC");
                    $usersList = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($usersList as $userItem): ?>
                        <option value="<?= $userItem['id'] ?>" <?= (isset($_GET['user_id']) && $_GET['user_id'] == $userItem['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($userItem['full_name'] ? $userItem['full_name'] : $userItem['nickname']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary">Показать заказы</button>
            </form>
        </div>
    </div>
    
    <?php if (isset($_GET['user_id']) && intval($_GET['user_id']) > 0): 
        $filterUserId = intval($_GET['user_id']);
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = :uid ORDER BY created_at DESC");
        $stmt->execute([':uid' => $filterUserId]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
        <div class="card mb-4">
            <div class="card-header">Заказы пользователя (ID: <?= $filterUserId ?>)</div>
            <div class="card-body">
                <?php if (empty($orders)): ?>
                    <p>Заказов для выбранного пользователя не найдено.</p>
                <?php else: ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID заказа</th>
                                <th>Дата заказа</th>
                                <th>Сумма</th>
                                <th>Способ оплаты</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?= htmlspecialchars($order['id']) ?></td>
                                    <td><?= date("d.m.Y H:i", strtotime($order['created_at'])) ?></td>
                                    <td><?= number_format($order['total'], 2) ?> руб.</td>
                                    <td><?= htmlspecialchars($order['payment_method']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
    
</div>