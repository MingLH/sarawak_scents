-- Project: Sarawak Scents
-- Updated: Added 'is_active' for Soft Delete + Full Demo Data (Sept - Dec 24)

CREATE DATABASE IF NOT EXISTS sarawak_scents_db;
USE sarawak_scents_db;

-- ==========================================
-- 1. DROP TABLES (Cleanup)
-- ==========================================
DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

-- ==========================================
-- 2. CREATE TABLES
-- ==========================================
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

-- UPDATED: Added 'is_active' column
CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255), 
    category_id INT,
    is_active TINYINT(1) DEFAULT 1, -- 1 = Selling, 0 = Archived
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Pending', 'Paid', 'Shipped', 'Cancelled') DEFAULT 'Paid',
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

-- A. CATEGORIES
INSERT INTO categories (category_name) VALUES 
('Perfume'), ('Soap'), ('Candle'), ('Gift Sets');

-- B. PRODUCTS (Active by default)
INSERT INTO products (name, description, price, image, category_id, is_active) VALUES
('Rainforest Mist Eau de Parfum', 'A sophisticated, unisex fragrance inspired by the scent of the Borneo rainforest after rain.', 129.00, 'rainforest_mist.png', 1, 1),
('Pepper Berry Artisan Soap', 'Hand-milled luxury bar soap featuring real Sarawak black pepper.', 45.00, 'pepper_soap.png', 2, 1),
('Orchid Bloom Scented Candle', '100% natural soy-wax candle scented with a delicate floral blend.', 89.00, 'orchid_candle.png', 3, 1),
('The Discovery Set', 'A beautifully packaged miniature collection of our three signature products.', 199.00, 'discovery_set.png', 4, 1);

-- C. USERS (Password: User1$)
INSERT INTO users (full_name, email, password, phone_number, role, address, created_at) VALUES
('Admin Sarawak', 'admin@sarawakscents.com', '$2y$10$T5J1GAOBnoCAlfrCu/G30.5XLxOZmxbFspO9q5K6Wq/CwcZAhTlte', '0123456789', 'admin', 'HQ Kuching', '2025-08-01 08:00:00'),
('Ali Ahmad', 'ali@example.com', '$2y$10$lB5Phib.RF7Ag0z/kZ5VC.0hbnPthW8w/sCeb5qAlwouJLvzpwOBO', '0121111111', 'member', 'No 5, Petra Jaya, Kuching', '2025-09-01 10:00:00'),
('Siti Sarah', 'siti@example.com', '$2y$10$lB5Phib.RF7Ag0z/kZ5VC.0hbnPthW8w/sCeb5qAlwouJLvzpwOBO', '0122222222', 'member', 'Lot 88, Tabuan Jaya, Kuching', '2025-09-05 14:30:00'),
('John Tan', 'john@example.com', '$2y$10$lB5Phib.RF7Ag0z/kZ5VC.0hbnPthW8w/sCeb5qAlwouJLvzpwOBO', '0123333333', 'member', 'Green Road, Kuching', '2025-09-10 09:15:00'),
('Mei Ling', 'mei@example.com', '$2y$10$lB5Phib.RF7Ag0z/kZ5VC.0hbnPthW8w/sCeb5qAlwouJLvzpwOBO', '0124444444', 'member', 'Padungan Street, Kuching', '2025-09-12 11:20:00'),
('Raju Singh', 'raju@example.com', '$2y$10$lB5Phib.RF7Ag0z/kZ5VC.0hbnPthW8w/sCeb5qAlwouJLvzpwOBO', '0125555555', 'member', 'Kota Samarahan, Sarawak', '2025-10-01 16:45:00'),
('Dayang Nurul', 'dayang@example.com', '$2y$10$lB5Phib.RF7Ag0z/kZ5VC.0hbnPthW8w/sCeb5qAlwouJLvzpwOBO', '0126666666', 'member', 'Matang Jaya, Kuching', '2025-10-15 09:00:00'),
('Michael Wong', 'mike@example.com', '$2y$10$lB5Phib.RF7Ag0z/kZ5VC.0hbnPthW8w/sCeb5qAlwouJLvzpwOBO', '0127777777', 'member', 'Batu Kawa, Kuching', '2025-11-05 13:10:00');

-- D. ORDERS (Combined Set: Sept 2025 to Dec 24, 2025)
INSERT INTO orders (user_id, total_amount, status, order_date) VALUES
-- PAST MONTHS
(2, 129.00, 'Paid', '2025-09-02 10:30:00'),
(3, 174.00, 'Paid', '2025-09-06 15:00:00'),
(2, 45.00,  'Paid', '2025-09-15 11:20:00'),
(4, 199.00, 'Paid', '2025-09-20 09:00:00'),
(5, 89.00,  'Paid', '2025-09-25 16:50:00'),
(2, 387.00, 'Paid', '2025-10-02 09:30:00'),
(6, 45.00,  'Paid', '2025-10-08 14:15:00'),
(3, 129.00, 'Paid', '2025-10-14 10:00:00'),
(4, 89.00,  'Paid', '2025-10-20 18:45:00'),
(5, 134.00, 'Paid', '2025-10-28 11:00:00'),
(4, 45.00,  'Cancelled', '2025-11-02 10:00:00'),
(5, 199.00, 'Paid',    '2025-11-05 09:30:00'),
(6, 129.00, 'Shipped', '2025-11-10 12:00:00'),
(7, 45.00,  'Paid',    '2025-11-15 14:20:00'),
(8, 89.00,  'Paid',    '2025-11-20 16:45:00'),
(2, 258.00, 'Paid',    '2025-11-25 09:15:00'),
(3, 45.00,  'Pending', '2025-12-01 10:30:00'),
(4, 199.00, 'Paid',    '2025-12-03 11:00:00'),
(5, 129.00, 'Shipped', '2025-12-05 13:45:00'),
(6, 45.00,  'Shipped', '2025-12-08 15:30:00'),
(7, 178.00, 'Paid',    '2025-12-10 09:00:00'),
(8, 89.00,  'Pending', '2025-12-12 10:15:00'),
(2, 199.00, 'Paid',    '2025-12-14 11:30:00'),
(3, 129.00, 'Pending', '2025-12-15 09:00:00'),
(4, 45.00,  'Pending', '2025-12-15 14:00:00'),

-- RECENT DATA (For Dashboard 'Weekly' & 'Daily' Demos)
(6, 129.00, 'Shipped', '2025-12-17 12:00:00'),
(7, 45.00,  'Paid',    '2025-12-18 14:20:00'),
(8, 89.00,  'Paid',    '2025-12-19 16:45:00'),
(2, 258.00, 'Paid',    '2025-12-20 09:15:00'),
(3, 45.00,  'Pending', '2025-12-21 10:30:00'),
(4, 199.00, 'Paid',    '2025-12-23 11:00:00'),
(5, 129.00, 'Paid',    '2025-12-24 09:45:00'), -- TODAY
(6, 45.00,  'Shipped', '2025-12-24 12:30:00'), -- TODAY
(7, 178.00, 'Paid',    '2025-12-24 14:00:00'), -- TODAY
(8, 89.00,  'Pending', '2025-12-24 15:15:00'), -- TODAY
(2, 199.00, 'Paid',    CURRENT_TIMESTAMP);

-- E. ORDER ITEMS
INSERT INTO order_items (order_id, product_id, quantity, price) VALUES
(1, 1, 1, 129.00),
(2, 3, 1, 89.00), (2, 2, 1, 45.00),
(3, 2, 1, 45.00), (4, 4, 1, 199.00), (5, 3, 1, 89.00),
(6, 1, 3, 129.00), (7, 2, 1, 45.00), (8, 1, 1, 129.00),
(9, 3, 1, 89.00), (10, 2, 1, 45.00), (10, 3, 1, 89.00),
(11, 2, 1, 45.00), (12, 4, 1, 199.00), (13, 1, 1, 129.00),
(14, 2, 1, 45.00), (15, 3, 1, 89.00), (16, 1, 2, 129.00),
(17, 2, 1, 45.00), (18, 4, 1, 199.00), (19, 1, 1, 129.00),
(20, 2, 1, 45.00), (21, 3, 2, 89.00), (22, 3, 1, 89.00),
(23, 4, 1, 199.00), (24, 1, 1, 129.00), (25, 2, 1, 45.00),
-- Items for Recent Orders
(26, 1, 1, 129.00), (27, 2, 1, 45.00), (28, 3, 1, 89.00),
(29, 1, 2, 129.00), (30, 2, 1, 45.00), (31, 4, 1, 199.00),
(32, 1, 1, 129.00), (33, 2, 1, 45.00), (34, 3, 2, 89.00),
(35, 3, 1, 89.00), (36, 4, 1, 199.00);

-- F. TRANSACTIONS
INSERT INTO transactions (order_id, payment_method, payment_status, transaction_date) VALUES
(1, 'Credit Card', 'Success', '2025-09-02 10:35:00'),
(2, 'Online Banking', 'Success', '2025-09-06 15:05:00'),
(3, 'E-Wallet', 'Success', '2025-09-15 11:25:00'),
(4, 'Credit Card', 'Success', '2025-09-20 09:05:00'),
(5, 'Online Banking', 'Success', '2025-09-25 16:55:00'),
(6, 'Credit Card', 'Success', '2025-10-02 09:35:00'),
(7, 'E-Wallet', 'Success', '2025-10-08 14:20:00'),
(8, 'Credit Card', 'Success', '2025-10-14 10:05:00'),
(9, 'Online Banking', 'Success', '2025-10-20 18:50:00'),
(10, 'E-Wallet', 'Success', '2025-10-28 11:05:00'),
(11, 'Online Banking', 'Failed', '2025-11-02 10:05:00'), 
(12, 'Credit Card', 'Success', '2025-11-05 09:35:00'),
(13, 'E-Wallet', 'Success', '2025-11-10 12:05:00'),
(14, 'Online Banking', 'Success', '2025-11-15 14:25:00'),
(15, 'Credit Card', 'Success', '2025-11-20 16:50:00'),
(16, 'Online Banking', 'Success', '2025-11-25 09:20:00'),
(17, 'E-Wallet', 'Success', '2025-12-01 10:35:00'),
(18, 'Credit Card', 'Success', '2025-12-03 11:05:00'),
(19, 'Online Banking', 'Success', '2025-12-05 13:50:00'),
(20, 'E-Wallet', 'Success', '2025-12-08 15:35:00'),
(21, 'Credit Card', 'Success', '2025-12-10 09:05:00'),
(22, 'Online Banking', 'Success', '2025-12-12 10:20:00'),
(23, 'E-Wallet', 'Success', '2025-12-14 11:35:00'),
(24, 'Credit Card', 'Success', '2025-12-15 09:05:00'),
(25, 'Online Banking', 'Success', '2025-12-15 14:05:00'),
-- Recent Transactions
(26, 'E-Wallet', 'Success', '2025-12-17 12:05:00'),
(27, 'Online Banking', 'Success', '2025-12-18 14:25:00'),
(28, 'Credit Card', 'Success', '2025-12-19 16:50:00'),
(29, 'Online Banking', 'Success', '2025-12-20 09:20:00'),
(30, 'E-Wallet', 'Success', '2025-12-21 10:35:00'),
(31, 'Credit Card', 'Success', '2025-12-23 11:05:00'),
(32, 'Online Banking', 'Success', '2025-12-24 09:50:00'),
(33, 'E-Wallet', 'Success', '2025-12-24 12:35:00'),
(34, 'Credit Card', 'Success', '2025-12-24 14:05:00'),
(35, 'Online Banking', 'Success', '2025-12-24 15:20:00'),
(36, 'E-Wallet', 'Success', CURRENT_TIMESTAMP);