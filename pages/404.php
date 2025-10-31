<?php
require_once '../includes/config.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Страница не найдена - BuyBit</title>
    <link rel="stylesheet" href="../css/style.css">
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
        <div class="error-page">
            <div class="error-content">
                <h1>404</h1>
                <h2>Страница не найдена</h2>
                <p>Извините, но страница, которую вы ищете, не существует.</p>
                <div class="error-actions">
                    <a href="../index.php" class="btn btn-primary">На главную</a>
                    <a href="products.php" class="btn btn-outline">К товарам</a>
                </div>
            </div>
        </div>
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
</body>
</html>