<?php
$old_price = $product['price'];
$discount = $product['discount'];
$price = $discount ? $old_price * (1 - $discount / 100) : $old_price;
?>

<div class="card">
    <img src="uploads/products/<?= $product['image'] ?>" class="card-img-top" alt="<?= $product['name'] ?>">
    <div class="card-body">
        <h5 class="card-title"><?= $product['name'] ?></h5>
        <?php if ($discount): ?>
            <p><span style="text-decoration: line-through; color: #999;"><?= number_format($old_price, 2) ?>₽</span></p>
            <p style="color:red;"><strong><?= number_format($price, 2) ?>₽ (Скидка <?= $discount ?>%)</strong></p>
        <?php else: ?>
            <p><strong><?= number_format($price, 2) ?>₽</strong></p>
        <?php endif; ?>
    </div>
</div>
