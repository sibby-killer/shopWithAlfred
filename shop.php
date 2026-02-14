<?php
$pageTitle = 'Shop';
require_once __DIR__ . '/includes/header.php';

$categories = getCategories($pdo);
$counties = getKenyanCounties();

// Build filters from GET params
$filters = [];
if (!empty($_GET['category'])) $filters['category_id'] = intval($_GET['category']);
if (!empty($_GET['gender'])) $filters['gender'] = $_GET['gender'];
if (!empty($_GET['search'])) $filters['search'] = $_GET['search'];
if (!empty($_GET['min_price'])) $filters['min_price'] = floatval($_GET['min_price']);
if (!empty($_GET['max_price'])) $filters['max_price'] = floatval($_GET['max_price']);
if (isset($_GET['in_stock']) && $_GET['in_stock'] === '1') $filters['in_stock'] = 1;

// Pagination
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 12;
$filters['limit'] = $perPage;
$filters['offset'] = ($page - 1) * $perPage;

$products = getProducts($pdo, $filters);

// Total count for pagination
$countFilters = $filters;
unset($countFilters['limit'], $countFilters['offset']);
$allProducts = getProducts($pdo, $countFilters);
$totalProducts = count($allProducts);
$totalPages = ceil($totalProducts / $perPage);
?>

<section class="page-header">
    <div class="container">
        <h1>All Products</h1>
        <div class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>/">Home</a>
            <i class="fas fa-chevron-right"></i>
            <span>Shop</span>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <!-- Mobile filter toggle -->
        <button class="btn btn-secondary" id="filterToggle" style="display:none;margin-bottom:20px">
            <i class="fas fa-filter"></i> Filters
        </button>

        <div class="shop-layout">
            <aside class="filters-sidebar">
                <form id="filterForm">
                    <div class="filter-group">
                        <h4><i class="fas fa-search"></i> Search</h4>
                        <input type="text" name="search" placeholder="Search products..." value="<?php echo sanitize($_GET['search'] ?? ''); ?>">
                    </div>
                    <div class="filter-group">
                        <h4><i class="fas fa-folder"></i> Category</h4>
                        <?php foreach ($categories as $cat): ?>
                        <label>
                            <input type="checkbox" name="category" value="<?php echo $cat['id']; ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'checked' : ''; ?>>
                            <?php echo sanitize($cat['name']); ?> (<?php echo $cat['product_count']; ?>)
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="filter-group">
                        <h4><i class="fas fa-venus-mars"></i> Gender</h4>
                        <?php foreach (['men' => 'Men', 'women' => 'Women', 'kids' => 'Kids', 'unisex' => 'Unisex'] as $val => $label): ?>
                        <label>
                            <input type="checkbox" name="gender" value="<?php echo $val; ?>" <?php echo (isset($_GET['gender']) && $_GET['gender'] === $val) ? 'checked' : ''; ?>>
                            <?php echo $label; ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="filter-group">
                        <h4><i class="fas fa-tag"></i> Price Range (KES)</h4>
                        <div class="price-range">
                            <input type="number" name="min_price" placeholder="Min" value="<?php echo sanitize($_GET['min_price'] ?? ''); ?>">
                            <span>-</span>
                            <input type="number" name="max_price" placeholder="Max" value="<?php echo sanitize($_GET['max_price'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="filter-group">
                        <label>
                            <input type="checkbox" name="in_stock" value="1" <?php echo (isset($_GET['in_stock']) && $_GET['in_stock'] === '1') ? 'checked' : ''; ?>>
                            In Stock Only
                        </label>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm clear-filters" style="width:100%">
                        <i class="fas fa-times"></i> Clear Filters
                    </button>
                </form>
            </aside>

            <div>
                <?php if (empty($products)): ?>
                <div class="empty-state">
                    <i class="fas fa-search"></i>
                    <h3>No products found</h3>
                    <p>Try different filters or search terms</p>
                </div>
                <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                    <a href="<?php echo BASE_URL; ?>/product.php?id=<?php echo $product['id']; ?>" class="product-card <?php echo !$product['in_stock'] ? 'out-of-stock' : ''; ?>">
                        <div class="product-card-image">
                            <img src="<?php echo sanitize(getFirstImage($product)); ?>" alt="<?php echo sanitize($product['name']); ?>" loading="lazy">
                            <?php if ($product['is_new']): ?><span class="product-badge badge-new">New</span><?php endif; ?>
                            <?php if (!$product['in_stock']): ?><span class="product-badge badge-oos">Out of Stock</span><?php endif; ?>
                        </div>
                        <div class="product-card-body">
                            <div class="product-card-category"><?php echo sanitize($product['category_name'] ?? ''); ?></div>
                            <h3 class="product-card-name"><?php echo sanitize($product['name']); ?></h3>
                            <div class="product-card-price"><?php echo formatPrice($product['price']); ?></div>
                            <button class="btn btn-primary btn-sm" <?php echo !$product['in_stock'] ? 'disabled' : ''; ?>>
                                <i class="fas fa-shopping-cart"></i> <?php echo $product['in_stock'] ? 'Order Now' : 'Out of Stock'; ?>
                            </button>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php
                    $params = $_GET;
                    $params['page'] = $i;
                    $url = '?' . http_build_query($params);
                    ?>
                    <?php if ($i === $page): ?>
                        <span class="active"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="<?php echo $url; ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
@media (max-width: 768px) { #filterToggle { display: block !important; } }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
