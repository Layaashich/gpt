<!-- Личный кабинет пользователя -->
<?php
require 'config.php';
if(!isset($_SESSION['user'])){
    header("Location: index.php");
    exit;
}
$user_id = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$error = '';
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])){
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    if(empty($full_name) || empty($email) || empty($phone)){
        $error = "Все поля обязательны.";
    } else {
        $stmt = $pdo->prepare("UPDATE users SET full_name = :full_name, email = :email, phone = :phone WHERE id = :id");
        $stmt->execute([
            ':full_name' => $full_name,
            ':email' => $email,
            ':phone' => $phone,
            ':id' => $user_id
        ]);
        $_SESSION['user']['full_name'] = $full_name;
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['phone'] = $phone;

        // Обработка новой фотографии профиля
        if(isset($_FILES['new_profile_pic']) && $_FILES['new_profile_pic']['error'] == 0){
            $ext = pathinfo($_FILES['new_profile_pic']['name'], PATHINFO_EXTENSION);
            $profile_pic = 'uploads/profiles/' . $user['nickname'] . '.' . $ext;
            move_uploaded_file($_FILES['new_profile_pic']['tmp_name'], $profile_pic);
            $stmt = $pdo->prepare("UPDATE users SET profile_pic = :pic WHERE id = :id");
            $stmt->execute([
                ':pic' => $profile_pic,
                ':id' => $user_id
            ]);
            $_SESSION['user']['profile_pic'] = $profile_pic;
        }
        header("Location: account.php");
        exit;
    }
}

// Получаем заказы пользователя
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->execute([':user_id' => $user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем отзывы пользователя
$stmt = $pdo->prepare("SELECT r.*, p.name as product_name FROM reviews r JOIN products p ON r.product_id = p.id WHERE r.user_id = :user_id ORDER BY r.created_at DESC");
$stmt->execute([':user_id' => $user_id]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>

<div class="container mt-4">
    <div class="legla">
    <h2>Личный кабинет</h2>
    <?php if(!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
        <div class="card-body">
            <h4><?= htmlspecialchars($user['nickname']) ?></h4>
            <p>ФИО: <?= htmlspecialchars($user['full_name']) ?></p>
            <p>Email: <?= htmlspecialchars($user['email']) ?></p>
            <p>Телефон: <?= htmlspecialchars($user['phone']) ?></p>
            <?php if(!empty($user['profile_pic'])): ?>
                <img src="<?= $user['profile_pic'] ?>" alt="Фото профиля" width="250">
            <?php endif; ?>
    </div>

    <!-- Форма редактирования данных -->
    <h4 class="mt-4">Редактировать профиль</h4>
        <div class="card-body">
            <form method="post" action="account.php" enctype="multipart/form-data">
                <table class="table table-borderless">
                    <tr>
                        <td><label>ФИО:</label></td>
                        <td>
                            <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Email:</label></td>
                        <td>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Телефон:</label></td>
                        <td>
                            <input type="text" name="phone" id="acc_phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>" required>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Новая фотография профиля (опционально):</label></td>
                        <td>
                            <input type="file" name="new_profile_pic" class="form-control-file">
                        </td>
                    </tr>
                </table>

                <div class="text-center mt-3">
                    <button type="submit" name="update" class="btn btn-primary">Обновить профиль</button>
                    <?php if ($_SESSION['user']['role'] != 'admin'): ?>
                        <a href="delete_account.php" class="btn btn-danger" onclick="return confirm('Вы уверены, что хотите удалить свой аккаунт? Это действие необратимо.');">
                            Удалить аккаунт
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>


<!-- Подключение jQuery и Inputmask -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"></script>
<script>
$(document).ready(function(){
    $('#acc_phone').inputmask("+7 999 999 99 99");
});
</script>
        <div class="btn-group mt-3">
        <a href="orders.php" class="btn btn-primary">Мои заказы</a>
        <a href="reviews.php" class="btn btn-secondary">Мои отзывы</a>
    </div>
</div></div>
