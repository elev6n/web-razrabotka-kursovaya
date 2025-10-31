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

function getCartItems($db) {
    $cart_json = $_COOKIE['cart'] ?? '[]';
    $cart = json_decode($cart_json, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON decode error in getCartItems: " . json_last_error_msg());
        $cart = [];
    }
    
    if (empty($cart)) {
        return [];
    }
    
    $product_ids = [];
    foreach ($cart as $item) {
        if (isset($item['id']) && is_numeric($item['id'])) {
            $product_ids[] = intval($item['id']);
        }
    }
    
    if (empty($product_ids)) {
        return [];
    }
    
    $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
    $query = "SELECT p.*, c.name as category_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE p.id IN ($placeholders)";
    
    try {
        $stmt = $db->prepare($query);
        $stmt->execute($product_ids);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $products_by_id = [];
        foreach ($products as $product) {
            $products_by_id[$product['id']] = $product;
        }
        
        $cart_items = [];
        foreach ($cart as $cart_item) {
            $product_id = intval($cart_item['id']);
            if (isset($products_by_id[$product_id])) {
                $product = $products_by_id[$product_id];
                $product['quantity'] = intval($cart_item['quantity']);
                $cart_items[] = $product;
            }
        }
        
        return $cart_items;
    } catch (Exception $e) {
        error_log("Error getting cart items: " . $e->getMessage());
        return [];
    }
}

function calculateTotalPrice($cart_items) {
    $total = 0;
    foreach ($cart_items as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

function updateCartQuantity($product_id, $quantity) {
    $cart = json_decode($_COOKIE['cart'] ?? '[]', true) ?? [];
    
    foreach ($cart as &$item) {
        if ($item['id'] == $product_id) {
            $item['quantity'] = $quantity;
            break;
        }
    }
    
setcookie('cart', json_encode($cart), time() + (86400 * 30), "/");
    $_COOKIE['cart'] = json_encode($cart); 
}

function removeFromCart($product_id) {
    $cart = json_decode($_COOKIE['cart'] ?? '[]', true) ?? [];
    
    $cart = array_filter($cart, function($item) use ($product_id) {
        return $item['id'] != $product_id;
    });
    
    $cart = array_values($cart); 
    
setcookie('cart', json_encode($cart), time() + (86400 * 30), "/");
    $_COOKIE['cart'] = json_encode($cart);
}

function clearCart() {
    setcookie('cart', '[]', time() + (86400 * 30), "/");
    $_COOKIE['cart'] = '[]';
}

function createOrder($db, $user_id, $cart_items, $total) {
    try {
        $db->beginTransaction();
        
        $query = "INSERT INTO orders (user_id, total, status) VALUES (:user_id, :total, 'pending')";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':total', $total);
        $stmt->execute();
        
        $order_id = $db->lastInsertId();
        
        $query = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)";
        $stmt = $db->prepare($query);
        
        foreach ($cart_items as $item) {
            $stmt->bindParam(':order_id', $order_id);
            $stmt->bindParam(':product_id', $item['id']);
            $stmt->bindParam(':quantity', $item['quantity']);
            $stmt->bindParam(':price', $item['price']);
            $stmt->execute();
            
            $update_query = "UPDATE products SET stock = stock - :quantity WHERE id = :product_id";
            $update_stmt = $db->prepare($update_query);
            $update_stmt->bindParam(':quantity', $item['quantity']);
            $update_stmt->bindParam(':product_id', $item['id']);
            $update_stmt->execute();
        }
        
        $db->commit();
        return $order_id;
        
    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}

function getUserOrders($db, $user_id) {
    $query = "SELECT o.*, 
                     COUNT(oi.id) as items_count,
                     GROUP_CONCAT(p.name SEPARATOR ', ') as product_names
              FROM orders o
              LEFT JOIN order_items oi ON o.id = oi.order_id
              LEFT JOIN products p ON oi.product_id = p.id
              WHERE o.user_id = :user_id
              GROUP BY o.id
              ORDER BY o.created_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getOrderDetails($db, $order_id, $user_id = null) {
    $query = "SELECT o.*, oi.*, p.name as product_name, p.image as product_image
              FROM orders o
              LEFT JOIN order_items oi ON o.id = oi.order_id
              LEFT JOIN products p ON oi.product_id = p.id
              WHERE o.id = :order_id";
    
    if ($user_id) {
        $query .= " AND o.user_id = :user_id";
    }
    
    $query .= " ORDER BY oi.id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':order_id', $order_id);
    if ($user_id) {
        $stmt->bindParam(':user_id', $user_id);
    }
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getWishlistProducts($db) {
    if (!isset($_COOKIE['wishlist'])) {
        return [];
    }
    
    $wishlist = json_decode($_COOKIE['wishlist'], true);
    if (empty($wishlist)) {
        return [];
    }
    
    $placeholders = str_repeat('?,', count($wishlist) - 1) . '?';
    
    $query = "SELECT p.*, c.name as category_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE p.id IN ($placeholders)";
    
    $stmt = $db->prepare($query);
    $stmt->execute($wishlist);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addToWishlist($product_id) {
    $wishlist = json_decode($_COOKIE['wishlist'] ?? '[]', true) ?? [];
    
    if (!in_array($product_id, $wishlist)) {
        $wishlist[] = $product_id;
        setcookie('wishlist', json_encode($wishlist), time() + (86400 * 365), "/"); 
        $_COOKIE['wishlist'] = json_encode($wishlist);
        return true;
    }
    
    return false;
}

function removeFromWishlist($product_id) {
    $wishlist = json_decode($_COOKIE['wishlist'] ?? '[]', true) ?? [];
    
    $wishlist = array_filter($wishlist, function($id) use ($product_id) {
        return $id != $product_id;
    });
    
    $wishlist = array_values($wishlist);
    
    setcookie('wishlist', json_encode($wishlist), time() + (86400 * 365), "/");
    $_COOKIE['wishlist'] = json_encode($wishlist);
    return true;
}

function isValidProduct($db, $product_id) {
    $query = "SELECT id FROM products WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $product_id);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}

function isValidCategory($db, $category_id) {
    $query = "SELECT id FROM categories WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $category_id);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}

function isValidOrder($db, $order_id, $user_id = null) {
    $query = "SELECT id FROM orders WHERE id = :order_id";
    if ($user_id) {
        $query .= " AND user_id = :user_id";
    }
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':order_id', $order_id);
    if ($user_id) {
        $stmt->bindParam(':user_id', $user_id);
    }
    $stmt->execute();
    return $stmt->rowCount() > 0;
}

function redirectTo404() {
    header("HTTP/1.0 404 Not Found");
    header("Location: /buybit/pages/404.php");
    exit;
}
?>