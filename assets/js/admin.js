/**
 * ShopWithAlfred - Admin Dashboard JavaScript
 */
document.addEventListener('DOMContentLoaded', function () {

    const BASE = document.querySelector('meta[name="base-url"]')?.content || '';

    // =====================================================
    // SIDEBAR TOGGLE
    // =====================================================
    const sidebar = document.querySelector('.admin-sidebar');
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const mobileMenuBtn = document.getElementById('adminMobileMenu');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('collapsed'));
    }
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', () => sidebar.classList.toggle('mobile-open'));
    }

    // =====================================================
    // MODAL HELPERS
    // =====================================================
    window.openModal = function (id) {
        const m = document.getElementById(id);
        if (m) { m.classList.add('active'); document.body.style.overflow = 'hidden'; }
    };
    window.closeModal = function (id) {
        const m = document.getElementById(id);
        if (m) { m.classList.remove('active'); document.body.style.overflow = ''; }
    };
    document.querySelectorAll('.modal-close').forEach(btn => {
        btn.addEventListener('click', () => {
            const m = btn.closest('.modal-overlay');
            if (m) { m.classList.remove('active'); document.body.style.overflow = ''; }
        });
    });
    document.querySelectorAll('.modal-overlay').forEach(m => {
        m.addEventListener('click', (e) => {
            if (e.target === m) { m.classList.remove('active'); document.body.style.overflow = ''; }
        });
    });

    // =====================================================
    // TOAST
    // =====================================================
    window.showToast = function (msg, type = 'success') {
        let c = document.querySelector('.toast-container');
        if (!c) { c = document.createElement('div'); c.className = 'toast-container'; document.body.appendChild(c); }
        const t = document.createElement('div');
        t.className = `toast ${type}`;
        t.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i><span>${msg}</span>`;
        c.appendChild(t);
        setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 300); }, 4000);
    };

    // =====================================================
    // API HELPER
    // =====================================================
    async function api(endpoint, method = 'GET', data = null) {
        const opts = { method, headers: {} };
        if (data instanceof FormData) {
            opts.body = data;
        } else if (data) {
            opts.headers['Content-Type'] = 'application/json';
            opts.body = JSON.stringify(data);
        }
        const res = await fetch(BASE + '/api/' + endpoint, opts);
        return res.json();
    }

    // =====================================================
    // PRODUCTS MANAGEMENT
    // =====================================================
    const addProductBtn = document.getElementById('addProductBtn');
    const productForm = document.getElementById('productForm');
    const extractBtn = document.getElementById('extractJumia');

    if (addProductBtn) {
        addProductBtn.addEventListener('click', () => {
            if (productForm) productForm.reset();
            document.getElementById('productFormTitle').textContent = 'Add New Product';
            document.getElementById('productId').value = '';
            openModal('productModal');
        });
    }

    // Jumia extraction — uses bookmarklet approach (CORS proxies don't work due to Cloudflare)
    // Helper: parse extracted Jumia data and fill form fields
    function fillFromJumiaData(data) {
        if (data.name) document.getElementById('productName').value = data.name;
        if (data.price) document.getElementById('productPrice').value = data.price;
        if (data.description) document.getElementById('productDesc').value = data.description;
        if (data.images && data.images.length) document.getElementById('productImages').value = data.images.join('\n');
        return !!(data.name || data.price);
    }

    if (extractBtn) {
        // Change button to show instructions
        extractBtn.addEventListener('click', () => {
            const url = document.getElementById('jumiaLink').value.trim();
            showExtractHelper(url);
        });
    }

    function showExtractHelper(url) {
        let helper = document.getElementById('extractHelper');
        if (helper) { helper.style.display = 'block'; return; }

        const jumiaGroup = document.getElementById('jumiaLink')?.closest('.form-group');
        if (!jumiaGroup) return;

        helper = document.createElement('div');
        helper.id = 'extractHelper';
        helper.style.cssText = 'margin-top:12px;padding:16px;background:var(--bg-secondary);border-radius:8px;border:1px solid var(--border)';

        // Bookmarklet code - extracts product data from Jumia page
        const bookmarkletCode = `javascript:void(function(){try{var d={};var h=document.querySelector('h1');if(h)d.name=h.textContent.trim();var p=document.body.innerHTML.match(/KSh\\s*([\\d,]+)/i);if(p)d.price=p[1].replace(/,/g,'');var desc=document.querySelector('[class*=markup]');if(desc)d.description=desc.textContent.trim().substring(0,500);d.images=[];document.querySelectorAll('[data-zoom-image]').forEach(function(e){var s=e.getAttribute('data-zoom-image');if(s&&d.images.indexOf(s)<0)d.images.push(s)});if(!d.images.length){document.querySelectorAll('img').forEach(function(e){var s=e.src;if(s&&s.indexOf('jumia')>-1&&(s.indexOf('.jpg')>-1||s.indexOf('.png')>-1)&&d.images.indexOf(s)<0&&d.images.length<5)d.images.push(s)})}var t='JUMIA_DATA:'+JSON.stringify(d);navigator.clipboard.writeText(t).then(function(){alert('Product data copied! Go to ShopWithAlfred admin and paste it.')}).catch(function(){prompt('Copy this text:',t)})}catch(e){alert('Error: '+e.message)}})()`;

        helper.innerHTML = `
            <p style="font-size:14px;font-weight:600;margin-bottom:12px;color:var(--text-primary)">
                <i class="fas fa-magic" style="color:var(--accent)"></i> How to Extract from Jumia:
            </p>
            <div style="background:var(--bg-primary);padding:12px;border-radius:6px;margin-bottom:12px">
                <p style="font-size:13px;margin-bottom:8px"><strong>Option 1: Bookmarklet</strong> (one-time setup)</p>
                <ol style="font-size:12px;padding-left:20px;color:var(--text-secondary);line-height:1.8">
                    <li>Drag this button to your <strong>Bookmarks Bar</strong>: 
                        <a href="${bookmarkletCode}" onclick="event.preventDefault();showToast('Drag this to your bookmarks bar!','warning')" 
                           style="display:inline-block;padding:4px 12px;background:var(--accent);color:white;border-radius:4px;text-decoration:none;font-weight:600;font-size:11px;cursor:grab">
                           📦 Grab Jumia Product</a></li>
                    <li>Go to any <strong>Jumia product page</strong></li>
                    <li>Click the <strong>"Grab Jumia Product"</strong> bookmark</li>
                    <li>Come back here and <strong>paste</strong> in the box below</li>
                </ol>
            </div>
            <div style="background:var(--bg-primary);padding:12px;border-radius:6px;margin-bottom:12px">
                <p style="font-size:13px;margin-bottom:8px"><strong>Option 2: Quick Copy-Paste</strong></p>
                <ol style="font-size:12px;padding-left:20px;color:var(--text-secondary);line-height:1.8">
                    <li>Open the Jumia product page</li>
                    <li>Open browser console: <kbd style="background:var(--bg-secondary);padding:2px 6px;border-radius:3px;font-size:11px">F12</kbd> → Console tab</li>
                    <li>Paste this code and press Enter:</li>
                </ol>
                <div style="position:relative;margin-top:8px">
                    <pre id="consoleCode" style="background:#1a1a2e;color:#0f0;padding:10px;border-radius:4px;font-size:11px;overflow-x:auto;white-space:pre-wrap;cursor:pointer"
                         onclick="navigator.clipboard.writeText(this.textContent);showToast('Code copied!','success')"
                    >var d={};var h=document.querySelector('h1');if(h)d.name=h.textContent.trim();var p=document.body.innerHTML.match(/KSh\\s*([\\d,]+)/i);if(p)d.price=p[1].replace(/,/g,'');d.images=[];document.querySelectorAll('[data-zoom-image]').forEach(function(e){var s=e.getAttribute('data-zoom-image');if(s)d.images.push(s)});copy('JUMIA_DATA:'+JSON.stringify(d));console.log('Copied!',d);</pre>
                    <small style="color:var(--text-secondary)">(Click the code to copy it)</small>
                </div>
            </div>
            <div style="margin-top:12px">
                <label style="font-size:13px;font-weight:500;display:block;margin-bottom:4px">Paste extracted data here:</label>
                <textarea id="pasteJumiaData" rows="2" placeholder="Paste here after using the bookmarklet or console code..." style="width:100%;font-size:12px"></textarea>
                <button type="button" class="btn btn-primary btn-sm" id="parseJumiaData" style="margin-top:8px">
                    <i class="fas fa-check"></i> Fill Product Details
                </button>
            </div>`;
        jumiaGroup.appendChild(helper);

        // Parse pasted data
        document.getElementById('parseJumiaData').addEventListener('click', () => {
            const raw = document.getElementById('pasteJumiaData').value.trim();
            if (!raw) { showToast('Please paste the extracted data first', 'error'); return; }

            try {
                let jsonStr = raw;
                // Handle JUMIA_DATA: prefix
                if (raw.startsWith('JUMIA_DATA:')) {
                    jsonStr = raw.substring('JUMIA_DATA:'.length);
                }
                const data = JSON.parse(jsonStr);
                if (fillFromJumiaData(data)) {
                    showToast('Product details filled!', 'success');
                    helper.style.display = 'none';
                } else {
                    showToast('No product details found in pasted data.', 'error');
                }
            } catch (e) {
                showToast('Invalid data format. Make sure you copied correctly.', 'error');
            }
        });
    }

    // Save product
    if (productForm) {
        productForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const fd = new FormData(productForm);
            const id = fd.get('id');
            fd.append('action', id ? 'update' : 'create');
            try {
                const data = await api('products.php', 'POST', fd);
                if (data.success) {
                    showToast(id ? 'Product updated!' : 'Product added!', 'success');
                    closeModal('productModal');
                    setTimeout(() => location.reload(), 500);
                } else {
                    showToast(data.message || 'Failed to save.', 'error');
                }
            } catch (e) { showToast('Error saving product.', 'error'); }
        });
    }

    // Edit product
    document.querySelectorAll('.edit-product').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            try {
                const data = await api('products.php?id=' + id);
                if (data.success && data.product) {
                    const p = data.product;
                    document.getElementById('productFormTitle').textContent = 'Edit Product';
                    document.getElementById('productId').value = p.id;
                    document.getElementById('productName').value = p.name;
                    document.getElementById('jumiaLink').value = p.jumia_link || '';
                    document.getElementById('productPrice').value = p.price;
                    document.getElementById('productCategory').value = p.category_id || '';
                    document.getElementById('productGender').value = p.gender || 'unisex';
                    document.getElementById('productDesc').value = p.description || '';
                    document.getElementById('productImages').value = (JSON.parse(p.images || '[]')).join('\n');
                    document.getElementById('productStock').checked = p.in_stock == 1;
                    document.getElementById('productFeatured').checked = p.is_featured == 1;
                    document.getElementById('productNew').checked = p.is_new == 1;
                    openModal('productModal');
                }
            } catch (e) { showToast('Error loading product.', 'error'); }
        });
    });

    // Delete product
    document.querySelectorAll('.delete-product').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            const name = btn.dataset.name || 'this product';
            document.getElementById('deleteProductName').textContent = name;
            document.getElementById('confirmDeleteProduct').onclick = async () => {
                try {
                    const data = await api('products.php', 'POST', { action: 'delete', id });
                    if (data.success) { showToast('Product deleted!', 'success'); closeModal('deleteModal'); setTimeout(() => location.reload(), 500); }
                    else showToast(data.message || 'Failed.', 'error');
                } catch (e) { showToast('Error.', 'error'); }
            };
            openModal('deleteModal');
        });
    });

    // Stock toggle
    document.querySelectorAll('.stock-toggle').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            const current = btn.classList.contains('active');
            try {
                const data = await api('products.php', 'POST', { action: 'toggle_stock', id, in_stock: current ? 0 : 1 });
                if (data.success) {
                    btn.classList.toggle('active');
                    showToast(current ? 'Marked out of stock' : 'Marked in stock', 'success');
                }
            } catch (e) { showToast('Error.', 'error'); }
        });
    });

    // =====================================================
    // CATEGORIES MANAGEMENT
    // =====================================================
    const addCategoryBtn = document.getElementById('addCategoryBtn');
    const categoryForm = document.getElementById('categoryForm');

    if (addCategoryBtn) {
        addCategoryBtn.addEventListener('click', () => {
            if (categoryForm) categoryForm.reset();
            document.getElementById('categoryFormTitle').textContent = 'Add Category';
            document.getElementById('categoryId').value = '';
            openModal('categoryModal');
        });
    }
    if (categoryForm) {
        categoryForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const fd = new FormData(categoryForm);
            const id = fd.get('id');
            fd.append('action', id ? 'update' : 'create');
            try {
                const data = await api('categories.php', 'POST', fd);
                if (data.success) { showToast('Category saved!', 'success'); closeModal('categoryModal'); setTimeout(() => location.reload(), 500); }
                else showToast(data.message || 'Failed.', 'error');
            } catch (e) { showToast('Error.', 'error'); }
        });
    }
    document.querySelectorAll('.edit-category').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            try {
                const data = await api('categories.php?id=' + id);
                if (data.success) {
                    document.getElementById('categoryFormTitle').textContent = 'Edit Category';
                    document.getElementById('categoryId').value = data.category.id;
                    document.getElementById('categoryName').value = data.category.name;
                    document.getElementById('categoryIcon').value = data.category.icon || '';
                    openModal('categoryModal');
                }
            } catch (e) { showToast('Error.', 'error'); }
        });
    });
    document.querySelectorAll('.delete-category').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('deleteProductName').textContent = btn.dataset.name || 'this category';
            document.getElementById('confirmDeleteProduct').onclick = async () => {
                try {
                    const data = await api('categories.php', 'POST', { action: 'delete', id: btn.dataset.id });
                    if (data.success) { showToast('Deleted!', 'success'); closeModal('deleteModal'); setTimeout(() => location.reload(), 500); }
                } catch (e) { showToast('Error.', 'error'); }
            };
            openModal('deleteModal');
        });
    });

    // =====================================================
    // ORDERS MANAGEMENT
    // =====================================================
    document.querySelectorAll('.view-order').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            try {
                const data = await api('orders.php?id=' + id);
                if (data.success) {
                    const o = data.order;
                    const detail = document.getElementById('orderDetail');
                    if (detail) {
                        detail.innerHTML = `
                        <div class="form-row"><div class="form-group"><label>Customer Name</label><p>${o.customer_name}</p></div>
                        <div class="form-group"><label>Phone</label><p>${o.customer_phone}</p></div></div>
                        <div class="form-row"><div class="form-group"><label>Alt Phone</label><p>${o.customer_alt_phone || 'N/A'}</p></div>
                        <div class="form-group"><label>Email</label><p>${o.customer_email}</p></div></div>
                        <div class="form-row"><div class="form-group"><label>Gender</label><p>${o.customer_gender}</p></div>
                        <div class="form-group"><label>County</label><p>${o.county}</p></div></div>
                        <div class="form-group"><label>Address</label><p>${o.address}</p></div>
                        <div class="form-row"><div class="form-group"><label>Delivery Date</label><p>${o.delivery_date}</p></div>
                        <div class="form-group"><label>Notes</label><p>${o.notes || 'None'}</p></div></div>
                        <hr style="margin:16px 0;border-color:var(--border)">
                        <div class="form-row"><div class="form-group"><label>Product</label><p>${o.product_name}</p></div>
                        <div class="form-group"><label>Quantity</label><p>${o.quantity}</p></div></div>
                        <div class="form-row"><div class="form-group"><label>Unit Price</label><p>KES ${parseFloat(o.unit_price).toLocaleString()}</p></div>
                        <div class="form-group"><label>Subtotal</label><p>KES ${parseFloat(o.subtotal).toLocaleString()}</p></div></div>
                        ${o.jumia_link ? `<div class="form-group"><label>Jumia Link (Internal)</label><p><a href="${o.jumia_link}" target="_blank" style="color:var(--accent)">${o.jumia_link}</a></p></div>` : ''}
                        <div class="form-row">
                        <div class="form-group"><label>Transport Fee</label><input type="number" id="transportFee" value="${o.transport_fee || ''}" placeholder="Enter fee"></div>
                        <div class="form-group"><label>Status</label><select id="orderStatus">
                        <option value="pending" ${o.status === 'pending' ? 'selected' : ''}>Pending</option>
                        <option value="confirmed" ${o.status === 'confirmed' ? 'selected' : ''}>Confirmed</option>
                        <option value="shipped" ${o.status === 'shipped' ? 'selected' : ''}>Shipped</option>
                        <option value="delivered" ${o.status === 'delivered' ? 'selected' : ''}>Delivered</option>
                        <option value="cancelled" ${o.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                        </select></div></div>
                        <button class="btn btn-primary" onclick="updateOrder(${o.id})"><span class="btn-text">Save Changes</span><span class="spinner"></span></button>`;
                    }
                    openModal('orderModal');
                }
            } catch (e) { showToast('Error loading order.', 'error'); }
        });
    });

    window.updateOrder = async function (id) {
        const status = document.getElementById('orderStatus').value;
        const tf = document.getElementById('transportFee').value;
        try {
            const data = await api('orders.php', 'POST', { action: 'update', id, status, transport_fee: tf || null });
            if (data.success) { showToast('Order updated!', 'success'); setTimeout(() => location.reload(), 500); }
            else showToast(data.message || 'Failed.', 'error');
        } catch (e) { showToast('Error.', 'error'); }
    };

    // =====================================================
    // SETTINGS
    // =====================================================
    document.querySelectorAll('.settings-form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const fd = new FormData(form);
            const settings = {};
            for (const [k, v] of fd) settings[k] = v;
            // Handle unchecked checkboxes
            form.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                if (!cb.checked) settings[cb.name] = '0';
            });
            try {
                const data = await api('settings.php', 'POST', settings);
                if (data.success) showToast('Settings saved!', 'success');
                else showToast(data.message || 'Failed.', 'error');
            } catch (e) { showToast('Error saving.', 'error'); }
        });
    });

    // Admin password change
    const pwForm = document.getElementById('adminPasswordForm');
    if (pwForm) {
        pwForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const fd = new FormData(pwForm);
            if (fd.get('new_password') !== fd.get('confirm_password')) {
                showToast('Passwords do not match', 'error'); return;
            }
            try {
                const data = await api('auth.php', 'POST', { action: 'change_admin_password', current: fd.get('current_password'), password: fd.get('new_password') });
                if (data.success) { showToast('Password changed!', 'success'); pwForm.reset(); }
                else showToast(data.message || 'Failed.', 'error');
            } catch (e) { showToast('Error.', 'error'); }
        });
    }

    // =====================================================
    // TABLE SEARCH/FILTER
    // =====================================================
    document.querySelectorAll('.admin-search').forEach(input => {
        input.addEventListener('input', () => {
            const q = input.value.toLowerCase();
            const table = input.closest('.admin-table-wrapper')?.querySelector('tbody');
            if (!table) return;
            table.querySelectorAll('tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    });

    document.querySelectorAll('.admin-filter-select').forEach(sel => {
        sel.addEventListener('change', () => {
            const col = parseInt(sel.dataset.col);
            const val = sel.value.toLowerCase();
            const table = sel.closest('.admin-table-wrapper')?.querySelector('tbody');
            if (!table) return;
            table.querySelectorAll('tr').forEach(row => {
                if (!val) { row.style.display = ''; return; }
                const cell = row.cells[col];
                row.style.display = cell && cell.textContent.toLowerCase().includes(val) ? '' : 'none';
            });
        });
    });

    // CSV Export
    const exportCSVBtn = document.getElementById('exportCSV');
    if (exportCSVBtn) {
        exportCSVBtn.addEventListener('click', () => {
            const table = document.querySelector('.subscribers-table');
            if (!table) return;
            let csv = '';
            table.querySelectorAll('tr').forEach(row => {
                const cols = [];
                row.querySelectorAll('th, td').forEach(cell => cols.push('"' + cell.textContent.trim() + '"'));
                csv += cols.join(',') + '\n';
            });
            const blob = new Blob([csv], { type: 'text/csv' });
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'subscribers.csv';
            a.click();
        });
    }
});
