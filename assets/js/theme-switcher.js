/**
 * ShopWithAlfred - Theme Switcher
 */
(function() {
    const THEMES = [
        { id: 'navy-gold', name: 'Navy & Gold', swatch: '#1E3A5F' },
        { id: 'soft-blue', name: 'Soft Blue', swatch: '#4A90D9' },
        { id: 'soft-purple', name: 'Soft Purple', swatch: '#7C3AED' },
        { id: 'warm-orange', name: 'Warm Orange', swatch: '#EA580C' }
    ];

    function getStoredTheme() {
        return localStorage.getItem('swa-theme') || 'navy-gold';
    }
    function getStoredDark() {
        return localStorage.getItem('swa-dark') === 'true';
    }

    function applyTheme(themeId, isDark) {
        document.documentElement.setAttribute('data-theme', themeId);
        document.documentElement.setAttribute('data-dark', isDark.toString());
        localStorage.setItem('swa-theme', themeId);
        localStorage.setItem('swa-dark', isDark.toString());
        updateUI(themeId, isDark);
    }

    function updateUI(themeId, isDark) {
        document.querySelectorAll('.theme-option').forEach(opt => {
            opt.classList.toggle('active', opt.dataset.theme === themeId);
        });
        const toggle = document.querySelector('.toggle-switch');
        if (toggle) toggle.classList.toggle('active', isDark);
    }

    function createDropdown() {
        const wrapper = document.querySelector('.theme-toggle-wrapper');
        if (!wrapper) return;

        let html = '<div class="theme-dropdown" id="themeDropdown">';
        THEMES.forEach(t => {
            html += `<div class="theme-option" data-theme="${t.id}">
                <span class="theme-swatch" style="background:${t.swatch}"></span>
                <span>${t.name}</span>
            </div>`;
        });
        html += `<div class="dark-mode-toggle">
            <span><i class="fas fa-moon"></i> Dark Mode</span>
            <div class="toggle-switch" id="darkToggle"></div>
        </div></div>`;
        wrapper.insertAdjacentHTML('beforeend', html);

        const dd = document.getElementById('themeDropdown');
        const btn = wrapper.querySelector('.nav-icon');
        if (btn) {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                dd.classList.toggle('active');
            });
        }

        dd.querySelectorAll('.theme-option').forEach(opt => {
            opt.addEventListener('click', () => {
                applyTheme(opt.dataset.theme, getStoredDark());
            });
        });

        const darkToggle = document.getElementById('darkToggle');
        if (darkToggle) {
            darkToggle.addEventListener('click', () => {
                applyTheme(getStoredTheme(), !getStoredDark());
            });
        }

        document.addEventListener('click', (e) => {
            if (!wrapper.contains(e.target)) dd.classList.remove('active');
        });
    }

    // Apply on load
    applyTheme(getStoredTheme(), getStoredDark());
    document.addEventListener('DOMContentLoaded', createDropdown);
})();
