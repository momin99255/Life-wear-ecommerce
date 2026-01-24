<?php
include 'db.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($product_id <= 0) {
    header('Location: index.php');
    exit;
}

$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if(!$product) {
    header('Location: index.php');
    exit;
}

$success_msg = '';
// Handle Add to Cart
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    if(!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    
    $quantity = intval($_POST['quantity'] ?? 1);
    $size = trim($_POST['size'] ?? '');
    $user_id = $_SESSION['user_id'];
    
    if($quantity > 0 && !empty($size)) {
        // Check if product already in cart
        $check_sql = "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $user_id, $product_id);
        $check_stmt->execute();
        $existing = $check_stmt->get_result()->fetch_assoc();
        
        if($existing) {
            // Update quantity
            $new_qty = $existing['quantity'] + $quantity;
            $update_sql = "UPDATE cart SET quantity = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ii", $new_qty, $existing['id']);
            $update_stmt->execute();
        } else {
            // Add new item to cart
            $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("iii", $user_id, $product_id, $quantity);
            $insert_stmt->execute();
        }
        
        $success_msg = 'Added to cart! Redirecting...';
        header('refresh:2;url=cart.php');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="favicon.png">
    <title><?php echo $product['name']; ?> - Life Wear</title>
    <link rel="stylesheet" href="css/style.css">
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
        
        .product-container {
            max-width: 1000px;
            margin: 120px auto 50px;
            padding: 0 20px;
        }
        
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            color: #000;
            text-decoration: none;
            font-size: 14px;
        }
        
        .back-btn:hover {
            color: #f1c40f;
        }
        
        .product-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            background: white;
            padding: 30px;
            border-radius: 8px;
        }
        
        .product-image {
            background: #f5f5f5;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 450px;
            overflow: hidden;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .product-details h1 {
            font-size: 28px;
            margin-bottom: 15px;
            font-weight: 700;
        }
        
        .category {
            display: inline-block;
            background: #f0f0f0;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        
        .price {
            font-size: 32px;
            color: #f1c40f;
            font-weight: 700;
            margin: 20px 0 25px 0;
        }
        
        .description {
            color: #555;
            line-height: 1.6;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 13px;
        }
        
        .form-group select,
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 13px;
            font-family: 'Poppins', Arial;
        }
        
        .form-group select:focus,
        .form-group input:focus {
            outline: none;
            border-color: #000;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        
        .btn-add-cart {
            width: 100%;
            background: #000;
            color: white;
            border: none;
            padding: 14px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-add-cart:hover {
            background: #333;
        }
        
        .btn-cart {
            display: inline-block;
            background: #f1c40f;
            color: #000;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            margin-top: 10px;
            text-align: center;
        }
        
        .btn-cart:hover {
            background: #e0b50f;
        }
        
        .login-msg {
            background: #f0f0f0;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
        
        .login-msg a {
            color: #f1c40f;
            font-weight: 600;
            text-decoration: none;
        }
        
        .success-msg {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 13px;
        }
        
        .product-info-box {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-top: 25px;
        }
        
        .product-info-box h3 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 12px;
        }
        
        .product-info-box ul {
            list-style: none;
        }
        
        .product-info-box li {
            padding: 8px 0;
            font-size: 13px;
            color: #555;
            border-bottom: 1px solid #eee;
        }
        
        footer {
            background: #000;
            color: white;
            text-align: center;
            padding: 30px;
            margin-top: 50px;
            font-size: 12px;
        }
        
        @media (max-width: 768px) {
            .product-wrapper {
                grid-template-columns: 1fr;
            }
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
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="cart.php" title="Shopping Cart"><i class="fas fa-shopping-bag"></i></a>
                <a href="logout.php" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
            <?php else: ?>
                <a href="login.php" title="Login"><i class="fas fa-user"></i></a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="product-container">
        <a href="index.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Home</a>
        
        <?php if(!empty($success_msg)): ?>
            <div class="success-msg"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        
        <div class="product-wrapper">
            <div class="product-image">
                <img src="<?php echo !empty($product['image']) ? (strpos($product['image'], 'http') === 0 ? $product['image'] : 'uploads/' . $product['image']) : 'https://via.placeholder.com/400x500'; ?>" alt="<?php echo $product['name']; ?>">
            </div>
            
            <div class="product-details">
                <h1><?php echo $product['name']; ?></h1>
                
                <div class="category">
                    <?php echo ucfirst($product['category']); ?> - <?php echo ucfirst($product['subcategory']); ?>
                </div>
                
                <div class="price">Tk. <?php echo $product['price']; ?></div>
                
                <p class="description"><?php echo $product['description']; ?></p>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <form method="POST" class="add-to-cart-form">
                        <div class="form-group">
                            <label for="size">Select Size *</label>
                            <select id="size" name="size" required>
                                <option value="">-- Choose Size --</option>
                                <option value="XS">XS (Extra Small)</option>
                                <option value="S">S (Small)</option>
                                <option value="M">M (Medium)</option>
                                <option value="L">L (Large)</option>
                                <option value="XL">XL (Extra Large)</option>
                                <option value="XXL">XXL (2XL)</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="quantity">Quantity *</label>
                            <input type="number" id="quantity" name="quantity" min="1" max="100" value="1" required>
                        </div>
                        
                        <button type="submit" name="add_to_cart" class="btn-add-cart">
                            <i class="fas fa-shopping-cart"></i> ADD TO CART
                        </button>
                    </form>
                    
                    <a href="cart.php" class="btn-cart">View Cart</a>
                <?php else: ?>
                    <div class="login-msg">
                        Please <a href="login.php">login</a> to add items to cart
                    </div>
                <?php endif; ?>
                
                <div class="product-info-box">
                    <h3>Product Information</h3>
                    <ul>
                        <li><strong>Category:</strong> <?php echo ucfirst($product['category']); ?></li>
                        <li><strong>Type:</strong> <?php echo ucfirst($product['subcategory']); ?></li>
                        <li><strong>Price:</strong> Tk. <?php echo $product['price']; ?></li>
                        <li><strong>Status:</strong> In Stock</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Life Wear. All rights reserved.</p>
    </footer>
</body>
</html>
