<!-- Вывод отзывов -->
<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("SELECT r.*, p.name as product_name, r.rating FROM reviews r JOIN products p ON r.product_id = p.id WHERE r.user_id = :user_id ORDER BY r.created_at DESC");

$stmt->execute([':user_id' => $_SESSION['user_id']]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<div class="container mt-4">
<?php if(!empty($reviews)): ?>
    <?php foreach($reviews as $review): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5>Товар: <?= htmlspecialchars($review['product_name']) ?></h5>
                <p>Оценка: 
                    <?php for($i = 1; $i <= 5; $i++): ?>
                        <span class="fa fa-star <?= $i <= $review['rating'] ? 'text-warning' : 'text-muted' ?>"></span>
                    <?php endfor; ?>
                </p>
                <p>Отзыв: <?= nl2br(htmlspecialchars($review['review_text'])) ?></p>
                <p><small>Оставлен: <?= date("d.m.Y H:i", strtotime($review['created_at'])) ?></small></p>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Вы ещё не оставляли отзывы.</p>
<?php endif; ?>

</div>