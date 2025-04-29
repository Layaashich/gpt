<?php
// cart.php - Страница корзины
require 'config.php';
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (!isset($_SESSION['user'])) {
    $_SESSION['message'] = "Чтобы добавить товар в корзину, необходимо зарегистрироваться или авторизоваться.";
    header("Location: index.php");
    exit;
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $productId = isset($_GET['id']) ? $_GET['id'] : null;
    if ($action == 'add' && $productId) {
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]++;
        } else {
            $_SESSION['cart'][$productId] = 1;
        }
        header("Location: cart.php");
        exit;
    }
    if ($action == 'remove' && $productId) {
        unset($_SESSION['cart'][$productId]);
        header("Location: cart.php");
        exit;
    }
    if ($action == 'update' && isset($_POST['quantities'])) {
        foreach ($_POST['quantities'] as $id => $qty) {
            $_SESSION['cart'][$id] = $qty;
        }
        header("Location: cart.php");
        exit;
    }
}

$cartItems = [];
$total = 0;
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cartItems as $item) {
        $finalPrice = $item['price'];
        if (isset($item['discount']) && $item['discount'] > 0) {
            $finalPrice = $item['price'] * (1 - $item['discount'] / 100);
        }
        $total += $finalPrice * $_SESSION['cart'][$item['id']];
    }
}

// Если сумма товаров меньше 2000 и корзина не пустая, добавляем стоимость доставки
$delivery = ($total < 2000 && $total > 0) ? 399 : 0;
$finalTotal = $total + $delivery;
?>
<?php include 'header.php'; ?>
<div class="container mt-4">
    <div class="legla">
        <h2>Корзина</h2>
        <form method="post" action="cart.php?action=update">
            <table class="table">
                <thead>
                    <tr>
                        <th>Товар</th>
                        <th>Цена</th>
                        <th>Количество</th>
                        <th>Сумма</th>
                        <th>Действие</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <?php
                        $finalPrice = $item['price'];
                        if (isset($item['discount']) && $item['discount'] > 0) {
                            $finalPrice = $item['price'] * (1 - $item['discount'] / 100);
                        }
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td>
                                <?php if (isset($item['discount']) && $item['discount'] > 0): ?>
                                    <span style="text-decoration: line-through; color: #888;"><?= number_format($item['price'], 2) ?> руб.</span>
                                    <br>
                                    <span style="color: red; font-weight: bold;"><?= number_format($finalPrice, 2) ?> руб.</span>
                                <?php else: ?>
                                    <?= number_format($item['price'], 2) ?> руб.
                                <?php endif; ?>
                            </td>
                            <td>
                                <input type="number" name="quantities[<?= $item['id'] ?>]" value="<?= $_SESSION['cart'][$item['id']] ?>" min="1" class="form-control">
                            </td>
                            <td><?= number_format($finalPrice * $_SESSION['cart'][$item['id']], 2) ?> руб.</td>
                            <td><a href="cart.php?action=remove&id=<?= $item['id'] ?>" class="btn btn-danger btn-sm">Удалить</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary">Обновить корзину</button>
        </form>
        
        <h3 class="mt-3">Общая сумма: <?= number_format($finalTotal, 2) ?> руб.</h3>
        
        <!-- Плашка доставки -->
        <div id="deliveryBanner" class="alert alert-info mt-3">
            <?php
            if ($total >= 2000 || $total == 0) {
                echo "Доставка бесплатна.";
            } else {
                echo "Доставка 399 руб, от 2000 руб бесплатно.";
            }
            ?>
        </div>
        
        <a href="checkout.php" class="btn btn-success mt-3">Оформить заказ</a>
    </div>
</div>