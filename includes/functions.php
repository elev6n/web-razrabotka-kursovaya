<?php
function getAllProducts($db) {
    $query = "SELECT p.*, c.name as category_name
              FROM products p
              LEFT JOIN categories c ON p.category_id = c.id
              ORDER BY p.created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProductById($db, $id) {
    $query = "SELECT p.*, c.name as category_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE p.id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getProductsByCategory($db, $category_id) {
    $query = "SELECT p.*, c.name as category_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE p.category_id = :category_id 
              ORDER BY p.created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':category_id', $category_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllCategories($db) {
    $query = "SELECT * FROM categories ORDER BY name";
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function searchProducts($db, $search_term) {
    $query = "SELECT p.*, c.name as category_name
              FROM products p
              LEFT JOIN categories c ON p.category_id = c.id
              WHERE p.name LIKE :search OR p.description LIKE :search
              ORDER BY p.created_at DESC";
    $stmt = $db->prepare($query);
    $search_term = "%$search_term%";
    $stmt->bindParam(':search', $search_term);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserInfo($db, $user_id) {
    $query = "SELECT id, email, name, created_at FROM users WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>