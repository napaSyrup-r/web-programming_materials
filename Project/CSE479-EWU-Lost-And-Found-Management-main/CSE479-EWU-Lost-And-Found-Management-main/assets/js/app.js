// Minimal JS for small interactions
document.addEventListener('DOMContentLoaded', () => {
  const closeFlash = document.querySelectorAll('[data-close]');
  closeFlash.forEach(btn => btn.addEventListener('click', () => btn.closest('.card')?.remove()));

  // Theme toggling (dark/light only)
  const themeBtns = Array.from(document.querySelectorAll('[data-theme-toggle]'));
  const themes = ['dark', 'light'];
  const root = document.documentElement;
  const saved = localStorage.getItem('lf_theme');
  if (saved) root.setAttribute('data-theme', saved);
  const setIcon = (btn, theme) => { if (btn) btn.textContent = theme === 'dark' ? '🌙' : '☀️'; };
  if (themeBtns.length) {
    // Initialize icons for all buttons
    const current = root.getAttribute('data-theme') || 'dark';
    themeBtns.forEach(btn => setIcon(btn, current));
    // Attach listeners to each button
    themeBtns.forEach(btn => btn.addEventListener('click', () => {
      const cur = root.getAttribute('data-theme') || 'dark';
      const idx = themes.indexOf(cur);
      const next = themes[(idx + 1) % themes.length];
      root.setAttribute('data-theme', next);
      localStorage.setItem('lf_theme', next);
      themeBtns.forEach(b => { setIcon(b, next); b.classList.add('pulse'); setTimeout(() => b.classList.remove('pulse'), 400); });
    }));
  }

  // Mobile menu toggle
  const menuBtn = document.querySelector('[data-menu-toggle]');
  const nav = document.querySelector('.nav');
  if (menuBtn && nav) {
    menuBtn.addEventListener('click', () => {
      const isOpen = nav.classList.contains('open');
      nav.classList.toggle('open', !isOpen);
      menuBtn.setAttribute('aria-expanded', String(!isOpen));
    });
  }
});
