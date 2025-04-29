<?php
require 'config.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: account.php");
    exit;
}

$order_id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id AND user_id = :user_id");
$stmt->execute([':id' => $order_id, ':user_id' => $_SESSION['user']['id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Заказ не найден.");
}

$stmt = $pdo->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = :order_id");
$stmt->execute([':order_id' => $order_id]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'header.php'; ?>
<div class="container mt-4">
    <div class="legla">
    <h2>Детали заказа №<?= $order['id'] ?></h2>
    <p><strong>Дата заказа:</strong> <?= $order['created_at'] ?></p>
    <p><strong>Сумма заказа:</strong> <?= number_format($order['total'], 2) ?> руб.</p>
    <p><strong>ФИО заказчика:</strong> <?= htmlspecialchars($order['full_name']) ?></p>
    <p><strong>Адрес доставки:</strong> <?= nl2br(htmlspecialchars($order['address'])) ?></p>
    <h4>Состав заказа:</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Товар</th>
                <th>Количество</th>
                <th>Цена за единицу</th>
                <th>Сумма</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $items_total = 0;
            foreach ($order_items as $item):
                $sum = $item['price'] * $item['quantity'];
                $items_total += $sum;
            ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= number_format($item['price'], 2) ?> руб.</td>
                    <td><?= number_format($sum, 2) ?> руб.</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p><strong>Стоимость товаров:</strong> <?= number_format($items_total, 2) ?> руб.</p>
    <p><strong>Итоговая сумма заказа (с доставкой):</strong> <?= number_format($order['total'], 2) ?> руб.</p>
    <a href="account.php" class="btn btn-secondary">Назад в личный кабинет</a>
</div>
</div>
