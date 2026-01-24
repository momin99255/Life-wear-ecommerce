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

// UPDATE ORDER STATUS
if(isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = trim($_POST['status']);
    
    $sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $order_id);
    if($stmt->execute()) {
        $success = 'Order status updated!';
    }
}

// DELETE ORDER
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM orders WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if($stmt->execute()) {
        $success = 'Order deleted!';
    }
}

// GET ALL ORDERS WITH USER INFO
$orders_sql = "SELECT o.id, o.user_id, o.total_price, o.status, o.created_at, u.name, u.email 
               FROM orders o 
               JOIN users u ON o.user_id = u.id 
               ORDER BY o.created_at DESC";
$orders = $conn->query($orders_sql)->fetch_all(MYSQLI_ASSOC);

// GET ORDER ITEMS IF VIEWING DETAILS
$order_items = [];
if(isset($_GET['view'])) {
    $view_order_id = intval($_GET['view']);
    $items_sql = "SELECT oi.*, p.name as product_name 
                  FROM order_items oi 
                  JOIN products p ON oi.product_id = p.id 
                  WHERE oi.order_id = ?";
    $items_stmt = $conn->prepare($items_sql);
    $items_stmt->bind_param("i", $view_order_id);
    $items_stmt->execute();
    $order_items = $items_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Orders</title>
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
            max-width: 1200px;
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
        
        .table-section {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
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
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 12px;
            text-align: center;
            width: 90px;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .btn {
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
        
        .btn-view {
            background: #17a2b8;
            color: white;
        }
        
        .btn-view:hover {
            background: #138496;
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        
        .btn-delete:hover {
            background: #c82333;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 8px;
            width: 600px;
            max-height: 600px;
            overflow-y: auto;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: #000;
        }
        
        .order-detail-item {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .form-row {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        button {
            background: #000;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        
        button:hover {
            background: #333;
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
        <h2>📦 All Orders</h2>
        
        <?php if($success): ?>
            <div class="alert success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Orders Table -->
        <div class="table-section">
            <h3>Orders (<?php echo count($orders); ?>)</h3>
            
            <?php if(count($orders) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo $order['name']; ?></td>
                            <td><?php echo $order['email']; ?></td>
                            <td>Tk. <?php echo number_format($order['total_price'], 2); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                    <?php echo strtoupper($order['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                            <td>
                                <button class="btn btn-view" onclick="viewOrder(<?php echo $order['id']; ?>)">View</button>
                                <a href="admin-orders.php?delete=<?php echo $order['id']; ?>" class="btn btn-delete" onclick="return confirm('Delete this order?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; padding: 40px; color: #999;">No orders found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal for Order Details and Status Update -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3 style="margin-bottom: 20px;">Order Details</h3>
            <div id="orderDetails"></div>
        </div>
    </div>

    <script>
        function viewOrder(orderId) {
            const modal = document.getElementById('orderModal');
            const orderDetails = document.getElementById('orderDetails');
            
            fetch(`admin-orders.php?view=${orderId}`)
                .then(response => response.text())
                .then(html => {
                    // Parse and extract order items from the response
                    orderDetails.innerHTML = `
                        <div class="order-detail-item">
                            <strong>Order ID:</strong> #${orderId}
                        </div>
                        <div id="items"></div>
                        <form method="POST" style="margin-top: 20px;">
                            <div class="form-row">
                                <input type="hidden" name="order_id" value="${orderId}">
                                <select name="status">
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                                <button type="submit" name="update_status">Update Status</button>
                            </div>
                        </form>
                    `;
                    
                    modal.style.display = 'block';
                });
        }
        
        function closeModal() {
            document.getElementById('orderModal').style.display = 'none';
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('orderModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
    
    <script src="js/script.js"></script>
</body>
</html>