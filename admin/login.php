<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// Redirect if already logged in as admin
if (isAdmin()) { header('Location: ' . BASE_URL . '/admin/'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        if (loginAdmin($pdo, $username, $password)) {
            header('Location: ' . BASE_URL . '/admin/');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="navy-gold">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - ShopWithAlfred</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/themes.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/admin.css">
</head>
<body>
<div class="admin-login-page">
    <div class="admin-login-card">
        <div class="logo-area">
            <div class="logo-icon">A</div>
        </div>
        <h2>Admin Login</h2>
        <p class="subtitle">ShopWithAlfred Dashboard</p>
        
        <?php if ($error): ?>
        <div class="login-error active"><i class="fas fa-exclamation-circle"></i> <?php echo sanitize($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required autofocus value="<?php echo sanitize($_POST['username'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;margin-top:8px">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        <p style="text-align:center;margin-top:20px;font-size:13px;color:var(--text-secondary)">
            <a href="<?php echo BASE_URL; ?>/" style="color:var(--accent)"><i class="fas fa-arrow-left"></i> Back to Store</a>
        </p>
    </div>
</div>
</body>
</html>
