<?php
// out_of_stock_products.php - Страница отображения товаров, закончившихся на складе
require 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM products WHERE status = 'active' AND stock = 0");
$outOfStockProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'header.php'; ?>

<div class="container mt-4">
    <h2>Закончившиеся товары</h2>
    <?php if (empty($outOfStockProducts)): ?>
        <p>Нет товаров, закончившихся на складе.</p>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Цена</th>
                    <th>Категория</th>
                    <th>Подкатегория</th>
                    <th>Действия (Пополнить)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($outOfStockProducts as $prod): ?>
                <tr>
                    <td><?= $prod['id'] ?></td>
                    <td><?= htmlspecialchars($prod['name']) ?></td>
                    <td><?= number_format($prod['price'], 2) ?> руб.</td>
                    <td><?= $prod['category_id'] ?></td>
                    <td><?= $prod['subcategory_id'] ?></td>
                    <td>
                        <a href="admin_edit_product.php?id=<?= $prod['id'] ?>" class="btn btn-primary btn-sm">Пополнить</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <a href="admin_products.php" class="btn btn-secondary">Назад</a>
</div>