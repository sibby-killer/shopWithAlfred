<?php $adminPage = $adminPage ?? ''; ?>
<aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-sidebar-header">
        <div class="logo-icon" style="width:36px;height:36px;background:var(--accent);color:var(--primary-dark);border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;">A</div>
        <h3>ShopWithAlfred</h3>
        <button class="sidebar-toggle"><i class="fas fa-bars"></i></button>
    </div>
    <nav class="admin-nav">
        <a href="<?php echo BASE_URL; ?>/admin/" class="<?php echo $adminPage === 'index' ? 'active' : ''; ?>"><i class="fas fa-chart-pie"></i> <span>Dashboard</span></a>
        <a href="<?php echo BASE_URL; ?>/admin/products.php" class="<?php echo $adminPage === 'products' ? 'active' : ''; ?>"><i class="fas fa-box"></i> <span>Products</span></a>
        <a href="<?php echo BASE_URL; ?>/admin/categories.php" class="<?php echo $adminPage === 'categories' ? 'active' : ''; ?>"><i class="fas fa-folder"></i> <span>Categories</span></a>
        <a href="<?php echo BASE_URL; ?>/admin/orders.php" class="<?php echo $adminPage === 'orders' ? 'active' : ''; ?>"><i class="fas fa-shopping-bag"></i> <span>Orders</span></a>
        <a href="<?php echo BASE_URL; ?>/admin/customers.php" class="<?php echo $adminPage === 'customers' ? 'active' : ''; ?>"><i class="fas fa-users"></i> <span>Customers</span></a>
        <a href="<?php echo BASE_URL; ?>/admin/subscribers.php" class="<?php echo $adminPage === 'subscribers' ? 'active' : ''; ?>"><i class="fas fa-envelope"></i> <span>Subscribers</span></a>
        <a href="<?php echo BASE_URL; ?>/admin/settings.php" class="<?php echo $adminPage === 'settings' ? 'active' : ''; ?>"><i class="fas fa-cog"></i> <span>Settings</span></a>
        <a href="<?php echo BASE_URL; ?>/admin/logout.php" style="margin-top:24px;color:rgba(255,255,255,0.5)"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
    </nav>
</aside>

<div class="admin-main">
    <header class="admin-topbar">
        <div style="display:flex;align-items:center;gap:12px;">
            <button id="adminMobileMenu" class="nav-icon" style="display:none"><i class="fas fa-bars"></i></button>
            <h2><?php echo $pageTitle ?? 'Dashboard'; ?></h2>
        </div>
        <div class="admin-user">
            <span><?php echo sanitize($_SESSION['admin_username'] ?? 'Admin'); ?></span>
            <div class="avatar"><?php echo strtoupper(substr($_SESSION['admin_username'] ?? 'A', 0, 1)); ?></div>
        </div>
    </header>
    <div class="admin-content">
