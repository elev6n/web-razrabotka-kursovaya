<?php
require_once '../includes/config.php';

if (!isset($_GET['id'])) {
    redirectTo404();
}

$product_id = intval($_GET['id']);
$product = getProductById($db, $product_id);

if (!$product) {
    redirectTo404();
}

$similar_products_query = "SELECT p.*, c.name as category_name 
                           FROM products p 
                           LEFT JOIN categories c ON p.category_id = c.id 
                           WHERE p.category_id = :category_id AND p.id != :product_id 
                           ORDER BY RAND() 
                           LIMIT 4";
$similar_stmt = $db->prepare($similar_products_query);
$similar_stmt->bindParam(':category_id', $product['category_id']);
$similar_stmt->bindParam(':product_id', $product_id);
$similar_stmt->execute();
$similar_products = $similar_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - BuyBit</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/products.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">BuyBit</div>
            <ul class="nav-links">
                <li><a href="../index.php">Главная</a></li>
                <li><a href="products.php">Товары</a></li>
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
        <div class="breadcrumbs">
            <a href="../index.php">Главная</a> / 
            <a href="products.php">Товары</a> / 
            <a href="products.php?category=<?php echo $product['category_id']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a> / 
            <span><?php echo htmlspecialchars($product['name']); ?></span>
        </div>

        <div class="product-detail">
            <div class="product-gallery">
                <div class="main-image">
                    <img src="<?php echo $product['image'] ? '../images/' . $product['image'] : '../images/placeholder.png'; ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         onerror="this.src='../images/placeholder.png'">
                </div>
            </div>
            
            <div class="product-info">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <div class="product-meta">
                    <span class="category">Категория: <?php echo htmlspecialchars($product['category_name']); ?></span>
                    <span class="product-stock <?php echo $product['stock'] > 0 ? 'in-stock' : 'out-of-stock'; ?>">
                        <?php echo $product['stock'] > 0 ? '✓ В наличии' : '✗ Нет в наличии'; ?>
                    </span>
                </div>
                
                <div class="product-price">
                    <?php echo number_format($product['price'], 0, ',', ' '); ?> ₽
                </div>
                
                <div class="product-description">
                    <h3>Описание</h3>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>
                
                <div class="product-actions">
                    <div class="quantity-selector">
                        <label for="quantity">Количество:</label>
                        <select id="quantity">
                            <?php for ($i = 1; $i <= min(10, $product['stock']); $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <button class="btn btn-primary btn-large" 
                            onclick="addToCartWithQuantity(<?php echo $product['id']; ?>)"
                            <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                        <?php echo $product['stock'] > 0 ? 'Добавить в корзину' : 'Товар закончился'; ?>
                    </button>
                    
                    <button class="btn btn-outline btn-large" onclick="addToWishlist(<?php echo $product['id']; ?>)">
                        В избранное
                    </button>
                </div>
            </div>
        </div>

        <?php if (!empty($similar_products)): ?>
        <section class="similar-products">
            <h2>Похожие товары</h2>
            <div class="products-grid">
                <?php foreach ($similar_products as $similar_product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo $similar_product['image'] ? '../images/' . $similar_product['image'] : '../images/placeholder.png'; ?>" 
                             alt="<?php echo htmlspecialchars($similar_product['name']); ?>"
                             onerror="this.src='../images/placeholder.png'">
                    </div>
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($similar_product['name']); ?></h3>
                        <p class="product-category"><?php echo htmlspecialchars($similar_product['category_name']); ?></p>
                        <div class="product-meta">
                            <span class="product-price"><?php echo number_format($similar_product['price'], 0, ',', ' '); ?> ₽</span>
                        </div>
                        <div class="product-actions">
                            <a href="product.php?id=<?php echo $similar_product['id']; ?>" class="btn btn-outline">Подробнее</a>
                            <button class="btn btn-primary" 
                                    onclick="addToCart(<?php echo $similar_product['id']; ?>)"
                                    <?php echo $similar_product['stock'] <= 0 ? 'disabled' : ''; ?>>
                                В корзину
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </main>
    
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>BuyBit</h3>
                <p>Лучший выбор компьютерных товаров по доступным ценам.</p>
            </div>
            <div class="footer-section">
                <h3>Категории</h3>
                <ul>
                    <?php foreach (getAllCategories($db) as $category): ?>
                    <li><a href="pages/products.php?category=<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Помощь</h3>
                <ul>
                    <li><a href="#">Доставка и оплата</a></li>
                    <li><a href="#">Гарантия</a></li>
                    <li><a href="#">Контакты</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 BuyBit. Все права защищены. Курсовой проект по веб-разработке.</p>
        </div>
    </footer>

    <script src="../js/script.js"></script>
    <script>
    function addToCartWithQuantity(productId) {
        const quantity = document.getElementById('quantity').value;
        addToCart(productId, parseInt(quantity));
    }
    </script>
</body>
</html>