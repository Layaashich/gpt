<!-- Функция просмотра пользователей для админа в админ панели -->
<?php
require 'config.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: index.php");
    exit;
}

if(!isset($_GET['id'])){
    header("Location: admin_users.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $_GET['id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$user){
    die("Пользователь не найден.");
}
?>
<?php include 'header.php'; ?>
<div class="container mt-4">
    <h2>Профиль пользователя <?= htmlspecialchars($user['nickname']) ?></h2>
    <p>ФИО: <?= htmlspecialchars($user['full_name']) ?></p>
    <p>Email: <?= htmlspecialchars($user['email']) ?></p>
    <p>Телефон: <?= htmlspecialchars($user['phone']) ?></p>
    <p>Роль: <?= htmlspecialchars($user['role']) ?></p>
    <?php if(!empty($user['profile_pic'])): ?>
        <img src="<?= $user['profile_pic'] ?>" alt="Фото профиля" width="550">
    <?php endif; ?>
    <br>
    <a href="admin_users.php" class="btn btn-secondary mt-3">Назад</a>
</div>