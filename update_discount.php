<?php
// update_discount.php - Обновление скидки для товара
require 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = intval($_POST['product_id']);
    $discount = intval($_POST['discount']);
    if ($discount < 0) { $discount = 0; }
    if ($discount > 100) { $discount = 100; }
    
    $stmt = $pdo->prepare("UPDATE products SET discount = :discount WHERE id = :id");
    $stmt->execute([
        ':discount' => $discount,
        ':id' => $productId
    ]);
}

header("Location: admin_discounts.php");
exit;
?>