<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Shop</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #8B4513, #D2691E);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }

        .page {
            display: none;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .page.active {
            display: block;
        }

        /* Login/Register Form Styles */
        .auth-form {
            max-width: 400px;
            margin: 0 auto;
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

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #8B4513;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: #8B4513;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
            margin-bottom: 10px;
        }

        .btn:hover {
            background: #654321;
        }

        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: #D2691E;
        }

        .btn-secondary:hover {
            background: #B8860B;
        }

        /* Coffee Shop Styles */
        .coffee-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #8B4513;
        }

        .user-info {
            color: #8B4513;
            font-weight: bold;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .menu-item {
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: transform 0.3s, border-color 0.3s;
        }

        .menu-item:hover {
            transform: translateY(-5px);
            border-color: #8B4513;
        }

        .menu-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .menu-item h3 {
            color: #8B4513;
            margin-bottom: 10px;
        }

        .price {
            font-size: 18px;
            font-weight: bold;
            color: #D2691E;
            margin-bottom: 15px;
        }

        .quantity-control {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .quantity-btn {
            width: 30px;
            height: 30px;
            border: none;
            background: #8B4513;
            color: white;
            border-radius: 50%;
            cursor: pointer;
            font-weight: bold;
        }

        .quantity {
            font-weight: bold;
            min-width: 20px;
            text-align: center;
        }

        .cart {
            background: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
        }

        .cart h3 {
            color: #8B4513;
            margin-bottom: 15px;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .cart-total {
            font-size: 18px;
            font-weight: bold;
            color: #8B4513;
            text-align: right;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #8B4513;
        }

        .order-form {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
        }

        .link-btn {
            background: none;
            border: none;
            color: #8B4513;
            text-decoration: underline;
            cursor: pointer;
            font-size: 14px;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }

        .success {
            color: green;
            font-size: 14px;
            margin-top: 5px;
        }

        .loading {
            text-align: center;
            color: #8B4513;
            padding: 20px;
        }

        .order-history {
            margin-top: 30px;
            background: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
        }

        .order-item {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .order-status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-pending { background: #fff3cd; color: #856404; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }

        .tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 2px solid #8B4513;
        }

        .tab {
            padding: 10px 20px;
            cursor: pointer;
            background: transparent;
            border: none;
            color: #8B4513;
            font-weight: bold;
            border-bottom: 2px solid transparent;
        }

        .tab.active {
            border-bottom-color: #8B4513;
            background: rgba(139, 69, 19, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>☕ Coffee Shop</h1>
        </div>

        <!-- Login Page -->
        <div id="loginPage" class="page active">
            <div class="auth-form">
                <h2 style="text-align: center; color: #8B4513; margin-bottom: 30px;">Login</h2>
                <form id="loginForm">
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" id="loginEmail" required>
                    </div>
                    <div class="form-group">
                        <label>Password:</label>
                        <input type="password" id="loginPassword" required>
                    </div>
                    <button type="submit" class="btn" id="loginBtn">Login</button>
                    <button type="button" class="btn btn-secondary" onclick="showRegister()">Register</button>
                </form>
                <div id="loginError" class="error"></div>
            </div>
        </div>

        <!-- Register Page -->
        <div id="registerPage" class="page">
            <div class="auth-form">
                <h2 style="text-align: center; color: #8B4513; margin-bottom: 30px;">Register</h2>
                <form id="registerForm">
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" id="regEmail" required>
                    </div>
                    <div class="form-group">
                        <label>User Name:</label>
                        <input type="text" id="regUsername" required>
                    </div>
                    <div class="form-group">
                        <label>Birth Date:</label>
                        <input type="date" id="regBirthDate" required>
                    </div>
                    <div class="form-group">
                        <label>Social Security (Last 4 digits):</label>
                        <input type="text" id="regSSN" maxlength="4" pattern="[0-9]{4}" required>
                    </div>
                    <div class="form-group">
                        <label>Password:</label>
                        <input type="password" id="regPassword" required minlength="6">
                    </div>
                    <button type="submit" class="btn" id="registerBtn">Register</button>
                    <button type="button" class="btn btn-secondary" onclick="showLogin()">Back to Login</button>
                </form>
                <div id="registerError" class="error"></div>
                <div id="registerSuccess" class="success"></div>
            </div>
        </div>

        <!-- Coffee Shop Page -->
        <div id="coffeeShopPage" class="page">
            <div class="coffee-header">
                <h2>Welcome to our Coffee Shop!</h2>
                <div class="user-info">
                    <span id="welcomeUser"></span>
                    <button class="btn" onclick="logout()" style="width: auto; margin-left: 10px; padding: 8px 15px;">Logout</button>
                </div>
            </div>

            <div class="tabs">
                <button class="tab active" onclick="showTab('menu')">Menu</button>
                <button class="tab" onclick="showTab('history')">Order History</button>
            </div>

            <!-- Menu Tab -->
            <div id="menuTab" class="tab-content">
                <div id="loadingProducts" class="loading">Loading products...</div>
                <div id="menuGrid" class="menu-grid" style="display: none;"></div>

                <div class="cart">
                    <h3>Your Order</h3>
                    <div id="cartItems"></div>
                    <div class="cart-total">
                        Total: $<span id="cartTotal">0.00</span>
                    </div>
                    
                    <div class="order-form">
                        <h4 style="color: #8B4513; margin-bottom: 15px;">Order Details</h4>
                        <div class="form-group">
                            <label>Name:</label>
                            <input type="text" id="orderName" required>
                        </div>
                        <div class="form-group">
                            <label>Time:</label>
                            <input type="time" id="orderTime" required>
                        </div>
                        <button class="btn" onclick="placeOrder()" id="placeOrderBtn">Place Order</button>
                    </div>
                </div>
            </div>

            <!-- Order History Tab -->
            <div id="historyTab" class="tab-content" style="display: none;">
                <div id="orderHistory" class="order-history"></div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let currentUser = null;
        let cart = [];
        let products = [];

        // API Base URL
        const API_BASE = 'api/';

        // Utility function for API calls
        async function apiCall(endpoint, method = 'GET', data = null) {
            const config = {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                }
            };

            if (data) {
                config.body = JSON.stringify(data);
            }

            try {
                const response = await fetch(API_BASE + endpoint, config);
                const result = await response.json();
                
                if (!response.ok) {
                    throw new Error(result.error || 'Request failed');
                }
                
                return result;
            } catch (error) {
                console.error('API Error:', error);
                throw error;
            }
        }

        // Page navigation functions
        function showLogin() {
            document.querySelectorAll('.page').forEach(page => page.classList.remove('active'));
            document.getElementById('loginPage').classList.add('active');
            clearMessages();
        }

        function showRegister() {
            document.querySelectorAll('.page').forEach(page => page.classList.remove('active'));
            document.getElementById('registerPage').classList.add('active');
            clearMessages();
        }

        function showCoffeeShop() {
            document.querySelectorAll('.page').forEach(page => page.classList.remove('active'));
            document.getElementById('coffeeShopPage').classList.add('active');
            document.getElementById('welcomeUser').textContent = `Hello, ${currentUser.username}!`;
            loadProducts();
            showTab('menu');
        }

        function clearMessages() {
            document.getElementById('loginError').textContent = '';
            document.getElementById('registerError').textContent = '';
            document.getElementById('registerSuccess').textContent = '';
        }

        // Tab management
        function showTab(tabName) {
            // Update tab buttons
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            event.target.classList.add('active');

            // Show/hide tab content
            document.getElementById('menuTab').style.display = tabName === 'menu' ? 'block' : 'none';
            document.getElementById('historyTab').style.display = tabName === 'history' ? 'block' : 'none';

            if (tabName === 'history') {
                loadOrderHistory();
            }
        }

        // Registration
        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const registerBtn = document.getElementById('registerBtn');
            registerBtn.disabled = true;
            registerBtn.textContent = 'Registering...';
            
            const formData = {
                email: document.getElementById('regEmail').value,
                username: document.getElementById('regUsername').value,
                birthDate: document.getElementById('regBirthDate').value,
                ssn: document.getElementById('regSSN').value,
                password: document.getElementById('regPassword').value
            };

            try {
                const result = await apiCall('register.php', 'POST', formData);
                document.getElementById('registerSuccess').textContent = result.message;
                document.getElementById('registerError').textContent = '';
                document.getElementById('registerForm').reset();
            } catch (error) {
                document.getElementById('registerError').textContent = error.message;
                document.getElementById('registerSuccess').textContent = '';
            } finally {
                registerBtn.disabled = false;
                registerBtn.textContent = 'Register';
            }
        });

        // Login
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const loginBtn = document.getElementById('loginBtn');
            loginBtn.disabled = true;
            loginBtn.textContent = 'Logging in...';
            
            const formData = {
                email: document.getElementById('loginEmail').value,
                password: document.getElementById('loginPassword').value
            };

            try {
                const result = await apiCall('login.php', 'POST', formData);
                currentUser = result.user;
                showCoffeeShop();
                document.getElementById('loginForm').reset();
            } catch (error) {
                document.getElementById('loginError').textContent = error.message;
            } finally {
                loginBtn.disabled = false;
                loginBtn.textContent = 'Login';
            }
        });

        // Load products from database
        async function loadProducts() {
            try {
                document.getElementById('loadingProducts').style.display = 'block';
                document.getElementById('menuGrid').style.display = 'none';
                
                const result = await apiCall('products.php');
                products = result.products;
                displayProducts();
            } catch (error) {
                console.error('Failed to load products:', error);
                document.getElementById('loadingProducts').textContent = 'Failed to load products';
            }
        }

        // Display products
        function displayProducts() {
            const menuGrid = document.getElementById('menuGrid');
            let html = '';

            products.forEach(product => {
                html += `
                    <div class="menu-item">
                        <div style="width: 100%; height: 200px; background: linear-gradient(45deg, #8B4513, #D2691E); border-radius: 8px; margin-bottom: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">☕</div>
                        <h3>${product.name}</h3>
                        <p>${product.description}</p>
                        <div class="price">$${product.price}</div>
                        <div class="quantity-control">
                            <button class="quantity-btn" onclick="changeQuantity(${product.id}, -1)">-</button>
                            <span class="quantity" id="qty-${product.id}">0</span>
                            <button class="quantity-btn" onclick="changeQuantity(${product.id}, 1)">+</button>
                        </div>
                        <button class="btn" onclick="addToCart(${product.id})">Add to Cart</button>
                    </div>
                `;
            });

            menuGrid.innerHTML = html;
            document.getElementById('loadingProducts').style.display = 'none';
            document.getElementById('menuGrid').style.display = 'grid';
            updateCartDisplay();
        }

        // Quantity controls
        function changeQuantity(productId, change) {
            const qtyElement = document.getElementById(`qty-${productId}`);
            let currentQty = parseInt(qtyElement.textContent);
            currentQty = Math.max(0, currentQty + change);
            qtyElement.textContent = currentQty;
        }

        // Add to cart
        function addToCart(productId) {
            const quantity = parseInt(document.getElementById(`qty-${productId}`).textContent);
            
            if (quantity === 0) {
                alert('Please select quantity first!');
                return;
            }

            const product = products.find(p => p.id == productId);
            const existingItem = cart.find(item => item.productId == productId);
            
            if (existingItem) {
                existingItem.quantity += quantity;
            } else {
                cart.push({
                    productId: productId,
                    name: product.name,
                    price: parseFloat(product.price),
                    quantity: quantity
                });
            }

            document.getElementById(`qty-${productId}`).textContent = '0';
            updateCartDisplay();
        }

        // Update cart display
        function updateCartDisplay() {
            const cartItemsElement = document.getElementById('cartItems');
            const cartTotalElement = document.getElementById('cartTotal');

            if (cart.length === 0) {
                cartItemsElement.innerHTML = '<p>Your cart is empty</p>';
                cartTotalElement.textContent = '0.00';
                return;
            }

            let html = '';
            let total = 0;

            cart.forEach((item, index) => {
                const itemTotal = item.price * item.quantity;
                total += itemTotal;
                
                html += `
                    <div class="cart-item">
                        <span>${item.name} x ${item.quantity}</span>
                        <span>$${itemTotal.toFixed(2)}</span>
                        <button onclick="removeFromCart(${index})" style="background: red; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer;">Remove</button>
                    </div>
                `;
            });

            cartItemsElement.innerHTML = html;
            cartTotalElement.textContent = total.toFixed(2);
        }

        // Remove from cart
        function removeFromCart(index) {
            cart.splice(index, 1);
            updateCartDisplay();
        }

        // Place order
        async function placeOrder() {
            const orderName = document.getElementById('orderName').value;
            const orderTime = document.getElementById('orderTime').value;

            if (!orderName || !orderTime) {
                alert('Please fill in all order details!');
                return;
            }

            if (cart.length === 0) {
                alert('Your cart is empty!');
                return;
            }

            const placeOrderBtn = document.getElementById('placeOrderBtn');
            placeOrderBtn.disabled = true;
            placeOrderBtn.textContent = 'Placing Order...';

            const orderData = {
                customerName: orderName,
                orderTime: orderTime,
                items: cart.map(item => ({
                    productId: item.productId,
                    quantity: item.quantity,
                    price: item.price
                }))
            };

            try {
                const result = await apiCall('place_order.php', 'POST', orderData);
                
                alert(`Order placed successfully!
                
Order ID: ${result.orderId}
Customer: ${orderName}
Time: ${orderTime}
Items: ${result.items.map(item => `${item.name} x${item.quantity}`).join(', ')}
Total: $${result.totalAmount}

Thank you for your order!`);

                // Clear cart and form
                cart = [];
                updateCartDisplay();
                document.getElementById('orderName').value = '';
                document.getElementById('orderTime').value = '';
                
            } catch (error) {
                alert('Failed to place order: ' + error.message);
            } finally {
                placeOrderBtn.disabled = false;
                placeOrderBtn.textContent = 'Place Order';
            }
        }

        // Load order history
        async function loadOrderHistory() {
            const historyDiv = document.getElementById('orderHistory');
            historyDiv.innerHTML = '<div class="loading">Loading order history...</div>';

            try {
                const result = await apiCall('order_history.php');
                displayOrderHistory(result.orders);
            } catch (error) {
                historyDiv.innerHTML = '<p>Failed to load order history: ' + error.message + '</p>';
            }
        }

        // Display order history
        function displayOrderHistory(orders) {
            const historyDiv = document.getElementById('orderHistory');
            
            if (orders.length === 0) {
                historyDiv.innerHTML = '<p>No orders found.</p>';
                return;
            }

            let html = '<h3 style="color: #8B4513; margin-bottom: 20px;">Your Order History</h3>';

            orders.forEach(order => {
                html += `
                    <div class="order-item">
                        <div class="order-header">
                            <div>
                                <strong>Order #${order.id}</strong> - ${order.created_at}
                                <br><small>Customer: ${order.customer_name} | Time: ${order.order_time}</small>
                            </div>
                            <div>
                                <span class="order-status status-${order.status}">${order.status.toUpperCase()}</span>
                                <div style="margin-top: 5px; font-weight: bold;">$${order.total_amount}</div>
                            </div>
                        </div>
                        <div>
                            ${order.items.map(item => 
                                `<div style="display: flex; justify-content: space-between; padding: 2px 0;">
                                    <span>${item.name} x ${item.quantity}</span>
                                    <span>$${item.subtotal}</span>
                                </div>`
                            ).join('')}
                        </div>
                    </div>
                `;
            });

            historyDiv.innerHTML = html;
        }

        // Logout
        async function logout() {
            try {
                await apiCall('logout.php');
            } catch (error) {
                console.error('Logout error:', error);
            } finally {
                currentUser = null;
                cart = [];
                showLogin();
            }
        }

        // Initialize the application
        updateCartDisplay();
    </script>
</body>
</html>