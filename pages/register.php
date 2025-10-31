<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $name = trim($_POST['name']);

    if (empty($email) || empty($password) || empty($name)) {
        $error = 'Все поля обязательны для заполнения';
    } elseif ($password !== $confirm_password) {
        $error = 'Пароли не совпадают';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен содержать минимум 6 символов';
    } else {
        $result = registerUser($db, $email, $password, $name);
        if ($result === true) {
            $success = 'Регистрация успешна! Теперь вы можете войти.';
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
    <title>Регистрация - BuyBit</title>
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
                <li><a href="login.php">Войти</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="auth-container">
            <div class="auth-form">
                <h2>Регистрация</h2>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <form method="POST" action="" onsubmit="return validateRegisterForm()">
                    <div class="form-group">
                        <label for="name">Имя:</label>
                        <input 
                            type="text" id="name" name="name" 
                            value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" 
                            required>
                        <span class="error-message" id="name-error"></span>
                    </div>

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

                    <div class="form-group">
                        <label for="confirm_password">Подтвердите пароль:</label>
                        <input 
                            type="password" id="confirm_password" name="confirm_password" 
                            required>
                        <span class="error-message" id="confirm-password-error"></span>
                    </div>

                    <button type="submit" class="btn btn-primary btn-full">Зарегистрироваться</button>
                </form>

                <div class="auth-links">
                    <p>Уже есть аккаунт? <a href="login.php">Войдите</a></p>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 BuyBit. Все права защищены.</p>
    </footer>

    <script src="../js/auth.js"></script>
</body>
</html>