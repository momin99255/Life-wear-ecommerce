# Life Wear - Full Stack E-Commerce Platform

A complete, professional e-commerce website for clothing and accessories. Built with PHP, MySQL, and vanilla JavaScript.

![Version](https://img.shields.io/badge/version-1.0.0-blue)
![License](https://img.shields.io/badge/license-MIT-green)
![Status](https://img.shields.io/badge/status-Active-brightgreen)

---

## 📋 Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
- [Usage](#usage)
- [Admin Panel](#admin-panel)
- [Deployment](#deployment)
- [Contributors](#contributors)

---

## ✨ Features

### 👥 User Features
- ✅ User Registration & Login (Secure password hashing)
- ✅ Browse Products by Category
- ✅ Product Search Functionality
- ✅ Product Details Page
- ✅ Shopping Cart Management
- ✅ Checkout Process
- ✅ Order History
- ✅ Size Selection
- ✅ Responsive Design

### 🛡️ Admin Features
- ✅ Complete Admin Dashboard
- ✅ Product Management (Add/Edit/Delete)
- ✅ User Management
- ✅ Order Management
- ✅ View Customer Orders
- ✅ Stock Management

### 🔒 Security Features
- ✅ Password Hashing (password_hash)
- ✅ Prepared Statements (SQL Injection Prevention)
- ✅ Session Management
- ✅ Role-Based Access Control

---

## 🛠️ Tech Stack

| Component | Technology |
|-----------|-----------|
| **Frontend** | HTML5, CSS3, JavaScript (Vanilla) |
| **Backend** | PHP 7.4+ |
| **Database** | MySQL 5.7+ |
| **Server** | Apache (XAMPP/Hosting) |
| **Icons** | Font Awesome 6.0 |
| **Images** | Local (images folder) |

---

## 📁 Project Structure

```
Web Project/
│
├── 📄 Core Files
│   ├── index.php                 # Homepage
│   ├── db.php                    # Database connection
│   ├── login.php                 # User login
│   ├── register.php              # User registration
│   ├── logout.php                # User logout
│   └── generate-hash.php         # Password hashing utility
│
├── 🛍️ Shopping Files
│   ├── products.php              # Products listing & filtering
│   ├── product-detail.php        # Single product detail page
│   ├── cart.php                  # Shopping cart
│   ├── checkout.php              # Checkout page
│   └── order-success.php         # Order confirmation
│
├── 👨‍💼 Admin Files
│   ├── admin.php                 # Admin dashboard (Products)
│   ├── admin-users.php           # Manage users
│   ├── admin-orders.php          # Manage orders
│   └── admin_backup/             # Admin backup files
│
├── 📁 Folders
│   ├── css/                      # Stylesheets (admin.css, style.css, etc.)
│   ├── js/                       # JavaScript files (script.js)
│   ├── uploads/                  # Product images (user uploads)
│   ├── images/                   # Category images (mens.jpg, womens.jpg, etc.)
│   └── admin_backup/             # Admin backup folder
│
├── 🗄️ Database
│   └── database.sql              # Database schema & sample data
│
├── 📖 Documentation
│   ├── README.md                 # This file
│   └── favicon.png               # Website favicon
│
└── 🔑 Configuration
    └── .htaccess                 # Server configuration (optional)
```

---

## 🚀 Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache (XAMPP/Wamp)
- Modern web browser

### Step 1: Setup Local Environment

```bash
# Using XAMPP:
1. Download XAMPP from apache.org
2. Install & Start Apache + MySQL
3. Navigate to: C:\xampp\htdocs
4. Extract project here
```

### Step 2: Create Database

```sql
-- Using phpMyAdmin or MySQL CLI:
CREATE DATABASE lifewear_db;
CREATE USER 'lifewear_user'@'localhost' IDENTIFIED BY 'password123';
GRANT ALL PRIVILEGES ON lifewear_db.* TO 'lifewear_user'@'localhost';
FLUSH PRIVILEGES;
```

### Step 3: Import Database Schema

```
1. Open phpMyAdmin
2. Select 'lifewear_db'
3. Click 'Import' tab
4. Upload 'database.sql'
5. Click 'Go'
```

### Step 4: Configure Database Connection

Edit `db.php`:

```php
<?php
$servername = "localhost";
$username = "lifewear_user";
$password = "password123";
$dbname = "lifewear_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

### Step 5: Add Category Images

Create `images` folder and add 4 images:
- `mens.jpg`
- `womens.jpg`
- `shirt.jpg`
- `accessories.jpg`

### Step 6: Start Application

```
1. Open: http://localhost/web%20project/
2. Register new account or login
3. Browse products and shop!
```

---

## ⚙️ Configuration

### Database Setup

**Tables:**

1. **users** - User accounts
   - id, name, email, password, role, created_at

2. **products** - Product catalog
   - id, name, category, subcategory, price, description, image, stock

3. **cart** - Shopping cart
   - id, user_id, product_id, quantity, added_at

4. **orders** - Orders
   - id, user_id, total_price, status, created_at

5. **order_items** - Order items
   - id, order_id, product_id, quantity, price

### Default Admin Credentials

```
Email: admin@lifewear.com
Password: admin123
Role: admin
```

⚠️ **Change these credentials after first login!**

---

## 💻 Usage

### User Journey

```
Homepage → Browse Products → View Details → Add to Cart → Checkout → Order Confirmation
```

### Categories
- **MEN'S** - Men's clothing products
- **WOMEN** - Women's clothing products
- **SHIRT** - All shirt products
- **ACCESSORIES** - Accessories & bags

### Search
- Search by product name
- Search by product description

---

## 👨‍💼 Admin Panel

### Access Admin Panel

```
1. Login: admin@lifewear.com / admin123
2. Navigate to: http://localhost/web%20project/admin.php
```

### Admin Features

- ✅ Add new products
- ✅ Edit existing products
- ✅ Delete products
- ✅ View all users
- ✅ View all orders
- ✅ View order details
- ✅ Manage order status

---

## 🌐 Deployment

### Recommended Hosting

| Provider | Cost | Features |
|----------|------|----------|
| Infinity Free | Free | PHP, MySQL, SSL, FTP |
| Hostinger | $3/mo | PHP, MySQL, SSL, Domains |
| SiteGround | $2.99/mo | PHP, MySQL, SSL, Support |

### Deployment Steps

**Using Infinity Free (Free):**

```
1. Sign up at infinityfree.net
2. Create hosting account
3. Use FTP to upload files
4. Create MySQL database
5. Import database.sql
6. Update db.php
7. Live at: yourusername.infinityfree.app
```

**Using Hostinger (Paid):**

```
1. Sign up at hostinger.com
2. Choose PHP hosting
3. Access cPanel
4. Create MySQL database
5. Use FTP to upload files
6. Import database.sql
7. Update db.php
8. Link custom domain
```

---

## 🔐 Security Features

### Password Security
- Password hashing using password_hash()
- Secure password verification

### SQL Injection Prevention
- Prepared statements for all queries
- Input validation and sanitization

### Session Management
- Secure session handling
- Role-based access control

---

## 📱 Responsive Design

- ✅ Mobile-friendly interface
- ✅ Tablet optimized
- ✅ Desktop enhanced
- ✅ Cross-browser compatible

---

## 🐛 Troubleshooting

| Issue | Solution |
|-------|----------|
| Database Connection Error | Check db.php credentials |
| Images Not Loading | Verify uploads/ folder & permissions |
| Cart Not Working | Clear browser cache, login again |
| Admin Not Accessible | Check user role in database |

---

## 📊 File Descriptions

### Core Pages

| File | Purpose |
|------|---------|
| `index.php` | Homepage with featured products |
| `products.php` | Products listing with search & filters |
| `product-detail.php` | Single product view |
| `cart.php` | Shopping cart |
| `checkout.php` | Order placement |
| `order-success.php` | Order confirmation |

### Admin Pages

| File | Purpose |
|------|---------|
| `admin.php` | Product management dashboard |
| `admin-users.php` | User management |
| `admin-orders.php` | Order management |

---

## 👥 Contributors

**Development Team:** Web Development Team  
**Year:** 2026

---

## 📄 License

This project is open source and available under the MIT License.

---

## 🎯 Future Enhancements

- [ ] Email notifications
- [ ] Payment gateway integration
- [ ] Product reviews & ratings
- [ ] Wishlist feature
- [ ] Mobile app version
- [ ] Advanced analytics
- [ ] SMS notifications

---

**Happy Shopping! 🛍️**

For deployment help or issues, refer to the installation guide above.

4. **Access Application**
   - Open browser and go to: `http://localhost/Web Project/`

## Usage

### For Users
1. **Create Account**: Click "Login" → "Register here"
2. **Browse Products**: View products on home page
3. **View Details**: Click on any product to see full details
4. **Add to Cart**: Click "Add to Cart" (requires login)
5. **Checkout**: Go to cart and proceed to checkout
6. **Order**: Place order with shipping details
7. **Track Orders**: View order history in account

### For Admin
1. **Login as Admin**: Use admin account (create one with role='admin')
2. **Dashboard**: View key statistics
3. **Manage Products**: Add, edit, delete products
4. **Manage Orders**: View and track orders
5. **Manage Users**: View user information

### Default Admin Creation
To create an admin user, insert directly into database:
```sql
INSERT INTO users (name, email, password, role) 
VALUES ('Admin User', 'admin@example.com', PASSWORD('password123'), 'admin');
```

Or use phpMyAdmin to add with `password_hash('password123', PASSWORD_DEFAULT)` as the hashed password.

## Key Technologies

- **Backend**: PHP (procedural with MySQLi)
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Icons**: Font Awesome 6.0
- **Fonts**: Google Fonts (Poppins)
- **Authentication**: Session-based with password hashing

## Security Features

- Password hashing using `password_hash()`
- Prepared statements to prevent SQL injection
- Session-based authentication
- Role-based access control (RBAC)
- Input validation and sanitization

## Features Roadmap

- [ ] Payment gateway integration
- [ ] Product reviews and ratings
- [ ] Wishlist functionality
- [ ] Email notifications
- [ ] SMS order tracking
- [ ] Advanced search and filters
- [ ] Product recommendations
- [ ] Coupon and discount system
- [ ] API endpoints

## Troubleshooting

### Database Connection Error
- Check MySQL is running in XAMPP
- Verify database credentials in `db.php`
- Ensure `ecommerce_db` database exists

### Login Issues
- Clear browser cookies
- Check if user exists in database
- Verify password is correct

### Cart Not Working
- Ensure user is logged in
- Check if JavaScript is enabled
- Verify session is active

### Admin Access Denied
- Check if user role is set to 'admin'
- Clear session and login again
- Verify database record

## Contact & Support

For issues or questions, please contact the development team.

---

**Version**: 1.0  
**Last Updated**: January 2026  
**Developed by**: Development Team
