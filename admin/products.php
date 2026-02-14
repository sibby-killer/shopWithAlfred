<?php
$pageTitle = 'Products';
require_once __DIR__ . '/../includes/admin-header.php';
require_once __DIR__ . '/../includes/admin-sidebar.php';

$products = getProducts($pdo);
$categories = getCategories($pdo);
?>

<div class="admin-table-wrapper">
    <div class="admin-table-header">
        <h3>All Products (<?php echo count($products); ?>)</h3>
        <div class="admin-table-filters">
            <input type="text" class="admin-search" placeholder="Search products...">
            <button class="btn btn-primary btn-sm" id="addProductBtn"><i class="fas fa-plus"></i> Add Product</button>
        </div>
    </div>
    <div style="overflow-x:auto">
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Gender</th>
                    <th>Stock</th>
                    <th>Featured</th>
                    <th>New</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                <tr><td colspan="9" style="text-align:center;padding:32px;color:var(--text-secondary)">No products yet. Click "Add Product" to get started.</td></tr>
                <?php else: ?>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td><img src="<?php echo sanitize(getFirstImage($p)); ?>" class="table-img" alt=""></td>
                    <td><strong><?php echo sanitize($p['name']); ?></strong></td>
                    <td><?php echo sanitize($p['category_name'] ?? '—'); ?></td>
                    <td style="font-weight:600"><?php echo formatPrice($p['price']); ?></td>
                    <td><?php echo ucfirst($p['gender']); ?></td>
                    <td><button class="stock-toggle <?php echo $p['in_stock'] ? 'active' : ''; ?>" data-id="<?php echo $p['id']; ?>"></button></td>
                    <td><span class="check-badge <?php echo $p['is_featured'] ? 'active' : 'inactive'; ?>"><i class="fas <?php echo $p['is_featured'] ? 'fa-check' : 'fa-minus'; ?>"></i></span></td>
                    <td><span class="check-badge <?php echo $p['is_new'] ? 'active' : 'inactive'; ?>"><i class="fas <?php echo $p['is_new'] ? 'fa-check' : 'fa-minus'; ?>"></i></span></td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-edit edit-product" data-id="<?php echo $p['id']; ?>" title="Edit"><i class="fas fa-pen"></i></button>
                            <button class="btn-delete delete-product" data-id="<?php echo $p['id']; ?>" data-name="<?php echo sanitize($p['name']); ?>" title="Delete"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Product Modal -->
<div class="modal-overlay" id="productModal">
    <div class="modal">
        <div class="modal-header">
            <h3 id="productFormTitle">Add New Product</h3>
            <button class="modal-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <form id="productForm">
                <input type="hidden" name="id" id="productId">

                <div class="form-group">
                    <label>Jumia Link (Optional)</label>
                    <div style="display:flex;gap:8px">
                        <input type="url" id="jumiaLink" placeholder="Paste Jumia product URL..." style="flex:1">
                        <button type="button" class="btn btn-secondary btn-sm" id="extractJumia"><i class="fas fa-download"></i> Extract</button>
                    </div>
                    <small style="color:var(--text-secondary)">Auto-fill product details from Jumia (for internal use only)</small>
                </div>

                <div class="form-group">
                    <label>Product Name <span class="required">*</span></label>
                    <input type="text" id="productName" name="name" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Price (KES) <span class="required">*</span></label>
                        <input type="number" id="productPrice" name="price" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select id="productCategory" name="category_id">
                            <option value="">Select category</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo sanitize($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Gender Target</label>
                    <select id="productGender" name="gender">
                        <option value="unisex">Unisex</option>
                        <option value="men">Men</option>
                        <option value="women">Women</option>
                        <option value="kids">Kids</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea id="productDesc" name="description" rows="3" placeholder="Product description..."></textarea>
                </div>
                <div class="form-group">
                    <label>Image URLs (one per line)</label>
                    <textarea id="productImages" name="images" rows="3" placeholder="https://example.com/image1.jpg&#10;https://example.com/image2.jpg"></textarea>
                    <small style="color:var(--text-secondary)">Paste image URLs, one per line</small>
                </div>
                <div class="form-row">
                    <label class="form-check"><input type="checkbox" id="productStock" name="in_stock" value="1" checked> In Stock</label>
                    <label class="form-check"><input type="checkbox" id="productFeatured" name="is_featured" value="1"> Featured</label>
                    <label class="form-check"><input type="checkbox" id="productNew" name="is_new" value="1"> New Arrival</label>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;margin-top:16px">
                    <i class="fas fa-save"></i> <span class="btn-text">Save Product</span><span class="spinner"></span>
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>
