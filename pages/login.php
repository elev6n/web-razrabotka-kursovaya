<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (isLoggedIn()) {
    header("Location: ../index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = 'Все поля обязательны для заполнения';
    } else {
        $result = loginUser($db, $email, $password);
        if ($result === true) {
            header("Location: ../index.php");
            exit;
        } else {
            $error = $result;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - BuyBit</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/auth.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">BuyBit</div>
            <ul class="nav-links">
                <li><a href="../index.php">Главная</a></li>
                <li><a href="products.php">Товары</a></li>
                <li><a href="cart.php">Корзина</a></li>
                <li><a href="register.php">Регистрация</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="auth-container">
            <div class="auth-form">
                <h2>Вход в аккаунт</h2>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="" onsubmit="return validateLoginForm()">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input 
                            type="email" id="email" name="email"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                            required>
                        <span class="error-message" id="email-error"></span> 
                    </div>

                    <div class="form-group">
                        <label for="password">Пароль:</label>
                        <input 
                            type="password" id="password" name="password"
                            required>
                        <span class="error-message" id="password-error"></span> 
                    </div>

                    <button type="submit" class="btn btn-primary btn-full">Войти</button>
                </form>

                <div class="auth-links">
                    <p>Нет аккаунта? <a href="register.php">Зарегистрируйтесь</a></p>
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

    <script src="../js/auth.js"></script>
</body>
</html>