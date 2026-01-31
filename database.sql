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
-- Men - Shirts
('White Shirt', 'men', 'shirt', 1200, 'Basic white cotton shirt', 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Striped Shirt', 'men', 'shirt', 1400, 'Slim-fit striped shirt', 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Formal White Shirt', 'men', 'shirt', 2200, 'Premium formal shirt', 'https://images.unsplash.com/photo-1520975917966-0c10f6f3c9a6?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Linen Casual Shirt', 'men', 'shirt', 1800, 'Breathable linen casual shirt', 'https://images.unsplash.com/photo-1520975917965-4c7f9aa2b6b9?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),

-- Men - T-Shirts
('Blue T-Shirt', 'men', 'tshirt', 800, 'Comfortable blue t-shirt', 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Graphic Tee', 'men', 'tshirt', 700, 'Casual graphic t-shirt', 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('V-Neck T-Shirt', 'men', 'tshirt', 850, 'Soft v-neck cotton t-shirt', 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Polo Shirt', 'men', 'tshirt', 1200, 'Classic polo shirt', 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),

-- Men - Punjabi
('Traditional Punjabi', 'men', 'punjabi', 2500, 'Traditional punjabi dress', 'https://images.unsplash.com/photo-1618895917637-8e8fada66cd1?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Embroidered Punjabi', 'men', 'punjabi', 3000, 'Hand-embroidered punjabi', 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Silk Punjabi', 'men', 'punjabi', 4200, 'Luxury silk punjabi', 'https://images.unsplash.com/photo-1520975683670-9fe9a0d0b5af?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Casual Punjabi', 'men', 'punjabi', 2200, 'Everyday casual punjabi', 'https://images.unsplash.com/photo-1562158070-2c5b1382f667?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),

-- Accessories (mixed)
('Premium Watch', 'men', 'accessories', 3000, 'Premium watch accessories', 'https://images.unsplash.com/photo-1523170335258-f5ed11844a49?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Gold Jewelry', 'women', 'accessories', 5000, 'Elegant gold accessories', 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Leather Belt', 'men', 'accessories', 800, 'Genuine leather belt', 'https://images.unsplash.com/photo-1526178611993-0a1c5f8b8b41?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Sunglasses', 'women', 'accessories', 1200, 'Stylish sunglasses', 'https://images.unsplash.com/photo-1503342452485-86f7a6a3f8f7?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Leather Wallet', 'men', 'accessories', 900, 'Slim leather wallet', 'https://images.unsplash.com/photo-1555529771-61b33c4430b8?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),

-- Women - Sarees
('Red Saree', 'women', 'saree', 4000, 'Beautiful red silk saree', 'https://images.unsplash.com/photo-1609450220061-c8e05f8e51f2?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Floral Saree', 'women', 'saree', 3500, 'Lightweight floral saree', 'https://images.unsplash.com/photo-1520975921048-1f3ab5f1b3f1?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Silk Designer Saree', 'women', 'saree', 6500, 'Designer silk saree', 'https://images.unsplash.com/photo-1543163521-1bf539c5d032?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Party Wear Saree', 'women', 'saree', 4800, 'Glamorous party saree', 'https://images.unsplash.com/photo-1570111690804-8f6f5c0a9e4f?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),

-- Women - Tops
('Black Top', 'women', 'tops', 1500, 'Trendy black tops', 'https://images.unsplash.com/photo-1515866152651-dbc61b4044a9?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Blue Bottom', 'women', 'tops', 2000, 'Comfortable blue bottom', 'https://images.unsplash.com/photo-1541099810657-40d6b5f55f15?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Casual Crop Top', 'women', 'tops', 900, 'Casual crop top', 'https://images.unsplash.com/photo-1520975676020-9a5f5d3b5e1e?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Silk Blouse', 'women', 'tops', 2200, 'Elegant silk blouse', 'https://images.unsplash.com/photo-1520975711006-1b2dca0d9df4?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),

-- Winter - Jackets
('Black Jacket', 'winter', 'jacket', 5000, 'Warm winter jacket', 'https://images.unsplash.com/photo-1611312652856-da8042baf1f9?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Navy Blazer', 'winter', 'jacket', 6000, 'Formal navy blazers', 'https://images.unsplash.com/photo-1591047990941-d866834b24e5?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Puffer Jacket', 'winter', 'jacket', 4800, 'Insulated puffer jacket', 'https://images.unsplash.com/photo-1549213783-8284d0336c9a?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Leather Jacket', 'winter', 'jacket', 7500, 'Genuine leather jacket', 'https://source.unsplash.com/400x400/?leather-jacket'),

-- Winter - Hoodies
('Grey Hoodie', 'winter', 'hoodie', 3500, 'Cozy grey hoodie', 'https://images.unsplash.com/photo-1556821552-107fcfaa4caf?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Zip Hoodie', 'winter', 'hoodie', 2800, 'Zip-up hoodie', 'https://images.unsplash.com/photo-1544025162-d76694265947?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Graphic Hoodie', 'winter', 'hoodie', 3200, 'Cozy graphic hoodie', 'https://images.unsplash.com/photo-1541534401786-5f5fc8b0b0a9?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),
('Fleece Hoodie', 'winter', 'hoodie', 2500, 'Warm fleece hoodie', 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80'),

-- Additional Men Products (to reach minimum 5 per subcategory)
('Casual Chambray Shirt', 'men', 'shirt', 1600, 'Casual chambray shirt with soft texture', 'https://source.unsplash.com/400x400/?casual-shirt'),
('Long Sleeve Tee', 'men', 'tshirt', 900, 'Comfortable long sleeve tee', 'https://source.unsplash.com/400x400/?long-sleeve-t-shirt'),
('Festive Punjabi', 'men', 'punjabi', 2800, 'Festive embroidered punjabi', 'https://source.unsplash.com/400x400/?kurta'),
('Canvas Backpack', 'men', 'accessories', 1400, 'Durable canvas backpack', 'https://source.unsplash.com/400x400/?canvas-backpack'),
('Cufflinks', 'men', 'accessories', 500, 'Classic cufflinks set', 'https://source.unsplash.com/400x400/?cufflinks'),

-- Additional Women Products (to reach minimum 5 per subcategory)
('Chiffon Saree', 'women', 'saree', 3200, 'Light chiffon saree with floral print', 'https://source.unsplash.com/400x400/?saree'),
('Summer Tank Top', 'women', 'tops', 700, 'Breathable summer tank top', 'https://source.unsplash.com/400x400/?tank-top'),
('Beaded Necklace', 'women', 'accessories', 800, 'Handmade beaded necklace', 'https://source.unsplash.com/400x400/?necklace'),
('Handbag', 'women', 'accessories', 2200, 'Elegant leather handbag', 'https://source.unsplash.com/400x400/?handbag'),
('Charm Bracelet', 'women', 'accessories', 600, 'Delicate charm bracelet', 'https://source.unsplash.com/400x400/?bracelet');

-- Sample Admin User (Plain password - no hashing)
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@example.com', 'admin123', 'admin');
