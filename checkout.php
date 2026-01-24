<?php
include 'db.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Get cart items
$cart_sql = "SELECT c.*, p.price 
             FROM cart c 
             JOIN products p ON c.product_id = p.id 
             WHERE c.user_id = ?";
$cart_stmt = $conn->prepare($cart_sql);
$cart_stmt->bind_param("i", $user_id);
$cart_stmt->execute();
$cart_items = $cart_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if(count($cart_items) == 0) {
    header('Location: cart.php');
    exit;
}

$total = 0;
foreach($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Handle place order
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    // Insert order
    $order_sql = "INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'pending')";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->bind_param("id", $user_id, $total);
    
    if($order_stmt->execute()) {
        $order_id = $order_stmt->insert_id;
        
        // Insert order items
        $items_inserted = true;
        foreach($cart_items as $item) {
            $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $item_stmt = $conn->prepare($item_sql);
            $item_stmt->bind_param("iiii", $order_id, $item['product_id'], $item['quantity'], $item['price']);
            
            if(!$item_stmt->execute()) {
                $items_inserted = false;
                break;
            }
        }
        
        if($items_inserted) {
            // Clear cart
            $clear_sql = "DELETE FROM cart WHERE user_id = ?";
            $clear_stmt = $conn->prepare($clear_sql);
            $clear_stmt->bind_param("i", $user_id);
            $clear_stmt->execute();
            
            header('Location: order-success.php?order_id=' . $order_id);
            exit;
        } else {
            $error = 'Error placing order. Please try again.';
        }
    } else {
        $error = 'Error creating order. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="favicon.png">
    <title>Checkout - Life Wear</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #fff; font-family: 'Poppins', Arial; }
        
        .top-bar {
            background: #000;
            color: #fff;
            text-align: center;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 2px;
            padding: 10px;
            text-transform: uppercase;
        }
        
        nav {
            position: fixed;
            top: 50px;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            height: 40px;
            background: #000;
            border-radius: 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
            z-index: 1000;
        }
        
        nav .logo {
            font-size: 18px;
            font-weight: bold;
            color: #fff;
            text-transform: uppercase;
        }
        
        nav ul {
            display: flex;
            list-style: none;
            gap: 30px;
        }
        
        nav a {
            color: white;
            text-decoration: none;
            font-size: 12px;
            opacity: 0.8;
        }
        
        nav a:hover {
            opacity: 1;
            color: #f1c40f;
        }
        
        .checkout-container {
            margin-top: 120px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            padding: 0 20px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #333;
            text-decoration: none;
            font-weight: 500;
        }
        .back-link:hover {
            color: #ff6b6b;
        }
        .checkout-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }
        .checkout-main h1 {
            margin-bottom: 20px;
            font-size: 28px;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .order-summary-section, .billing-section {
            background: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .order-summary-section h3, .billing-section h3 {
            margin-bottom: 20px;
            font-size: 18px;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: 'Arial', sans-serif;
        }
        .form-group textarea {
            resize: vertical;
        }
        .form-group.checkbox {
            display: flex;
            align-items: center;
        }
        .form-group.checkbox input {
            width: auto;
            margin-right: 10px;
        }
        .btn-place-order {
            width: 100%;
            padding: 12px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
        }
        .btn-place-order:hover {
            background-color: #555;
        }
        .checkout-sidebar {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            height: fit-content;
        }
        .price-breakdown h3 {
            margin-bottom: 20px;
            font-size: 18px;
        }
        .breakdown-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .breakdown-row.total {
            border-bottom: none;
            font-weight: bold;
            font-size: 16px;
            margin-top: 10px;
        }
        footer {
            background: #000;
            color: white;
            text-align: center;
            padding: 30px;
            margin-top: 50px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="top-bar">PREFER QUALITY OVER QUANTITY</div>
    
    <nav>
        <div class="logo">LIFE WEAR</div>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="products.php?category=men">Men</a></li>
            <li><a href="products.php?category=women">Women</a></li>
            <li><a href="products.php?category=winter">Winter</a></li>
        </ul>
        <div style="display: flex; gap: 15px;">
            <a href="cart.php" title="Shopping Cart"><i class="fas fa-shopping-bag"></i></a>
            <a href="logout.php" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </nav>

    <div class="checkout-container">
        <a href="cart.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Cart</a>

        <div class="checkout-content">
            <div class="checkout-main">
                <h1>Checkout</h1>

                <?php if($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="order-summary-section">
                    <h3>Order Summary</h3>
                    <div class="order-items">
                        <?php foreach($cart_items as $item): ?>
                        <div class="order-item">
                            <span><?php echo $item['quantity']; ?>x <?php echo 'Product ID: ' . $item['product_id']; ?></span>
                            <span>Tk. <?php echo $item['price'] * $item['quantity']; ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="billing-section">
                    <h3>Billing Information</h3>
                    <form method="POST" class="checkout-form">
                        <div class="form-group">
                            <label>Email Address:</label>
                            <input type="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label>Full Name:</label>
                            <input type="text" name="name" required>
                        </div>

                        <div class="form-group">
                            <label>Phone Number:</label>
                            <input type="tel" name="phone" required>
                        </div>

                        <div class="form-group">
                            <label>Address:</label>
                            <textarea name="address" rows="3" required></textarea>
                        </div>

                        <div class="form-group">
                            <label>City:</label>
                            <input type="text" name="city" required>
                        </div>

                        <div class="form-group">
                            <label>Postal Code:</label>
                            <input type="text" name="postal" required>
                        </div>

                        <div class="form-group">
                            <label>Payment Method:</label>
                            <select name="payment_method" required>
                                <option value="">Select Payment Method</option>
                                <option value="cod">Cash on Delivery</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="card">Credit/Debit Card</option>
                            </select>
                        </div>

                        <div class="form-group checkbox">
                            <input type="checkbox" name="agree_terms" required>
                            <label>I agree to the terms and conditions</label>
                        </div>

                        <button type="submit" name="place_order" class="btn-place-order">Place Order</button>
                    </form>
                </div>
            </div>

            <div class="checkout-sidebar">
                <div class="price-breakdown">
                    <h3>Price Breakdown</h3>
                    <div class="breakdown-row">
                        <span>Subtotal:</span>
                        <span>Tk. <?php echo $total; ?></span>
                    </div>
                    <div class="breakdown-row">
                        <span>Shipping:</span>
                        <span>Tk. 0</span>
                    </div>
                    <div class="breakdown-row">
                        <span>Tax:</span>
                        <span>Tk. 0</span>
                    </div>
                    <div class="breakdown-row total">
                        <span>Total:</span>
                        <span>Tk. <?php echo $total; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Life Wear. All rights reserved.</p>
        <div style="margin-top:10px; font-size:16px;">
            <i class="fab fa-facebook"></i> &nbsp; <i class="fab fa-instagram"></i> &nbsp; <i class="fab fa-tiktok"></i>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
