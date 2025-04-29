<!-- Функция удаления аккаунта (только обычным пользователям) -->
<?php
require 'config.php';

// Проверяем, что пользователь авторизован
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Если пользователь является администратором, запрещаем удаление
if ($_SESSION['user']['role'] == 'admin') {
    $_SESSION['message'] = "Администратор не может удалить свой аккаунт.";
    header("Location: account.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// Если подтверждение удаления не получено, выводим форму подтверждения
if (!isset($_POST['confirm'])) {
    include 'header.php';
    ?>
    <div class="container mt-4">
        <h2>Удаление аккаунта</h2>
        <div class="alert alert-danger">
            <strong>Внимание!</strong> Это действие необратимо. При удалении аккаунта вся информация, связанная с ним (заказы, отзывы, избранное и т.д.), будет удалена.
        </div>
        <form method="post" action="delete_account.php">
            <input type="hidden" name="confirm" value="yes">
            <button type="submit" class="btn btn-danger">Удалить аккаунт</button>
            <a href="account.php" class="btn btn-secondary">Отмена</a>
        </form>
    </div>
    <?php
    include 'footer.php';
    exit;
}

// Если подтверждение получено, выполняем удаление пользователя.
// При условии, что внешние ключи настроены с ON DELETE CASCADE для связанных таблиц.
$stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
$stmt->execute([':id' => $user_id]);

// Завершаем сессию пользователя
session_destroy();

// Перенаправляем на главную страницу с сообщением об удалении
header("Location: index.php?message=Account+deleted");
exit;
?>