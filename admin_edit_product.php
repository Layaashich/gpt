<!-- Страница редактирования товара в админ панели -->
<?php
require 'config.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: index.php");
    exit;
}

if(!isset($_GET['id'])){
    header("Location: admin_products.php");
    exit;
}

$product_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute([':id' => $product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$product){
    die("Товар не найден.");
}

// Получаем категории
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем подкатегории для выбранной категории
$stmt = $pdo->prepare("SELECT * FROM subcategories WHERE category_id = :cat_id");
$stmt->execute([':cat_id' => $product['category_id']]);
$subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$error = '';
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])){
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $color = trim($_POST['color']);
    $material = trim($_POST['material']);
    $country = trim($_POST['country']);
    $size = trim($_POST['size']);
    $price = $_POST['price'];
    $category_id = $_POST['category'];
    $subcategory_id = $_POST['subcategory'];
    
    if(empty($name) || empty($price) || empty($category_id) || empty($subcategory_id)){
        $error = "Пожалуйста, заполните обязательные поля.";
    } else {
        $stmt = $pdo->prepare("UPDATE products SET name = :name, description = :description, color = :color, material = :material, country = :country, size = :size, price = :price, category_id = :category_id, subcategory_id = :subcategory_id WHERE id = :id");
        $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':color' => $color,
            ':material' => $material,
            ':country' => $country,
            ':size' => $size,
            ':price' => $price,
            ':category_id' => $category_id,
            ':subcategory_id' => $subcategory_id,
            ':id' => $product_id
        ]);
        // Обработка загрузки новых фотографий
        for($i = 1; $i <= 5; $i++){
            if(isset($_FILES["photo$i"]) && $_FILES["photo$i"]['error'] == 0){
                $ext = pathinfo($_FILES["photo$i"]['name'], PATHINFO_EXTENSION);
                $filename = 'uploads/products/' . $product_id . "_$i." . $ext;
                move_uploaded_file($_FILES["photo$i"]['tmp_name'], $filename);

                // Обновление или вставка фото
                $stmt = $pdo->prepare("SELECT id FROM product_photos WHERE product_id = :product_id AND photo LIKE :pattern");
                $stmt->execute([
                    ':product_id' => $product_id,
                    ':pattern' => '%_'.$i.'.%'
                ]);
                $photoEntry = $stmt->fetch(PDO::FETCH_ASSOC);
                if($photoEntry){
                    $stmt = $pdo->prepare("UPDATE product_photos SET photo = :photo WHERE id = :id");
                    $stmt->execute([':photo' => $filename, ':id' => $photoEntry['id']]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO product_photos (product_id, photo) VALUES (:product_id, :photo)");
                    $stmt->execute([':product_id' => $product_id, ':photo' => $filename]);
                }
            }
        }
        header("Location: admin_products.php");
        exit;
    }
}
?>
<?php include 'header.php'; ?>
<div class="container mt-4">
    <div class="legla">
    <h2>Редактировать товар</h2>
    <?php if(!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" action="admin_edit_product.php?id=<?= $product_id ?>" enctype="multipart/form-data">
        <div class="form-group">
            <label>Название:</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
        </div>
        <div class="form-group">
            <label>Описание:</label>
            <textarea name="description" class="form-control"><?= htmlspecialchars($product['description']) ?></textarea>
        </div>
        <div class="form-group">
            <label>Цвет:</label>
            <input type="text" name="color" class="form-control" value="<?= htmlspecialchars($product['color']) ?>">
        </div>
        <div class="form-group">
            <label>Материал:</label>
            <input type="text" name="material" class="form-control" value="<?= htmlspecialchars($product['material']) ?>">
        </div>
        <div class="form-group">
            <label>Страна изготовитель:</label>
            <input type="text" name="country" class="form-control" value="<?= htmlspecialchars($product['country']) ?>">
        </div>
        <div class="form-group">
            <label>Размер (опционально):</label>
            <input type="text" name="size" class="form-control" value="<?= htmlspecialchars($product['size']) ?>">
        </div>
        <div class="form-group">
            <label>Цена (руб.):</label>
            <input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price'] ?>" required>
        </div>
        <div class="form-group">
            <label>Категория:</label>
            <select name="category" id="editCategorySelect" class="form-control" required>
                <option value="">Выберите категорию</option>
                <?php foreach($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?php if($cat['id'] == $product['category_id']) echo 'selected'; ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Подкатегория:</label>
            <select name="subcategory" id="editSubcategorySelect" class="form-control" required>
                <option value="">Выберите подкатегорию</option>
                <?php foreach($subcategories as $sub): ?>
                    <option value="<?= $sub['id'] ?>" <?php if($sub['id'] == $product['subcategory_id']) echo 'selected'; ?>>
                        <?= htmlspecialchars($sub['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Заменить фотографии (до 5 штук):</label>
            <?php for($i=1; $i<=5; $i++): ?>
                <input type="file" name="photo<?= $i ?>" class="form-control-file mb-2">
            <?php endfor; ?>
        </div>
        <button type="submit" name="update_product" class="btn btn-success">Сохранить изменения</button>
        <a href="admin_products.php" class="btn btn-secondary">Отмена</a>
    </form>
</div>
</div>

<script>
// Динамическая загрузка подкатегорий при выборе категории в форме редактирования
document.getElementById('editCategorySelect').addEventListener('change', function(){
    var categoryId = this.value;
    var subSelect = document.getElementById('editSubcategorySelect');
    subSelect.innerHTML = '<option value="">Загрузка...</option>';
    <?php
    $stmt = $pdo->query("SELECT * FROM subcategories");
    $allSubs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $subsByCat = [];
    foreach($allSubs as $sub){
        $subsByCat[$sub['category_id']][] = $sub;
    }
    ?>
    var subs = <?php echo json_encode($subsByCat); ?>;
    subSelect.innerHTML = '<option value="">Выберите подкатегорию</option>';
    if(subs[categoryId]){
        subs[categoryId].forEach(function(sub){
            subSelect.innerHTML += '<option value="'+sub.id+'">'+sub.name+'</option>';
        });
    }
});
</script>