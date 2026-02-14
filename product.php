<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: ' . BASE_URL . '/shop.php'); exit; }

$product = getProduct($pdo, $id);
if (!$product) { header('Location: ' . BASE_URL . '/shop.php'); exit; }

$pageTitle = $product['name'];
$images = getProductImages($product);
$counties = getKenyanCounties();

// Similar products
$similar = getProducts($pdo, ['category_id' => $product['category_id'], 'limit' => 4]);
$similar = array_filter($similar, function($p) use ($id) { return $p['id'] != $id; });
$similar = array_slice($similar, 0, 4);

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-header" style="padding:100px 0 40px">
    <div class="container">
        <div class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>/">Home</a>
            <i class="fas fa-chevron-right"></i>
            <a href="<?php echo BASE_URL; ?>/shop.php?category=<?php echo $product['category_id']; ?>"><?php echo sanitize($product['category_name'] ?? 'Products'); ?></a>
            <i class="fas fa-chevron-right"></i>
            <span><?php echo sanitize($product['name']); ?></span>
        </div>
    </div>
</section>

<section class="section" style="padding-top:40px">
    <div class="container">
        <div class="product-detail">
            <div class="product-gallery">
                <div class="main-image">
                    <img src="<?php echo sanitize($images[0]); ?>" alt="<?php echo sanitize($product['name']); ?>" id="mainImage">
                </div>
                <?php if (count($images) > 1): ?>
                <div class="gallery-thumbs">
                    <?php foreach ($images as $i => $img): ?>
                    <img src="<?php echo sanitize($img); ?>" alt="Image <?php echo $i + 1; ?>" class="<?php echo $i === 0 ? 'active' : ''; ?>">
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="product-info">
                <h1><?php echo sanitize($product['name']); ?></h1>
                <div class="product-price"><?php echo formatPrice($product['price']); ?></div>
                
                <div class="product-meta">
                    <span class="meta-badge"><i class="fas <?php echo sanitize($product['category_icon'] ?? 'fa-tag'); ?>"></i> <?php echo sanitize($product['category_name'] ?? ''); ?></span>
                    <span class="meta-badge"><i class="fas fa-venus-mars"></i> <?php echo ucfirst($product['gender']); ?></span>
                    <?php if ($product['in_stock']): ?>
                    <span class="meta-badge in-stock"><i class="fas fa-check-circle"></i> In Stock</span>
                    <?php else: ?>
                    <span class="meta-badge out-of-stock"><i class="fas fa-times-circle"></i> Out of Stock</span>
                    <?php endif; ?>
                </div>

                <?php if ($product['description']): ?>
                <div class="product-description"><?php echo nl2br(sanitize($product['description'])); ?></div>
                <?php endif; ?>

                <?php if ($product['in_stock']): ?>
                <div class="quantity-selector" data-price="<?php echo $product['price']; ?>">
                    <label style="font-weight:600;margin-right:12px">Quantity:</label>
                    <button type="button" class="qty-minus"><i class="fas fa-minus"></i></button>
                    <input type="number" value="1" min="1" max="10" id="productQty">
                    <button type="button" class="qty-plus"><i class="fas fa-plus"></i></button>
                </div>
                <div class="subtotal">Subtotal: <span><?php echo formatPrice($product['price']); ?></span></div>
                <button class="btn btn-primary btn-lg open-order-modal">
                    <i class="fas fa-shopping-cart"></i> <span class="btn-text">Order Now</span>
                </button>
                <?php else: ?>
                <button class="btn btn-primary btn-lg" disabled>
                    <i class="fas fa-times-circle"></i> Out of Stock
                </button>
                <div class="notify-form">
                    <h4><i class="fas fa-bell"></i> Notify Me When Available</h4>
                    <form id="notifyForm" class="form-inline" style="display:flex;gap:8px;margin-top:12px">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="email" placeholder="Your email address" required style="flex:1;padding:10px 14px;border:1px solid var(--border);border-radius:var(--radius-sm);font-family:Poppins,sans-serif">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-bell"></i> Notify Me</button>
                    </form>
                </div>
                <?php endif; ?>
                
                <div class="transport-note" style="margin-top:20px">
                    <i class="fas fa-truck"></i> Transport fee will be communicated after confirming your location
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Similar Products -->
<?php if (!empty($similar)): ?>
<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <h2>You May Also Like</h2>
            <div class="section-line"></div>
        </div>
        <div class="products-grid">
            <?php foreach ($similar as $sp): ?>
            <a href="<?php echo BASE_URL; ?>/product.php?id=<?php echo $sp['id']; ?>" class="product-card <?php echo !$sp['in_stock'] ? 'out-of-stock' : ''; ?>">
                <div class="product-card-image">
                    <img src="<?php echo sanitize(getFirstImage($sp)); ?>" alt="<?php echo sanitize($sp['name']); ?>" loading="lazy">
                    <?php if (!$sp['in_stock']): ?><span class="product-badge badge-oos">Out of Stock</span><?php endif; ?>
                </div>
                <div class="product-card-body">
                    <div class="product-card-category"><?php echo sanitize($sp['category_name'] ?? ''); ?></div>
                    <h3 class="product-card-name"><?php echo sanitize($sp['name']); ?></h3>
                    <div class="product-card-price"><?php echo formatPrice($sp['price']); ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Order Modal -->
<div class="modal-overlay" id="orderModal">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-shopping-cart"></i> Complete Your Order</h3>
            <button class="modal-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="order-summary">
                <img src="<?php echo sanitize($images[0]); ?>" alt="<?php echo sanitize($product['name']); ?>">
                <div class="order-summary-info">
                    <h4><?php echo sanitize($product['name']); ?></h4>
                    <p style="color:var(--accent);font-weight:600"><?php echo formatPrice($product['price']); ?></p>
                    <p style="font-size:13px;color:var(--text-secondary)">Subtotal: <span class="order-subtotal"><?php echo formatPrice($product['price']); ?></span></p>
                </div>
            </div>
            <div class="transport-note">
                <i class="fas fa-info-circle"></i> Transport fee will be confirmed based on your location
            </div>
            
            <form id="orderForm">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="hidden" name="product_name" value="<?php echo sanitize($product['name']); ?>">
                <input type="hidden" name="unit_price" value="<?php echo $product['price']; ?>">
                <input type="hidden" name="category" value="<?php echo sanitize($product['category_name'] ?? ''); ?>">
                <input type="hidden" name="product_gender" value="<?php echo $product['gender']; ?>">
                <input type="hidden" name="quantity" value="1" id="orderQty">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                <div class="form-group">
                    <label>Full Name <span class="required">*</span></label>
                    <input type="text" name="customer_name" required>
                    <div class="error-message"></div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Phone Number <span class="required">*</span></label>
                        <input type="tel" name="customer_phone" placeholder="07XXXXXXXX" required>
                        <div class="error-message"></div>
                    </div>
                    <div class="form-group">
                        <label>Alt Phone</label>
                        <input type="tel" name="customer_alt_phone" placeholder="Optional">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Email <span class="required">*</span></label>
                        <input type="email" name="customer_email" required>
                        <div class="error-message"></div>
                    </div>
                    <div class="form-group">
                        <label>Gender <span class="required">*</span></label>
                        <select name="customer_gender" required>
                            <option value="">Select</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                        <div class="error-message"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label>County <span class="required">*</span></label>
                    <select name="county" required>
                        <option value="">Select County</option>
                        <?php foreach ($counties as $county): ?>
                        <option value="<?php echo sanitize($county); ?>"><?php echo sanitize($county); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="error-message"></div>
                </div>
                <div class="form-group">
                    <label>Delivery Address <span class="required">*</span></label>
                    <textarea name="address" rows="2" required placeholder="Enter your full delivery address"></textarea>
                    <div class="error-message"></div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Preferred Delivery Date <span class="required">*</span></label>
                        <input type="date" name="delivery_date" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                        <div class="error-message"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Additional Notes</label>
                    <textarea name="notes" rows="2" placeholder="Any special instructions?"></textarea>
                </div>

                <?php if (($settings['customer_accounts_enabled'] ?? '1') === '1' && !isLoggedIn()): ?>
                <label class="form-check">
                    <input type="checkbox" name="create_account">
                    <span>Create an account to track your orders</span>
                </label>
                <div id="passwordFields" style="display:none">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Password <span class="required">*</span></label>
                            <input type="password" name="password">
                            <div class="error-message"></div>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password <span class="required">*</span></label>
                            <input type="password" name="confirm_password">
                            <div class="error-message"></div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary btn-lg btn-submit" style="width:100%;margin-top:16px">
                    <i class="fab fa-whatsapp"></i> <span class="btn-text">Send Order via WhatsApp</span><span class="spinner"></span>
                </button>
            </form>
        </div>
        <div class="modal-footer">
            <p><i class="fas fa-lock"></i> By ordering, you agree to our terms. Payment details will be shared on WhatsApp.</p>
        </div>
    </div>
</div>

<script>
// Sync quantity between product page and modal
const productQty = document.getElementById('productQty');
const orderQty = document.getElementById('orderQty');
if (productQty && orderQty) {
    const observer = new MutationObserver(() => { orderQty.value = productQty.value; });
    productQty.addEventListener('change', () => { orderQty.value = productQty.value; });
    productQty.addEventListener('input', () => { orderQty.value = productQty.value; });
    
    // Also watch the qty buttons
    document.querySelectorAll('.qty-minus, .qty-plus').forEach(b => {
        b.addEventListener('click', () => { setTimeout(() => { orderQty.value = productQty.value; }, 10); });
    });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
