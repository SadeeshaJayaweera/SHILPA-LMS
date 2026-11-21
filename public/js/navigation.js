/**
 * SHILPA LMS — Enterprise Navigation & Workspace Controller
 * Accessible, keyboard-navigable, responsive state management.
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', initNavigation);

    function initNavigation() {
        initSidebar();
        initMobileDrawer();
        initDropdowns();
        initKeyboardShortcuts();
        highlightActiveNavigation();
    }

    /**
     * Desktop Sidebar Collapse State with Persistence
     */
    function initSidebar() {
        const sidebar = document.querySelector('.workspace-sidebar');
        const toggleBtn = document.getElementById('sidebar-toggle-btn');
        if (!sidebar || !toggleBtn) return;

        // Restore persisted state for desktop screens
        if (window.innerWidth > 1024) {
            const isCollapsed = localStorage.getItem('shilpa_sidebar_collapsed') === 'true';
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
                toggleBtn.setAttribute('aria-expanded', 'false');
            }
        }

        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            const nowCollapsed = sidebar.classList.contains('collapsed');
            toggleBtn.setAttribute('aria-expanded', (!nowCollapsed).toString());
            localStorage.setItem('shilpa_sidebar_collapsed', nowCollapsed.toString());
        });
    }

    /**
     * Mobile Off-Canvas Drawer with Backdrop & Focus Trap
     */
    function initMobileDrawer() {
        const sidebar = document.querySelector('.workspace-sidebar');
        const openBtn = document.getElementById('mobile-menu-toggle');
        const closeBtn = document.getElementById('sidebar-close-btn');
        const overlay = document.getElementById('mobile-drawer-overlay');

        if (!sidebar || !openBtn || !overlay) return;

        function openDrawer() {
            sidebar.classList.add('mobile-open');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
            openBtn.setAttribute('aria-expanded', 'true');
        }

        function closeDrawer() {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
            openBtn.setAttribute('aria-expanded', 'false');
        }

        openBtn.addEventListener('click', openDrawer);
        if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
        overlay.addEventListener('click', closeDrawer);

        window.addEventListener('resize', function () {
            if (window.innerWidth > 1024 && sidebar.classList.contains('mobile-open')) {
                closeDrawer();
            }
        });
    }

    /**
     * Accessible Floating Dropdowns (Notifications & User Profile)
     */
    function initDropdowns() {
        const dropdownToggles = document.querySelectorAll('[data-dropdown-toggle]');

        dropdownToggles.forEach(toggle => {
            const targetId = toggle.getAttribute('data-dropdown-toggle');
            const menu = document.getElementById(targetId);
            if (!menu) return;

            toggle.addEventListener('click', function (e) {
                e.stopPropagation();
                const isOpen = menu.classList.contains('open');

                // Close all other open dropdowns first
                closeAllDropdowns();

                if (!isOpen) {
                    menu.classList.add('open');
                    toggle.setAttribute('aria-expanded', 'true');
                }
            });
        });

        // Close on outside click
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.dropdown-panel') && !e.target.closest('[data-dropdown-toggle]')) {
                closeAllDropdowns();
            }
        });

        // Close on ESC key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeAllDropdowns();
                const sidebar = document.querySelector('.workspace-sidebar');
                const overlay = document.getElementById('mobile-drawer-overlay');
                if (sidebar && sidebar.classList.contains('mobile-open')) {
                    sidebar.classList.remove('mobile-open');
                    if (overlay) overlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            }
        });
    }

    function closeAllDropdowns() {
        document.querySelectorAll('.dropdown-panel.open').forEach(panel => {
            panel.classList.remove('open');
        });
        document.querySelectorAll('[data-dropdown-toggle][aria-expanded="true"]').forEach(btn => {
            btn.setAttribute('aria-expanded', 'false');
        });
    }

    /**
     * Global Keyboard Shortcuts (Ctrl+K / Cmd+K to Search)
     */
    function initKeyboardShortcuts() {
        document.addEventListener('keydown', function (e) {
            if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.getElementById('global-search-input');
                const searchTrigger = document.querySelector('.global-search-trigger');
                if (searchInput) {
                    searchInput.focus();
                } else if (searchTrigger) {
                    searchTrigger.click();
                }
            }
        });
    }

    /**
     * Auto Highlight Current Navigation Item
     */
    function highlightActiveNavigation() {
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.nav-item-link');

        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href === currentPath || (href !== '/' && currentPath.startsWith(href))) {
                link.classList.add('active');
                link.setAttribute('aria-current', 'page');
            }
        });
    }
})();
