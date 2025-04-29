<!-- Управление пользователями в админ панели -->
<?php
require 'config.php';

// Проверяем, что пользователь авторизован и имеет роль администратора
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Обработка удаления пользователя
if (isset($_GET['delete'])) {
    $delete_user_id = intval($_GET['delete']);

    // Получаем информацию о пользователе, которого пытаемся удалить
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute([':id' => $delete_user_id]);
    $user_to_delete = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user_to_delete) {
        // Запрещаем удаление текущего администратора
        if ($user_to_delete['id'] == $_SESSION['user']['id']) {
            $_SESSION['message'] = "Вы не можете удалить свой собственный аккаунт.";
        } elseif ($user_to_delete['role'] == 'admin') {
            $_SESSION['message'] = "Вы не можете удалить аккаунт другого администратора.";
        } else {
            // Удаляем связанные записи (заказы, отзывы, избранное) – если внешние ключи не настроены с ON DELETE CASCADE
            $stmt = $pdo->prepare("DELETE FROM orders WHERE user_id = :id");
            $stmt->execute([':id' => $delete_user_id]);

            $stmt = $pdo->prepare("DELETE FROM reviews WHERE user_id = :id");
            $stmt->execute([':id' => $delete_user_id]);

            $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = :id");
            $stmt->execute([':id' => $delete_user_id]);

            // Удаляем пользователя
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute([':id' => $delete_user_id]);

            $_SESSION['message'] = "Пользователь успешно удалён.";
        }
    } else {
        $_SESSION['message'] = "Пользователь не найден.";
    }
    header("Location: admin_users.php");
    exit;
}

// Получаем список пользователей (выбираем нужные поля)
$stmt = $pdo->query("SELECT id, full_name, email, phone, role, profile_pic FROM users ORDER BY id ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<div class="container mt-4">
    <div class="legla">
    <h2>Управление пользователями</h2>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($_SESSION['message']) ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Фото</th>
                <th>ФИО</th>
                <th>Email</th>
                <th>Телефон</th>
                <th>Роль</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td>
                        <?php if (!empty($user['profile_pic']) && file_exists($user['profile_pic'])): ?>
                            <img src="<?= htmlspecialchars($user['profile_pic']) ?>" alt="Фото" style="width:50px; height:50px; object-fit:cover; border-radius:50%;">
                        <?php else: ?>
                            <img src="uploads/profiles/default.jpg" alt="Фото" style="width:50px; height:50px; object-fit:cover; border-radius:50%;">
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($user['full_name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['phone']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td>
                        <?php if ($user['id'] != $_SESSION['user']['id'] && $user['role'] !== 'admin'): ?>
                            <a href="admin_users.php?delete=<?= $user['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Удалить пользователя?');">Удалить</a>
                        <?php else: ?>
                            <span class="text-muted">Удаление недоступно</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</div>
