<?php
$pageTitle = 'Contact Us';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1>Contact Us</h1>
        <div class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>/">Home</a>
            <i class="fas fa-chevron-right"></i>
            <span>Contact</span>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="contact-grid">
            <div class="contact-info">
                <h2>Get In Touch</h2>
                <p>Have a question or need help? We're here for you.</p>

                <div class="contact-card">
                    <div class="contact-card-icon whatsapp"><i class="fab fa-whatsapp"></i></div>
                    <div>
                        <h4>WhatsApp</h4>
                        <p>Chat with us directly</p>
                        <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>" target="_blank" class="btn btn-sm whatsapp-btn">
                            <i class="fab fa-whatsapp"></i> Chat Now
                        </a>
                    </div>
                </div>

                <div class="contact-card">
                    <div class="contact-card-icon"><i class="fas fa-envelope"></i></div>
                    <div>
                        <h4>Email</h4>
                        <p><?php echo sanitize($settings['contact_email'] ?? ADMIN_EMAIL); ?></p>
                    </div>
                </div>

                <div class="contact-card">
                    <div class="contact-card-icon"><i class="fas fa-phone"></i></div>
                    <div>
                        <h4>Phone</h4>
                        <p><?php echo sanitize($settings['whatsapp'] ?? WHATSAPP_DISPLAY); ?></p>
                    </div>
                </div>

                <?php if (!empty($settings['location'])): ?>
                <div class="contact-card">
                    <div class="contact-card-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div>
                        <h4>Location</h4>
                        <p><?php echo sanitize($settings['location']); ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <div class="social-section" style="margin-top:32px">
                    <h4>Follow Us</h4>
                    <div class="social-icons" style="margin-top:12px">
                        <?php if (!empty($settings['facebook_url'])): ?>
                        <a href="<?php echo sanitize($settings['facebook_url']); ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($settings['instagram_url'])): ?>
                        <a href="<?php echo sanitize($settings['instagram_url']); ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($settings['tiktok_url'])): ?>
                        <a href="<?php echo sanitize($settings['tiktok_url']); ?>" target="_blank"><i class="fab fa-tiktok"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($settings['twitter_url'])): ?>
                        <a href="<?php echo sanitize($settings['twitter_url']); ?>" target="_blank"><i class="fab fa-x-twitter"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="contact-form-wrapper">
                <h2>Send a Message</h2>
                <p>Fill out the form and we'll respond via WhatsApp</p>
                <form id="contactForm">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <div class="form-group">
                        <label>Your Name</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Your Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Subject</label>
                        <input type="text" name="subject" required>
                    </div>
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg" style="width:100%">
                        <i class="fab fa-whatsapp"></i> <span class="btn-text">Send via WhatsApp</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
