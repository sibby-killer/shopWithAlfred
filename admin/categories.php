<?php
$pageTitle = 'Categories';
require_once __DIR__ . '/../includes/admin-header.php';
require_once __DIR__ . '/../includes/admin-sidebar.php';

$categories = getCategories($pdo);
?>

<div class="admin-table-wrapper">
    <div class="admin-table-header">
        <h3>All Categories (<?php echo count($categories); ?>)</h3>
        <button class="btn btn-primary btn-sm" id="addCategoryBtn"><i class="fas fa-plus"></i> Add Category</button>
    </div>
    <div style="overflow-x:auto">
        <table>
            <thead>
                <tr>
                    <th>Icon</th>
                    <th>Name</th>
                    <th>Products</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                <tr><td colspan="4" style="text-align:center;padding:32px;color:var(--text-secondary)">No categories yet</td></tr>
                <?php else: ?>
                <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><i class="fas <?php echo sanitize($cat['icon']); ?>" style="font-size:20px;color:var(--accent)"></i></td>
                    <td><strong><?php echo sanitize($cat['name']); ?></strong></td>
                    <td><?php echo $cat['product_count']; ?></td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-edit edit-category" data-id="<?php echo $cat['id']; ?>" title="Edit"><i class="fas fa-pen"></i></button>
                            <button class="btn-delete delete-category" data-id="<?php echo $cat['id']; ?>" data-name="<?php echo sanitize($cat['name']); ?>" title="Delete"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Category Modal -->
<div class="modal-overlay" id="categoryModal">
    <div class="modal" style="max-width:500px">
        <div class="modal-header">
            <h3 id="categoryFormTitle">Add Category</h3>
            <button class="modal-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <form id="categoryForm">
                <input type="hidden" name="id" id="categoryId">
                <div class="form-group">
                    <label>Category Name <span class="required">*</span></label>
                    <input type="text" id="categoryName" name="name" required>
                </div>
                <div class="form-group">
                    <label>Font Awesome Icon Class</label>
                    <input type="text" id="categoryIcon" name="icon" placeholder="fa-tshirt" value="fa-tag">
                    <small style="color:var(--text-secondary)">Use Font Awesome 6 icon class, e.g. fa-shoe-prints, fa-tshirt, fa-watch</small>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;margin-top:16px">
                    <i class="fas fa-save"></i> Save Category
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>
