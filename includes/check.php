<?php
require_once 'config.php';

try {
    $db->query("SELECT 1");
    echo "✅ Подключение к БД успешно\n";
} catch (PDOException $e) {
    echo "❌ Ошибка подключения к БД: " . $e->getMessage() . "\n";
}

if (session_status() === PHP_SESSION_ACTIVE) {
    echo "✅ Сессии работают\n";
} else {
    echo "❌ Сессии не работают\n";
}

$tables = ['users', 'products', 'categories', 'orders', 'order_items'];
foreach ($tables as $table) {
    try {
        $db->query("SELECT 1 FROM $table LIMIT 1");
        echo "✅ Таблица $table существует\n";
    } catch (PDOException $e) {
        echo "❌ Таблица $table отсутствует\n";
    }
}
?>