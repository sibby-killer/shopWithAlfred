<?php
/**
 * ShopWithAlfred - Configuration File
 * Update these values for your hosting environment
 */

// =====================================================
// DATABASE CONFIGURATION
// =====================================================
// Update these with your InfinityFree database credentials
define('DB_HOST', 'sql300.infinityfree.com'); // InfinityFree MySQL host
define('DB_NAME', 'if0_your_database');       // Your database name
define('DB_USER', 'if0_your_username');        // Your database username
define('DB_PASS', 'your_password');            // Your database password
define('DB_CHARSET', 'utf8mb4');

// =====================================================
// SITE CONFIGURATION
// =====================================================
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('BASE_URL', $protocol . '://' . $host);
define('SITE_NAME', 'ShopWithAlfred');
define('SITE_TAGLINE', 'Shop Smart. Shop With Alfred.');

// =====================================================
// WHATSAPP CONFIGURATION
// =====================================================
define('WHATSAPP_NUMBER', '254762667048'); // International format without +
define('WHATSAPP_DISPLAY', '0762667048');

// =====================================================
// EMAIL CONFIGURATION
// =====================================================
define('ADMIN_EMAIL', 'alfred.dev8@gmail.com');
define('SMTP_HOST', 'smtp.gmail.com');        // Update for your email provider
define('SMTP_PORT', 587);
define('SMTP_USER', 'alfred.dev8@gmail.com'); // Your email
define('SMTP_PASS', '');                       // App password (Gmail requires this)
define('SMTP_FROM', 'alfred.dev8@gmail.com');
define('SMTP_FROM_NAME', 'ShopWithAlfred');

// =====================================================
// SESSION CONFIGURATION
// =====================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =====================================================
// DATABASE CONNECTION (PDO)
// =====================================================
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    // Don't expose error details in production
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed. Please check your configuration.");
}

// =====================================================
// TIMEZONE
// =====================================================
date_default_timezone_set('Africa/Nairobi');
