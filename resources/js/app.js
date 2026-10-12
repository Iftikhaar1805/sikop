import './bootstrap';

const rupiah = (n) => 'Rp ' + Math.round(n).toLocaleString('id-ID');
window.sikopRupiah = rupiah;

// Prevent double submits: the first click locks the button until the page changes.
document.addEventListener('submit', (e) => {
    const form = e.target;
    if (e.defaultPrevented || !(form instanceof HTMLFormElement)) return;
    if (form.dataset.submitting === '1') {
        e.preventDefault();
        return;
    }
    form.dataset.submitting = '1';
    form.querySelectorAll('button[type="submit"]').forEach((btn) => {
        btn.classList.add('is-busy');
        btn.setAttribute('aria-busy', 'true');
    });
});

// Coming back with the browser's Back button must not leave buttons locked.
window.addEventListener('pageshow', (e) => {
    document.querySelectorAll('form[data-submitting]').forEach((form) => {
        delete form.dataset.submitting;
        form.querySelectorAll('.is-busy').forEach((btn) => {
            btn.classList.remove('is-busy');
            btn.removeAttribute('aria-busy');
        });
    });

    // A login page restored from cache carries an expired token: load a fresh one.
    if (e.persisted && document.body.hasAttribute('data-fresh-form')) {
        location.reload();
    }
});

// A login page left open for a long time gets a fresh token when the user returns to it.
if (document.body.hasAttribute('data-fresh-form')) {
    const openedAt = Date.now();
    const maxAge = 60 * 60 * 1000;
    document.addEventListener('visibilitychange', () => {
        const typed = [...document.querySelectorAll('input[type="email"], input[type="password"]')].some((i) => i.value);
        if (!document.hidden && !typed && Date.now() - openedAt > maxAge) location.reload();
    });
}

// Show the amount as rupiah under money inputs, so long numbers are easy to check.
const moneyInputs = 'input[name="harga_beli"], input[name="harga_jual"], input[data-rupiah]';
const showRupiah = (input) => {
    let hint = input.nextElementSibling;
    if (!hint || !hint.matches('[data-rupiah-hint]')) {
        hint = document.createElement('span');
        hint.dataset.rupiahHint = '';
        hint.className = 'mt-1 block text-xs text-muted';
        input.insertAdjacentElement('afterend', hint);
    }
    const value = parseFloat(input.value);
    hint.textContent = value > 0 ? rupiah(value) : '';
};
document.querySelectorAll(moneyInputs).forEach(showRupiah);
document.addEventListener('input', (e) => {
    if (e.target.matches && e.target.matches(moneyInputs)) showRupiah(e.target);
});

// Show / hide password.
document.addEventListener('click', (e) => {
    const toggle = e.target.closest('[data-password-toggle]');
    if (!toggle) return;
    const input = document.getElementById(toggle.dataset.passwordToggle);
    if (!input) return;
    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    toggle.setAttribute('aria-pressed', show ? 'true' : 'false');
    toggle.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
});
