<?php
$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];

$clean_uri = strtok($request_uri, '?');

if ($clean_uri != '/' && !file_exists(ltrim($clean_uri, '/'))) {
    $possible_pages = [
        '/index.php',
        '/pages/products.php',
        '/pages/product.php',
        '/pages/cart.php',
        '/pages/checkout.php',
        '/pages/order_success.php',
        '/pages/order_details.php',
        '/pages/login.php',
        '/pages/register.php',
        '/pages/profile.php',
        '/pages/404.php'
    ];
    
    $is_valid_page = false;
    foreach ($possible_pages as $page) {
        if (strpos($clean_uri, $page) !== false) {
            $is_valid_page = true;
            break;
        }
    }
    
    if (!$is_valid_page && $clean_uri != '/') {
        header("HTTP/1.0 404 Not Found");
        include 'pages/404.php';
        exit;
    }
}

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

        <section class="features">
            <div class="container">
                <h2>Почему выбирают BuyBit?</h2>
                <div class="features-grid">
                    <div class="feature-card" id="delivery">
                        <div class="feature-icon">🚚</div>
                        <h3>Бесплатная доставка</h3>
                        <p>Бесплатная доставка при заказе от 10 000 ₽</p>
                    </div>
                    <div class="feature-card" id="warranty">
                        <div class="feature-icon">🔧</div>
                        <h3>Гарантия качества</h3>
                        <p>Все товары проходят тщательную проверку</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">💳</div>
                        <h3>Удобная оплата</h3>
                        <p>Оплата картой, наличными или онлайн</p>
                    </div>
                    <div class="feature-card" id="support">
                        <div class="feature-icon">📞</div>
                        <h3>Поддержка 24/7</h3>
                        <p>Всегда готовы помочь с выбором</p>
                    </div>
                </div>
            </div>
        </section>
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
                    <li><a href="#delivery">Доставка и оплата</a></li>
                    <li><a href="#warranty">Гарантия</a></li>
                    <li><a href="#support">Контакты</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 BuyBit. Все права защищены. Курсовой проект по веб-разработке.</p>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>