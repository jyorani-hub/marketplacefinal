-- Online Second-Hand Marketplace Database
-- Import this file into phpMyAdmin to create the database

CREATE DATABASE IF NOT EXISTS marketplace CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE marketplace;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255),
    category VARCHAR(50) NOT NULL,
    `condition` VARCHAR(50) DEFAULT 'Used',
    stock INT DEFAULT 1,
    seller_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Cart table
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(30) DEFAULT 'Pending',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order items table (tracks what was in each order)
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT,
    product_name VARCHAR(200),
    price DECIMAL(10,2),
    quantity INT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Default admin account: admin@marketplace.com / admin123
INSERT INTO users (name, email, password, is_admin) VALUES
('Admin', 'admin@marketplace.com', '$2y$10$L9RrsrgZlJpp.crr/jbQXuXz.imRMRGMWdOgI7QXGGcoyoHWVNmmS', 1);

-- Sample products
INSERT INTO products (name, description, price, image_url, category, `condition`, stock, seller_id) VALUES
('Vintage Vinyl Record - Jazz Classics', 'Original 1970s pressing, plays cleanly with minimal surface noise.', 25.00, '', 'Vinyl', 'Good', 1, 1),
('Used iPhone 11', 'Unlocked, 128GB, minor scratches on back. Battery health 87%.', 220.00, '', 'Electronics', 'Used', 1, 1),
('Hardcover Novel Collection', 'Set of 5 classic novels in great condition.', 18.50, '', 'Books', 'Like New', 3, 1),
('Leather Jacket (Medium)', 'Genuine leather, barely worn. Size M.', 55.00, '', 'Clothing', 'Like New', 1, 1),
('Retro Video Game CD', 'PS1 era classic, disc in excellent condition.', 15.00, '', 'CDs', 'Good', 2, 1),
('Baseball Card Collection', 'Small collectible set from the 90s.', 40.00, '', 'Collectibles', 'Good', 1, 1);
