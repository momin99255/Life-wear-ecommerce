<?php
include 'db.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if($order_id <= 0) {
    header('Location: index.php');
    exit;
}

// Get order details
$order_sql = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
$order_stmt->execute();
$order = $order_stmt->get_result()->fetch_assoc();

if(!$order) {
    header('Location: index.php');
    exit;
}

// Get order items
$items_sql = "SELECT oi.*, p.name 
              FROM order_items oi 
              JOIN products p ON oi.product_id = p.id 
              WHERE oi.order_id = ?";
$items_stmt = $conn->prepare($items_sql);
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$order_items = $items_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="favicon.png">
    <title>Order Successful - Life Wear</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/success.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="top-bar">PREFER QUALITY OVER QUANTITY</div>

    <div class="hero-wrapper">
        <nav id="navbar">
            <div class="logo">LIFE WEAR</div>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="products.php?category=men">Men</a></li>
                <li><a href="products.php?category=women">Women</a></li>
                <li><a href="products.php?category=winter">Winter</a></li>
            </ul>
            
            <div class="icons">
                <i class="fas fa-search" onclick="toggleSearch()"></i>
                <a href="cart.php"><i class="fas fa-shopping-bag"></i></a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i></a>

                <div class="search-box" id="searchBox">
                    <form action="index.php" method="GET" style="display:flex;">
                        <input type="text" name="search" placeholder="Search...">
                        <button type="submit">GO</button>
                    </form>
                </div>
            </div>
        </nav>
    </div>

    <div class="success-container">
        <div class="success-card">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>

            <h1>Order Placed Successfully!</h1>
            <p class="order-number">Order ID: #<?php echo $order_id; ?></p>

            <div class="order-details">
                <h3>Order Summary</h3>
                <table class="details-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($order_items as $item): ?>
                        <tr>
                            <td><?php echo $item['name']; ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>Tk. <?php echo $item['price']; ?></td>
                            <td>Tk. <?php echo $item['quantity'] * $item['price']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="order-total">
                    <h3>Total Amount: Tk. <?php echo $order['total_price']; ?></h3>
                    <p>Status: <span class="status-badge"><?php echo ucfirst($order['status']); ?></span></p>
                </div>
            </div>

            <div class="next-steps">
                <h3>What's Next?</h3>
                <ul>
                    <li>You will receive an email confirmation shortly</li>
                    <li>Your order is being processed</li>
                    <li>You can track your order from your account</li>
                </ul>
            </div>

            <div class="action-buttons">
                <a href="index.php" class="btn-primary">Continue Shopping</a>
                <a href="order-details.php?order_id=<?php echo $order_id; ?>" class="btn-secondary">View Order Details</a>
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
