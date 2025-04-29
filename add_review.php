<!-- Добавление отзыва к товару -->
<?php
require 'config.php';

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user'])){
    $product_id = $_POST['product_id'];
    $review_text = trim($_POST['review_text']);
    $rating = intval($_POST['rating']);
    $stmt = $pdo->prepare("INSERT INTO reviews (product_id, user_id, review_text, rating) VALUES (:product_id, :user_id, :review_text, :rating)");
    $stmt->execute([
        ':product_id' => $product_id,
        ':user_id' => $_SESSION['user']['id'],
        ':review_text' => $review_text,
        ':rating' => $rating
    ]);
}
header("Location: product.php?id=" . $product_id);
exit;
?>