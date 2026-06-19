// Apply theme immediately to avoid flicker
let currentTheme = 'light';
try {
    currentTheme = localStorage.getItem('theme') || 'light';
} catch (e) {
    console.warn("LocalStorage not accessible:", e);
}
document.documentElement.setAttribute('data-theme', currentTheme);

// Synchronize theme across tabs instantly
window.addEventListener('storage', (e) => {
    if (e.key === 'theme') {
        const theme = e.newValue || 'light';
        document.documentElement.setAttribute('data-theme', theme);
        updateThemeIcon(theme);
    }
});

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initNavbar();
    initModals();
    setupToastContainer();
    initAdminSidebar();
});

// ─── Theme Toggle ─────────────────────────────────────────────────────────────
function initTheme() {
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    if (!themeToggleBtn) return;

    updateThemeIcon(currentTheme);

    themeToggleBtn.addEventListener('click', () => {
        let theme = document.documentElement.getAttribute('data-theme');
        theme = theme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', theme);
        try {
            localStorage.setItem('theme', theme);
        } catch (e) {
            console.warn("LocalStorage setItem failed:", e);
        }
        updateThemeIcon(theme);
    });
}

function updateThemeIcon(theme) {
    const iconSpan = document.getElementById('themeIconContainer');
    if (!iconSpan) return;
    if (theme === 'dark') {
        iconSpan.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>`;
    } else {
        iconSpan.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>`;
    }
}

// ─── Resolve base URL from <meta name="base-url"> ───────────────────────────
const _baseMeta = document.querySelector('meta[name="base-url"]');
const base_url = _baseMeta ? _baseMeta.getAttribute('content') : '';

// ─── Toast Notifications ─────────────────────────────────────────────────────
function setupToastContainer() {
    if (!document.getElementById('toast-container')) {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
}

window.showToast = function(message, type = 'success') {
    setupToastContainer(); // ensure it exists even if called early
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;

    const icon = type === 'success'
        ? `<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>`
        : `<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>`;

    toast.innerHTML = `${icon} <span>${message}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
};

// ─── Navbar ───────────────────────────────────────────────────────────────────
function initNavbar() {
    const hamburger = document.getElementById('navToggle') || document.querySelector('.hamburger:not(#closeSidebar):not(#sidebarToggle)');
    const navLinks  = document.getElementById('navLinks') || document.querySelector('.nav-links');
    const overlay   = document.getElementById('navOverlay');
    const closeNav  = document.getElementById('closeNav');

    if (hamburger && navLinks) {
        hamburger.addEventListener('click', () => {
            navLinks.classList.add('active');
            if (overlay) overlay.classList.add('active');
        });

        if (closeNav) {
            closeNav.addEventListener('click', () => {
                navLinks.classList.remove('active');
                if (overlay) overlay.classList.remove('active');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                navLinks.classList.remove('active');
                overlay.classList.remove('active');
            });
        }

        // Close nav on link click
        navLinks.querySelectorAll('.nav-link, .btn:not(.hamburger), .nav-user').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('active');
                if (overlay) overlay.classList.remove('active');
            });
        });
    }
}

// ─── Admin Sidebar (mobile toggle) ───────────────────────────────────────────
function initAdminSidebar() {
    const sidebar  = document.getElementById('adminSidebar');
    const overlay  = document.getElementById('adminSidebarOverlay');
    const closeBtn = document.getElementById('closeSidebar');

    // Create hamburger toggle button in admin header if not present
    const adminHeader = document.querySelector('.admin-header');
    if (adminHeader && sidebar) {
        // Add sidebar toggle btn to admin header
        if (!document.getElementById('sidebarToggle')) {
            const toggleBtn = document.createElement('button');
            toggleBtn.id = 'sidebarToggle';
            toggleBtn.className = 'hamburger';
            toggleBtn.setAttribute('aria-label', 'Toggle sidebar');
            toggleBtn.innerHTML = `<svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><path fill-rule="evenodd" d="M3 5h18v2H3V5zm0 6h18v2H3v-2zm0 6h18v2H3v-2z" clip-rule="evenodd"></path></svg>`;
            toggleBtn.style.color = 'var(--dark-navy)';
            adminHeader.insertBefore(toggleBtn, adminHeader.firstChild);

            toggleBtn.addEventListener('click', () => {
                sidebar.classList.add('active');
                if (overlay) overlay.classList.add('active');
            });
        }
    }

    if (closeBtn && sidebar) {
        closeBtn.style.display = 'block';
        closeBtn.addEventListener('click', () => {
            sidebar.classList.remove('active');
            if (overlay) overlay.classList.remove('active');
        });
    }

    if (overlay && sidebar) {
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    }
}

// ─── Modals ───────────────────────────────────────────────────────────────────
function initModals() {
    document.querySelectorAll('.modal-close').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const modal = e.target.closest('.modal-overlay');
            if (modal) closeModal(modal.id);
        });
    });

    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) closeModal(overlay.id);
        });
    });

    // Close modals on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.active').forEach(m => closeModal(m.id));
        }
    });
}

window.openModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
};

window.closeModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        // Only restore overflow if no other modals are open
        if (!document.querySelector('.modal-overlay.active')) {
            document.body.style.overflow = '';
        }
    }
};

// ─── Auth ─────────────────────────────────────────────────────────────────────
window.handleAuth = function(event, type) {
    event.preventDefault();
    const form     = event.target;
    const formData = new FormData(form);
    formData.append('action', type);

    const btn          = form.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    btn.innerHTML      = 'Processing...';
    btn.disabled       = true;

    // Use base_url resolved from meta tag; fallback to relative path
    const apiPath = base_url ? base_url + '/api/auth.php' : 'api/auth.php';

    fetch(apiPath, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            showToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 1200);
        } else {
            showToast(data.message || 'An error occurred', 'error');
            btn.innerHTML = originalText;
            btn.disabled  = false;
        }
    })
    .catch(() => {
        showToast('Network error, please try again.', 'error');
        btn.innerHTML = originalText;
        btn.disabled  = false;
    });
};

window.toggleAuthForm = function(formId) {
    document.getElementById('login-form').style.display    = 'none';
    document.getElementById('register-form').style.display = 'none';
    document.getElementById(formId).style.display          = 'block';
};
