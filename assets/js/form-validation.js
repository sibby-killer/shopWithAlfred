/**
 * ShopWithAlfred - Form Validation
 */
const FormValidator = {
    validatePhone(phone) {
        phone = phone.replace(/\s+/g, '');
        return /^0[17]\d{8}$/.test(phone);
    },
    validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    },
    validatePassword(pw) {
        if (pw.length < 8) return { valid: false, msg: 'Minimum 8 characters' };
        if (!/[A-Z]/.test(pw)) return { valid: false, msg: 'At least one uppercase letter required' };
        if (!/[0-9]/.test(pw)) return { valid: false, msg: 'At least one number required' };
        return { valid: true, msg: '' };
    },
    validateRequired(value) {
        return value.trim().length > 0;
    },
    validateFutureDate(dateStr) {
        const d = new Date(dateStr);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        return d >= tomorrow;
    },
    showError(field, message) {
        const group = field.closest('.form-group');
        if (!group) return;
        group.classList.add('has-error');
        let errEl = group.querySelector('.error-message');
        if (!errEl) {
            errEl = document.createElement('div');
            errEl.className = 'error-message';
            group.appendChild(errEl);
        }
        errEl.textContent = message;
        errEl.style.display = 'block';
    },
    clearError(field) {
        const group = field.closest('.form-group');
        if (!group) return;
        group.classList.remove('has-error');
        const errEl = group.querySelector('.error-message');
        if (errEl) errEl.style.display = 'none';
    },
    clearAllErrors(form) {
        form.querySelectorAll('.form-group').forEach(g => {
            g.classList.remove('has-error');
            const e = g.querySelector('.error-message');
            if (e) e.style.display = 'none';
        });
    },
    validateOrderForm(form) {
        this.clearAllErrors(form);
        let valid = true;
        let firstError = null;

        const fields = [
            { name: 'customer_name', label: 'Full name is required', type: 'required' },
            { name: 'customer_phone', label: 'Valid Kenyan phone (07/01, 10 digits)', type: 'phone' },
            { name: 'customer_email', label: 'Valid email is required', type: 'email' },
            { name: 'customer_gender', label: 'Please select gender', type: 'required' },
            { name: 'county', label: 'Please select county', type: 'required' },
            { name: 'address', label: 'Delivery address is required', type: 'required' },
            { name: 'delivery_date', label: 'Please select a future date', type: 'date' }
        ];

        fields.forEach(f => {
            const el = form.querySelector(`[name="${f.name}"]`);
            if (!el) return;
            let isValid = true;

            if (f.type === 'required') isValid = this.validateRequired(el.value);
            else if (f.type === 'phone') isValid = this.validatePhone(el.value);
            else if (f.type === 'email') isValid = this.validateEmail(el.value);
            else if (f.type === 'date') isValid = this.validateFutureDate(el.value);

            if (!isValid) {
                this.showError(el, f.label);
                if (!firstError) firstError = el;
                valid = false;
            }
        });

        // Password validation if creating account
        const createAccount = form.querySelector('[name="create_account"]');
        if (createAccount && createAccount.checked) {
            const pw = form.querySelector('[name="password"]');
            const cpw = form.querySelector('[name="confirm_password"]');
            if (pw) {
                const result = this.validatePassword(pw.value);
                if (!result.valid) {
                    this.showError(pw, result.msg);
                    if (!firstError) firstError = pw;
                    valid = false;
                }
            }
            if (cpw && pw && cpw.value !== pw.value) {
                this.showError(cpw, 'Passwords do not match');
                if (!firstError) firstError = cpw;
                valid = false;
            }
        }

        if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return valid;
    },

    // Add live validation listeners
    initLiveValidation(form) {
        form.querySelectorAll('input, select, textarea').forEach(f => {
            f.addEventListener('input', () => this.clearError(f));
            f.addEventListener('change', () => this.clearError(f));
        });
    }
};
