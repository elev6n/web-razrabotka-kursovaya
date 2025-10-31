<?php
require_once '../includes/config.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user = getUserInfo($db, $user_id);
$user_orders = getUserOrders($db, $user_id);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    
    $error = '';
    $success = '';
    
    if (empty($name) || empty($email)) {
        $error = 'Имя и email обязательны для заполнения';
    } else {
        $check_query = "SELECT id FROM users WHERE email = :email AND id != :user_id";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bindParam(':email', $email);
        $check_stmt->bindParam(':user_id', $user_id);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            $error = 'Этот email уже используется другим пользователем';
        } else {
            if (!empty($new_password)) {
                if (empty($current_password)) {
                    $error = 'Для смены пароля введите текущий пароль';
                } else {
                    $check_password_query = "SELECT id FROM users WHERE id = :user_id AND password = :password";
                    $check_password_stmt = $db->prepare($check_password_query);
                    $check_password_stmt->bindParam(':user_id', $user_id);
                    $check_password_stmt->bindParam(':password', $current_password);
                    $check_password_stmt->execute();
                    
                    if ($check_password_stmt->rowCount() === 0) {
                        $error = 'Неверный текущий пароль';
                    } else {
                        $update_query = "UPDATE users SET name = :name, email = :email, password = :password WHERE id = :user_id";
                        $update_stmt = $db->prepare($update_query);
                        $update_stmt->bindParam(':name', $name);
                        $update_stmt->bindParam(':email', $email);
                        $update_stmt->bindParam(':password', $new_password);
                        $update_stmt->bindParam(':user_id', $user_id);
                        
                        if ($update_stmt->execute()) {
                            $_SESSION['user_name'] = $name;
                            $_SESSION['user_email'] = $email;
                            $success = 'Профиль успешно обновлен';
                            $user = getUserInfo($db, $user_id);
                        } else {
                            $error = 'Ошибка при обновлении профиля';
                        }
                    }
                }
            } else {
                $update_query = "UPDATE users SET name = :name, email = :email WHERE id = :user_id";
                $update_stmt = $db->prepare($update_query);
                $update_stmt->bindParam(':name', $name);
                $update_stmt->bindParam(':email', $email);
                $update_stmt->bindParam(':user_id', $user_id);
                
                if ($update_stmt->execute()) {
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_email'] = $email;
                    $success = 'Профиль успешно обновлен';
                    $user = getUserInfo($db, $user_id); 
                } else {
                    $error = 'Ошибка при обновлении профиля';
                }
            }
        }
    }
}

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет - BuyBit</title>
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
                <li><a href="profile.php" class="active">Профиль</a></li>
                <li><a href="../includes/logout.php">Выйти</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <div class="profile-container">
            <h1>Личный кабинет</h1>
            
            <div class="profile-content">
                <div class="profile-sidebar">
                    <div class="user-welcome">
                        <h3>Привет, <?php echo htmlspecialchars($user['name']); ?>!</h3>
                        <p>Дата регистрации: <?php echo date('d.m.Y', strtotime($user['created_at'])); ?></p>
                    </div>
                    
                    <nav class="profile-nav">
                        <a href="?tab=profile" class="nav-item <?php echo $active_tab == 'profile' ? 'active' : ''; ?>">
                            📝 Мой профиль
                        </a>
                        <a href="?tab=orders" class="nav-item <?php echo $active_tab == 'orders' ? 'active' : ''; ?>">
                            🛒 Мои заказы
                        </a>
                        <a href="?tab=wishlist" class="nav-item <?php echo $active_tab == 'wishlist' ? 'active' : ''; ?>">
                            ❤️ Избранное
                        </a>
                    </nav>
                </div>
                
                <div class="profile-main">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-error"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($active_tab == 'profile'): ?>
                        <div class="tab-content active" id="profile">
                            <h2>Настройки профиля</h2>
                            
                            <form method="POST" class="profile-form">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="name">Имя:</label>
                                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="email">Email:</label>
                                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="password-section">
                                    <h3>Смена пароля</h3>
                                    <p class="section-description">Оставьте поля пустыми, если не хотите менять пароль</p>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="current_password">Текущий пароль:</label>
                                            <input type="password" id="current_password" name="current_password">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="new_password">Новый пароль:</label>
                                            <input type="password" id="new_password" name="new_password">
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" name="update_profile" class="btn btn-primary">Сохранить изменения</button>
                            </form>
                        </div>
                    
                    <?php elseif ($active_tab == 'orders'): ?>
                        <div class="tab-content active" id="orders">
                            <h2>История заказов</h2>
                            
                            <?php if (empty($user_orders)): ?>
                                <div class="empty-state">
                                    <div class="empty-icon">📦</div>
                                    <h3>Заказов пока нет</h3>
                                    <p>Сделайте свой первый заказ в нашем магазине</p>
                                    <a href="products.php" class="btn btn-primary">Перейти к покупкам</a>
                                </div>
                            <?php else: ?>
                                <div class="orders-list">
                                    <?php foreach ($user_orders as $order): ?>
                                    <div class="order-card">
                                        <div class="order-header">
                                            <div class="order-info">
                                                <h4>Заказ #<?php echo $order['id']; ?></h4>
                                                <span class="order-date"><?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></span>
                                            </div>
                                            <div class="order-status">
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
                                        </div>
                                        
                                        <div class="order-details">
                                            <div class="order-items">
                                                <strong>Товары:</strong> 
                                                <?php 
                                                $product_names = explode(', ', $order['product_names']);
                                                if (count($product_names) > 3) {
                                                    echo implode(', ', array_slice($product_names, 0, 3)) . '...';
                                                } else {
                                                    echo $order['product_names'];
                                                }
                                                ?>
                                            </div>
                                            <div class="order-meta">
                                                <span class="items-count"><?php echo $order['items_count']; ?> товар(ов)</span>
                                                <span class="order-total"><?php echo number_format($order['total'], 0, ',', ' '); ?> ₽</span>
                                            </div>
                                        </div>
                                        
                                        <div class="order-actions">
                                            <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-outline">Подробнее</a>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    
                    <?php elseif ($active_tab == 'wishlist'): ?>
                        <div class="tab-content active" id="wishlist">
                            <h2>Избранные товары</h2>
                            <div id="wishlist-content">
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
    
    <footer>
        <p>&copy; 2025 BuyBit. Все права защищены.</p>
    </footer>

    <script src="../js/profile.js"></script>
</body>
</html>