<!-- Функция архивирования и восстановления товара -->
<?php
require 'config.php';

// Проверяем, что пользователь авторизован и является администратором
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id']) || !isset($_GET['action'])) {
    header("Location: admin_products.php");
    exit;
}

$product_id = intval($_GET['id']);
$action = $_GET['action'];

$new_status = ($action == 'archive') ? 'archived' : 'active';

$stmt = $pdo->prepare("UPDATE products SET status = :status WHERE id = :id");
$stmt->execute([':status' => $new_status, ':id' => $product_id]);

header("Location: admin_products.php?message=Product+status+updated");
exit;
?>