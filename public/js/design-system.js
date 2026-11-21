/**
 * SHILPA LMS — Design System Micro-Interactions
 * Theme Switching, Accessible Dialogs, Dropdowns, and Toast Notifications.
 */
(function () {
  'use strict';

  // 1. Theme Initializer & Switcher
  const savedTheme = localStorage.getItem('shilpa-theme') || 
    (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
  document.documentElement.setAttribute('data-theme', savedTheme);

  window.toggleTheme = function () {
    const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('shilpa-theme', newTheme);
    updateThemeToggleIcons();
  };

  function updateThemeToggleIcons() {
    const isDark = (document.documentElement.getAttribute('data-theme') || 'dark') === 'dark';
    document.querySelectorAll('[data-theme-icon]').forEach(el => {
      el.textContent = isDark ? '🌙' : '☀️';
    });
    document.querySelectorAll('[data-theme-label]').forEach(el => {
      el.textContent = isDark ? 'Dark Mode' : 'Light Mode';
    });
  }

  // 2. Modal Handlers
  window.openModal = function (modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.add('open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  };

  window.closeModal = function (modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.remove('open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  };

  // Close modal when clicking outside content
  document.addEventListener('click', function (e) {
    if (e.target.classList.contains('modal-backdrop')) {
      e.target.classList.remove('open');
      document.body.style.overflow = '';
    }
  });

  // Close modal on Escape key
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal-backdrop.open').forEach(el => {
        el.classList.remove('open');
      });
      document.body.style.overflow = '';
    }
  });

  // 3. Dropdown Handlers
  document.addEventListener('click', function (e) {
    const trigger = e.target.closest('[data-dropdown-trigger]');
    if (trigger) {
      const dropdown = trigger.closest('.dropdown');
      if (dropdown) {
        dropdown.classList.toggle('open');
      }
    } else {
      document.querySelectorAll('.dropdown.open').forEach(d => d.classList.remove('open'));
    }
  });

  // Run initial icon update when DOM ready
  document.addEventListener('DOMContentLoaded', updateThemeToggleIcons);
})();
