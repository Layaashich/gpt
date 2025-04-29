<!-- Удаление товара в админ панели -->
<?php

require 'config.php';

// Проверяем, что пользователь авторизован и является администратором
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

// Проверяем, что передан параметр id товара
if (!isset($_GET['id'])) {
    header("Location: admin_products.php");
    exit;
}

$product_id = intval($_GET['id']);

// Проверяем, существует ли товар
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute([':id' => $product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Товар не найден.");
}

// Выполняем удаление товара
$stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
$stmt->execute([':id' => $product_id]);

// Перенаправляем обратно на страницу управления товарами с сообщением об успешном удалении
header("Location: admin_products.php?message=Product+deleted");
exit;
?>