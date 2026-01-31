<?php
include 'db.php';
$category = $_GET['category'] ?? 'all';
$subcategory = $_GET['subcategory'] ?? '';
$search = $_GET['search'] ?? '';

$sql = "SELECT * FROM products WHERE 1=1";

if($category != 'all' && empty($search)) {
    $sql .= " AND category = '" . $conn->real_escape_string($category) . "'";
}

if($subcategory && empty($search)) {
    $sql .= " AND (subcategory = '" . $conn->real_escape_string($subcategory) . "' OR category = '" . $conn->real_escape_string($subcategory) . "')";
}

if($search) {
    $search_term = $conn->real_escape_string($search);
    $sql .= " AND (name LIKE '%" . $search_term . "%' OR description LIKE '%" . $search_term . "%')";
}

$result = $conn->query($sql);
$products = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="favicon.png">
    <title><?php echo $search ? "Search: " . htmlspecialchars($search) : (ucfirst($subcategory ?: $category) . ' Products'); ?> - Life Wear</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="top-bar">PREFER QUALITY OVER QUANTITY</div>

    <nav id="navbar">
        <div class="logo">LIFE WEAR</div>
        <ul>
            <li><a href="index.php">Home</a></li>
            <?php if($category == 'men'): ?>
                <li><a href="products.php?category=men&subcategory=tshirt">T-Shirt</a></li>
                <li><a href="products.php?category=men&subcategory=shirt">Shirt</a></li>
                <li><a href="products.php?category=men&subcategory=punjabi">Punjabi</a></li>
                <li><a href="products.php?category=men&subcategory=accessories">Accessories</a></li>
            <?php elseif($category == 'women'): ?>
                <li><a href="products.php?category=women&subcategory=saree">Saree</a></li>
                <li><a href="products.php?category=women&subcategory=tops">Tops</a></li>
                <li><a href="products.php?category=women&subcategory=accessories">Accessories</a></li>
            <?php elseif($category == 'winter'): ?>
                <li><a href="products.php?category=winter&subcategory=hoodie">Hoodie</a></li>
                <li><a href="products.php?category=winter&subcategory=jacket">Jacket</a></li>
            <?php else: ?>
                <li><a href="products.php?category=men">Men</a></li>
                <li><a href="products.php?category=women">Women</a></li>
                <li><a href="products.php?category=winter">Winter</a></li>
            <?php endif; ?>
        </ul>
        <div class="icons">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i></a>
            <?php else: ?>
                <a href="login.php"><i class="fas fa-user"></i></a>
            <?php endif; ?>
        </div>
    </nav>

    <div style="max-width: 1200px; margin: 0 auto; padding: 120px 20px 0;">
        <h1><?php echo $search ? "Search Results for: <strong>" . htmlspecialchars($search) . "</strong>" : (ucfirst($subcategory ?: $category) . ' Products'); ?></h1>
        
        <div class="product-grid">
            <?php if(count($products) > 0): ?>
                <?php foreach($products as $product): ?>
                    <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="product-card" style="text-decoration: none; color: inherit;">
                        <img src="<?php echo !empty($product['image']) ? (strpos($product['image'], 'http') === 0 ? $product['image'] : 'uploads/' . $product['image']) : 'https://via.placeholder.com/300x400'; ?>">
                        <h3><?php echo $product['name']; ?></h3>
                        <p style="font-size: 12px; color: #666;"><?php echo substr($product['description'], 0, 40); ?></p>
                        <div class="price">Tk. <?php echo $product['price']; ?></div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No products found</p>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Life Wear. All rights reserved.</p>
    </footer>
    <script src="js/script.js"></script>
</body>
</html>
