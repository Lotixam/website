/**
 * Carrousel hero blog : 2–3 articles, défilement auto et contrôles.
 */
(function () {
    const root = document.querySelector('[data-blog-hero]');
    if (!root) {
        return;
    }

    const slides = Array.from(root.querySelectorAll('[data-hero-slide]'));
    if (slides.length === 0) {
        return;
    }

    let index = slides.findIndex((el) => el.classList.contains('is-active'));
    if (index < 0) {
        index = 0;
    }

    const dots = Array.from(root.querySelectorAll('[data-hero-dot]'));
    const prevBtn = root.querySelector('[data-hero-prev]');
    const nextBtn = root.querySelector('[data-hero-next]');
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const slideCount = slides.length;

    function goTo(i) {
        const next = ((i % slideCount) + slideCount) % slideCount;
        index = next;
        slides.forEach((el, j) => {
            el.classList.toggle('is-active', j === index);
            el.setAttribute('aria-hidden', j === index ? 'false' : 'true');
        });
        dots.forEach((dot, j) => {
            dot.classList.toggle('is-active', j === index);
            if (j === index) {
                dot.setAttribute('aria-current', 'true');
            } else {
                dot.removeAttribute('aria-current');
            }
        });
    }

    let timer = null;

    function startAuto() {
        if (prefersReducedMotion || slideCount < 2) {
            return;
        }
        stopAuto();
        timer = window.setInterval(() => {
            goTo(index + 1);
        }, 7000);
    }

    function stopAuto() {
        if (timer !== null) {
            window.clearInterval(timer);
            timer = null;
        }
    }

    goTo(index);

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            stopAuto();
            goTo(index - 1);
            startAuto();
        });
    }
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            stopAuto();
            goTo(index + 1);
            startAuto();
        });
    }

    dots.forEach((dot, j) => {
        dot.addEventListener('click', () => {
            stopAuto();
            goTo(j);
            startAuto();
        });
    });

    root.addEventListener('mouseenter', stopAuto);
    root.addEventListener('mouseleave', startAuto);
    root.addEventListener('focusin', stopAuto);
    root.addEventListener('focusout', startAuto);

    startAuto();
})();
