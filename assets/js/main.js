/**
 * ShopWithAlfred - Main Frontend JavaScript
 */
document.addEventListener('DOMContentLoaded', function () {

    // =====================================================
    // NAVBAR
    // =====================================================
    const navbar = document.querySelector('.navbar');
    const hamburger = document.querySelector('.hamburger');
    const mobileNav = document.querySelector('.mobile-nav');

    window.addEventListener('scroll', () => {
        if (navbar) navbar.classList.toggle('scrolled', window.scrollY > 50);
    });

    if (hamburger) {
        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            mobileNav.classList.toggle('active');
            document.body.style.overflow = mobileNav.classList.contains('active') ? 'hidden' : '';
        });
    }

    // =====================================================
    // SECRET ADMIN ACCESS (4 taps on logo)
    // =====================================================
    const logo = document.querySelector('.navbar-logo');
    let logoTaps = 0, logoTimer = null;
    if (logo) {
        logo.addEventListener('click', (e) => {
            logoTaps++;
            if (logoTaps === 1) {
                logoTimer = setTimeout(() => { logoTaps = 0; }, 2000);
            }
            if (logoTaps >= 4) {
                clearTimeout(logoTimer);
                logoTaps = 0;
                window.location.href = (typeof BASE_URL !== 'undefined' ? BASE_URL : '') + '/admin/login.php';
            }
        });
    }

    // =====================================================
    // QUANTITY SELECTOR
    // =====================================================
    document.querySelectorAll('.quantity-selector').forEach(sel => {
        const input = sel.querySelector('input');
        const minusBtn = sel.querySelector('.qty-minus');
        const plusBtn = sel.querySelector('.qty-plus');
        if (!input) return;

        const updateSubtotal = () => {
            const price = parseFloat(sel.dataset.price || 0);
            const qty = parseInt(input.value) || 1;
            const subtotalEl = document.querySelector('.subtotal span');
            if (subtotalEl) subtotalEl.textContent = 'KES ' + (price * qty).toLocaleString();
            // Update modal too
            const modalSubtotal = document.querySelector('.order-subtotal');
            if (modalSubtotal) modalSubtotal.textContent = 'KES ' + (price * qty).toLocaleString();
        };

        if (minusBtn) minusBtn.addEventListener('click', () => {
            const v = parseInt(input.value) || 1;
            if (v > 1) { input.value = v - 1; updateSubtotal(); }
        });
        if (plusBtn) plusBtn.addEventListener('click', () => {
            const v = parseInt(input.value) || 1;
            if (v < 10) { input.value = v + 1; updateSubtotal(); }
        });
        input.addEventListener('change', () => {
            let v = parseInt(input.value) || 1;
            if (v < 1) v = 1; if (v > 10) v = 10;
            input.value = v;
            updateSubtotal();
        });
    });

    // =====================================================
    // ORDER MODAL
    // =====================================================
    const orderModal = document.getElementById('orderModal');
    const openOrderBtns = document.querySelectorAll('.open-order-modal');
    const closeModalBtns = document.querySelectorAll('.modal-close');

    openOrderBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            if (orderModal) {
                orderModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        });
    });

    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const modal = btn.closest('.modal-overlay');
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });

    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });

    // Create account checkbox toggle
    const createAccountCheck = document.querySelector('[name="create_account"]');
    const passwordFields = document.getElementById('passwordFields');
    if (createAccountCheck && passwordFields) {
        createAccountCheck.addEventListener('change', () => {
            passwordFields.style.display = createAccountCheck.checked ? 'block' : 'none';
        });
    }

    // =====================================================
    // ORDER FORM SUBMISSION
    // =====================================================
    const orderForm = document.getElementById('orderForm');
    if (orderForm) {
        FormValidator.initLiveValidation(orderForm);

        orderForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (!FormValidator.validateOrderForm(orderForm)) return;

            const submitBtn = orderForm.querySelector('.btn-submit');
            if (submitBtn) { submitBtn.classList.add('loading'); submitBtn.disabled = true; }

            const formData = new FormData(orderForm);

            try {
                // Save order to database
                const response = await fetch((typeof BASE_URL !== 'undefined' ? BASE_URL : '') + '/api/orders.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    // Build WhatsApp message
                    const msg = buildWhatsAppMessage(formData, result.reference || '');
                    const waNumber = typeof WHATSAPP_NUMBER !== 'undefined' ? WHATSAPP_NUMBER : '254762667048';
                    const waUrl = `https://wa.me/${waNumber}?text=${encodeURIComponent(msg)}`;
                    window.open(waUrl, '_blank');
                    showToast('Order placed successfully!', 'success');
                    orderModal.classList.remove('active');
                    document.body.style.overflow = '';
                    orderForm.reset();
                } else {
                    showToast(result.message || 'Something went wrong.', 'error');
                }
            } catch (err) {
                showToast('Network error. Please try again.', 'error');
            }

            if (submitBtn) { submitBtn.classList.remove('loading'); submitBtn.disabled = false; }
        });
    }

    function buildWhatsAppMessage(fd, reference) {
        const productName = fd.get('product_name') || '';
        const category = fd.get('category') || '';
        const gender = fd.get('product_gender') || '';
        const qty = fd.get('quantity') || '1';
        const unitPrice = fd.get('unit_price') || '0';
        const subtotal = parseInt(qty) * parseFloat(unitPrice);

        return `🛒 *NEW ORDER - ShopWithAlfred*

*CUSTOMER DETAILS:*
Name: ${fd.get('customer_name') || ''}
Phone: ${fd.get('customer_phone') || ''}
Alt Phone: ${fd.get('customer_alt_phone') || 'Not provided'}
Email: ${fd.get('customer_email') || ''}
Gender: ${fd.get('customer_gender') || ''}

*DELIVERY DETAILS:*
County: ${fd.get('county') || ''}
Address: ${fd.get('address') || ''}
Preferred Date: ${fd.get('delivery_date') || ''}
Notes: ${fd.get('notes') || 'None'}

*ORDER DETAILS:*
Product: ${productName}
Category: ${category} - ${gender}
Quantity: ${qty}
Unit Price: KES ${parseFloat(unitPrice).toLocaleString()}
Subtotal: KES ${subtotal.toLocaleString()}

*TOTAL: KES ${subtotal.toLocaleString()}*
_Transport fee (to be confirmed)_
================================
Order Reference: #${reference}
Date: ${new Date().toLocaleString('en-KE')}`;
    }

    // =====================================================
    // TOAST NOTIFICATIONS
    // =====================================================
    window.showToast = function (message, type = 'success') {
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i><span>${message}</span>`;
        container.appendChild(toast);
        setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 4000);
    };

    // =====================================================
    // NEWSLETTER SUBSCRIBE
    // =====================================================
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = newsletterForm.querySelector('input[type="email"]').value;
            if (!FormValidator.validateEmail(email)) {
                showToast('Please enter a valid email.', 'error');
                return;
            }
            try {
                const res = await fetch((typeof BASE_URL !== 'undefined' ? BASE_URL : '') + '/api/subscribers.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email })
                });
                const data = await res.json();
                showToast(data.message || 'Subscribed!', data.success ? 'success' : 'error');
                if (data.success) newsletterForm.reset();
            } catch (e) { showToast('Network error.', 'error'); }
        });
    }

    // =====================================================
    // NOTIFY ME (restock)
    // =====================================================
    const notifyForm = document.getElementById('notifyForm');
    if (notifyForm) {
        notifyForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = notifyForm.querySelector('input[type="email"]').value;
            const productId = notifyForm.querySelector('[name="product_id"]').value;
            if (!FormValidator.validateEmail(email)) {
                showToast('Please enter a valid email.', 'error'); return;
            }
            try {
                const res = await fetch((typeof BASE_URL !== 'undefined' ? BASE_URL : '') + '/api/subscribers.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, product_id: productId, type: 'restock' })
                });
                const data = await res.json();
                showToast(data.message || 'You will be notified!', data.success ? 'success' : 'error');
                if (data.success) notifyForm.reset();
            } catch (e) { showToast('Network error.', 'error'); }
        });
    }

    // =====================================================
    // SEARCH
    // =====================================================
    const searchIcon = document.getElementById('searchToggle');
    const searchBar = document.getElementById('searchBar');
    if (searchIcon && searchBar) {
        searchIcon.addEventListener('click', () => {
            searchBar.classList.toggle('active');
            if (searchBar.classList.contains('active')) {
                searchBar.querySelector('input').focus();
            }
        });
    }

    // =====================================================
    // SHOP FILTERS
    // =====================================================
    const filterForm = document.getElementById('filterForm');
    const filterToggle = document.getElementById('filterToggle');
    const filterSidebar = document.querySelector('.filters-sidebar');

    if (filterToggle && filterSidebar) {
        filterToggle.addEventListener('click', () => {
            filterSidebar.classList.toggle('active');
        });
    }

    if (filterForm) {
        filterForm.addEventListener('submit', (e) => { e.preventDefault(); applyFilters(); });
        filterForm.addEventListener('change', applyFilters);
        const clearBtn = filterForm.querySelector('.clear-filters');
        if (clearBtn) clearBtn.addEventListener('click', () => { filterForm.reset(); applyFilters(); });
    }

    function applyFilters() {
        if (!filterForm) return;
        const fd = new FormData(filterForm);
        const params = new URLSearchParams();
        for (const [k, v] of fd) { if (v) params.append(k, v); }
        window.location.href = window.location.pathname + '?' + params.toString();
    }

    // =====================================================
    // PRODUCT IMAGE GALLERY
    // =====================================================
    document.querySelectorAll('.gallery-thumbs img').forEach(thumb => {
        thumb.addEventListener('click', () => {
            document.querySelectorAll('.gallery-thumbs img').forEach(t => t.classList.remove('active'));
            thumb.classList.add('active');
            const mainImg = document.querySelector('.main-image img');
            if (mainImg) mainImg.src = thumb.src;
        });
    });

    // =====================================================
    // CONTACT FORM
    // =====================================================
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const fd = new FormData(contactForm);
            const msg = `Hi ShopWithAlfred,\n\nName: ${fd.get('name')}\nEmail: ${fd.get('email')}\nSubject: ${fd.get('subject')}\n\n${fd.get('message')}`;
            const waUrl = `https://wa.me/${typeof WHATSAPP_NUMBER !== 'undefined' ? WHATSAPP_NUMBER : '254762667048'}?text=${encodeURIComponent(msg)}`;
            window.open(waUrl, '_blank');
            showToast('Opening WhatsApp...', 'success');
            contactForm.reset();
        });
    }

    // =====================================================
    // SCROLL ANIMATIONS
    // =====================================================
    const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -50px 0px' };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.product-card, .category-card, .benefit-card, .contact-card').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(el);
    });

});
