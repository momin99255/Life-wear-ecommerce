<?php
include 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="favicon.png">
    <title>Plus Point Clone</title>
    <link rel="stylesheet" href="css/style.css">
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
                <a href="<?php echo isset($_SESSION['user_id']) ? 'cart.php' : 'login.php'; ?>"><i class="fas fa-shopping-bag"></i></a>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <?php 
                    $user_sql = "SELECT role FROM users WHERE id = ?";
                    $user_stmt = $conn->prepare($user_sql);
                    $user_stmt->bind_param("i", $_SESSION['user_id']);
                    $user_stmt->execute();
                    $user_role = $user_stmt->get_result()->fetch_assoc();
                    ?>
                    
                    <?php if($user_role['role'] == 'admin'): ?>
                        <a href="admin.php"><i class="fas fa-cog"></i></a>
                    <?php endif; ?>
                    
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i></a>
                <?php else: ?>
                    <a href="login.php"><i class="fas fa-user"></i></a>
                <?php endif; ?>

                <div class="search-box" id="searchBox">
                    <form action="products.php" method="GET" style="display:flex;">
                        <input type="text" name="search" placeholder="Search...">
                        <button type="submit">GO</button>
                    </form>
                </div>
            </div>
        </nav>

        <div class="hero-slider">
            <div class="slide active">
                <img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?ixlib=rb-1.2.1&auto=format&fit=crop&w=1500&q=80" alt="Banner 1">
                <div class="slide-content">
                    <h2 style="font-size: 50px; margin-bottom:10px;">WINTER '26</h2>
                    <a href="products.php" class="btn-white">SHOP NOW</a>
                </div>
            </div>
            <div class="slide">
                <img src="https://images.unsplash.com/photo-1512436991641-6745cdb1723f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1500&q=80" alt="Banner 2">
                <div class="slide-content">
                    <h2 style="font-size: 50px; margin-bottom:10px;">NEW COLLECTION</h2>
                    <a href="products.php" class="btn-white">EXPLORE</a>
                </div>
            </div>
        </div>
    </div>

    <div class="category-wrapper">
        <a href="products.php?category=men" class="cat-oval" style="text-decoration: none;">
            <img src="images/mens.jpg" alt="Men's Clothing">
            <div class="cat-btn">MEN'S</div>
        </a>
        <a href="products.php?category=women" class="cat-oval" style="text-decoration: none;">
            <img src="images/womens.jpg" alt="Women's Fashion">
            <div class="cat-btn">WOMEN</div>
        </a>
        <a href="products.php?subcategory=shirt" class="cat-oval" style="text-decoration: none;">
            <img src="images/shirt.jpg" alt="Shirts">
            <div class="cat-btn">SHIRT</div>
        </a>
        <a href="products.php?subcategory=accessories" class="cat-oval" style="text-decoration: none;">
            <img src="images/accessories.jpg" alt="Accessories Bag">
            <div class="cat-btn">ACCESSORIES</div>
        </a>
    </div>

    <div class="mid-banner">
        <img src="https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?ixlib=rb-1.2.1&auto=format&fit=crop&w=1500&q=80" alt="Big Sale">
    </div>

    <h2 class="section-title">FEATURED PRODUCTS</h2>
    <div class="product-grid">
        <?php
        $sql = "SELECT * FROM products LIMIT 4";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                ?>
                <a href="product-detail.php?id=<?php echo $row['id']; ?>" class="product-card">
                    <img src="<?php echo !empty($row['image']) ? 'uploads/' . $row['image'] : 'https://via.placeholder.com/300x400'; ?>">
                    <h3><?php echo $row['name']; ?></h3>
                    <div class="price">Tk. <?php echo $row['price']; ?></div>
                </a>
                <?php
            }
        } else {
             for($i=0; $i<4; $i++){
                echo '
                <div class="product-card">
                    <img src="https://via.placeholder.com/300x400/000000/ffffff?text=Product" alt="Product">
                    <h3>Sample Product</h3>
                    <div class="price">Tk. 2,500</div>
                </div>';
            }
        }
        ?>
    </div>

    <h2 class="section-title">NEW ARRIVALS</h2>
    <div class="scroll-container">
        <?php
        $new_sql = "SELECT id, name, price, image FROM products ORDER BY id DESC LIMIT 6";
        $new_result = $conn->query($new_sql);
        
        if ($new_result && $new_result->num_rows > 0) {
            while($new_row = $new_result->fetch_assoc()) {
                ?>
                <a href="product-detail.php?id=<?php echo $new_row['id']; ?>" class="scroll-item" style="text-decoration: none; color: inherit;">
                    <img src="<?php echo !empty($new_row['image']) ? 'uploads/' . $new_row['image'] : 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'; ?>">
                    <h3 style="font-size:14px; margin-top:5px;"><?php echo $new_row['name']; ?></h3>
                    <div style="font-size:13px; color:#555;">Tk. <?php echo $new_row['price']; ?></div>
                </a>
                <?php
            }
        } else {
            for($i=1; $i<=6; $i++): ?>
            <div class="scroll-item">
                <img src="https://images.unsplash.com/photo-1596755094514-f87e34085b2c?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80">
                <h3 style="font-size:14px; margin-top:5px;">Summer Collection</h3>
                <div style="font-size:13px; color:#555;">Tk. 1,499</div>
            </div>
            <?php endfor;
        }
        ?>
    </div>

    <div class="info-section">
        <div class="info-box">
            <h4>Free Shipping</h4>
            <p>On orders 5000+ BDT or all the time when signed in & subscribed to emails.</p>
        </div>
        <div class="info-box">
            <h4>Free Returns</h4>
            <p>We want you to be completely satisfied with your purchase. If your product does not fit, you can return it.</p>
        </div>
        <div class="info-box">
            <h4>Customer Care</h4>
            <p>Our Customer Care team is dedicated to providing you with quick, friendly, and reliable support.</p>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Plus Point Clone. Developed by Team.</p>
        <div style="margin-top:10px; font-size:16px;">
            <i class="fab fa-facebook"></i> &nbsp; <i class="fab fa-instagram"></i> &nbsp; <i class="fab fa-tiktok"></i>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>