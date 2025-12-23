-- Project: Sarawak Scents

CREATE DATABASE IF NOT EXISTS sarawak_scents_db;
USE sarawak_scents_db;

-- 1. DROP TABLES (Cleanup)
DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

-- 2. CREATE TABLES
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, 
    phone_number VARCHAR(20),
    role ENUM('member', 'admin') DEFAULT 'member',
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL
);

CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255), -- Will store 'filename.png'
    category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Paid', 'Cancelled') DEFAULT 'Paid',
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

CREATE TABLE transactions (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    payment_method VARCHAR(50),
    payment_status ENUM('Success', 'Failed') DEFAULT 'Success',
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

-- ==========================================
-- 3. INSERT DATA
-- ==========================================

-- Categories
INSERT INTO categories (category_name) VALUES ('Perfume'), ('Soap'), ('Candle'), ('Gift Sets');

-- Products (STRICTLY 4 ITEMS, .png IMAGES)
INSERT INTO products (name, description, price, image, category_id) VALUES
('Rainforest Mist Eau de Parfum', 'A sophisticated, unisex fragrance inspired by the scent of the Borneo rainforest after rain.', 129.00, 'rainforest_mist.png', 1),
('Pepper Berry Artisan Soap', 'Hand-milled luxury bar soap featuring real Sarawak black pepper.', 45.00, 'pepper_soap.png', 2),
('Orchid Bloom Scented Candle', '100% natural soy-wax candle scented with a delicate floral blend.', 89.00, 'orchid_candle.png', 3),
('The Discovery Set', 'A beautifully packaged miniature collection of our three signature products.', 199.00, 'discovery_set.png', 4);

-- Users (Admin1$ and User1$)
INSERT INTO users (full_name, email, password, phone_number, role, address, created_at) VALUES
('Admin Sarawak', 'admin@sarawakscents.com', '$2y$10$T5J1GAOBnoCAlfrCu/G30.5XLxOZmxbFspO9q5K6Wq/CwcZAhTlte', '0123456789', 'admin', 'HQ Kuching', '2025-11-01 10:00:00'),
('Ali Ahmad', 'ali@gmail.com', '$2y$10$lB5Phib.RF7Ag0z/kZ5VC.0hbnPthW8w/sCeb5qAlwouJLvzpwOBO', '0121111111', 'member', 'No 5, Petra Jaya', '2025-12-01 10:00:00'),
('Siti Sarah', 'siti@gmail.com', '$2y$10$lB5Phib.RF7Ag0z/kZ5VC.0hbnPthW8w/sCeb5qAlwouJLvzpwOBO', '0122222222', 'member', 'Lot 88, Tabuan Jaya', '2025-12-05 14:30:00'),
('John Tan', 'john@gmail.com', '$2y$10$lB5Phib.RF7Ag0z/kZ5VC.0hbnPthW8w/sCeb5qAlwouJLvzpwOBO', '0123333333', 'member', 'Green Road', '2025-12-10 09:15:00');

-- Orders (History for Charts)
INSERT INTO orders (user_id, total_amount, status, order_date) VALUES
(2, 129.00, 'Paid', '2025-12-02 10:30:00'),
(3, 174.00, 'Paid', '2025-12-06 15:00:00'),
(2, 45.00,  'Paid', '2025-12-15 11:20:00'),
(4, 199.00, 'Paid', '2025-12-20 09:00:00'),
(3, 89.00,  'Paid', '2025-12-22 16:50:00'),
(2, 387.00, 'Paid', '2025-12-25 09:30:00'),
(3, 134.00, 'Paid', '2026-01-02 11:00:00'),
(4, 45.00,  'Cancelled', '2026-01-03 10:00:00'),
(2, 89.00,  'Paid', CURRENT_TIMESTAMP); -- Recent Order

-- Order Items
INSERT INTO order_items (order_id, product_id, quantity, price) VALUES
(1, 1, 1, 129.00),
(2, 3, 1, 89.00), (2, 2, 1, 45.00),
(3, 2, 1, 45.00),
(4, 4, 1, 199.00),
(5, 3, 1, 89.00),
(6, 1, 3, 129.00),
(7, 2, 1, 45.00), (7, 3, 1, 89.00),
(8, 2, 1, 45.00),
(9, 3, 1, 89.00);

-- Transactions
INSERT INTO transactions (order_id, payment_method, payment_status, transaction_date) VALUES
(1, 'Credit Card', 'Success', '2025-12-02 10:35:00'),
(2, 'Online Banking', 'Success', '2025-12-06 15:05:00'),
(3, 'E-Wallet', 'Success', '2025-12-15 11:25:00'),
(4, 'Credit Card', 'Success', '2025-12-20 09:05:00'),
(5, 'Online Banking', 'Success', '2025-12-22 16:55:00'),
(6, 'Credit Card', 'Success', '2025-12-25 09:35:00'),
(7, 'E-Wallet', 'Success', '2026-01-02 11:05:00'),
(8, 'Online Banking', 'Failed', '2026-01-03 10:05:00'),
(9, 'E-Wallet', 'Success', CURRENT_TIMESTAMP);