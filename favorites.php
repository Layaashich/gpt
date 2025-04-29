<!-- Страница избранного -->
<?php
// favorites.php - Страница избранного
require 'config.php';
session_start();

if (!isset($_SESSION['user'])) {
    $_SESSION['message'] = "Чтобы добавить товар в избранное, необходимо зарегистрироваться или авторизоваться.";
    header("Location: index.php");
    exit;
}

if(isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])){
    $productId = $_GET['id'];
    if(!in_array($productId, $_SESSION['favorites'])){
        $_SESSION['favorites'][] = $productId;
    }
    header("Location: favorites.php");
    exit;
}

if(isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])){
    $productId = $_GET['id'];
    if(($key = array_search($productId, $_SESSION['favorites'])) !== false){
        unset($_SESSION['favorites'][$key]);
    }
    // Если избранное пустое, перенаправляем на главную страницу
    if(empty($_SESSION['favorites'])){
        header("Location: index.php");
    } else {
        header("Location: favorites.php");
    }
    exit;
}

// Получаем данные избранных товаров
$favProducts = [];
if(!empty($_SESSION['favorites'])){
    $ids = implode(',', $_SESSION['favorites']);
    $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
    $favProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Получаем данные избранных товаров (только активные)
// Вместо SELECT * FROM products, используем подзапрос для получения первого фото
$favProducts = [];
if (!empty($_SESSION['favorites'])) {
    $ids = implode(',', $_SESSION['favorites']);
    $sql = "SELECT p.*,
               (SELECT photo 
                FROM product_photos 
                WHERE product_id = p.id 
                ORDER BY id ASC 
                LIMIT 1) AS photo
            FROM products p
            WHERE p.id IN ($ids) 
              AND p.status = 'active'";
    $stmt = $pdo->query($sql);
    $favProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>
<?php include 'header.php'; ?>
<div class="container mt-4">
    <div class="legla">
    <h2>Избранное</h2>
    <br>
    <div class="row">
        <?php if (empty($favProducts)): ?>
            <p>   Избранных товаров нет.</p>
        <?php else: ?>
            <?php foreach ($favProducts as $product): ?>
                <div class="col-md-4 product-card mb-4">
                    <div class="card">
                        <a href="product.php?id=<?= $product['id'] ?>">
                            <img src="<?= htmlspecialchars($product['photo'] ? $product['photo'] : 'uploads/products/default.jpg') ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="card-text"><?= number_format($product['price'], 2) ?> руб.</p>
                            <div class="d-flex">
                                <button class="btn btn-success btn-sm" onclick="location.href='cart.php?action=add&id=<?= $product['id'] ?>'">Добавить в корзину</button>
                                <button class="btn btn-outline-danger btn-sm ml-2" onclick="location.href='favorites.php?action=remove&id=<?= $product['id'] ?>'">
                                    Удалить из избранного
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</div>