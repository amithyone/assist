import '../css/assist.css';

document.querySelectorAll('[data-assist-tabs]').forEach((root) => {
  const buttons = root.querySelectorAll('[data-assist-tab]');
  const host = root.closest('.glass-panel') || root.parentElement;
  const panels = host ? host.querySelectorAll('[data-assist-panel]') : [];
  buttons.forEach((btn) => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-assist-tab');
      buttons.forEach((b) => b.classList.toggle('active', b === btn));
      panels.forEach((p) => {
        p.hidden = p.getAttribute('data-assist-panel') !== id;
      });
    });
  });
});
