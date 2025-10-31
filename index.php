<?php
require_once 'includes/config.php';

$products = getAllProducts($db);
$categories = getAllCategories($db);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuyBit - Магазин компьютерных товаров</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">BuyBit</div>
            <ul class="nav-links">
                <li><a href="index.php" class="active">Главная</a></li>
                <li><a href="pages/products.php">Товары</a></li>
                <li><a href="pages/cart.php">Корзина</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="pages/profile.php">Профиль</a></li>
                    <li><a href="includes/logout.php">Выйти</a></li>
                <?php else: ?>
                    <li><a href="pages/login.php">Войти</a></li>
                    <li><a href="pages/register.php">Регистрация</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <section class="hero">
            <h1>Добро пожаловать в BuyBit</h1>
            <p>Лучшие компьютерные товары по доступным ценам</p>
        </section>

        <section class="categories">
            <h2>Категории товаров</h2>
            <div class="category-grid">
                <?php foreach ($categories as $category): ?>
                <div class="category-card">
                    <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                    <p><?php echo htmlspecialchars($category['description']); ?></p>
                    <a href="pages/products.php?category=<?php echo $category['id']; ?>" class="btn">
                        Смотреть товары
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="featured-products">
            <h2>Популярные товары</h2>
            <div class="products-grid">
                <?php foreach (array_slice($products, 0, 6) as $product): ?>
                <div class="products-card">
                    <div class="product-image">
                        <img src="<?php echo $product['image'] ? 'images/' . $product['image'] : 'images/placeholder.png'; ?>"
                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </div>
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                        <p class="product-price"><?php echo number_format($product['price'], 0, ',', ' '); ?></p>
                        <div class="product-actions">
                            <a href="pages/product.php?id=<?php echo $product['id']; ?>" class="btn">
                                Подробнее
                            </a>
                            <button class="btn btn-primary" onclick="addToCart(<?php echo $product['id']; ?>)">
                                В корзину
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 BuyBit. Все права защищены.</p>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>