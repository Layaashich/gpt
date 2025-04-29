<!-- Функция вывода заказов -->
<?php
require 'config.php';
session_start();

$_SESSION['user_id'] = $_SESSION['user']['id'];

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>

<div class="container mt-4">
    <?php if (!empty($orders)): ?>
        <ul class="list-group">
            <?php foreach ($orders as $order): ?>
                <li class="list-group-item">
                    <a href="order_details.php?id=<?= $order['id'] ?>">
                        Заказ №<?= $order['id'] ?> - <?= htmlspecialchars($order['created_at']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>У вас пока нет заказов.</p>
    <?php endif; ?>
</div>
