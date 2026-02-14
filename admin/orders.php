<?php
$pageTitle = 'Orders';
require_once __DIR__ . '/../includes/admin-header.php';
require_once __DIR__ . '/../includes/admin-sidebar.php';

$stmt = $pdo->query("SELECT o.*, p.name as product_name, p.jumia_link FROM orders o LEFT JOIN products p ON o.product_id = p.id ORDER BY o.created_at DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="admin-table-wrapper">
    <div class="admin-table-header">
        <h3>All Orders (<?php echo count($orders); ?>)</h3>
        <div class="admin-table-filters">
            <input type="text" class="admin-search" placeholder="Search orders...">
            <select class="admin-filter-select" data-col="4">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>
    <div style="overflow-x:auto">
        <table>
            <thead>
                <tr>
                    <th>Ref</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Product</th>
                    <th>Status</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                <tr><td colspan="8" style="text-align:center;padding:32px;color:var(--text-secondary)">No orders yet</td></tr>
                <?php else: ?>
                <?php foreach ($orders as $o): ?>
                <tr>
                    <td><strong>#<?php echo sanitize($o['reference']); ?></strong></td>
                    <td><?php echo sanitize($o['customer_name']); ?></td>
                    <td><?php echo sanitize($o['customer_phone']); ?></td>
                    <td><?php echo sanitize($o['product_name'] ?? 'Deleted'); ?></td>
                    <td>
                        <?php
                        $sc = ['pending' => '#F59E0B', 'confirmed' => '#3B82F6', 'shipped' => '#8B5CF6', 'delivered' => '#10B981', 'cancelled' => '#EF4444'];
                        $c = $sc[$o['status']] ?? '#888';
                        ?>
                        <span style="padding:4px 10px;border-radius:20px;font-size:12px;font-weight:500;background:<?php echo $c; ?>20;color:<?php echo $c; ?>"><?php echo ucfirst($o['status']); ?></span>
                    </td>
                    <td style="font-weight:600"><?php echo formatPrice($o['subtotal']); ?></td>
                    <td style="font-size:13px;color:var(--text-secondary)"><?php echo date('M j, Y', strtotime($o['created_at'])); ?></td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-view view-order" data-id="<?php echo $o['id']; ?>" title="View"><i class="fas fa-eye"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Order Detail Modal -->
<div class="modal-overlay" id="orderModal">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-shopping-bag"></i> Order Details</h3>
            <button class="modal-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body" id="orderDetail">
            <div style="text-align:center;padding:40px;color:var(--text-secondary)">Loading...</div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>
