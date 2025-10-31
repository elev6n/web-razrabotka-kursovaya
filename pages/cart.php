<?php
require_once '../includes/config.php';

$cart_items = getCartItems($db);
$total_price = calculateTotalPrice($cart_items);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина - BuyBit</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/cart.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">BuyBit</div>
            <ul class="nav-links">
                <li><a href="../index.php">Главная</a></li>
                <li><a href="products.php">Товары</a></li>
                <li><a href="cart.php" class="active">Корзина</a></li>
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
        <div class="cart-container">
            <h1>Корзина покупок</h1>
            <?php if (empty($cart_items)): ?>
            <div class="empty-cart" style="<?php echo !empty($cart_items) ? 'display: none;' : ''; ?>">
                <div class="empty-cart-icon">🛒</div>
                <h2>Ваша корзина пуста</h2>
                <p>Добавьте товары из каталога, чтобы сделать заказ</p>
                <a href="products.php" class="btn btn-primary">Перейти к покупкам</a>
            </div>
            <?php else: ?>
                <div class="cart-content">
                    <div class="cart-items">
                        <div class="cart-header">
                            <span>Товар</span>
                            <span>Цена</span>
                            <span>Количество</span>
                            <span>Итого</span>
                            <span>Действия</span>
                        </div>
                        
                        <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item" data-product-id="<?php echo $item['id']; ?>">
                            <div class="product-info">
                                <div class="product-image">
                                    <img src="<?php echo $item['image'] ? '../images/' . $item['image'] : '../images/placeholder.png'; ?>" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                                         onerror="this.src='../images/placeholder.png'">
                                </div>
                                <div class="product-details">
                                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <p class="product-category"><?php echo htmlspecialchars($item['category_name']); ?></p>
                                    <?php if ($item['stock'] < $item['quantity']): ?>
                                        <p class="stock-warning">⚠️ На складе осталось: <?php echo $item['stock']; ?> шт.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="product-price">
                                <?php echo number_format($item['price'], 0, ',', ' '); ?> ₽
                            </div>
                            
                            <div class="quantity-controls">
                                <div class="quantity-form">
                                    <button type="button" class="quantity-btn minus" onclick="updateQuantity(<?php echo $item['id']; ?>, -1)">-</button>
                                    <input type="number" value="<?php echo $item['quantity']; ?>" 
                                        min="1" max="<?php echo $item['stock']; ?>" 
                                        class="quantity-input" 
                                        onchange="updateQuantityInput(<?php echo $item['id']; ?>, this.value)">
                                    <button type="button" class="quantity-btn plus" onclick="updateQuantity(<?php echo $item['id']; ?>, 1)">+</button>
                                </div>
                            </div>
                            
                            <div class="item-total">
                                <?php echo number_format($item['price'] * $item['quantity'], 0, ',', ' '); ?> ₽
                            </div>
                            
                            <div class="item-actions">
                                <button type="button" class="btn-remove" 
                                        onclick="removeFromCart(<?php echo $item['id']; ?>)" 
                                        title="Удалить из корзины">
                                    🗑️
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <div class="cart-actions">
                            <button type="button" class="btn btn-outline" onclick="clearCart()">
                                Очистить корзину
                            </button>
                            <a href="products.php" class="btn btn-outline">Продолжить покупки</a>
                        </div>
                    </div>
                    
                    <div class="cart-summary">
                        <div class="summary-card">
                            <h3>Итого</h3>
                            <div class="summary-row">
                                <span>Товары (<?php echo array_sum(array_column($cart_items, 'quantity')); ?> шт.)</span>
                                <span><?php echo number_format($total_price, 0, ',', ' '); ?> ₽</span>
                            </div>
                            <div class="summary-row">
                                <span>Доставка</span>
                                <span>Бесплатно</span>
                            </div>
                            <div class="summary-divider"></div>
                            <div class="summary-row total">
                                <span>Общая сумма</span>
                                <span><?php echo number_format($total_price, 0, ',', ' '); ?> ₽</span>
                            </div>
                            
                            <?php if (isLoggedIn()): ?>
                                <a href="checkout.php" class="btn btn-primary btn-large btn-full">Перейти к оформлению</a>
                            <?php else: ?>
                                <div class="auth-required">
                                    <p>Для оформления заказа необходимо войти в систему</p>
                                    <div class="auth-buttons">
                                        <a href="login.php" class="btn btn-primary">Войти</a>
                                        <a href="register.php" class="btn btn-outline">Регистрация</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
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

    <script src="../js/cart.js"></script>
</body>
</html>