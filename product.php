<?php
require 'config.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id AND status = 'active'");
$stmt->execute([':id' => $_GET['id']]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
    die("Товар не найден.");
}

// Получаем фотографии товара
$stmt = $pdo->prepare("SELECT * FROM product_photos WHERE product_id = :id ORDER BY id ASC");
$stmt->execute([':id' => $product['id']]);
$photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Вычисляем средний рейтинг товара
$stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as count_reviews FROM reviews WHERE product_id = :id");
$stmt->execute([':id' => $product['id']]);
$ratingData = $stmt->fetch(PDO::FETCH_ASSOC);
$avgRating = $ratingData['avg_rating'];
$countReviews = $ratingData['count_reviews'];

// Проверяем, покупал ли пользователь товар
$hasPurchased = false;
if (isset($_SESSION['user'])) {
    $stmt = $pdo->prepare("SELECT o.id FROM orders o JOIN order_items oi ON o.id = oi.order_id WHERE o.user_id = :user_id AND oi.product_id = :product_id");
    $stmt->execute([':user_id' => $_SESSION['user']['id'], ':product_id' => $product['id']]);
    if ($stmt->rowCount() > 0) {
        $hasPurchased = true;
    }
}

// Проверяем, оставлял ли пользователь уже отзыв
$hasReviewed = false;
if (isset($_SESSION['user'])) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reviews WHERE product_id = :pid AND user_id = :uid");
    $stmt->execute([':pid' => $product['id'], ':uid' => $_SESSION['user']['id']]);
    $hasReviewed = ($stmt->fetchColumn() > 0);
}

// Рекомендуемые товары (4 самых покупаемых)
$recommended_stmt = $pdo->query("
SELECT p.*, 
       (SELECT photo FROM product_photos WHERE product_id = p.id ORDER BY id ASC LIMIT 1) AS photo,
       IFNULL(SUM(oi.quantity), 0) AS total_sold
FROM products p
JOIN order_items oi ON p.id = oi.product_id
WHERE p.status = 'active'
GROUP BY p.id
ORDER BY total_sold DESC
LIMIT 4
");
$recommended_products = $recommended_stmt->fetchAll(PDO::FETCH_ASSOC);

// Рассчитываем цены с учётом скидки
$oldPrice = $product['price'];
$discount = isset($product['discount']) ? $product['discount'] : 0;
if ($discount > 0) {
    $finalPrice = $oldPrice * (1 - $discount / 100);
} else {
    $finalPrice = $oldPrice;
}

include 'header.php';
?>
<link rel="stylesheet" type="text/css" href="CSS/style_rating.css">
<style>
/* Стили для скидочной наклейки на странице товара */
.discount-sticker {
    position: absolute;
    top: 10px;
    left: 10px;
    background-color: red;
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
    font-weight: bold;
    font-size: 1em;
}
</style>

<div class="container mt-4">
    <div class="legla">
    <div class="row">
        <!-- Слайдер фотографий товара -->
        <div class="col-md-6">
            <?php if (count($photos) > 0): ?>
                <div id="carouselProduct" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner">
                        <?php foreach ($photos as $index => $photo): ?>
                            <div class="carousel-item <?= $index == 0 ? 'active' : '' ?>">
                                <img src="<?= htmlspecialchars($photo['photo']) ?>" class="d-block w-100" alt="Фото товара">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a class="carousel-control-prev" href="#carouselProduct" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </a>
                    <a class="carousel-control-next" href="#carouselProduct" role="button" data-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </a>
                </div>
            <?php else: ?>
                <img src="uploads/products/default.jpg" class="img-fluid" alt="Фото товара">
            <?php endif; ?>
        </div>
        
        <!-- Детали товара -->
        <div class="col-md-6">
            <h2><?= htmlspecialchars($product['name']) ?></h2>
            <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            <p><strong>Цвет:</strong> <?= htmlspecialchars($product['color']) ?></p>
            <p><strong>Материал:</strong> <?= htmlspecialchars($product['material']) ?></p>
            <p><strong>Страна изготовитель:</strong> <?= htmlspecialchars($product['country']) ?></p>
            <?php if (!empty($product['size'])): ?>
                <p><strong>Размер:</strong> <?= htmlspecialchars($product['size']) ?></p>
            <?php endif; ?>
            
            <!-- Цена со скидкой -->
            <p><strong>Цена:</strong>
                <?php if ($discount > 0): ?>
                    <span style="text-decoration: line-through; color: #888;">
                        <?= number_format($oldPrice, 2) ?> руб.
                    </span>
                    <span style="color:red; font-weight:bold; margin-left:10px;">
                        <?= number_format($finalPrice, 2) ?> руб.
                    </span>
                <?php else: ?>
                    <span><?= number_format($oldPrice, 2) ?> руб.</span>
                <?php endif; ?>
            </p>
            
            <!-- Отображение среднего рейтинга -->
            <p><strong>Рейтинг:</strong>
                <?php 
                if ($avgRating) {
                    echo number_format($avgRating, 1) . " / 5";
                } else {
                    echo "Нет оценок";
                }
                ?>
                (<?= $countReviews ?> отзывов)
            </p>
            
            <div class="d-flex">
                <button class="btn btn-success mr-2" onclick="location.href='cart.php?action=add&id=<?= $product['id'] ?>'">Добавить в корзину</button>
                <button class="btn btn-outline-success" onclick="location.href='favorites.php?action=add&id=<?= $product['id'] ?>'">Избранное</button>
            </div>
        </div>
    </div>
    
    <!-- Блок отзывов -->
    <div class="row mt-4">
        <div class="col-md-12">
            <h4>Отзывы</h4>
            <?php
            $stmt = $pdo->prepare("SELECT r.*, u.nickname FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = :id ORDER BY r.created_at DESC");
            $stmt->execute([':id' => $product['id']]);
            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($reviews as $review):
            ?>
                <div class="border p-2 mb-2">
                    <p><strong><?= htmlspecialchars($review['nickname']) ?></strong> оценка: <?= $review['rating'] ?>/5</p>
                    <p><?= nl2br(htmlspecialchars($review['review_text'])) ?></p>
                </div>
            <?php endforeach; ?>
            
            <?php if (isset($_SESSION['user'])): ?>
                <?php if (!$hasPurchased): ?>
                    <p>Оставлять отзывы могут только пользователи, совершившие покупку.</p>
                <?php elseif ($hasReviewed): ?>
                    <p>Вы уже оставили отзыв для этого товара.</p>
                <?php else: ?>
                    <!-- Форма отзыва с выбором оценки в виде звёздочек -->
<form method="post" action="submit_review.php">
  <div class="form-group">
    <label>Ваш отзыв:</label>
    <textarea name="review_text" class="form-control" rows="4" required></textarea>
  </div>
  <label>Ваша оценка:</label>
  <div class="form-group">
    <div class="star-rating">
      <input type="radio" id="star5" name="rating" value="5" /><label for="star5" title="Отлично"><i class="fas fa-star"></i></label>
      <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="Хорошо"><i class="fas fa-star"></i></label>
      <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="Удовлетворительно"><i class="fas fa-star"></i></label>
      <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="Плохо"><i class="fas fa-star"></i></label>
      <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="Очень плохо"><i class="fas fa-star"></i></label>
    </div>
  </div>
  <button type="submit" class="btn btn-primary">Отправить отзыв</button>
</form>
                <?php endif; ?>
            <?php else: ?>
                <p>Оставлять отзывы могут только авторизованные пользователи.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Рекомендуемые товары -->
    <div class="container mt-5">
        <h3 class="mb-4">Рекомендуемые товары</h3>
        <div class="recommended-products d-flex flex-wrap justify-content-between">
            <?php foreach ($recommended_products as $rec): ?>
                <?php
                    $oldPriceRec = $rec['price'];
                    $discountRec = $rec['discount'];
                    if ($discountRec > 0) {
                        $finalPriceRec = $oldPriceRec * (1 - $discountRec / 100);
                    } else {
                        $finalPriceRec = $oldPriceRec;
                    }
                ?>
                <div class="recommended-item" style="flex: 1 1 18%; min-width: 200px; margin-bottom:20px;">
                    <div class="card" style="position: relative;">
                        <a href="product.php?id=<?= $rec['id'] ?>">
                            <img src="<?= htmlspecialchars($rec['photo'] ? $rec['photo'] : 'uploads/products/default.jpg') ?>" class="card-img-top" alt="<?= htmlspecialchars($rec['name']) ?>">
                            <?php if ($discountRec > 0): ?>
                            <?php endif; ?>
                        </a>
                        <div class="card-body">
                            <h6 class="card-title"><?= htmlspecialchars($rec['name']) ?></h6>
                            <p class="card-text">
                                <?php if ($discountRec > 0): ?>
                                    <span style="text-decoration: line-through; color: #888;">
                                        <?= number_format($oldPriceRec, 2) ?> руб.
                                    </span>
                                    <br>
                                    <span style="color: red; font-weight: bold;">
                                        <?= number_format($finalPriceRec, 2) ?> руб.
                                    </span>
                                <?php else: ?>
                                    <span><?= number_format($oldPriceRec, 2) ?> руб.</span>
                                <?php endif; ?>
                            </p>
                            <a href="product.php?id=<?= $rec['id'] ?>" class="btn btn-sm btn-outline-primary">Подробнее</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
</div>