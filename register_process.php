<!-- Обработка регистрации пользователя -->
<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nickname   = trim($_POST['nickname']);
    $password   = $_POST['password'];
    $confirm    = $_POST['confirm'];
    $full_name  = trim($_POST['full_name']);
    $email      = trim($_POST['email']);
    $phone      = trim($_POST['phone']);
    $birth_date = $_POST['birth_date'];
    $error = '';

    if (empty($nickname) || empty($password) || empty($confirm) || empty($full_name) || empty($email) || empty($phone) || empty($birth_date)) {
        $error = "Все поля обязательны для заполнения.";
    } elseif ($password !== $confirm) {
        $error = "Пароли не совпадают.";
    } elseif (strpos($email, '@') === false) {
        $error = "Неверный Email.";
    } elseif (!preg_match('/^\+7 \d{3} \d{3} \d{2} \d{2}$/', $phone)) {
        $error = "Неверный формат телефона. Пример: +7 000 000 00 00";
    } elseif (new DateTime($birth_date) > new DateTime('today')) {
        $error = "Дата рождения не может быть в будущем.";
    }
    
    if (!empty($error)) {
        $_SESSION['message'] = $error;
        header("Location: index.php");
        exit;
    } else {
        $profile_pic = '';
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
            $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
            $profile_pic = 'uploads/profiles/' . $nickname . '.' . $ext;
            move_uploaded_file($_FILES['profile_pic']['tmp_name'], $profile_pic);
        }
        $stmt = $pdo->prepare("INSERT INTO users (nickname, password, full_name, email, phone, birth_date, profile_pic) VALUES (:nickname, :password, :full_name, :email, :phone, :birth_date, :profile_pic)");
        $stmt->execute([
            ':nickname'    => $nickname,
            ':password'    => password_hash($password, PASSWORD_DEFAULT),
            ':full_name'   => $full_name,
            ':email'       => $email,
            ':phone'       => $phone,
            ':birth_date'  => $birth_date,
            ':profile_pic' => $profile_pic
        ]);
        // Автоматическая авторизация после регистрации
        $user_id = $pdo->lastInsertId();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $user_id]);
        $_SESSION['user'] = $stmt->fetch(PDO::FETCH_ASSOC);
        // Очищаем корзину и избранное
        $_SESSION['cart'] = [];
        $_SESSION['favorites'] = [];
        $_SESSION['message'] = "Регистрация прошла успешно. Вы авторизованы.";
        header("Location: index.php");
        exit;
    }
}
?>