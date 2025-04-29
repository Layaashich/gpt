<!-- Страница авторизации (сейчас не используемая) -->
<?php
require 'config.php';

$error = '';
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $nickname = trim($_POST['nickname']);
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE nickname = :nickname");
    $stmt->execute([':nickname' => $nickname]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if($user && password_verify($password, $user['password'])){
        $_SESSION['user'] = $user;
        header("Location: index.php");
        exit;
    } else {
        $error = "Неверный никнейм или пароль.";
    }
}
?>
<?php include 'header.php'; ?>
<div class="container mt-4">
    <h2>Вход</h2>
    <?php if(!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" action="login.php">
        <div class="form-group">
            <label>Никнейм:</label>
            <input type="text" name="nickname" class="form-control" placeholder="Введите никнейм" required>
        </div>
        <div class="form-group">
            <label>Пароль:</label>
            <input type="password" name="password" class="form-control" placeholder="Введите пароль" required>
        </div>
        <button type="submit" class="btn btn-primary">Войти</button>
    </form>
</div>