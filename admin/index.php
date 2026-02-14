<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../includes/admin-header.php';
require_once __DIR__ . '/../includes/admin-sidebar.php';

// Stats
$totalProducts = getStatCount($pdo, 'products');
$totalOrders = getStatCount($pdo, 'orders');
$totalCustomers = getStatCount($pdo, 'customers');
$totalSubscribers = getStatCount($pdo, 'subscribers');
$pendingOrders = getStatCount($pdo, 'orders', "status = 'pending'");
$outOfStock = getStatCount($pdo, 'products', "in_stock = 0");

// Recent orders
$stmt = $pdo->query("SELECT o.*, p.name as product_name FROM orders o LEFT JOIN products p ON o.product_id = p.id ORDER BY o.created_at DESC LIMIT 10");
$recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-box"></i></div>
        <div class="stat-info"><h4><?php echo $totalProducts; ?></h4><p>Total Products</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-shopping-bag"></i></div>
        <div class="stat-info"><h4><?php echo $totalOrders; ?></h4><p>Total Orders</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-users"></i></div>
        <div class="stat-info"><h4><?php echo $totalCustomers; ?></h4><p>Customers</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow"><i class="fas fa-clock"></i></div>
        <div class="stat-info"><h4><?php echo $pendingOrders; ?></h4><p>Pending Orders</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-envelope"></i></div>
        <div class="stat-info"><h4><?php echo $totalSubscribers; ?></h4><p>Subscribers</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-times-circle"></i></div>
        <div class="stat-info"><h4><?php echo $outOfStock; ?></h4><p>Out of Stock</p></div>
    </div>
</div>

<!-- Recent Orders -->
<div class="admin-table-wrapper">
    <div class="admin-table-header">
        <h3>Recent Orders</h3>
        <a href="<?php echo BASE_URL; ?>/admin/orders.php" class="btn btn-secondary btn-sm">View All</a>
    </div>
    <div style="overflow-x:auto">
        <table>
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentOrders)): ?>
                <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--text-secondary)">No orders yet</td></tr>
                <?php else: ?>
                <?php foreach ($recentOrders as $order): ?>
                <tr>
                    <td><strong>#<?php echo sanitize($order['reference']); ?></strong></td>
                    <td><?php echo sanitize($order['customer_name']); ?></td>
                    <td><?php echo sanitize($order['product_name'] ?? 'Deleted Product'); ?></td>
                    <td style="font-weight:600"><?php echo formatPrice($order['subtotal']); ?></td>
                    <td>
                        <?php
                        $statusColors = ['pending' => '#F59E0B', 'confirmed' => '#3B82F6', 'shipped' => '#8B5CF6', 'delivered' => '#10B981', 'cancelled' => '#EF4444'];
                        $sc = $statusColors[$order['status']] ?? '#888';
                        ?>
                        <span style="padding:4px 10px;border-radius:20px;font-size:12px;font-weight:500;background:<?php echo $sc; ?>20;color:<?php echo $sc; ?>"><?php echo ucfirst($order['status']); ?></span>
                    </td>
                    <td style="font-size:13px;color:var(--text-secondary)"><?php echo date('M j, g:i A', strtotime($order['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>
