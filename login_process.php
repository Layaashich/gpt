<!-- Обработка авторизации пользователя -->
<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nickname = trim($_POST['nickname']);
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE nickname = :nickname");
    $stmt->execute([':nickname' => $nickname]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        // Очищаем корзину и избранное при авторизации
        $_SESSION['cart'] = [];
        $_SESSION['favorites'] = [];
        $_SESSION['message'] = "Вы успешно авторизованы.";
    } else {
        $_SESSION['message'] = "Неверный логин или пароль.";
    }
    $_SESSION['user'] = $user;
    $_SESSION['user_id'] = $user['id'];
}
header("Location: index.php");
exit;
?>
