<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !$_SESSION['logged_in']) {
    http_response_code(401);
    echo json_encode(['error' => 'Please login to view order history']);
    exit;
}

try {
    $db = getDB();
    $userId = $_SESSION['user_id'];
    
    // Get user's orders with items
    $orderQuery = "SELECT o.id, o.customer_name, o.order_time, o.total_amount, o.status, o.created_at,
                          oi.quantity, oi.price_per_item, p.name as product_name
                   FROM orders o
                   LEFT JOIN order_items oi ON o.id = oi.order_id
                   LEFT JOIN products p ON oi.product_id = p.id
                   WHERE o.user_id = :user_id
                   ORDER BY o.created_at DESC, o.id DESC";
    
    $orderStmt = $db->prepare($orderQuery);
    $orderStmt->bindParam(':user_id', $userId);
    $orderStmt->execute();
    
    $orderData = $orderStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group items by order
    $orders = [];
    foreach ($orderData as $row) {
        $orderId = $row['id'];
        
        if (!isset($orders[$orderId])) {
            $orders[$orderId] = [
                'id' => $row['id'],
                'customer_name' => $row['customer_name'],
                'order_time' => $row['order_time'],
                'total_amount' => number_format($row['total_amount'], 2),
                'status' => $row['status'],
                'created_at' => date('M d, Y g:i A', strtotime($row['created_at'])),
                'items' => []
            ];
        }
        
        if ($row['product_name']) {
            $orders[$orderId]['items'][] = [
                'name' => $row['product_name'],
                'quantity' => $row['quantity'],
                'price_per_item' => number_format($row['price_per_item'], 2),
                'subtotal' => number_format($row['quantity'] * $row['price_per_item'], 2)
            ];
        }
    }
    
    // Convert to indexed array
    $ordersArray = array_values($orders);
    
    echo json_encode([
        'success' => true,
        'orders' => $ordersArray
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>