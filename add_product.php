<?php
// add_product.php - Страница добавления товара с красивым оформлением

require 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {

    // Обработка добавления товара
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $color = trim($_POST['color']);
    $material = trim($_POST['material']);
    $country = trim($_POST['country']);
    $size = trim($_POST['size']);
    $price = $_POST['price'];
    $stock = isset($_POST['stock']) ? intval($_POST['stock']) : 0;
    $category_id = $_POST['category'];
    $subcategory_id = $_POST['subcategory'];
    
    if (empty($name) || empty($price) || empty($category_id) || empty($subcategory_id)) {
        $error = "Пожалуйста, заполните обязательные поля.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, color, material, country, size, price, stock, category_id, subcategory_id, status) 
                               VALUES (:name, :description, :color, :material, :country, :size, :price, :stock, :category_id, :subcategory_id, 'active')");
        $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':color' => $color,
            ':material' => $material,
            ':country' => $country,
            ':size' => $size,
            ':price' => $price,
            ':stock' => $stock,
            ':category_id' => $category_id,
            ':subcategory_id' => $subcategory_id
        ]);
        $product_id = $pdo->lastInsertId();
        
        // Обработка загрузки до 5 фотографий
        for ($i = 1; $i <= 5; $i++) {
            if (isset($_FILES["photo$i"]) && $_FILES["photo$i"]['error'] == 0) {
                $ext = pathinfo($_FILES["photo$i"]['name'], PATHINFO_EXTENSION);
                $filename = 'uploads/products/' . $product_id . "_$i." . $ext;
                move_uploaded_file($_FILES["photo$i"]['tmp_name'], $filename);
                $stmt = $pdo->prepare("INSERT INTO product_photos (product_id, photo) VALUES (:product_id, :photo)");
                $stmt->execute([':product_id' => $product_id, ':photo' => $filename]);
            }
        }
        header("Location: admin_products.php");
        exit;
    }
}

// Получаем категории для формы
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<div class="container mt-4">
    <div class="legla">
    <h2 class="text-center mb-4">Добавить товар</h2>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Оформление в виде карточки с таблицей для ввода данных -->
        <div class="card-body">
            <form method="post" action="add_product.php" enctype="multipart/form-data">
                <table class="table table-borderless">
                    <tr>
                        <td><label>Название:</label></td>
                        <td><input type="text" name="name" class="form-control" placeholder="Введите название товара" required></td>
                    </tr>
                    <tr>
                        <td><label>Описание:</label></td>
                        <td><textarea name="description" class="form-control" placeholder="Введите описание товара"></textarea></td>
                    </tr>
                    <tr>
                        <td><label>Цвет:</label></td>
                        <td><input type="text" name="color" class="form-control" placeholder="Введите цвет товара"></td>
                    </tr>
                    <tr>
                        <td><label>Материал:</label></td>
                        <td><input type="text" name="material" class="form-control" placeholder="Введите материал"></td>
                    </tr>
                    <tr>
                        <td><label>Страна изготовитель:</label></td>
                        <td><input type="text" name="country" class="form-control" placeholder="Введите страну изготовитель"></td>
                    </tr>
                    <tr>
                        <td><label>Размер (опционально):</label></td>
                        <td><input type="text" name="size" class="form-control" placeholder="Введите размер"></td>
                    </tr>
                    <tr>
                        <td><label>Цена (руб.):</label></td>
                        <td><input type="number" step="0.01" name="price" class="form-control" placeholder="Введите цену" required></td>
                    </tr>
                    <tr>
                        <td><label>Количество на складе:</label></td>
                        <td><input type="number" name="stock" class="form-control" placeholder="Введите количество" required min="0"></td>
                    </tr>
                    <tr>
                        <td><label>Категория:</label></td>
                        <td>
                            <select name="category" id="categorySelect" class="form-control" required>
                                <option value="">Выберите категорию</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Подкатегория:</label></td>
                        <td>
                            <select name="subcategory" id="subcategorySelect" class="form-control" required>
                                <option value="">Выберите подкатегорию</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Фотографии (до 5 штук):</label></td>
                        <td>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <input type="file" name="photo<?= $i ?>" class="form-control-file mb-2">
                            <?php endfor; ?>
                        </td>
                    </tr>
                </table>
                <div class="text-center">
                    <button type="submit" name="add_product" class="btn btn-success">Добавить товар</button>
                </div>
            </form>
        </div>
</div>
</div>

<script>
// Динамическая загрузка подкатегорий при выборе категории
document.getElementById('categorySelect').addEventListener('change', function(){
    var categoryId = this.value;
    var subSelect = document.getElementById('subcategorySelect');
    subSelect.innerHTML = '<option value="">Загрузка...</option>';
    <?php
    $stmt = $pdo->query("SELECT * FROM subcategories");
    $allSubs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $subsByCat = [];
    foreach ($allSubs as $sub) {
        $subsByCat[$sub['category_id']][] = $sub;
    }
    ?>
    var subs = <?php echo json_encode($subsByCat); ?>;
    subSelect.innerHTML = '<option value="">Выберите подкатегорию</option>';
    if (subs[categoryId]) {
        subs[categoryId].forEach(function(sub){
            subSelect.innerHTML += '<option value="'+sub.id+'">'+sub.name+'</option>';
        });
    }
});
</script>