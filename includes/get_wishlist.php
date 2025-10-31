<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_COOKIE['wishlist'])) {
    echo json_encode([]);
    exit;
}

$wishlist = json_decode($_COOKIE['wishlist'], true);
if (empty($wishlist)) {
    echo json_encode([]);
    exit;
}

$placeholders = str_repeat('?,', count($wishlist) - 1) . '?';

$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE p.id IN ($placeholders)";

$stmt = $db->prepare($query);
$stmt->execute($wishlist);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($products);
?>