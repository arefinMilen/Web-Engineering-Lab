<?php
session_start();
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.php');
    exit;
}

$db = getDB();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_product':
                $name = trim($_POST['name']);
                $description = trim($_POST['description']);
                $price = floatval($_POST['price']);
                
                $query = "INSERT INTO products (name, description, price) VALUES (:name, :description, :price)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':price', $price);
                
                if ($stmt->execute()) {
                    $success_msg = "Product added successfully!";
                } else {
                    $error_msg = "Failed to add product.";
                }
                break;
                
            case 'update_order_status':
                $orderId = intval($_POST['order_id']);
                $status = $_POST['status'];
                
                $query = "UPDATE orders SET status = :status WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':status', $status);
                $stmt->bindParam(':id', $orderId);
                
                if ($stmt->execute()) {
                    $success_msg = "Order status updated!";
                } else {
                    $error_msg = "Failed to update order status.";
                }
                break;
        }
    }
}

// Get all products
$products_query = "SELECT * FROM products ORDER BY name";
$products_stmt = $db->prepare($products_query);
$products_stmt->execute();
$products = $products_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all orders with user details
$orders_query = "SELECT o.*, u.username, u.email 
                 FROM orders o 
                 JOIN users u ON o.user_id = u.id 
                 ORDER BY o.created_at DESC";
$orders_stmt = $db->prepare($orders_query);
$orders_stmt->execute();
$orders = $orders_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get order items for each order
$order_items = [];
foreach ($orders as $order) {
    $items_query = "SELECT oi.*, p.name as product_name 
                    FROM order_items oi 
                    JOIN products p ON oi.product_id = p.id 
                    WHERE oi.order_id = :order_id";
    $items_stmt = $db->prepare($items_query);
    $items_stmt->bindParam(':order_id', $order['id']);
    $items_stmt->execute();
    $order_items[$order['id']] = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Shop Admin Panel</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #8B4513;
        }
        
        .tabs {
            display: flex;
            margin-bottom: 30px;
        }
        
        .tab {
            padding: 12px 24px;
            cursor: pointer;
            background: #f0f0f0;
            border: none;
            margin-right: 5px;
            border-radius: 5px 5px 0 0;
            font-weight: bold;
        }
        
        .tab.active {
            background: #8B4513;
            color: white;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #8B4513;
        }
        
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .btn {
            padding: 10px 20px;
            background: #8B4513;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .btn:hover {
            background: #654321;
        }
        
        .btn-danger {
            background: #dc3545;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .table th {
            background: #8B4513;
            color: white;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-pending { background: #fff3cd; color: #856404; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>☕ Coffee Shop Admin Panel</h1>
            <div>
                <span>Welcome, <?= $_SESSION['admin_username'] ?>!</span>
                <a href="logout.php" class="btn" style="margin-left: 10px; text-decoration: none;">Logout</a>
            </div>
        </div>

        <?php if (isset($success_msg)): ?>
            <div class="alert alert-success"><?= $success_msg ?></div>
        <?php endif; ?>

        <?php if (isset($error_msg)): ?>
            <div class="alert alert-error"><?= $error_msg ?></div>
        <?php endif; ?>

        <div class="tabs">
            <button class="tab active" onclick="showTab('products')">Manage Products</button>
            <button class="tab" onclick="showTab('orders')">View Orders</button>
            <button class="tab" onclick="showTab('stats')">Statistics</button>
        </div>

        <!-- Products Tab -->
        <div id="productsTab" class="tab-content active">
            <h2>Product Management</h2>
            
            <!-- Add Product Form -->
            <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                <h3>Add New Product</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="add_product">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Product Name:</label>
                            <input type="text" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Price ($):</label>
                            <input type="number" name="price" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description:</label>
                        <textarea name="description" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn">Add Product</button>
                </form>
            </div>

            <!-- Products List -->
            <h3>Current Products</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= $product['id'] ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['description']) ?></td>
                        <td>$<?= number_format($product['price'], 2) ?></td>
                        <td>
                            <span class="status-badge <?= $product['is_active'] ? 'status-completed' : 'status-cancelled' ?>">
                                <?= $product['is_active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td><?= date('M d, Y', strtotime($product['created_at'])) ?></td>
                        <td>
                            <button class="btn" style="font-size: 12px; padding: 5px 10px;" 
                                    onclick="toggleProduct(<?= $product['id'] ?>, <?= $product['is_active'] ? 0 : 1 ?>)">
                                <?= $product['is_active'] ? 'Deactivate' : 'Activate' ?>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Orders Tab -->
        <div id="ordersTab" class="tab-content">
            <h2>Order Management</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>User Email</th>
                        <th>Order Time</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?= $order['id'] ?></td>
                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                        <td><?= htmlspecialchars($order['email']) ?></td>
                        <td><?= $order['order_time'] ?></td>
                        <td>$<?= number_format($order['total_amount'], 2) ?></td>
                        <td>
                            <span class="status-badge status-<?= $order['status'] ?>">
                                <?= strtoupper($order['status']) ?>
                            </span>
                        </td>
                        <td><?= date('M d, Y g:i A', strtotime($order['created_at'])) ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="update_order_status">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <select name="status" onchange="this.form.submit()" style="padding: 5px; font-size: 12px;">
                                    <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                    <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </form>
                            <button class="btn" style="font-size: 12px; padding: 5px 10px; margin-left: 5px;" 
                                    onclick="showOrderDetails(<?= $order['id'] ?>)">Details</button>
                        </td>
                    </tr>
                    <tr id="details-<?= $order['id'] ?>" style="display: none;">
                        <td colspan="8" style="background: #f9f9f9; padding: 15px;">
                            <strong>Order Items:</strong><br>
                            <?php if (isset($order_items[$order['id']])): ?>
                                <?php foreach ($order_items[$order['id']] as $item): ?>
                                    <div style="margin: 5px 0;">
                                        <?= htmlspecialchars($item['product_name']) ?> 
                                        x <?= $item['quantity'] ?> 
                                        @ $<?= number_format($item['price_per_item'], 2) ?> 
                                        = $<?= number_format($item['quantity'] * $item['price_per_item'], 2) ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Statistics Tab -->
        <div id="statsTab" class="tab-content">
            <h2>Statistics</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
                <?php
                // Calculate statistics
                $total_orders = count($orders);
                $total_revenue = array_sum(array_column($orders, 'total_amount'));
                $pending_orders = count(array_filter($orders, function($o) { return $o['status'] === 'pending'; }));
                $completed_orders = count(array_filter($orders, function($o) { return $o['status'] === 'completed'; }));
                
                $users_query = "SELECT COUNT(*) as total_users FROM users";
                $users_stmt = $db->prepare($users_query);
                $users_stmt->execute();
                $total_users = $users_stmt->fetch(PDO::FETCH_ASSOC)['total_users'];
                ?>
                
                <div style="background: #8B4513; color: white; padding: 20px; border-radius: 8px; text-align: center;">
                    <h3><?= $total_orders ?></h3>
                    <p>Total Orders</p>
                </div>
                
                <div style="background: #D2691E; color: white; padding: 20px; border-radius: 8px; text-align: center;">
                    <h3>$<?= number_format($total_revenue, 2) ?></h3>
                    <p>Total Revenue</p>
                </div>
                
                <div style="background: #228B22; color: white; padding: 20px; border-radius: 8px; text-align: center;">
                    <h3><?= $completed_orders ?></h3>
                    <p>Completed Orders</p>
                </div>
                
                <div style="background: #DC143C; color: white; padding: 20px; border-radius: 8px; text-align: center;">
                    <h3><?= $pending_orders ?></h3>
                    <p>Pending Orders</p>
                </div>
                
                <div style="background: #4169E1; color: white; padding: 20px; border-radius: 8px; text-align: center;">
                    <h3><?= $total_users ?></h3>
                    <p>Total Users</p>
                </div>
                
                <div style="background: #FF8C00; color: white; padding: 20px; border-radius: 8px; text-align: center;">
                    <h3><?= count($products) ?></h3>
                    <p>Total Products</p>
                </div>
            </div>

            <!-- Recent Orders -->
            <h3>Recent Orders (Last 10)</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($orders, 0, 10) as $order): ?>
                    <tr>
                        <td>#<?= $order['id'] ?></td>
                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                        <td>$<?= number_format($order['total_amount'], 2) ?></td>
                        <td>
                            <span class="status-badge status-<?= $order['status'] ?>">
                                <?= strtoupper($order['status']) ?>
                            </span>
                        </td>
                        <td><?= date('M d, Y g:i A', strtotime($order['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab content
            document.getElementById(tabName + 'Tab').classList.add('active');
            
            // Add active class to clicked tab
            event.target.classList.add('active');
        }

        function showOrderDetails(orderId) {
            const detailRow = document.getElementById('details-' + orderId);
            if (detailRow.style.display === 'none') {
                detailRow.style.display = 'table-row';
            } else {
                detailRow.style.display = 'none';
            }
        }

        function toggleProduct(productId, newStatus) {
            if (confirm('Are you sure you want to ' + (newStatus ? 'activate' : 'deactivate') + ' this product?')) {
                // Create a form and submit it
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="toggle_product">
                    <input type="hidden" name="product_id" value="${productId}">
                    <input type="hidden" name="is_active" value="${newStatus}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>