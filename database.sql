-- =====================================================
-- ShopWithAlfred Database Schema
-- Run this SQL in phpMyAdmin on InfinityFree
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+03:00";

-- =====================================================
-- Table: categories
-- =====================================================
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `icon` VARCHAR(100) DEFAULT 'fa-tag',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default categories
INSERT INTO `categories` (`name`, `icon`) VALUES
('Fashion', 'fa-shirt'),
('Electronics', 'fa-laptop'),
('Health & Beauty', 'fa-heart-pulse'),
('Baby Products', 'fa-baby');

-- =====================================================
-- Table: products
-- =====================================================
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `price` DECIMAL(10,2) NOT NULL,
  `category_id` INT,
  `gender` ENUM('men','women','kids','unisex') DEFAULT 'unisex',
  `images` TEXT COMMENT 'JSON array of image URLs',
  `jumia_link` VARCHAR(500) DEFAULT NULL COMMENT 'Hidden from customers',
  `in_stock` TINYINT(1) DEFAULT 1,
  `is_featured` TINYINT(1) DEFAULT 0,
  `is_new` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Table: customers
-- =====================================================
CREATE TABLE IF NOT EXISTS `customers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `phone` VARCHAR(15),
  `password` VARCHAR(255) NOT NULL,
  `gender` ENUM('male','female') DEFAULT NULL,
  `county` VARCHAR(100) DEFAULT NULL,
  `address` TEXT,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Table: orders
-- =====================================================
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `reference` VARCHAR(20) NOT NULL UNIQUE,
  `customer_id` INT DEFAULT NULL,
  `customer_name` VARCHAR(255) NOT NULL,
  `customer_phone` VARCHAR(15) NOT NULL,
  `customer_alt_phone` VARCHAR(15) DEFAULT NULL,
  `customer_email` VARCHAR(255) NOT NULL,
  `customer_gender` ENUM('male','female') NOT NULL,
  `county` VARCHAR(100) NOT NULL,
  `address` TEXT NOT NULL,
  `delivery_date` DATE NOT NULL,
  `notes` TEXT,
  `product_id` INT,
  `product_name` VARCHAR(255) NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `unit_price` DECIMAL(10,2) NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL,
  `transport_fee` DECIMAL(10,2) DEFAULT NULL,
  `total` DECIMAL(10,2) DEFAULT NULL,
  `status` ENUM('pending','confirmed','shipped','delivered','cancelled') DEFAULT 'pending',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Table: admins
-- =====================================================
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255),
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin (password: admin@guru123)
INSERT INTO `admins` (`username`, `password`, `email`) VALUES
('Guruadmin', '$2y$10$YKBKEzI0gxUaB3QLwGJPOeSMHW8JMDICX7JCkN6vJX0eVw6K0bFWa', 'alfred.dev8@gmail.com');

-- =====================================================
-- Table: subscribers
-- =====================================================
CREATE TABLE IF NOT EXISTS `subscribers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Table: restock_notifications
-- =====================================================
CREATE TABLE IF NOT EXISTS `restock_notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL,
  `product_id` INT,
  `notified` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Table: settings
-- =====================================================
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default settings
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('store_name', 'ShopWithAlfred'),
('tagline', 'Shop Smart. Shop With Alfred.'),
('contact_email', 'alfred.dev8@gmail.com'),
('whatsapp', '0762667048'),
('location', 'Kakamega, Lurambi, Kenya'),
('facebook_enabled', '0'),
('facebook_url', ''),
('instagram_enabled', '0'),
('instagram_url', ''),
('tiktok_enabled', '0'),
('tiktok_url', ''),
('whatsapp_channel_enabled', '0'),
('whatsapp_channel_url', ''),
('twitter_enabled', '0'),
('twitter_url', ''),
('newsletter_enabled', '1'),
('whatsapp_group_enabled', '0'),
('whatsapp_group_url', ''),
('customer_accounts_enabled', '1'),
('default_theme', 'navy-gold');
