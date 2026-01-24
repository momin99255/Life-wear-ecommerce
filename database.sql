-- Fresh Database Schema
DROP DATABASE IF EXISTS ecommerce_db;
CREATE DATABASE ecommerce_db;
USE ecommerce_db;

-- Users Table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    subcategory VARCHAR(50),
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cart Table
CREATE TABLE cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Orders Table
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order Items Table
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Sample Products
INSERT INTO products (name, category, subcategory, price, description, image) VALUES
-- Men
('White Shirt', 'men', 'shirt', 1200, 'Basic white cotton shirt', 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Blue T-Shirt', 'men', 'tshirt', 800, 'Comfortable blue t-shirt', 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Traditional Punjabi', 'men', 'punjabi', 2500, 'Traditional punjabi dress', 'https://images.unsplash.com/photo-1618895917637-8e8fada66cd1?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Premium Watch', 'men', 'accessories', 3000, 'Premium watch accessories', 'https://images.unsplash.com/photo-1523170335258-f5ed11844a49?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),

-- Women
('Red Saree', 'women', 'saree', 4000, 'Beautiful red silk saree', 'https://images.unsplash.com/photo-1609450220061-c8e05f8e51f2?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Black Top', 'women', 'tops', 1500, 'Trendy black tops', 'https://images.unsplash.com/photo-1515866152651-dbc61b4044a9?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Blue Bottom', 'women', 'tops', 2000, 'Comfortable blue bottom', 'https://images.unsplash.com/photo-1541099810657-40d6b5f55f15?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Gold Jewelry', 'women', 'accessories', 5000, 'Elegant gold accessories', 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),

-- Winter
('Black Jacket', 'winter', 'jacket', 5000, 'Warm winter jacket', 'https://images.unsplash.com/photo-1611312652856-da8042baf1f9?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Grey Hoodie', 'winter', 'hoodie', 3500, 'Cozy grey hoodie', 'https://images.unsplash.com/photo-1556821552-107fcfaa4caf?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Navy Blazer', 'winter', 'jacket', 6000, 'Formal navy blazers', 'https://images.unsplash.com/photo-1591047990941-d866834b24e5?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80');

-- Sample Admin User (Plain password - no hashing)
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@example.com', 'admin123', 'admin');
