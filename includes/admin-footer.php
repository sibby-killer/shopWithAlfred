    </div><!-- /admin-content -->
</div><!-- /admin-main -->
</div><!-- /admin-layout -->

<!-- Delete Confirmation Modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Confirm Delete</h3>
            <button class="modal-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body" style="text-align:center;padding:32px">
            <i class="fas fa-exclamation-triangle" style="font-size:48px;color:var(--error);margin-bottom:16px"></i>
            <p>Are you sure you want to delete <strong id="deleteProductName"></strong>?</p>
            <p style="font-size:13px;color:var(--text-secondary);margin-top:8px">This action cannot be undone.</p>
            <div style="display:flex;gap:12px;justify-content:center;margin-top:24px">
                <button class="btn btn-secondary" onclick="closeModal('deleteModal')">Cancel</button>
                <button class="btn btn-danger" id="confirmDeleteProduct">Delete</button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>/assets/js/admin.js"></script>
</body>
</html>
