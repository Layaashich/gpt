<!-- Функция завершения ессии -->
<?php
require 'config.php';
session_destroy();
session_start(); // Начинаем новую сессию для установки сообщения
$_SESSION['message'] = "Вы успешно вышли из системы.";
header("Location: index.php");
exit;
?>