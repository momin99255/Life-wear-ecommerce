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

// DELETE USER
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Don't delete admin account
    $check_role = $conn->query("SELECT role FROM users WHERE id = $id")->fetch_assoc();
    if($check_role['role'] == 'admin') {
        $error = 'Cannot delete admin account!';
    } else {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        if($stmt->execute()) {
            $success = 'User deleted successfully!';
        }
    }
}

// GET ALL USERS (excluding admin)
$users = $conn->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Users</title>
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
        
        .delete-btn {
            padding: 6px 12px;
            font-size: 12px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            background: #dc3545;
            color: white;
            text-decoration: none;
            display: inline-block;
            transition: 0.3s;
        }
        
        .delete-btn:hover {
            background: #c82333;
        }
        
        .badge {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge.admin {
            background: #ffc107;
            color: #000;
        }
        
        .badge.user {
            background: #007bff;
            color: white;
        }
        
        .empty-message {
            text-align: center;
            padding: 40px;
            color: #999;
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
        <h2>👥 Manage Users</h2>
        
        <?php if($success): ?>
            <div class="alert success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Users Table -->
        <div class="table-section">
            <h3>📋 All Users (<?php echo count($users); ?>)</h3>
            
            <?php if(count($users) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $u): ?>
                        <tr>
                            <td>#<?php echo $u['id']; ?></td>
                            <td><?php echo $u['name']; ?></td>
                            <td><?php echo $u['email']; ?></td>
                            <td>
                                <?php if($u['role'] == 'admin'): ?>
                                    <span class="badge admin">ADMIN</span>
                                <?php else: ?>
                                    <span class="badge user">USER</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                            <td>
                                <?php if($u['role'] != 'admin'): ?>
                                    <a href="admin-users.php?delete=<?php echo $u['id']; ?>" class="delete-btn" onclick="return confirm('Delete this user?')">Delete</a>
                                <?php else: ?>
                                    <span style="color: #999; font-size: 12px;">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-message">No users found.</div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="js/script.js"></script>
</body>
</html>