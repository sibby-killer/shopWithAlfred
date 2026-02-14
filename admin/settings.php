<?php
$pageTitle = 'Settings';
require_once __DIR__ . '/../includes/admin-header.php';
require_once __DIR__ . '/../includes/admin-sidebar.php';

$settings = getSettings($pdo);
?>

<div class="admin-tabs">
    <button class="active" onclick="switchTab(this, 'general')">General</button>
    <button onclick="switchTab(this, 'social')">Social Media</button>
    <button onclick="switchTab(this, 'whatsapp')">WhatsApp</button>
    <button onclick="switchTab(this, 'features')">Features</button>
    <button onclick="switchTab(this, 'account')">Admin Account</button>
</div>

<!-- General -->
<div class="tab-content" id="tab-general">
    <form class="settings-form">
        <div class="settings-section">
            <h3><i class="fas fa-store"></i> Store Information</h3>
            <div class="form-row">
                <div class="form-group">
                    <label>Store Name</label>
                    <input type="text" name="store_name" value="<?php echo sanitize($settings['store_name'] ?? SITE_NAME); ?>">
                </div>
                <div class="form-group">
                    <label>Tagline</label>
                    <input type="text" name="tagline" value="<?php echo sanitize($settings['tagline'] ?? SITE_TAGLINE); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Contact Email</label>
                    <input type="email" name="contact_email" value="<?php echo sanitize($settings['contact_email'] ?? ADMIN_EMAIL); ?>">
                </div>
                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" value="<?php echo sanitize($settings['location'] ?? ''); ?>" placeholder="e.g. Nairobi, Kenya">
                </div>
            </div>
            <div class="form-group">
                <label>Default Theme</label>
                <select name="default_theme">
                    <option value="navy-gold" <?php echo ($settings['default_theme'] ?? '') === 'navy-gold' ? 'selected' : ''; ?>>Navy & Gold</option>
                    <option value="soft-blue" <?php echo ($settings['default_theme'] ?? '') === 'soft-blue' ? 'selected' : ''; ?>>Soft Blue</option>
                    <option value="soft-purple" <?php echo ($settings['default_theme'] ?? '') === 'soft-purple' ? 'selected' : ''; ?>>Soft Purple</option>
                    <option value="warm-orange" <?php echo ($settings['default_theme'] ?? '') === 'warm-orange' ? 'selected' : ''; ?>>Warm Orange</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Settings</button>
        </div>
    </form>
</div>

<!-- Social Media -->
<div class="tab-content" id="tab-social" style="display:none">
    <form class="settings-form">
        <div class="settings-section">
            <h3><i class="fas fa-share-nodes"></i> Social Media Links</h3>
            <?php
            $socials = [
                ['facebook', 'Facebook', 'fab fa-facebook-f'],
                ['instagram', 'Instagram', 'fab fa-instagram'],
                ['tiktok', 'TikTok', 'fab fa-tiktok'],
                ['twitter', 'X/Twitter', 'fab fa-x-twitter'],
            ];
            foreach ($socials as $s):
            ?>
            <div class="toggle-row" style="flex-wrap:wrap;gap:12px">
                <div style="display:flex;align-items:center;gap:12px">
                    <input type="checkbox" name="<?php echo $s[0]; ?>_enabled" value="1" <?php echo ($settings[$s[0].'_enabled'] ?? '0') === '1' ? 'checked' : ''; ?>>
                    <i class="<?php echo $s[2]; ?>" style="width:20px"></i>
                    <label><?php echo $s[1]; ?></label>
                </div>
                <input type="url" name="<?php echo $s[0]; ?>_url" value="<?php echo sanitize($settings[$s[0].'_url'] ?? ''); ?>" placeholder="<?php echo $s[1]; ?> URL" style="flex:1;min-width:200px;padding:8px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-family:Poppins,sans-serif;font-size:13px">
            </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary" style="margin-top:20px"><i class="fas fa-save"></i> Save Social Settings</button>
        </div>
    </form>
</div>

<!-- WhatsApp -->
<div class="tab-content" id="tab-whatsapp" style="display:none">
    <form class="settings-form">
        <div class="settings-section">
            <h3><i class="fab fa-whatsapp"></i> WhatsApp Settings</h3>
            <div class="form-row">
                <div class="form-group">
                    <label>WhatsApp Number (with country code)</label>
                    <input type="text" name="whatsapp" value="<?php echo sanitize($settings['whatsapp'] ?? WHATSAPP_DISPLAY); ?>" placeholder="+254762667048">
                </div>
            </div>
            <div class="toggle-row">
                <label><input type="checkbox" name="whatsapp_group_enabled" value="1" <?php echo ($settings['whatsapp_group_enabled'] ?? '0') === '1' ? 'checked' : ''; ?>> Enable WhatsApp Group Link</label>
            </div>
            <div class="form-group">
                <label>WhatsApp Group URL</label>
                <input type="url" name="whatsapp_group_url" value="<?php echo sanitize($settings['whatsapp_group_url'] ?? ''); ?>" placeholder="https://chat.whatsapp.com/...">
            </div>
            <div class="toggle-row">
                <label><input type="checkbox" name="whatsapp_channel_enabled" value="1" <?php echo ($settings['whatsapp_channel_enabled'] ?? '0') === '1' ? 'checked' : ''; ?>> Enable WhatsApp Channel Link</label>
            </div>
            <div class="form-group">
                <label>WhatsApp Channel URL</label>
                <input type="url" name="whatsapp_channel_url" value="<?php echo sanitize($settings['whatsapp_channel_url'] ?? ''); ?>" placeholder="https://whatsapp.com/channel/...">
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top:16px"><i class="fas fa-save"></i> Save WhatsApp Settings</button>
        </div>
    </form>
</div>

<!-- Features -->
<div class="tab-content" id="tab-features" style="display:none">
    <form class="settings-form">
        <div class="settings-section">
            <h3><i class="fas fa-toggle-on"></i> Feature Toggles</h3>
            <div class="toggle-row">
                <label>Customer Accounts</label>
                <input type="checkbox" name="customer_accounts_enabled" value="1" <?php echo ($settings['customer_accounts_enabled'] ?? '1') === '1' ? 'checked' : ''; ?>>
            </div>
            <div class="toggle-row">
                <label>Newsletter Subscription</label>
                <input type="checkbox" name="newsletter_enabled" value="1" <?php echo ($settings['newsletter_enabled'] ?? '1') === '1' ? 'checked' : ''; ?>>
            </div>
            <div class="toggle-row">
                <label>Restock Notifications</label>
                <input type="checkbox" name="restock_notifications_enabled" value="1" <?php echo ($settings['restock_notifications_enabled'] ?? '1') === '1' ? 'checked' : ''; ?>>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top:20px"><i class="fas fa-save"></i> Save Features</button>
        </div>
    </form>
</div>

<!-- Admin Account -->
<div class="tab-content" id="tab-account" style="display:none">
    <div class="settings-section">
        <h3><i class="fas fa-user-shield"></i> Change Admin Password</h3>
        <form id="adminPasswordForm">
            <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current_password" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-key"></i> Change Password</button>
        </form>
    </div>
    <div class="danger-zone">
        <h3><i class="fas fa-exclamation-triangle"></i> Danger Zone</h3>
        <p style="font-size:14px;margin-bottom:16px">These actions are irreversible. Please be careful.</p>
        <button class="btn btn-danger btn-sm" onclick="if(confirm('Delete ALL orders? This cannot be undone!')) api('orders.php',{action:'delete_all'}).then(d=>{showToast(d.message||'Done');location.reload()})">
            <i class="fas fa-trash"></i> Delete All Orders
        </button>
        <button class="btn btn-danger btn-sm" onclick="if(confirm('Delete ALL subscribers? This cannot be undone!')) api('subscribers.php',{action:'delete_all'}).then(d=>{showToast(d.message||'Done');location.reload()})">
            <i class="fas fa-trash"></i> Delete All Subscribers
        </button>
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
