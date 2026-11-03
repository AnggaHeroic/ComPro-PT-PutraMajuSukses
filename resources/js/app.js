/**
 * Skrip situs publik (layout app dan error). Skrip panel admin ada di admin.js.
 */
import Alpine from 'alpinejs';
import { documentationLightbox } from './lightbox';

window.Alpine = Alpine;

Alpine.data('documentationLightbox', documentationLightbox);

/**
 * Carousel kartu layanan di beranda: geser otomatis ke samping, berhenti saat
 * kursor/fokus berada di dalamnya atau tab tidak aktif, dan tidak berputar
 * otomatis bila pengguna memilih "kurangi gerakan".
 */
Alpine.data('serviceCarousel', (total, interval = 6000) => ({
    total,
    current: 0,
    paused: false,
    timer: null,
    touchStartX: null,

    init() {
        const reduceMotion = window.matchMedia(
            '(prefers-reduced-motion: reduce)'
        ).matches;

        if (reduceMotion || this.total < 2) {
            return;
        }

        this.start();

        document.addEventListener('visibilitychange', () => {
            this.paused = document.hidden;
        });
    },

    start() {
        this.stop();

        this.timer = setInterval(() => {
            if (!this.paused) {
                this.next();
            }
        }, interval);
    },

    stop() {
        clearInterval(this.timer);
        this.timer = null;
    },

    restart() {
        // Hanya mulai ulang bila autoplay memang aktif.
        if (this.timer !== null) {
            this.start();
        }
    },

    next(manual = false) {
        this.current = (this.current + 1) % this.total;

        if (manual) this.restart();
    },

    prev(manual = false) {
        this.current = (this.current - 1 + this.total) % this.total;

        if (manual) this.restart();
    },

    go(index) {
        this.current = index;
        this.restart();
    },

    swipeStart(event) {
        this.touchStartX = event.changedTouches[0].clientX;
    },

    swipeEnd(event) {
        if (this.touchStartX === null) return;

        const delta = event.changedTouches[0].clientX - this.touchStartX;
        this.touchStartX = null;

        if (Math.abs(delta) < 50) return;

        delta < 0 ? this.next(true) : this.prev(true);
    },
}));

Alpine.start();
