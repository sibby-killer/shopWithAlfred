<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) { header('Location: ' . BASE_URL . '/account.php'); exit; }

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'login') {
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $error = 'Please fill in all fields.';
        } else {
            $result = loginCustomer($pdo, $email, $password);
            if ($result['success']) {
                header('Location: ' . BASE_URL . '/account.php');
                exit;
            } else {
                $error = $result['message'];
            }
        }
    } elseif ($action === 'register') {
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        
        if (empty($name) || empty($email) || empty($phone) || empty($password)) {
            $error = 'Please fill in all fields.';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters.';
        } else {
            $result = registerCustomer($pdo, $name, $email, $phone, $password);
            if ($result['success']) {
                $success = 'Account created! Please log in.';
            } else {
                $error = $result['message'];
            }
        }
    }
}

$pageTitle = 'Login / Register';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1>My Account</h1>
        <div class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>/">Home</a>
            <i class="fas fa-chevron-right"></i>
            <span>Login / Register</span>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="auth-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:40px;max-width:800px;margin:0 auto">
            <!-- Login -->
            <div class="auth-form">
                <h2><i class="fas fa-sign-in-alt"></i> Login</h2>
                <?php if ($error && ($_POST['action'] ?? '') === 'login'): ?>
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <input type="hidden" name="action" value="login">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%">Log In</button>
                </form>
            </div>

            <!-- Register -->
            <div class="auth-form">
                <h2><i class="fas fa-user-plus"></i> Register</h2>
                <?php if ($error && ($_POST['action'] ?? '') === 'register'): ?>
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <input type="hidden" name="action" value="register">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" placeholder="07XXXXXXXX" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%">Create Account</button>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
@media (max-width:768px) { .auth-grid { grid-template-columns: 1fr !important; gap: 24px !important; } }
.auth-form { background: var(--card-bg); padding: 28px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); }
.auth-form h2 { font-size: 18px; margin-bottom: 20px; }
.alert { padding: 12px 16px; border-radius: var(--radius-sm); margin-bottom: 16px; font-size: 14px; display: flex; align-items: center; gap: 8px; }
.alert-error { background: #FEE2E2; color: #991B1B; }
.alert-success { background: #D1FAE5; color: #065F46; }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
