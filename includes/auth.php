<?php 
function registerUser($db, $email, $password, $name) {
    $check_query = "SELECT id FROM users WHERE email = :email";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':email', $email);
    $check_stmt->execute();

    if ($check_stmt->rowCount() > 0) {
        return "Пользователь с таким email уже существует";
    }

    $query = "INSERT INTO users (email, password, name) VALUES (:email, :password, :name)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':name', $name);

    if ($stmt->execute()) {
        return true;
    } else {
        return "Ошибка при регистрации пользователя";
    }
}

function loginUser($db, $email, $password) {
    $query = "SELECT id, email, password, name FROM users WHERE email = :email AND password = :password";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        return true;
    } else {
        return "Неверный email или пароль";
    }
}

function logoutUser() {
    session_destroy();
    header("Location: ../index.php");
    exit;
}
?>