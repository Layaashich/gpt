<!-- Страница регистрации пользователя (сейчас не используемая) -->
<?php
require 'config.php';

$error = '';
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $nickname = trim($_POST['nickname']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $birth_date = $_POST['birth_date'];
    // Валидация полей
    if(empty($nickname) || empty($password) || empty($confirm) || empty($full_name) || empty($email) || empty($phone) || empty($birth_date)){
        $error = "Все поля обязательны для заполнения.";
    } elseif($password !== $confirm){
        $error = "Пароли не совпадают.";
    } elseif(strpos($email, '@') === false){
        $error = "Неверный Email.";
    } elseif(!preg_match('/^\+7 \d{3} \d{3} \d{2} \d{2}$/', $phone)){
        $error = "Неверный формат телефона. Пример: +7 000 000 00 00";
    } elseif(new DateTime($birth_date) < new DateTime('yesterday')){
        $error = "Дата рождения не может быть раньше вчерашней.";
    }
    
    if(empty($error)){
        // Загрузка фото профиля
        $profile_pic = '';
        if(isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0){
            $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
            $profile_pic = 'uploads/profiles/' . $nickname . '.' . $ext;
            move_uploaded_file($_FILES['profile_pic']['tmp_name'], $profile_pic);
        }
        // Добавляем пользователя в БД
        $stmt = $pdo->prepare("INSERT INTO users (nickname, password, full_name, email, phone, birth_date, profile_pic) VALUES (:nickname, :password, :full_name, :email, :phone, :birth_date, :profile_pic)");
        $stmt->execute([
            ':nickname' => $nickname,
            ':password' => password_hash($password, PASSWORD_DEFAULT),
            ':full_name' => $full_name,
            ':email' => $email,
            ':phone' => $phone,
            ':birth_date' => $birth_date,
            ':profile_pic' => $profile_pic
        ]);
        header("Location: login.php");
        exit;
    }
}
?>
<?php include 'header.php'; ?>
<div class="container mt-4">
    <h2>Регистрация</h2>
    <?php if(!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" action="register.php" enctype="multipart/form-data">
        <div class="form-group">
            <label>Никнейм:</label>
            <input type="text" name="nickname" class="form-control" placeholder="Введите никнейм" required>
        </div>
        <div class="form-group">
            <label>Пароль:</label>
            <input type="password" name="password" class="form-control" placeholder="Введите пароль" required>
        </div>
        <div class="form-group">
            <label>Повтор пароля:</label>
            <input type="password" name="confirm" class="form-control" placeholder="Повторите пароль" required>
        </div>
        <div class="form-group">
            <label>ФИО:</label>
            <input type="text" name="full_name" class="form-control" placeholder="Введите ФИО" required>
        </div>
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" class="form-control" placeholder="Введите Email" required>
        </div>
        <div class="form-group">
            <label>Телефон:</label>
            <input type="text" name="phone" class="form-control" placeholder="+7 000 000 00 00" required>
        </div>
        <div class="form-group">
            <label>Дата рождения:</label>
            <input type="date" name="birth_date" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Фото профиля:</label>
            <input type="file" name="profile_pic" class="form-control-file">
        </div>
        <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
    </form>
</div>