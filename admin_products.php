<?php
require 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

// Обработка поиска (поиск по названию товара)
$searchQuery = '';
$params = [];
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $searchQuery = trim($_GET['q']);
    $whereClause = " WHERE name LIKE :q ";
    $params[':q'] = '%' . $searchQuery . '%';
} else {
    $whereClause = "";
}

// Получаем список всех товаров
$stmt = $pdo->prepare("SELECT * FROM products $whereClause ORDER BY id ASC");
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем количество товаров с stock = 0 (закончившихся)
$stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE status = 'active' AND stock = 0");
$outOfStockCount = $stmt->fetchColumn();
?>
<?php include 'header.php'; ?>

<div class="container mt-4">
    <div class="legla">
    <h2>Управление товарами</h2>
    
    <!-- Панель поиска -->
    <form method="get" action="admin_products.php" class="form-inline mb-3">
        <input type="text" name="q" class="form-control mr-2" placeholder="Поиск товара..." value="<?= htmlspecialchars($searchQuery) ?>">
        <button type="submit" class="btn btn-primary">Поиск</button>
    </form>
    
    <div class="mb-3 text-right">
        <a href="add_product.php" class="btn btn-primary">Добавить товар</a>
        <a href="admin_discounts.php" class="btn btn-primary">Управление скидками</a>
    </div>

    <h4>Список товаров</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Цена</th>
                <th>На складе</th>
                <th>Категория</th>
                <th>Подкатегория</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $prod): ?>
            <tr>
                <td><?= $prod['id'] ?></td>
                <td><?= htmlspecialchars($prod['name']) ?></td>
                <td><?= number_format($prod['price'], 2) ?> руб.</td>
                <td><?= htmlspecialchars($prod['stock']) ?></td>
                <td><?= $prod['category_id'] ?></td>
                <td><?= $prod['subcategory_id'] ?></td>
                <td>
                    <?php 
                        if ($prod['status'] == 'active') { echo 'Активно'; }
                        elseif ($prod['status'] == 'archived') { echo 'Архивировано'; }
                        else { echo htmlspecialchars($prod['status']); }
                    ?>
                </td>
                <td>
                    <a href="admin_edit_product.php?id=<?= $prod['id'] ?>" class="btn btn-primary btn-sm">Редактировать</a>
                    <?php if ($prod['status'] == 'active'): ?>
                        <a href="admin_toggle_product_status.php?id=<?= $prod['id'] ?>&action=archive" class="btn btn-warning btn-sm" onclick="return confirm('Архивировать товар?');">Архивировать</a>
                    <?php else: ?>
                        <a href="admin_toggle_product_status.php?id=<?= $prod['id'] ?>&action=restore" class="btn btn-success btn-sm" onclick="return confirm('Восстановить товар?');">Восстановить</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</div>