<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add_to_cart':
            $product_id = intval($_POST['product_id']);
            $quantity = intval($_POST['quantity'] ?? 1);
            
            $cart = json_decode($_COOKIE['cart'] ?? '[]', true) ?? [];
            
            $found = false;
            foreach ($cart as &$item) {
                if ($item['id'] == $product_id) {
                    $item['quantity'] += $quantity;
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $cart[] = ['id' => $product_id, 'quantity' => $quantity];
            }
            
            setcookie('cart', json_encode($cart), time() + (86400 * 30), "/");
            $_COOKIE['cart'] = json_encode($cart);
            
            echo json_encode([
                'success' => true, 
                'cart_count' => array_sum(array_column($cart, 'quantity')),
                'message' => 'Товар добавлен в корзину'
            ]);
            break;
            
        case 'update_quantity':
            $product_id = intval($_POST['product_id']);
            $quantity = intval($_POST['quantity']);
            
            $cart = json_decode($_COOKIE['cart'] ?? '[]', true) ?? [];
            
            foreach ($cart as &$item) {
                if ($item['id'] == $product_id) {
                    if ($quantity <= 0) {
                        // Удаляем товар
                        $cart = array_filter($cart, function($item) use ($product_id) {
                            return $item['id'] != $product_id;
                        });
                        $cart = array_values($cart);
                    } else {
                        $item['quantity'] = $quantity;
                    }
                    break;
                }
            }
            
            setcookie('cart', json_encode($cart), time() + (86400 * 30), "/");
            $_COOKIE['cart'] = json_encode($cart);
            
            echo json_encode([
                'success' => true,
                'cart_count' => array_sum(array_column($cart, 'quantity'))
            ]);
            break;
            
        case 'remove_item':
            $product_id = intval($_POST['product_id']);
            
            $cart = json_decode($_COOKIE['cart'] ?? '[]', true) ?? [];
            $cart = array_filter($cart, function($item) use ($product_id) {
                return $item['id'] != $product_id;
            });
            $cart = array_values($cart);
            
            setcookie('cart', json_encode($cart), time() + (86400 * 30), "/");
            $_COOKIE['cart'] = json_encode($cart);
            
            echo json_encode([
                'success' => true,
                'cart_count' => array_sum(array_column($cart, 'quantity'))
            ]);
            break;
            
        case 'clear_cart':
            setcookie('cart', '[]', time() + (86400 * 30), "/");
            $_COOKIE['cart'] = '[]';
            
            echo json_encode([
                'success' => true,
                'cart_count' => 0
            ]);
            break;
            
        case 'get_cart_count':
            $cart = json_decode($_COOKIE['cart'] ?? '[]', true) ?? [];
            $cart_count = array_sum(array_column($cart, 'quantity'));
            
            echo json_encode([
                'success' => true,
                'cart_count' => $cart_count
            ]);
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Неизвестное действие']);
    }
    exit;
}
?>