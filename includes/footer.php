<?php
$settings = $settings ?? getSettings($pdo);
?>
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-about">
                <a href="<?php echo BASE_URL; ?>/" class="navbar-logo">
                    <div class="logo-icon">A</div>
                    Shop<span>WithAlfred</span>
                </a>
                <p><?php echo sanitize($settings['tagline'] ?? SITE_TAGLINE); ?></p>
                <div class="social-icons">
                    <?php if (($settings['facebook_enabled'] ?? '0') === '1' && !empty($settings['facebook_url'])): ?>
                    <a href="<?php echo sanitize($settings['facebook_url']); ?>" target="_blank" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <?php endif; ?>
                    <?php if (($settings['instagram_enabled'] ?? '0') === '1' && !empty($settings['instagram_url'])): ?>
                    <a href="<?php echo sanitize($settings['instagram_url']); ?>" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <?php endif; ?>
                    <?php if (($settings['tiktok_enabled'] ?? '0') === '1' && !empty($settings['tiktok_url'])): ?>
                    <a href="<?php echo sanitize($settings['tiktok_url']); ?>" target="_blank" title="TikTok"><i class="fab fa-tiktok"></i></a>
                    <?php endif; ?>
                    <?php if (($settings['twitter_enabled'] ?? '0') === '1' && !empty($settings['twitter_url'])): ?>
                    <a href="<?php echo sanitize($settings['twitter_url']); ?>" target="_blank" title="X/Twitter"><i class="fab fa-x-twitter"></i></a>
                    <?php endif; ?>
                    <?php if (($settings['whatsapp_channel_enabled'] ?? '0') === '1' && !empty($settings['whatsapp_channel_url'])): ?>
                    <a href="<?php echo sanitize($settings['whatsapp_channel_url']); ?>" target="_blank" title="WhatsApp Channel"><i class="fab fa-whatsapp"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            <div>
                <h4>Quick Links</h4>
                <div class="footer-links">
                    <a href="<?php echo BASE_URL; ?>/">Home</a>
                    <a href="<?php echo BASE_URL; ?>/shop.php">Shop</a>
                    <a href="<?php echo BASE_URL; ?>/contact.php">Contact</a>
                    <?php if (($settings['customer_accounts_enabled'] ?? '1') === '1'): ?>
                    <a href="<?php echo BASE_URL; ?>/login.php">My Account</a>
                    <?php endif; ?>
                </div>
            </div>
            <div>
                <h4>Categories</h4>
                <div class="footer-links">
                    <?php
                    $footerCategories = getCategories($pdo);
                    foreach ($footerCategories as $cat): ?>
                    <a href="<?php echo BASE_URL; ?>/shop.php?category=<?php echo $cat['id']; ?>"><?php echo sanitize($cat['name']); ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="footer-contact">
                <h4>Contact Us</h4>
                <p><i class="fab fa-whatsapp"></i> <?php echo sanitize($settings['whatsapp'] ?? WHATSAPP_DISPLAY); ?></p>
                <p><i class="fas fa-envelope"></i> <?php echo sanitize($settings['contact_email'] ?? ADMIN_EMAIL); ?></p>
                <p><i class="fas fa-map-marker-alt"></i> <?php echo sanitize($settings['location'] ?? ''); ?></p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php echo sanitize($settings['store_name'] ?? SITE_NAME); ?>. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="<?php echo BASE_URL; ?>/assets/js/form-validation.js"></script>
<script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
