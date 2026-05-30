const menuToggle = document.querySelector('.menu-toggle');
const mainNav = document.querySelector('#main-nav');

function setMenu(open) {
    if (!menuToggle || !mainNav) return;
    menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    menuToggle.setAttribute('aria-label', open ? 'Cerrar menú' : 'Abrir menú');
    document.body.classList.toggle('nav-open', open);
}

menuToggle?.addEventListener('click', () => {
    setMenu(menuToggle.getAttribute('aria-expanded') !== 'true');
});

mainNav?.addEventListener('click', (event) => {
    if (event.target.closest('a')) {
        setMenu(false);
    }
});

window.addEventListener('resize', () => {
    if (window.innerWidth > 760) {
        setMenu(false);
    }
});
