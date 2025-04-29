<?php
require 'config.php';

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['full_name']) || empty($_POST['address'])) {
        $error = "Пожалуйста, заполните обязательные поля.";
    } else {
        $payment_method = $_POST['payment_method'];
        if ($payment_method == 'card') {
            $card_number = preg_replace('/\s+/', '', $_POST['card_number']);
            $card_expiry = $_POST['card_expiry'];
            $card_cvc = $_POST['card_cvc'];

            if (!preg_match('/^\d{16}$/', $card_number)) {
                $error = "Неверный номер карты.";
            }
            if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $card_expiry)) {
                $error = "Неверный срок карты. Формат должен быть MM/YY.";
            } else {
                list($month, $year) = explode('/', $card_expiry);
                $full_year = '20' . $year;
                $expiryDate = DateTime::createFromFormat('Y-m-d', "$full_year-$month-01");
                $expiryDate->modify('last day of this month');
                $currentDate = new DateTime();
                if ($expiryDate < $currentDate) {
                    $error = "Срок действия карты истёк.";
                }
            }
            if (!preg_match('/^\d{3}$/', $card_cvc)) {
                $error = "Неверный CVC код.";
            }
        }
    }

    if (empty($error)) {
        $total = 0;
        $ids = implode(',', array_keys($_SESSION['cart']));
        $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids) AND status = 'active'");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $final_items = [];
        foreach ($products as $product) {
            $qty = $_SESSION['cart'][$product['id']];
            $price = $product['price'];
            $discount = $product['discount'] ?? 0;
            $discounted_price = $price;
            if ($discount > 0) {
                $discounted_price = round($price * (1 - $discount / 100), 2);
            }
            $total += $discounted_price * $qty;
            $final_items[] = [
                'id' => $product['id'],
                'qty' => $qty,
                'final_price' => $discounted_price
            ];
        }

        $delivery = ($total < 2000 && $total > 0) ? 399 : 0;
        $finalTotal = $total + $delivery;

        $stmt = $pdo->prepare("INSERT INTO orders (user_id, full_name, address, payment_method, total) VALUES (:user_id, :full_name, :address, :payment_method, :total)");
        $stmt->execute([
            ':user_id'    => $_SESSION['user']['id'] ?? 0,
            ':full_name'  => $_POST['full_name'],
            ':address'    => $_POST['address'],
            ':payment_method' => $payment_method,
            ':total'      => $finalTotal
        ]);
        $order_id = $pdo->lastInsertId();

        foreach ($final_items as $item) {
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)");
            $stmt->execute([
                ':order_id'  => $order_id,
                ':product_id'=> $item['id'],
                ':quantity'  => $item['qty'],
                ':price'     => $item['final_price']
            ]);
        }

        $_SESSION['cart'] = [];

        $successMessage = "Заказ №$order_id успешно оформлен, доставка будет осуществлена в течение 3-х дней.";
    }
}
?>
<?php include 'header.php'; ?>
<!-- Подключаем Inputmask -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"></script>
<div class="container mt-4">
    <div class="legla">
    <h2>Оформление заказа</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif (isset($successMessage)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
    <?php endif; ?>
    <?php if (!isset($successMessage)): ?>
    <form method="post" action="checkout.php">
        <div class="form-group">
            <label>ФИО заказчика:</label>
            <input type="text" name="full_name" class="form-control" placeholder="Введите ФИО" required>
        </div>
        <div class="form-group">
            <label>Адрес доставки:</label>
            <textarea name="address" class="form-control" placeholder="Введите адрес доставки" required></textarea>
        </div>
        <div class="form-group">
            <label>Способ оплаты:</label><br>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="payment_method" value="cash" checked onclick="toggleCardFields(false)">
              <label class="form-check-label">Оплата наличными при получении</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="payment_method" value="card" onclick="toggleCardFields(true)">
              <label class="form-check-label">Оплата картой</label>
            </div>
        </div>
        <div id="cardFields" style="display:none;">
            <div class="form-group">
                <label>Номер карты:</label>
                <input type="text" name="card_number" class="form-control" placeholder="0000 0000 0000 0000">
            </div>
            <div class="form-group">
                <label>Срок карты (MM/YY):</label>
                <input type="text" name="card_expiry" class="form-control" placeholder="00/00">
            </div>
            <div class="form-group">
                <label>CVC (000):</label>
                <input type="text" name="card_cvc" class="form-control" placeholder="000">
            </div>
        </div>
        <button type="submit" class="btn btn-success">Оформить заказ</button>
    </form>
    <?php endif; ?>
</div>
</div>
<script>
function toggleCardFields(show){
    document.getElementById('cardFields').style.display = show ? 'block' : 'none';
}
$(document).ready(function(){
    $('input[name="card_number"]').inputmask("9999 9999 9999 9999");
    $('input[name="card_expiry"]').inputmask("99/99");
    $('input[name="card_cvc"]').inputmask("999");
});
</script>