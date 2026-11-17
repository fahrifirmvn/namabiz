const motionPreference = window.matchMedia('(prefers-reduced-motion: reduce)');
const seenElements = new WeakSet();
const observedElements = new Set();
const counters = new Map();
let revealObserver;
let enhancementScheduled = false;
let enhancementFrame;
let scrollProgressController;
let scrollProgressFrame;

function finishCounter(element) {
    const counter = counters.get(element);
    if (!counter) return;
    cancelAnimationFrame(counter.frame);
    element.textContent = new Intl.NumberFormat('id-ID').format(counter.value);
    counters.delete(element);
}

function animateCounter(element) {
    const value = Number(element.dataset.count);
    if (!Number.isFinite(value) || value === 0 || document.hidden) return;
    const counter = { value, start: performance.now(), frame: 0 };
    counters.set(element, counter);
    const tick = (now) => {
        if (!element.isConnected || motionPreference.matches) {
            finishCounter(element);
            return;
        }
        const progress = Math.min((now - counter.start) / 750, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        element.textContent = new Intl.NumberFormat('id-ID').format(Math.round(value * eased));
        if (progress < 1) counter.frame = requestAnimationFrame(tick);
        else counters.delete(element);
    };
    counter.frame = requestAnimationFrame(tick);
}

function enhancePage() {
    if (motionPreference.matches || !('IntersectionObserver' in window)) return;
    if (!revealObserver) {
        revealObserver = new IntersectionObserver((entries) => {
            let stagger = 0;
            entries.forEach(({ target: element, isIntersecting }) => {
                if (!isIntersecting || seenElements.has(element)) return;
                seenElements.add(element);
                revealObserver?.unobserve(element);
                observedElements.delete(element);
                if (element.hasAttribute('data-count')) {
                    animateCounter(element);
                } else if (element.hasAttribute('data-comparison-highlight')) {
                    element.classList.add('nb-highlight-ready');
                } else {
                    element.style.setProperty('--nb-reveal-delay', `${Math.min(stagger++, 5) * 60}ms`);
                    element.classList.add('nb-reveal');
                }
            });
        }, { threshold: 0.12 });
    }

    for (const element of observedElements) {
        if (!element.isConnected) {
            revealObserver.unobserve(element);
            observedElements.delete(element);
        }
    }
    for (const element of counters.keys()) {
        if (!element.isConnected) finishCounter(element);
    }
    document.querySelectorAll('[data-count], [data-reveal], [data-comparison-highlight]').forEach((element) => {
        if (seenElements.has(element) || observedElements.has(element)) return;
        observedElements.add(element);
        revealObserver.observe(element);
    });
}

function scheduleEnhancement() {
    if (enhancementScheduled) return;
    enhancementScheduled = true;
    enhancementFrame = requestAnimationFrame(() => {
        enhancementScheduled = false;
        enhancePage();
    });
}

function setupScrollProgress() {
    scrollProgressController?.abort();
    cancelAnimationFrame(scrollProgressFrame);
    const progress = document.querySelector('[data-scroll-progress]');
    if (!progress) return;
    scrollProgressController = new AbortController();
    let scheduled = false;
    const update = () => {
        const height = document.documentElement.scrollHeight - window.innerHeight;
        const ratio = height > 0 ? Math.min(Math.max(window.scrollY / height, 0), 1) : 0;
        progress.style.transform = `scaleX(${ratio})`;
        scheduled = false;
    };
    const schedule = () => {
        if (scheduled) return;
        scheduled = true;
        scrollProgressFrame = requestAnimationFrame(update);
    };
    window.addEventListener('scroll', schedule, { passive: true, signal: scrollProgressController.signal });
    window.addEventListener('resize', schedule, { passive: true, signal: scrollProgressController.signal });
    update();
}

function cleanupMotion() {
    revealObserver?.disconnect();
    revealObserver = undefined;
    observedElements.clear();
    for (const element of counters.keys()) finishCounter(element);
    scrollProgressController?.abort();
    cancelAnimationFrame(scrollProgressFrame);
    cancelAnimationFrame(enhancementFrame);
    enhancementScheduled = false;
}

function initializePage() {
    enhancePage();
    setupScrollProgress();
}

document.addEventListener('DOMContentLoaded', initializePage);
document.addEventListener('livewire:navigated', initializePage);
document.addEventListener('livewire:navigating', cleanupMotion);
document.addEventListener('visibilitychange', () => {
    if (document.hidden) for (const element of counters.keys()) finishCounter(element);
});
motionPreference.addEventListener('change', () => {
    cleanupMotion();
    document.querySelectorAll('.nb-reveal, .nb-highlight-ready').forEach((element) => {
        element.classList.remove('nb-reveal', 'nb-highlight-ready');
    });
    initializePage();
});

document.addEventListener('livewire:init', () => {
    Livewire.hook('morph.updated', scheduleEnhancement);
    Livewire.hook('request', ({ fail }) => {
        fail(({ status, preventDefault }) => {
            if ((status >= 500 || status === 0) && document.getElementById('catalog-search')) {
                preventDefault();
                window.dispatchEvent(new CustomEvent('catalog-request-failed'));
            }
        });
    });
});
