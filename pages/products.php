<?php
require_once '../includes/config.php';

$category_id = isset($_GET['category']) ? intval($_GET['category']) : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 9;
$offset = ($page - 1) * $limit;

if (!empty($search)) {
    $products = searchProducts($db, $search);
    $total_products = count($products);
    $products = array_slice($products, $offset, $limit);
} elseif ($category_id) {
    $products = getProductsByCategory($db, $category_id);
    $total_products = count($products);
    $products = array_slice($products, $offset, $limit);
} else {
    $count_query = "SELECT COUNT(*) as total FROM products";
    $count_stmt = $db->prepare($count_query);
    $count_stmt->execute();
    $total_products = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $query = "SELECT p.*, c.name as category_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              ORDER BY p.created_at DESC 
              LIMIT :limit OFFSET :offset";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$categories = getAllCategories($db);
$total_pages = ceil($total_products / $limit);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог товаров - BuyBit</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/products.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">BuyBit</div>
            <ul class="nav-links">
                <li><a href="../index.php">Главная</a></li>
                <li><a href="products.php" class="active">Товары</a></li>
                <li><a href="cart.php">Корзина</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="profile.php">Профиль</a></li>
                    <li><a href="../includes/logout.php">Выйти</a></li>
                <?php else: ?>
                    <li><a href="login.php">Войти</a></li>
                    <li><a href="register.php">Регистрация</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    
    <main>
        <div class="products-header">
            <h1>Каталог товаров</h1>
            
            <div class="filters">
                <form method="GET" class="search-form">
                    <div class="search-box">
                        <input type="text" name="search" placeholder="Поиск товаров..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-primary">Найти</button>
                    </div>
                </form>
                
                <div class="category-filters">
                    <a href="products.php" class="category-filter <?php echo !$category_id ? 'active' : ''; ?>">Все товары</a>
                    <?php foreach ($categories as $category): ?>
                        <a href="products.php?category=<?php echo $category['id']; ?>" 
                           class="category-filter <?php echo $category_id == $category['id'] ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php if (!empty($search)): ?>
            <div class="search-results-info">
                <h2>Результаты поиска: "<?php echo htmlspecialchars($search); ?>"</h2>
                <p>Найдено товаров: <?php echo $total_products; ?></p>
                <a href="products.php" class="btn btn-outline">Показать все товары</a>
            </div>
        <?php endif; ?>

        <div class="products-container">
            <?php if (empty($products)): ?>
                <div class="no-products">
                    <h3>Товары не найдены</h3>
                    <p>Попробуйте изменить параметры поиска или выбрать другую категорию</p>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?php echo $product['image'] ? '../images/' . $product['image'] : '../images/placeholder.png'; ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 onerror="this.src='../images/placeholder.png'">
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                            <p class="product-description"><?php echo htmlspecialchars(substr($product['description'], 0, 100)); ?>...</p>
                            <div class="product-meta">
                                <span class="product-stock <?php echo $product['stock'] > 0 ? 'in-stock' : 'out-of-stock'; ?>">
                                    <?php echo $product['stock'] > 0 ? 'В наличии' : 'Нет в наличии'; ?>
                                </span>
                                <span class="product-price"><?php echo number_format($product['price'], 0, ',', ' '); ?> ₽</span>
                            </div>
                            <div class="product-actions">
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline">Подробнее</a>
                                <button class="btn btn-primary" 
                                        onclick="addToCart(<?php echo $product['id']; ?>)"
                                        <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                                    В корзину
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?<?php echo buildPaginationUrl($page - 1, $category_id, $search); ?>" class="page-link">← Назад</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == 1 || $i == $total_pages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                            <a href="?<?php echo buildPaginationUrl($i, $category_id, $search); ?>" 
                               class="page-link <?php echo $i == $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                            <span class="page-dots">...</span>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?<?php echo buildPaginationUrl($page + 1, $category_id, $search); ?>" class="page-link">Вперед →</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>
    
    <footer>
        <p>&copy; 2025 BuyBit. Все права защищены.</p>
    </footer>

    <script src="../js/script.js"></script>
</body>
</html>

<?php
function buildPaginationUrl($page, $category_id, $search) {
    $params = [];
    if ($category_id) $params[] = "category=$category_id";
    if (!empty($search)) $params[] = "search=" . urlencode($search);
    $params[] = "page=$page";
    return implode('&', $params);
}
?>