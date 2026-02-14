<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if (!isLoggedIn()) { header('Location: ' . BASE_URL . '/login.php'); exit; }

$customer = getCustomer($pdo, $_SESSION['customer_id']);
if (!$customer) { session_destroy(); header('Location: ' . BASE_URL . '/login.php'); exit; }

// Get customer orders
$stmt = $pdo->prepare("SELECT o.*, p.name as product_name FROM orders o LEFT JOIN products p ON o.product_id = p.id WHERE o.customer_email = ? ORDER BY o.created_at DESC");
$stmt->execute([$customer['email']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'My Account';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1>My Account</h1>
        <div class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>/">Home</a>
            <i class="fas fa-chevron-right"></i>
            <span>My Account</span>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="account-grid" style="display:grid;grid-template-columns:260px 1fr;gap:32px">
            <div class="account-sidebar">
                <div class="account-card" style="background:var(--card-bg);border-radius:var(--radius-lg);padding:28px;box-shadow:var(--shadow-sm);text-align:center">
                    <div style="width:64px;height:64px;border-radius:50%;background:var(--primary);color:var(--accent);display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;margin:0 auto 12px">
                        <?php echo strtoupper(substr($customer['name'], 0, 1)); ?>
                    </div>
                    <h3 style="font-size:16px"><?php echo sanitize($customer['name']); ?></h3>
                    <p style="font-size:13px;color:var(--text-secondary)"><?php echo sanitize($customer['email']); ?></p>
                    <p style="font-size:13px;color:var(--text-secondary)"><?php echo sanitize($customer['phone']); ?></p>
                    <a href="<?php echo BASE_URL; ?>/logout.php" class="btn btn-secondary btn-sm" style="margin-top:16px;width:100%">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
                <div class="account-stats" style="background:var(--card-bg);border-radius:var(--radius-lg);padding:20px;box-shadow:var(--shadow-sm);margin-top:16px">
                    <div style="display:flex;justify-content:space-between;padding:8px 0;font-size:14px">
                        <span>Total Orders</span>
                        <strong><?php echo count($orders); ?></strong>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:8px 0;font-size:14px">
                        <span>Member Since</span>
                        <strong><?php echo date('M Y', strtotime($customer['created_at'])); ?></strong>
                    </div>
                </div>
            </div>

            <div class="account-main">
                <div style="background:var(--card-bg);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);overflow:hidden">
                    <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                        <h3 style="font-size:16px"><i class="fas fa-shopping-bag"></i> My Orders</h3>
                    </div>
                    <?php if (empty($orders)): ?>
                    <div class="empty-state" style="padding:48px 24px;text-align:center">
                        <i class="fas fa-shopping-bag" style="font-size:48px;color:var(--text-secondary);margin-bottom:16px;display:block"></i>
                        <h3>No orders yet</h3>
                        <p style="color:var(--text-secondary);margin-bottom:20px">Start shopping to see your orders here!</p>
                        <a href="<?php echo BASE_URL; ?>/shop.php" class="btn btn-primary"><i class="fas fa-shopping-bag"></i> Browse Products</a>
                    </div>
                    <?php else: ?>
                    <div style="overflow-x:auto">
                        <table style="width:100%;border-collapse:collapse">
                            <thead>
                                <tr>
                                    <th style="padding:12px 16px;text-align:left;font-size:12px;font-weight:600;text-transform:uppercase;color:var(--text-secondary);background:var(--background-alt)">Ref</th>
                                    <th style="padding:12px 16px;text-align:left;font-size:12px;font-weight:600;text-transform:uppercase;color:var(--text-secondary);background:var(--background-alt)">Product</th>
                                    <th style="padding:12px 16px;text-align:left;font-size:12px;font-weight:600;text-transform:uppercase;color:var(--text-secondary);background:var(--background-alt)">Total</th>
                                    <th style="padding:12px 16px;text-align:left;font-size:12px;font-weight:600;text-transform:uppercase;color:var(--text-secondary);background:var(--background-alt)">Status</th>
                                    <th style="padding:12px 16px;text-align:left;font-size:12px;font-weight:600;text-transform:uppercase;color:var(--text-secondary);background:var(--background-alt)">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td style="padding:12px 16px;font-size:14px;border-bottom:1px solid var(--border)">#<?php echo sanitize($order['reference']); ?></td>
                                    <td style="padding:12px 16px;font-size:14px;border-bottom:1px solid var(--border)"><?php echo sanitize($order['product_name']); ?></td>
                                    <td style="padding:12px 16px;font-size:14px;border-bottom:1px solid var(--border);font-weight:600"><?php echo formatPrice($order['subtotal']); ?></td>
                                    <td style="padding:12px 16px;font-size:14px;border-bottom:1px solid var(--border)">
                                        <?php
                                        $statusColors = ['pending' => '#F59E0B', 'confirmed' => '#3B82F6', 'shipped' => '#8B5CF6', 'delivered' => '#10B981', 'cancelled' => '#EF4444'];
                                        $color = $statusColors[$order['status']] ?? '#888';
                                        ?>
                                        <span style="padding:4px 10px;border-radius:20px;font-size:12px;font-weight:500;background:<?php echo $color; ?>20;color:<?php echo $color; ?>"><?php echo ucfirst($order['status']); ?></span>
                                    </td>
                                    <td style="padding:12px 16px;font-size:13px;color:var(--text-secondary);border-bottom:1px solid var(--border)"><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
@media (max-width:768px) { .account-grid { grid-template-columns: 1fr !important; } }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
