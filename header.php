<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Магазин "Фит-Плюс"</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="icon" href="icon.png?v=2" type="image/png">
    <link rel="stylesheet" type="text/css" href="CSS/header.css">
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <a class="navbar-brand" href="index.php">Фит-Плюс</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item"><a class="nav-link" href="index.php">Главная</a></li>
      <li class="nav-item"><a class="nav-link" href="about.php">О нас</a></li>
      <li class="nav-item"><a class="nav-link" href="contacts.php">Контакты</a></li>
      <li class="nav-item"><a class="nav-link" href="promotions.php">Акции</a></li>
      <li class="nav-item"><a class="nav-link" href="faq.php">FAQ</a></li>
    </ul>
    <ul class="navbar-nav">
    <?php if(isset($_SESSION['user'])): ?>
        <?php if($_SESSION['user']['role'] == 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="admin.php">Админ панель</a></li>
        <?php endif; ?>
        <!-- Подсчёт количества избранного -->
        <?php 
            $favCount = 0;
            if(isset($_SESSION['favorites']) && is_array($_SESSION['favorites'])) {
                $favCount = count($_SESSION['favorites']);
            }
        ?>
        <li class="nav-item">
            <a class="nav-link" href="favorites.php">
                Избранное <span class="badge badge-light"><?= $favCount ?></span>
            </a>
        </li>
        <li class="nav-item"><a class="nav-link" href="account.php">Личный кабинет</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Выход</a></li>
    <?php else: ?>
        <li class="nav-item">
            <a class="nav-link" href="#" data-toggle="modal" data-target="#loginModal">Вход</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" data-toggle="modal" data-target="#registerModal">Регистрация</a>
        </li>
    <?php endif; ?>
        <li class="nav-item">
            <a class="nav-link" href="cart.php">
                Корзина <span class="badge badge-light">
                <?= isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>
                </span>
            </a>
        </li>
    </ul>
  </div>
</nav>
<!-- Контейнер для уведомлений -->
<?php if (isset($_SESSION['message'])): ?>
    <div id="flash-message">
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['message']) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Закрыть">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<!-- Модальное окно входа -->
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="post" action="login_process.php">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="loginModalLabel">Вход</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <div class="form-group">
                <label>Никнейм:</label>
                <input type="text" name="nickname" class="form-control" placeholder="Введите никнейм" required>
            </div>
            <div class="form-group">
                <label>Пароль:</label>
                <input type="password" name="password" class="form-control" placeholder="Введите пароль" required>
            </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Войти</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
      </div>
    </div>
    </form>
  </div>
</div>

<!-- Модальное окно регистрации -->
<div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="registerModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="post" action="register_process.php" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="registerModalLabel">Регистрация</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
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
            <input type="text" name="phone" id="reg_phone" class="form-control" placeholder="+7 000 000 00 00" required>
          </div>
          <div class="form-group">
            <label>Дата рождения:</label>
            <input type="date" name="birth_date" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Фото профиля:</label>
            <input type="file" name="profile_pic" class="form-control-file">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Подключаем jQuery и Inputmask (если ещё не подключены) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function(){
    $('#reg_phone').inputmask("+7 999 999 99 99");
});
$(document).ready(function(){
    setTimeout(function(){
        $('#flash-message').fadeOut('slow');
    }, 1500); // Через 1.5 секунды
});
</script>