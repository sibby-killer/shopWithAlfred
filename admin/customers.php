<?php
$pageTitle = 'Customers';
require_once __DIR__ . '/../includes/admin-header.php';
require_once __DIR__ . '/../includes/admin-sidebar.php';

$stmt = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM orders WHERE customer_email = c.email) as order_count FROM customers c ORDER BY c.created_at DESC");
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="admin-table-wrapper">
    <div class="admin-table-header">
        <h3>Registered Customers (<?php echo count($customers); ?>)</h3>
        <input type="text" class="admin-search" placeholder="Search customers...">
    </div>
    <div style="overflow-x:auto">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Orders</th>
                    <th>Joined</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($customers)): ?>
                <tr><td colspan="5" style="text-align:center;padding:32px;color:var(--text-secondary)">No registered customers</td></tr>
                <?php else: ?>
                <?php foreach ($customers as $cust): ?>
                <tr>
                    <td><strong><?php echo sanitize($cust['name']); ?></strong></td>
                    <td><?php echo sanitize($cust['email']); ?></td>
                    <td><?php echo sanitize($cust['phone']); ?></td>
                    <td><?php echo $cust['order_count']; ?></td>
                    <td style="font-size:13px;color:var(--text-secondary)"><?php echo date('M j, Y', strtotime($cust['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>
