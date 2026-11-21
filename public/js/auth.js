/**
 * SHILPA LMS — Authentication Micro-Interactions
 * Password visibility toggles, live strength calculation, and loading states.
 */
document.addEventListener('DOMContentLoaded', function () {
  // 1. Password Visibility Toggle
  document.querySelectorAll('[data-password-toggle]').forEach(btn => {
    btn.addEventListener('click', function () {
      const targetId = this.getAttribute('data-password-toggle');
      const input = document.getElementById(targetId);
      if (!input) return;

      if (input.type === 'password') {
        input.type = 'text';
        this.innerHTML = '🙈';
        this.setAttribute('aria-label', 'Hide password');
      } else {
        input.type = 'password';
        this.innerHTML = '👁️';
        this.setAttribute('aria-label', 'Show password');
      }
    });
  });

  // 2. Password Strength Meter
  const passwordInput = document.getElementById('reg_password');
  const strengthMeter = document.getElementById('password_strength_bar');
  const strengthLabel = document.getElementById('password_strength_text');

  if (passwordInput && strengthMeter && strengthLabel) {
    passwordInput.addEventListener('input', function () {
      const val = this.value;
      let score = 0;
      if (val.length >= 8) score++;
      if (/[A-Z]/.test(val)) score++;
      if (/[0-9]/.test(val)) score++;
      if (/[^A-Za-z0-9]/.test(val)) score++;

      const levels = [
        { width: '0%', color: 'transparent', text: '' },
        { width: '25%', color: 'var(--danger-text)', text: 'Weak' },
        { width: '50%', color: 'var(--warning-text)', text: 'Fair' },
        { width: '75%', color: 'var(--shilpa-blue-400)', text: 'Good' },
        { width: '100%', color: 'var(--success-text)', text: 'Strong' },
      ];

      const current = levels[score];
      strengthMeter.style.width = current.width;
      strengthMeter.style.backgroundColor = current.color;
      strengthLabel.textContent = current.text;
      strengthLabel.style.color = current.color;
    });
  }

  // 3. Form Submit Button Loading State
  document.querySelectorAll('form[data-auth-form]').forEach(form => {
    form.addEventListener('submit', function () {
      const submitBtn = this.querySelector('button[type="submit"]');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.dataset.originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="skeleton" style="display:inline-block; width:16px; height:16px; border-radius:50%; margin-right:8px; vertical-align:middle;"></span> Please wait...';
      }
    });
  });
});
