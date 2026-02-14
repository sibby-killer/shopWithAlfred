<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

$settings = getSettings($pdo);
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?php echo $settings['default_theme'] ?? 'navy-gold'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo sanitize($settings['tagline'] ?? SITE_TAGLINE); ?> - Quality products delivered nationwide in Kenya.">
    <title><?php echo isset($pageTitle) ? sanitize($pageTitle) . ' - ' : ''; ?><?php echo sanitize($settings['store_name'] ?? SITE_NAME); ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/themes.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    
    <script>
        const BASE_URL = '<?php echo BASE_URL; ?>';
        const WHATSAPP_NUMBER = '<?php echo WHATSAPP_NUMBER; ?>';
    </script>
    <script src="<?php echo BASE_URL; ?>/assets/js/theme-switcher.js"></script>
</head>
<body>

<nav class="navbar" id="navbar">
    <div class="container">
        <a href="<?php echo BASE_URL; ?>/" class="navbar-logo">
            <div class="logo-icon">A</div>
            Shop<span>WithAlfred</span>
        </a>
        
        <div class="nav-links">
            <a href="<?php echo BASE_URL; ?>/" class="<?php echo $currentPage === 'index' ? 'active' : ''; ?>">Home</a>
            <a href="<?php echo BASE_URL; ?>/shop.php" class="<?php echo $currentPage === 'shop' ? 'active' : ''; ?>">Shop</a>
            <a href="<?php echo BASE_URL; ?>/shop.php#categories" class="<?php echo $currentPage === 'categories' ? 'active' : ''; ?>">Categories</a>
            <a href="<?php echo BASE_URL; ?>/contact.php" class="<?php echo $currentPage === 'contact' ? 'active' : ''; ?>">Contact</a>
        </div>

        <div class="nav-icons">
            <div class="theme-toggle-wrapper" style="position:relative">
                <button class="nav-icon" title="Change Theme"><i class="fas fa-palette"></i></button>
            </div>
            <?php if (($settings['customer_accounts_enabled'] ?? '1') === '1'): ?>
            <?php if (isLoggedIn()): ?>
                <a href="<?php echo BASE_URL; ?>/account.php" class="nav-icon" title="My Account"><i class="fas fa-user-circle"></i></a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>/login.php" class="nav-icon" title="Login"><i class="fas fa-user"></i></a>
            <?php endif; ?>
            <?php endif; ?>
            <button class="hamburger" id="hamburger" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</nav>

<div class="mobile-nav" id="mobileNav">
    <a href="<?php echo BASE_URL; ?>/"><i class="fas fa-home"></i> Home</a>
    <a href="<?php echo BASE_URL; ?>/shop.php"><i class="fas fa-shopping-bag"></i> Shop</a>
    <a href="<?php echo BASE_URL; ?>/shop.php#categories"><i class="fas fa-th-large"></i> Categories</a>
    <a href="<?php echo BASE_URL; ?>/contact.php"><i class="fas fa-envelope"></i> Contact</a>
    <?php if (($settings['customer_accounts_enabled'] ?? '1') === '1'): ?>
    <?php if (isLoggedIn()): ?>
        <a href="<?php echo BASE_URL; ?>/account.php"><i class="fas fa-user-circle"></i> My Account</a>
        <a href="<?php echo BASE_URL; ?>/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    <?php else: ?>
        <a href="<?php echo BASE_URL; ?>/login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
    <?php endif; ?>
    <?php endif; ?>
</div>
