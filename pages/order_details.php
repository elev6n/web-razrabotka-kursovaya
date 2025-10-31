<?php
require_once '../includes/config.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$order_items = getOrderDetails($db, $order_id, $_SESSION['user_id']);

if (empty($order_items)) {
    header("Location: profile.php?tab=orders");
    exit;
}

$order = $order_items[0]; 
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Детали заказа #<?php echo $order_id; ?> - BuyBit</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/profile.css">
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
        <div class="order-details-container">
            <div class="breadcrumbs">
                <a href="profile.php?tab=orders">← Назад к заказам</a>
            </div>
            
            <h1>Детали заказа #<?php echo $order_id; ?></h1>
            
            <div class="order-details-content">
                <div class="order-summary">
                    <div class="summary-card">
                        <h3>Информация о заказе</h3>
                        
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Номер заказа:</label>
                                <span>#<?php echo $order_id; ?></span>
                            </div>
                            <div class="info-item">
                                <label>Дата заказа:</label>
                                <span><?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></span>
                            </div>
                            <div class="info-item">
                                <label>Статус:</label>
                                <span class="status-badge status-<?php echo $order['status']; ?>">
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
                            <div class="info-item">
                                <label>Общая сумма:</label>
                                <span class="total-amount"><?php echo number_format($order['total'], 0, ',', ' '); ?> ₽</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="order-items-section">
                    <h3>Состав заказа</h3>
                    
                    <div class="order-items-list">
                        <?php foreach ($order_items as $item): ?>
                            <?php if (!empty($item['product_id'])): ?>
                            <div class="order-item">
                                <div class="item-image">
                                    <img src="<?php echo $item['product_image'] ? '../images/' . $item['product_image'] : '../images/placeholder.png'; ?>" 
                                         alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                         onerror="this.src='../images/placeholder.png'">
                                </div>
                                <div class="item-details">
                                    <h4><?php echo htmlspecialchars($item['product_name']); ?></h4>
                                    <div class="item-meta">
                                        <span class="item-quantity">Количество: <?php echo $item['quantity']; ?> шт.</span>
                                        <span class="item-price"><?php echo number_format($item['price'], 0, ',', ' '); ?> ₽ за шт.</span>
                                    </div>
                                </div>
                                <div class="item-total">
                                    <?php echo number_format($item['price'] * $item['quantity'], 0, ',', ' '); ?> ₽
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="order-total">
                        <div class="total-row">
                            <span>Итого:</span>
                            <span><?php echo number_format($order['total'], 0, ',', ' '); ?> ₽</span>
                        </div>
                    </div>
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