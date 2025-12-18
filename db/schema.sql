-- Database Schema for Mama Mboga
-- Database: mama_mboga_db

CREATE DATABASE IF NOT EXISTS `mama_mboga_db`;
USE `mama_mboga_db`;

-- 1. Users Table (Vendors and Admins)
CREATE TABLE IF NOT EXISTS `users` (
    `user_id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'vendor') DEFAULT 'vendor',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Customers Table
CREATE TABLE IF NOT EXISTS `customers` (
    `customer_id` INT AUTO_INCREMENT PRIMARY KEY,
    `vendor_id` INT NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(20) NOT NULL,
    `current_balance` DECIMAL(10, 2) DEFAULT 0.00,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`vendor_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 3. Sales Table (Credit Sales)
CREATE TABLE IF NOT EXISTS `sales` (
    `sale_id` INT AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INT NOT NULL,
    `vendor_id` INT NOT NULL,
    `item_name` VARCHAR(100) NOT NULL,
    `quantity` INT NOT NULL,
    `price_per_unit` DECIMAL(10, 2) NOT NULL,
    `total_amount` DECIMAL(10, 2) NOT NULL,
    `sale_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`customer_id`) ON DELETE CASCADE,
    FOREIGN KEY (`vendor_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Payments Table
CREATE TABLE IF NOT EXISTS `payments` (
    `payment_id` INT AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INT NOT NULL,
    `vendor_id` INT NOT NULL,
    `amount` DECIMAL(10, 2) NOT NULL,
    `payment_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`customer_id`) ON DELETE CASCADE,
    FOREIGN KEY (`vendor_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB;
