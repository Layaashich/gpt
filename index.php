<?php
require 'config.php';

// Получаем категории и подкатегории
$stmt = $pdo->query("SELECT c.id as cat_id, c.name as cat_name, s.id as sub_id, s.name as sub_name 
                     FROM categories c 
                     LEFT JOIN subcategories s ON c.id = s.category_id
                     ORDER BY c.id, s.id");
$categories = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $cat_id = $row['cat_id'];
    if (!isset($categories[$cat_id])) {
        $categories[$cat_id] = ['name' => $row['cat_name'], 'subcategories' => []];
    }
    if ($row['sub_id']) {
        $categories[$cat_id]['subcategories'][] = ['id' => $row['sub_id'], 'name' => $row['sub_name']];
    }
}

// Обработка поиска и фильтров
$where = "1";
$params = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $where .= " AND name LIKE :search";
    $params[':search'] = '%' . $_GET['search'] . '%';
}

if (isset($_GET['cat']) && !empty($_GET['cat'])) {
    $where .= " AND category_id = :cat";
    $params[':cat'] = $_GET['cat'];
}

if (isset($_GET['sub']) && !empty($_GET['sub'])) {
    $where .= " AND subcategory_id = :sub";
    $params[':sub'] = $_GET['sub'];
}

if (isset($_GET['price_from']) && is_numeric($_GET['price_from']) && isset($_GET['price_to']) && is_numeric($_GET['price_to'])) {
    $where .= " AND price BETWEEN :price_from AND :price_to";
    $params[':price_from'] = $_GET['price_from'];
    $params[':price_to'] = $_GET['price_to'];
}

// Определяем сортировку
$orderBy = "ORDER BY created_at DESC";
if (isset($_GET['sort'])) {
    if ($_GET['sort'] == 'price_asc') {
        $orderBy = "ORDER BY price ASC";
    } elseif ($_GET['sort'] == 'price_desc') {
        $orderBy = "ORDER BY price DESC";
    } elseif ($_GET['sort'] == 'rating') {
        $orderBy = "ORDER BY avg_rating DESC";
    }
}

// Список страниц
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$perPage = 15;
$offset = ($page - 1) * $perPage;

// Определяем общее количество активных товаров для пагинации
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE $where AND status = 'active'");
$countStmt->execute($params);
$totalProducts = $countStmt->fetchColumn();
$totalPages = ceil($totalProducts / $perPage);

// Запрос с подзапросами для получения первого фото и рейтинга товара, только активные товары
$sql = "SELECT p.*, 
           (SELECT photo FROM product_photos WHERE product_id = p.id ORDER BY id ASC LIMIT 1) AS photo,
           (SELECT AVG(rating) FROM reviews WHERE product_id = p.id) AS avg_rating,
           (SELECT COUNT(*) FROM reviews WHERE product_id = p.id) AS count_reviews
        FROM products p 
        WHERE $where AND status = 'active'
        $orderBy
        LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'header.php'; ?>

<main>
<link rel="stylesheet" type="text/css" href="CSS/index.css">
<div class="container mt-4">
    <!-- Горизонтальные фильтры -->
    <div class="row mb-3">
        <div class="col-12">
            <form method="get" action="index.php" class="form-inline">
                <input type="text" name="search" class="form-control mb-2 mr-sm-2" placeholder="Поиск товаров" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                
                <!-- Добавлены id для JS -->
                <select name="cat" id="categorySelect" class="form-control mb-2 mr-sm-2">
                    <option value="">Все категории</option>
                    <?php foreach ($categories as $cat_id => $cat): ?>
                        <option value="<?= $cat_id ?>" <?= (isset($_GET['cat']) && $_GET['cat'] == $cat_id) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <select name="sub" id="subcategorySelect" class="form-control mb-2 mr-sm-2">
                    <option value="">Все подкатегории</option>
                    <?php
                    if (isset($_GET['cat']) && isset($categories[$_GET['cat']])):
                        foreach ($categories[$_GET['cat']]['subcategories'] as $sub):
                    ?>
                            <option value="<?= $sub['id'] ?>" <?= (isset($_GET['sub']) && $_GET['sub'] == $sub['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($sub['name']) ?>
                            </option>
                    <?php endforeach; endif; ?>
                </select>
                
                <input type="number" name="price_from" class="form-control mb-2 mr-sm-2" placeholder="Цена от" value="<?= isset($_GET['price_from']) ? $_GET['price_from'] : ''; ?>">
                <input type="number" name="price_to" class="form-control mb-2 mr-sm-2" placeholder="Цена до" value="<?= isset($_GET['price_to']) ? $_GET['price_to'] : ''; ?>">
                
                <select name="sort" class="form-control mb-2 mr-sm-2">
                    <option value="">Сортировать по</option>
                    <option value="price_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : ''; ?>>Цена: от низкой к высокой</option>
                    <option value="price_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : ''; ?>>Цена: от высокой к низкой</option>
                    <option value="rating" <?= (isset($_GET['sort']) && $_GET['sort'] == 'rating') ? 'selected' : ''; ?>>Рейтинг</option>
                </select>
                
                <button type="submit" class="btn btn-primary mb-2">Применить</button>
            </form>
        </div>
    </div>
        
    <!-- Ряд карточек товаров -->
    <div class="row">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <?php 
                    $oldPrice = $product['price'];
                    $discount = isset($product['discount']) ? $product['discount'] : 0;
                    if ($discount > 0) {
                        $finalPrice = $oldPrice * (1 - $discount / 100);
                    } else {
                        $finalPrice = $oldPrice;
                    }
                ?>
                <div class="col-md-4 product-card mb-4">
                    <div class="card" style="position: relative;">
                        <a href="product.php?id=<?= $product['id'] ?>">
                            <img src="<?= htmlspecialchars($product['photo'] ? $product['photo'] : 'uploads/products/default.jpg') ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                            <?php if ($discount > 0): ?>
                            <?php endif; ?>
                        </a>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="card-text" style="display: flex; align-items: center;">
                                <?php if ($discount > 0): ?>
                                    <span style="text-decoration: line-through; color: #888; margin-right: 10px;">
                                        <?= number_format($oldPrice, 2) ?> руб.
                                    </span>
                                    <br>
                                    <span style="color: red; font-weight: bold;">
                                        <?= number_format($finalPrice, 2) ?> руб.
                                    </span>
                                <?php else: ?>
                                    <?= number_format($oldPrice, 2) ?> руб.
                                <?php endif; ?>
                            </p>
                            <?php if ($product['avg_rating']): ?>
                                <p class="mb-1">
                                    Рейтинг: <?= number_format($product['avg_rating'], 1) ?> / 5 (<?= $product['count_reviews'] ?> отзывов)
                                </p>
                            <?php else: ?>
                                <p class="mb-1">Нет оценок</p>
                            <?php endif; ?>
                            <div class="d-flex">
                                <button class="btn btn-success btn-sm" onclick="location.href='cart.php?action=add&id=<?= $product['id'] ?>'">Добавить в корзину</button>
                                <button class="btn btn-outline-danger btn-sm ml-2" onclick="location.href='favorites.php?action=add&id=<?= $product['id'] ?>'">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12"><p>Товары не найдены.</p></div>
        <?php endif; ?>
    </div>
        
    <!-- Пагинация -->
    <?php if ($totalPages > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>
</main>
</body>
<script>
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
<?php include 'footer.php'; ?>
