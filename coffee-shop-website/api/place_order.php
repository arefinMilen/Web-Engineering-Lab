<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !$_SESSION['logged_in']) {
    http_response_code(401);
    echo json_encode(['error' => 'Please login to place an order']);
    exit;
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (!isset($input['customerName']) || !isset($input['orderTime']) || !isset($input['items'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Customer name, order time, and items are required']);
    exit;
}

$customerName = trim($input['customerName']);
$orderTime = trim($input['orderTime']);
$items = $input['items'];

// Validate order time format (HH:MM)
if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $orderTime)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid time format']);
    exit;
}

// Validate items array
if (!is_array($items) || empty($items)) {
    http_response_code(400);
    echo json_encode(['error' => 'Order must contain at least one item']);
    exit;
}

try {
    $db = getDB();
    $db->beginTransaction();
    
    $userId = $_SESSION['user_id'];
    $totalAmount = 0;
    
    // Validate all items and calculate total
    $validatedItems = [];
    foreach ($items as $item) {
        if (!isset($item['productId']) || !isset($item['quantity']) || !isset($item['price'])) {
            $db->rollBack();
            http_response_code(400);
            echo json_encode(['error' => 'Invalid item data']);
            exit;
        }
        
        $productId = (int)$item['productId'];
        $quantity = (int)$item['quantity'];
        $price = (float)$item['price'];
        
        if ($quantity <= 0) {
            $db->rollBack();
            http_response_code(400);
            echo json_encode(['error' => 'Quantity must be greater than 0']);
            exit;
        }
        
        // Verify product exists and get current price
        $productQuery = "SELECT id, name, price FROM products WHERE id = :id AND is_active = 1";
        $productStmt = $db->prepare($productQuery);
        $productStmt->bindParam(':id', $productId);
        $productStmt->execute();
        
        if ($productStmt->rowCount() === 0) {
            $db->rollBack();
            http_response_code(400);
            echo json_encode(['error' => 'Invalid product ID: ' . $productId]);
            exit;
        }
        
        $product = $productStmt->fetch(PDO::FETCH_ASSOC);
        $currentPrice = (float)$product['price'];
        
        // Use current price from database (security measure)
        $itemTotal = $currentPrice * $quantity;
        $totalAmount += $itemTotal;
        
        $validatedItems[] = [
            'productId' => $productId,
            'quantity' => $quantity,
            'price' => $currentPrice,
            'name' => $product['name']
        ];
    }
    
    // Insert order
    $orderQuery = "INSERT INTO orders (user_id, customer_name, order_time, total_amount, status) 
                   VALUES (:user_id, :customer_name, :order_time, :total_amount, 'pending')";
    
    $orderStmt = $db->prepare($orderQuery);
    $orderStmt->bindParam(':user_id', $userId);
    $orderStmt->bindParam(':customer_name', $customerName);
    $orderStmt->bindParam(':order_time', $orderTime);
    $orderStmt->bindParam(':total_amount', $totalAmount);
    
    if (!$orderStmt->execute()) {
        $db->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create order']);
        exit;
    }
    
    $orderId = $db->lastInsertId();
    
    // Insert order items
    $itemQuery = "INSERT INTO order_items (order_id, product_id, quantity, price_per_item) 
                  VALUES (:order_id, :product_id, :quantity, :price_per_item)";
    $itemStmt = $db->prepare($itemQuery);
    
    foreach ($validatedItems as $item) {
        $itemStmt->bindParam(':order_id', $orderId);
        $itemStmt->bindParam(':product_id', $item['productId']);
        $itemStmt->bindParam(':quantity', $item['quantity']);
        $itemStmt->bindParam(':price_per_item', $item['price']);
        
        if (!$itemStmt->execute()) {
            $db->rollBack();
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add order items']);
            exit;
        }
    }
    
    $db->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully',
        'orderId' => $orderId,
        'totalAmount' => number_format($totalAmount, 2),
        'items' => $validatedItems
    ]);
    
} catch (PDOException $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>