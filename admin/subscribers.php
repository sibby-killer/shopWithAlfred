<?php
$pageTitle = 'Subscribers';
require_once __DIR__ . '/../includes/admin-header.php';
require_once __DIR__ . '/../includes/admin-sidebar.php';

$stmt = $pdo->query("SELECT * FROM subscribers ORDER BY created_at DESC");
$subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$restockStmt = $pdo->query("SELECT rn.*, p.name as product_name FROM restock_notifications rn LEFT JOIN products p ON rn.product_id = p.id ORDER BY rn.created_at DESC");
$restocks = $restockStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="admin-tabs">
    <button class="active" onclick="switchTab(this, 'newsletter')">Newsletter (<?php echo count($subscribers); ?>)</button>
    <button onclick="switchTab(this, 'restock')">Restock Alerts (<?php echo count($restocks); ?>)</button>
</div>

<!-- Newsletter Tab -->
<div class="tab-content" id="tab-newsletter">
    <div class="admin-table-wrapper">
        <div class="admin-table-header">
            <h3>Newsletter Subscribers</h3>
            <div class="admin-table-filters">
                <input type="text" class="admin-search" placeholder="Search subscribers...">
                <button class="btn btn-secondary btn-sm" id="exportCSV"><i class="fas fa-download"></i> Export CSV</button>
            </div>
        </div>
        <div style="overflow-x:auto">
            <table class="subscribers-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Email</th>
                        <th>Subscribed Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($subscribers)): ?>
                    <tr><td colspan="3" style="text-align:center;padding:32px;color:var(--text-secondary)">No subscribers yet</td></tr>
                    <?php else: ?>
                    <?php foreach ($subscribers as $i => $sub): ?>
                    <tr>
                        <td><?php echo $i + 1; ?></td>
                        <td><?php echo sanitize($sub['email']); ?></td>
                        <td style="font-size:13px;color:var(--text-secondary)"><?php echo date('M j, Y g:i A', strtotime($sub['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Restock Tab -->
<div class="tab-content" id="tab-restock" style="display:none">
    <div class="admin-table-wrapper">
        <div class="admin-table-header">
            <h3>Restock Notification Requests</h3>
        </div>
        <div style="overflow-x:auto">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Email</th>
                        <th>Product</th>
                        <th>Notified</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($restocks)): ?>
                    <tr><td colspan="5" style="text-align:center;padding:32px;color:var(--text-secondary)">No restock requests</td></tr>
                    <?php else: ?>
                    <?php foreach ($restocks as $i => $r): ?>
                    <tr>
                        <td><?php echo $i + 1; ?></td>
                        <td><?php echo sanitize($r['email']); ?></td>
                        <td><?php echo sanitize($r['product_name'] ?? 'Deleted'); ?></td>
                        <td>
                            <?php if ($r['notified']): ?>
                            <span class="check-badge active"><i class="fas fa-check"></i></span>
                            <?php else: ?>
                            <span class="check-badge inactive"><i class="fas fa-minus"></i></span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:13px;color:var(--text-secondary)"><?php echo date('M j, Y', strtotime($r['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function switchTab(btn, tabId) {
    document.querySelectorAll('.admin-tabs button').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.tab-content').forEach(t => t.style.display = 'none');
    document.getElementById('tab-' + tabId).style.display = 'block';
}
</script>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>
