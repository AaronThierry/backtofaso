/**
 * Animations du site, sans dépendance :
 *  - [data-reveal]          apparition au défilement (valeurs : up par défaut, left, right, zoom, fade)
 *  - [data-reveal-stagger]  sur un parent : ses enfants [data-reveal] apparaissent en cascade (pas en ms)
 *  - [data-lines]           titre révélé ligne par ligne
 *  - [data-draw]            trait qui se dessine
 *  - [data-count="6"]       compteur animé jusqu'à la valeur
 *  - [data-parallax="0.03"] léger suivi de la souris dans le parent [data-parallax-scope]
 *  - [data-scroll-progress] barre de progression de lecture (largeur en scaleX)
 * Tout est désactivé si l'utilisateur demande moins d'animations.
 */
const reduced = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

function reveal() {
    document.querySelectorAll('[data-reveal-stagger]').forEach((parent) => {
        const step = Number(parent.dataset.revealStagger) || 90;
        parent.querySelectorAll(':scope > [data-reveal], :scope > * > [data-reveal]').forEach((el, i) => {
            el.style.setProperty('--reveal-delay', `${i * step}ms`);
        });
    });

    const targets = document.querySelectorAll('[data-reveal], [data-lines], [data-draw], [data-count]');
    if (reduced() || !('IntersectionObserver' in window)) {
        document.querySelectorAll('[data-reveal]').forEach((el) => el.removeAttribute('data-reveal'));
        targets.forEach((el) => { el.classList.add('is-visible'); if (el.dataset.count) el.textContent = el.dataset.count; });
        return;
    }

    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            const el = entry.target;
            el.classList.add('is-visible');
            if (el.dataset.count) countUp(el);
            if (el.hasAttribute('data-reveal')) settle(el);
            io.unobserve(el);
        });
    }, { threshold: 0.14, rootMargin: '0px 0px -6% 0px' });

    // Les compteurs partent de 0 (la valeur finale reste dans le HTML pour les cas sans script)
    document.querySelectorAll('[data-count]').forEach((el) => { el.textContent = '0'; });
    targets.forEach((el) => io.observe(el));
}

/** Fin de l'apparition : l'élément retrouve ses propres transitions (survol des cartes, etc.) */
function settle(el) {
    const delay = parseFloat(getComputedStyle(el).getPropertyValue('--reveal-delay')) || 0;
    setTimeout(() => el.removeAttribute('data-reveal'), 900 + delay);
}

function countUp(el) {
    const end = Number(el.dataset.count);
    const duration = 1400;
    const start = performance.now();
    const tick = (now) => {
        const t = Math.min(1, (now - start) / duration);
        const eased = 1 - Math.pow(1 - t, 3);
        el.textContent = Math.round(end * eased);
        if (t < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
}

function parallax() {
    if (reduced() || !window.matchMedia('(pointer: fine)').matches) return;

    document.querySelectorAll('[data-parallax-scope]').forEach((scope) => {
        const layers = scope.querySelectorAll('[data-parallax]');
        let frame = null;
        scope.addEventListener('pointermove', (e) => {
            const r = scope.getBoundingClientRect();
            const x = (e.clientX - r.left) / r.width - 0.5;
            const y = (e.clientY - r.top) / r.height - 0.5;
            cancelAnimationFrame(frame);
            frame = requestAnimationFrame(() => layers.forEach((l) => {
                const depth = Number(l.dataset.parallax) * 100;
                l.style.transform = `translate3d(${x * depth}px, ${y * depth}px, 0)`;
            }));
        });
        scope.addEventListener('pointerleave', () => layers.forEach((l) => { l.style.transform = ''; }));
        layers.forEach((l) => { l.style.transition = 'transform 0.6s cubic-bezier(0.2, 0.7, 0.2, 1)'; });
    });
}

function scrollProgress() {
    const bar = document.querySelector('[data-scroll-progress]');
    if (!bar) return;
    let ticking = false;
    const update = () => {
        const max = document.documentElement.scrollHeight - window.innerHeight;
        bar.style.transform = `scaleX(${max > 0 ? window.scrollY / max : 0})`;
        ticking = false;
    };
    window.addEventListener('scroll', () => { if (!ticking) { ticking = true; requestAnimationFrame(update); } }, { passive: true });
    update();
}

export function initMotion() {
    const start = () => {
        document.documentElement.classList.add('motion-ready');
        reveal(); parallax(); scrollProgress();
    };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start);
    else start();
}
