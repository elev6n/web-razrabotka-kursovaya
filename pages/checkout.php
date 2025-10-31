<?php
require_once '../includes/config.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$cart_items = getCartItems($db);
if (empty($cart_items)) {
    header("Location: cart.php");
    exit;
}

$total_price = calculateTotalPrice($cart_items);
$user = getUserInfo($db, $_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = createOrder($db, $_SESSION['user_id'], $cart_items, $total_price);
    
    if ($order_id) {
        clearCart();
        
        header("Location: order_success.php?order_id=" . $order_id);
        exit;
    } else {
        $error = "Произошла ошибка при создании заказа. Попробуйте еще раз.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление заказа - BuyBit</title>
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
                <li><a href="cart.php">Корзина</a></li>
                <li><a href="profile.php">Профиль</a></li>
                <li><a href="../includes/logout.php">Выйти</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <div class="checkout-container">
            <h1>Оформление заказа</h1>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="checkout-content">
                <div class="checkout-form">
                    <h2>Данные для заказа</h2>
                    
                    <div class="user-info">
                        <div class="info-item">
                            <label>Имя:</label>
                            <span><?php echo htmlspecialchars($user['name']); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Email:</label>
                            <span><?php echo htmlspecialchars($user['email']); ?></span>
                        </div>
                    </div>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label for="shipping_address">Адрес доставки:</label>
                            <textarea id="shipping_address" name="shipping_address" rows="3" placeholder="Укажите адрес доставки..." required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Примечания к заказу:</label>
                            <textarea id="notes" name="notes" rows="3" placeholder="Дополнительные пожелания..."></textarea>
                        </div>
                        
                        <div class="payment-method">
                            <h3>Способ оплаты</h3>
                            <div class="payment-options">
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="card" checked>
                                    <span>Банковская карта</span>
                                </label>
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="cash">
                                    <span>Наличные при получении</span>
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-large btn-full">Подтвердить заказ</button>
                    </form>
                </div>
                
                <div class="order-summary">
                    <div class="summary-card">
                        <h3>Ваш заказ</h3>
                        
                        <div class="order-items">
                            <?php foreach ($cart_items as $item): ?>
                            <div class="order-item">
                                <div class="item-info">
                                    <span class="item-name"><?php echo htmlspecialchars($item['name']); ?></span>
                                    <span class="item-quantity">× <?php echo $item['quantity']; ?></span>
                                </div>
                                <span class="item-price"><?php echo number_format($item['price'] * $item['quantity'], 0, ',', ' '); ?> ₽</span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="summary-divider"></div>
                        
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
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <footer>
        <p>&copy; 2025 BuyBit. Все права защищены.</p>
    </footer>
</body>
</html>