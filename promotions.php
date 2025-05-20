<?php
// promotions.php
require_once 'config.php';
include 'header.php';
?>
<link rel="stylesheet" href="CSS/promotions.css">

<div class="legla">
<main class="promotions-page">
    <h1>Наши акции</h1>

    <div class="promotions-list">
        <!-- Первая акция -->
        <div class="promotion-item">
            <a href="free_delivery.php">
                <img src="Promotions-img/free_delivery.png" alt="Акция 1">
                <h3>Бесплатаная доставка</h3>
            </a>
        </div>
        <!-- Вторая акция -->
        <div class="promotion-item">
            <a href="birthday.php">
                <img src="Promotions-img/birthday.png" alt="Акция 2">
                <h3>День рождения</h3>
            </a>
        </div>
        <!-- Третья акция -->
        <div class="promotion-item">
            <a href="season_sale.php">
                <img src="Promotions-img/season_sale.png" alt="Акция 3">
                <h3>Сезонная распродажа</h3>
            </a>
        </div>
    </div>
</main>
</div>
