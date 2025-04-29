<!-- Страница админа -->
<?php
require 'config.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: index.php");
    exit;
}
?>
<?php
require 'config.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: index.php");
    exit;
}
?>
<?php include 'header.php'; ?>
<div class="container mt-4">
    
    <!-- Новая панель управления -->
    <div class="row mb-4">
        <!-- Управление товарами -->
        <div class="col-md-4">
            <a href="admin_products.php" class="text-decoration-none">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-box-open fa-3x mr-3"></i>
                        <div>
                            <h5 class="card-title">Управление товарами</h5>
                            <p class="card-text">Просмотр, редактирование, добавление</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <!-- Управление пользователями -->
        <div class="col-md-4">
            <a href="admin_users.php" class="text-decoration-none">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-users fa-3x mr-3"></i>
                        <div>
                            <h5 class="card-title">Управление пользователями</h5>
                            <p class="card-text">Редактирование профилей</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <!-- Настройки магазина -->
        <div class="col-md-4">
            <a href="admin_dashboard.php" class="text-decoration-none">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-cogs fa-3x mr-3"></i>
                        <div>
                            <h5 class="card-title">Дашборд</h5>
                            <p class="card-text">Статистика и прочие данные интернет магазина</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>