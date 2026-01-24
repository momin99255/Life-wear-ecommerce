<?php
include 'db.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_sql = "SELECT role FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $_SESSION['user_id']);
$user_stmt->execute();
$user_role = $user_stmt->get_result()->fetch_assoc();

if($user_role['role'] != 'admin') {
    header('Location: index.php');
    exit;
}

$success = '';
$error = '';

// DELETE PRODUCT
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if($stmt->execute()) {
        $success = 'Product deleted!';
    }
}

// ADD/EDIT PRODUCT
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $subcategory = trim($_POST['subcategory'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $image = '';
    
    // Handle image upload
    if(isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $file_name = $_FILES['image']['name'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_size = $_FILES['image']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if(in_array($file_ext, $allowed) && $file_size < 5000000) {
            $new_name = time() . '.' . $file_ext;
            $upload_path = 'uploads/' . $new_name;
            
            if(move_uploaded_file($file_tmp, $upload_path)) {
                $image = $new_name;
            } else {
                $error = 'Image upload failed';
            }
        } else {
            $error = 'Invalid image file (max 5MB, jpg/png/gif/webp)';
        }
    }
    
    if($name && $category && $subcategory && $price > 0 && !$error) {
        if(isset($_POST['edit_id'])) {
            $id = intval($_POST['edit_id']);
            if($image) {
                $sql = "UPDATE products SET name=?, category=?, subcategory=?, price=?, description=?, image=? WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssdsssi", $name, $category, $subcategory, $price, $description, $image, $id);
            } else {
                $sql = "UPDATE products SET name=?, category=?, subcategory=?, price=?, description=? WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssdsi", $name, $category, $subcategory, $price, $description, $id);
            }
            if($stmt->execute()) {
                $success = 'Product updated!';
            }
        } else {
            if($image) {
                $sql = "INSERT INTO products (name, category, subcategory, price, description, image) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssdsss", $name, $category, $subcategory, $price, $description, $image);
            } else {
                $sql = "INSERT INTO products (name, category, subcategory, price, description) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssdss", $name, $category, $subcategory, $price, $description);
            }
            if($stmt->execute()) {
                $success = 'Product added!';
            }
        }
    } else {
        if(!$error) {
            $error = 'Fill all fields with valid data';
        }
    }
}

// GET ALL PRODUCTS
$products = $conn->query("SELECT * FROM products ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
$edit_product = null;

if(isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $edit_product = $conn->query("SELECT * FROM products WHERE id = $id")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Manage Products</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', Arial; }
        
        body { 
            background: #f0f0f0;
            padding-top: 70px;
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
            transition: top 0.4s ease-in-out;
        }
        
        nav.nav-hide {
            top: -100px;
        }
        
        nav .logo {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 1px;
            color: #fff;
        }
        
        nav ul {
            display: flex;
            list-style: none;
            gap: 30px;
            align-items: center;
            height: 100%;
        }
        
        nav a {
            color: white;
            text-decoration: none;
            font-size: 12px;
            transition: 0.3s;
            opacity: 0.8;
            position: relative;
        }
        
        nav a:hover {
            opacity: 1;
            color: #f1c40f;
        }
        
        nav a::after {
            content: '';
            position: absolute;
            width: 0%;
            height: 2px;
            bottom: -5px;
            left: 0;
            background-color: #fff;
            transition: width 0.3s ease-in-out;
        }
        
        nav a:hover::after {
            width: 100%;
        }
        
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        
        h2 {
            font-size: 24px;
            margin-bottom: 25px;
            color: #333;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .form-section {
            background: white;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .form-section h3 {
            font-size: 18px;
            margin-bottom: 20px;
            color: #333;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        
        input, select, textarea {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: 'Poppins', Arial;
            font-size: 14px;
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #000;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        
        textarea {
            grid-column: 1 / -1;
            min-height: 80px;
            resize: vertical;
        }
        
        .form-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        button {
            background: #000;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: 0.3s;
        }
        
        button:hover {
            background: #333;
        }
        
        .cancel-link {
            background: #666;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            transition: 0.3s;
        }
        
        .cancel-link:hover {
            background: #555;
        }
        
        .table-section {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .table-section h3 {
            font-size: 18px;
            margin-bottom: 20px;
            color: #333;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: #f5f5f5;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #000;
            font-size: 14px;
        }
        
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }
        
        tr:hover {
            background: #f9f9f9;
        }
        
        .edit-btn, .delete-btn {
            padding: 6px 12px;
            margin-right: 5px;
            font-size: 12px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: 0.3s;
        }
        
        .edit-btn {
            background: #007bff;
            color: white;
        }
        
        .edit-btn:hover {
            background: #0056b3;
        }
        
        .delete-btn {
            background: #dc3545;
            color: white;
        }
        
        .delete-btn:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav id="navbar">
        <div class="logo">LIFE WEAR</div>
        <ul>
            <li><a href="admin.php">Products</a></li>
            <li><a href="admin-orders.php">Orders</a></li>
            <li><a href="admin-users.php">Users</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <h2>Manage Products</h2>
        
        <?php if($success): ?>
            <div class="alert success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Add/Edit Form -->
        <div class="form-section">
            <h3><?php echo isset($_GET['edit']) ? '✏️ Edit Product' : '➕ Add New Product'; ?></h3>
            <form method="POST" enctype="multipart/form-data">
                <?php if(isset($_GET['edit'])): ?>
                    <input type="hidden" name="edit_id" value="<?php echo $edit_product['id']; ?>">
                <?php endif; ?>
                
                <div class="form-row">
                    <input type="text" name="name" placeholder="Product Name" value="<?php echo htmlspecialchars($edit_product['name'] ?? ''); ?>" required>
                    <select name="category" id="category" onchange="updateSubcategory()" required>
                        <option value="">Select Category</option>
                        <option value="men" <?php echo ($edit_product['category'] ?? '') == 'men' ? 'selected' : ''; ?>>Men</option>
                        <option value="women" <?php echo ($edit_product['category'] ?? '') == 'women' ? 'selected' : ''; ?>>Women</option>
                        <option value="winter" <?php echo ($edit_product['category'] ?? '') == 'winter' ? 'selected' : ''; ?>>Winter</option>
                    </select>
                    <select name="subcategory" id="subcategory" required>
                        <option value="">Select Subcategory</option>
                    </select>
                    <input type="number" name="price" placeholder="Price (Tk.)" step="0.01" value="<?php echo $edit_product['price'] ?? ''; ?>" required>
                </div>
                
                <div class="form-row">
                    <textarea name="description" placeholder="Description"><?php echo htmlspecialchars($edit_product['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-row">
                    <input type="file" name="image" accept="image/*" placeholder="Upload Product Image">
                    <?php if(isset($edit_product['image']) && $edit_product['image']): ?>
                        <div style="padding: 10px; background: #f0f0f0; border-radius: 5px;">
                            Current: <img src="uploads/<?php echo $edit_product['image']; ?>" alt="Product" style="max-width: 100px; max-height: 100px;">
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-buttons">
                    <button type="submit"><?php echo isset($_GET['edit']) ? '💾 Update' : '➕ Add'; ?></button>
                    <?php if(isset($_GET['edit'])): ?>
                        <a href="admin.php" class="cancel-link">✕ Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Products Table -->
        <div class="table-section">
            <h3>📦 All Products (<?php echo count($products); ?>)</h3>
            
            <?php if(count($products) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $p): ?>
                        <tr>
                            <td>#<?php echo $p['id']; ?></td>
                            <td>
                                <?php if($p['image']): ?>
                                    <img src="uploads/<?php echo $p['image']; ?>" alt="Product" style="max-width: 60px; max-height: 60px; border-radius: 4px;">
                                <?php else: ?>
                                    <span style="color: #999;">No image</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $p['name']; ?></td>
                            <td><?php echo strtoupper($p['category']); ?></td>
                            <td>Tk. <?php echo number_format($p['price'], 2); ?></td>
                            <td>
                                <a href="admin.php?edit=<?php echo $p['id']; ?>" class="edit-btn">Edit</a>
                                <a href="admin.php?delete=<?php echo $p['id']; ?>" class="delete-btn" onclick="return confirm('Delete this product?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; padding: 40px; color: #999;">No products yet. Add your first product!</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const subcategoryMap = {
            men: ['tshirt', 'shirt', 'punjabi', 'accessories'],
            women: ['saree', 'tops', 'accessories'],
            winter: ['hoodie', 'jacket']
        };
        
        const subcategoryLabels = {
            tshirt: 'T-Shirt',
            shirt: 'Shirt',
            punjabi: 'Punjabi',
            accessories: 'Accessories',
            saree: 'Saree',
            tops: 'Tops',
            hoodie: 'Hoodie',
            jacket: 'Jacket'
        };
        
        function updateSubcategory() {
            const category = document.getElementById('category').value;
            const subcategorySelect = document.getElementById('subcategory');
            subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
            
            if(category && subcategoryMap[category]) {
                subcategoryMap[category].forEach(sub => {
                    const option = document.createElement('option');
                    option.value = sub;
                    option.text = subcategoryLabels[sub];
                    subcategorySelect.appendChild(option);
                });
            }
        }
        
        // Initialize on page load
        window.addEventListener('load', updateSubcategory);
    </script>
    
    <script src="js/script.js"></script>
</body>
</html>