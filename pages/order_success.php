<?php
require_once '../includes/config.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$order_details = getOrderDetails($db, $order_id, $_SESSION['user_id']);

if (empty($order_details)) {
    header("Location: profile.php");
    exit;
}

$order = $order_details[0]; 
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказ оформлен - BuyBit</title>
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
        <div class="order-success-container">
            <div class="success-message">
                <div class="success-icon">✅</div>
                <h1>Заказ успешно оформлен!</h1>
                <p>Спасибо за ваш заказ. Мы свяжемся с вами в ближайшее время.</p>
                
                <div class="order-info">
                    <div class="info-card">
                        <h3>Информация о заказе</h3>
                        <div class="info-row">
                            <span>Номер заказа:</span>
                            <strong>#<?php echo $order['order_id']; ?></strong>
                        </div>
                        <div class="info-row">
                            <span>Дата заказа:</span>
                            <span><?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></span>
                        </div>
                        <div class="info-row">
                            <span>Статус:</span>
                            <span class="status-<?php echo $order['status']; ?>">
                                <?php 
                                $statuses = [
                                    'pending' => 'В обработке',
                                    'processing' => 'Обрабатывается',
                                    'completed' => 'Завершен',
                                    'cancelled' => 'Отменен'
                                ];
                                echo $statuses[$order['status']] ?? $order['status']; 
                                ?>
                            </span>
                        </div>
                        <div class="info-row">
                            <span>Общая сумма:</span>
                            <strong><?php echo number_format($order['total'], 0, ',', ' '); ?> ₽</strong>
                        </div>
                    </div>
                </div>
                
                <div class="success-actions">
                    <a href="profile.php?tab=orders" class="btn btn-primary">Мои заказы</a>
                    <a href="products.php" class="btn btn-outline">Продолжить покупки</a>
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