<?php
$pageTitle = 'Home';
require_once __DIR__ . '/includes/header.php';

$featuredProducts = getProducts($pdo, ['is_featured' => true, 'limit' => 8]);
$newProducts = getProducts($pdo, ['is_new' => true, 'limit' => 8]);
$categories = getCategories($pdo);
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge"><i class="fas fa-star"></i> Premium Shopping Experience</div>
            <h1>Shop Smart. Shop With <span>Alfred.</span></h1>
            <p>Quality products delivered nationwide across Kenya. Browse our curated collection and order with ease.</p>
            <div style="display:flex;gap:12px;flex-wrap:wrap">
                <a href="<?php echo BASE_URL; ?>/shop.php" class="btn btn-primary btn-lg"><span class="btn-text"><i class="fas fa-shopping-bag"></i> Shop Now</span></a>
                <a href="<?php echo BASE_URL; ?>/contact.php" class="btn btn-outline-white btn-lg"><span class="btn-text"><i class="fas fa-envelope"></i> Contact Us</span></a>
            </div>
        </div>
        <div class="hero-visual">
            <div class="hero-shape"><i class="fas fa-bag-shopping"></i></div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<?php if (!empty($featuredProducts)): ?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Featured Products</h2>
            <p>Hand-picked quality products just for you</p>
            <div class="section-line"></div>
        </div>
        <div class="products-grid">
            <?php foreach ($featuredProducts as $product): ?>
            <a href="<?php echo BASE_URL; ?>/product.php?id=<?php echo $product['id']; ?>" class="product-card <?php echo !$product['in_stock'] ? 'out-of-stock' : ''; ?>">
                <div class="product-card-image">
                    <img src="<?php echo sanitize(getFirstImage($product)); ?>" alt="<?php echo sanitize($product['name']); ?>" loading="lazy">
                    <?php if ($product['is_new']): ?><span class="product-badge badge-new">New</span><?php endif; ?>
                    <?php if ($product['is_featured']): ?><span class="product-badge badge-featured">Featured</span><?php endif; ?>
                    <?php if (!$product['in_stock']): ?><span class="product-badge badge-oos">Out of Stock</span><?php endif; ?>
                </div>
                <div class="product-card-body">
                    <div class="product-card-category"><?php echo sanitize($product['category_name'] ?? ''); ?></div>
                    <h3 class="product-card-name"><?php echo sanitize($product['name']); ?></h3>
                    <div class="product-card-price"><?php echo formatPrice($product['price']); ?></div>
                    <button class="btn btn-primary btn-sm" <?php echo !$product['in_stock'] ? 'disabled' : ''; ?>>
                        <i class="fas fa-shopping-cart"></i> <span class="btn-text"><?php echo $product['in_stock'] ? 'Order Now' : 'Out of Stock'; ?></span>
                    </button>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <div style="text-align:center;margin-top:32px">
            <a href="<?php echo BASE_URL; ?>/shop.php" class="btn btn-secondary">View All Products <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Categories -->
<?php if (!empty($categories)): ?>
<section class="section section-alt" id="categories">
    <div class="container">
        <div class="section-header">
            <h2>Shop By Category</h2>
            <p>Browse our wide range of product categories</p>
            <div class="section-line"></div>
        </div>
        <div class="categories-grid">
            <?php foreach ($categories as $cat): ?>
            <a href="<?php echo BASE_URL; ?>/shop.php?category=<?php echo $cat['id']; ?>" class="category-card">
                <div class="category-card-icon"><i class="fas <?php echo sanitize($cat['icon']); ?>"></i></div>
                <h3><?php echo sanitize($cat['name']); ?></h3>
                <p><?php echo $cat['product_count']; ?> products</p>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- New Arrivals -->
<?php if (!empty($newProducts)): ?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>New Arrivals</h2>
            <p>Check out our latest products</p>
            <div class="section-line"></div>
        </div>
        <div class="products-grid">
            <?php foreach ($newProducts as $product): ?>
            <a href="<?php echo BASE_URL; ?>/product.php?id=<?php echo $product['id']; ?>" class="product-card <?php echo !$product['in_stock'] ? 'out-of-stock' : ''; ?>">
                <div class="product-card-image">
                    <img src="<?php echo sanitize(getFirstImage($product)); ?>" alt="<?php echo sanitize($product['name']); ?>" loading="lazy">
                    <span class="product-badge badge-new">New</span>
                    <?php if (!$product['in_stock']): ?><span class="product-badge badge-oos">Out of Stock</span><?php endif; ?>
                </div>
                <div class="product-card-body">
                    <div class="product-card-category"><?php echo sanitize($product['category_name'] ?? ''); ?></div>
                    <h3 class="product-card-name"><?php echo sanitize($product['name']); ?></h3>
                    <div class="product-card-price"><?php echo formatPrice($product['price']); ?></div>
                    <button class="btn btn-primary btn-sm" <?php echo !$product['in_stock'] ? 'disabled' : ''; ?>>
                        <i class="fas fa-shopping-cart"></i> <span class="btn-text"><?php echo $product['in_stock'] ? 'Order Now' : 'Out of Stock'; ?></span>
                    </button>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Why Shop With Us -->
<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <h2>Why Shop With Us</h2>
            <p>We make your shopping experience seamless</p>
            <div class="section-line"></div>
        </div>
        <div class="benefits-grid">
            <div class="benefit-card">
                <div class="benefit-icon"><i class="fas fa-truck-fast"></i></div>
                <h3>Nationwide Delivery</h3>
                <p>We deliver to all 47 counties across Kenya</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon"><i class="fas fa-shield-halved"></i></div>
                <h3>Quality Products</h3>
                <p>Every product is carefully selected and verified</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon"><i class="fas fa-lock"></i></div>
                <h3>Secure Ordering</h3>
                <p>Your information is safe and secure with us</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon"><i class="fas fa-headset"></i></div>
                <h3>24/7 Support</h3>
                <p>We are always available to help via WhatsApp</p>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter -->
<?php if (($settings['newsletter_enabled'] ?? '1') === '1'): ?>
<section class="newsletter-section">
    <div class="container">
        <h2><i class="fas fa-bell"></i> Stay Updated</h2>
        <p>Subscribe for deals and new arrivals</p>
        <form class="newsletter-form" id="newsletterForm">
            <input type="email" placeholder="Enter your email address" required>
            <button type="submit" class="btn btn-primary"><span class="btn-text">Subscribe</span><span class="spinner"></span></button>
        </form>
    </div>
</section>
<?php endif; ?>

<!-- WhatsApp Group -->
<?php if (($settings['whatsapp_group_enabled'] ?? '0') === '1' && !empty($settings['whatsapp_group_url'])): ?>
<section class="section" style="text-align:center">
    <div class="container whatsapp-section">
        <div class="section-header">
            <h2><i class="fab fa-whatsapp"></i> Join Our Community</h2>
            <p>Get exclusive deals and updates on our WhatsApp group</p>
            <div class="section-line"></div>
        </div>
        <a href="<?php echo sanitize($settings['whatsapp_group_url']); ?>" target="_blank" class="btn btn-lg whatsapp-btn">
            <i class="fab fa-whatsapp"></i> Join WhatsApp Group
        </a>
    </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
