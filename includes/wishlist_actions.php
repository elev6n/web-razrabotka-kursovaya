<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add_to_wishlist':
            $product_id = intval($_POST['product_id']);
            
            $wishlist = json_decode($_COOKIE['wishlist'] ?? '[]', true) ?? [];
            
            if (!in_array($product_id, $wishlist)) {
                $wishlist[] = $product_id;
                setcookie('wishlist', json_encode($wishlist), time() + (86400 * 365), "/");
                $_COOKIE['wishlist'] = json_encode($wishlist);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Товар добавлен в избранное'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Товар уже в избранном'
                ]);
            }
            break;
            
        case 'remove_from_wishlist':
            $product_id = intval($_POST['product_id']);
            
            $wishlist = json_decode($_COOKIE['wishlist'] ?? '[]', true) ?? [];
            $wishlist = array_filter($wishlist, function($id) use ($product_id) {
                return $id != $product_id;
            });
            $wishlist = array_values($wishlist);
            
            setcookie('wishlist', json_encode($wishlist), time() + (86400 * 365), "/");
            $_COOKIE['wishlist'] = json_encode($wishlist);
            
            echo json_encode([
                'success' => true,
                'message' => 'Товар удален из избранного'
            ]);
            break;
            
        case 'get_wishlist':
            $wishlist = json_decode($_COOKIE['wishlist'] ?? '[]', true) ?? [];
            
            if (empty($wishlist)) {
                echo json_encode([]);
                break;
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
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Неизвестное действие']);
    }
    exit;
}
?>