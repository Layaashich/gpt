<?php
// admin_discounts.php - Управление скидками
require 'config.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

// Получаем поисковый запрос и номер страницы
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 15;
$offset = ($page - 1) * $limit;

// Формируем запрос с поиском
$query = "SELECT * FROM products WHERE name LIKE ? LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($query);
$searchTerm = "%$search%";
$stmt->execute([$searchTerm]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем общее количество записей для пагинации
$countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM products WHERE name LIKE ?");
$countStmt->execute([$searchTerm]);
$totalRow = $countStmt->fetch(PDO::FETCH_ASSOC);
$totalPages = ceil($totalRow['total'] / $limit);
?>
<?php include 'header.php'; ?>

<div class="container mt-4">
    <div class="legla">
    <h2>Управление скидками</h2>
    
    <form method="GET" class="form-inline mb-3">
        <input type="text" name="search" class="form-control" placeholder="Поиск товара..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-primary ml-2">Поиск</button>
    </form>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID товара</th>
                <th>Название</th>
                <th>Цена (руб.)</th>
                <th>Скидка (%)</th>
                <th>Действие</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
            <tr>
                <form method="POST" action="update_discount.php">
                    <td><?= htmlspecialchars($product['id']) ?></td>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= number_format($product['price'], 2) ?></td>
                    <td>
                        <input type="number" name="discount" class="form-control" value="<?= htmlspecialchars($product['discount']) ?>" min="0" max="100">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    </td>
                    <td>
                        <button type="submit" class="btn btn-success">Сохранить</button>
                    </td>
                </form>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Пагинация -->
    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
            </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>
</div>