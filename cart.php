<?php
include 'db.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle remove from cart
if(isset($_GET['remove'])) {
    $item_id = intval($_GET['remove']);
    $delete_sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("ii", $item_id, $user_id);
    $delete_stmt->execute();
    header('Location: cart.php');
    exit;
}

// Handle update quantity
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_cart'])) {
    $cart_id = intval($_POST['cart_id']);
    $quantity = intval($_POST['quantity']);
    
    if($quantity > 0) {
        $update_sql = "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("iii", $quantity, $cart_id, $user_id);
        $update_stmt->execute();
    }
    header('Location: cart.php');
    exit;
}

// Get cart items
$cart_sql = "SELECT c.*, p.name, p.price, p.image 
             FROM cart c 
             JOIN products p ON c.product_id = p.id 
             WHERE c.user_id = ?
             ORDER BY c.added_at DESC";
$cart_stmt = $conn->prepare($cart_sql);
$cart_stmt->bind_param("i", $user_id);
$cart_stmt->execute();
$cart_items = $cart_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$total = 0;
foreach($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="favicon.png">
    <title>Shopping Cart - Life Wear</title>
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
        
        .cart-container {
            margin-top: 120px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            padding: 0 20px;
        }
        .cart-container h1 {
            margin-bottom: 30px;
            font-size: 28px;
        }
        .cart-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .cart-table thead {
            background-color: #f0f0f0;
        }
        .cart-table th, .cart-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .product-cell {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .product-cell img {
            width: 80px;
            height: 80px;
            object-fit: cover;
        }
        .qty-form {
            display: flex;
            gap: 5px;
            align-items: center;
        }
        .qty-form input {
            width: 60px;
            padding: 5px;
            border: 1px solid #ddd;
        }
        .btn-update {
            padding: 5px 10px;
            background-color: #333;
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 3px;
        }
        .btn-remove {
            color: #ff6b6b;
            text-decoration: none;
            cursor: pointer;
        }
        .cart-summary {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            height: fit-content;
        }
        .cart-summary h3 {
            margin-bottom: 20px;
            font-size: 18px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .summary-row.total {
            border-bottom: none;
            font-weight: bold;
            font-size: 18px;
            margin-top: 10px;
        }
        .btn-checkout, .btn-continue {
            display: block;
            width: 100%;
            padding: 12px;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
            cursor: pointer;
            border: none;
            font-size: 16px;
            font-weight: bold;
        }
        .btn-checkout {
            background-color: #333;
            color: #fff;
        }
        .btn-checkout:hover {
            background-color: #555;
        }
        .btn-continue {
            background-color: #ddd;
            color: #333;
        }
        .btn-continue:hover {
            background-color: #ccc;
        }
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            background: #fff;
            border-radius: 8px;
        }
        .empty-cart i {
            font-size: 60px;
            color: #ccc;
            margin-bottom: 20px;
        }
        .empty-cart h2 {
            margin: 20px 0;
            font-size: 24px;
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
        <div class="logo">MOMIN POINT</div>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="products.php?category=men">Men</a></li>
            <li><a href="products.php?category=women">Women</a></li>
            <li><a href="products.php?category=winter">Winter</a></li>
        </ul>
        <div style="display: flex; gap: 15px;">
            <a href="cart.php" title="Shopping Cart" style="color: #f1c40f;"><i class="fas fa-shopping-bag"></i></a>
            <a href="logout.php" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </nav>

    <div class="cart-container">
        <h1>Shopping Cart</h1>
        
        <?php if(count($cart_items) > 0): ?>
            <div class="cart-content">
                <div class="cart-items">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($cart_items as $item): ?>
                            <tr>
                                <td class="product-cell">
                                    <img src="<?php echo !empty($item['image']) ? 'uploads/' . $item['image'] : 'https://via.placeholder.com/80x80'; ?>" alt="<?php echo $item['name']; ?>">
                                    <span><?php echo $item['name']; ?></span>
                                </td>
                                <td>Tk. <?php echo $item['price']; ?></td>
                                <td>
                                    <form method="POST" class="qty-form">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="100">
                                        <button type="submit" name="update_cart" class="btn-update">Update</button>
                                    </form>
                                </td>
                                <td>Tk. <?php echo $item['price'] * $item['quantity']; ?></td>
                                <td>
                                    <a href="cart.php?remove=<?php echo $item['id']; ?>" class="btn-remove" onclick="return confirm('Remove this item?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="cart-summary">
                    <h3>Order Summary</h3>
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span>Tk. <?php echo $total; ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span>Tk. 0</span>
                    </div>
                    <div class="summary-row">
                        <span>Tax:</span>
                        <span>Tk. 0</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span>Tk. <?php echo $total; ?></span>
                    </div>
                    <a href="checkout.php" class="btn-checkout">Proceed to Checkout</a>
                    <a href="index.php" class="btn-continue">Continue Shopping</a>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h2>Your cart is empty</h2>
                <p>Add some items to get started!</p>
                <a href="index.php" class="btn-continue">Start Shopping</a>
            </div>
        <?php endif; ?>
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
