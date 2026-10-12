import './bootstrap';
import Alpine from 'alpinejs';
import { initMotion } from './motion';

window.Alpine = Alpine;
Alpine.start();

initMotion();

// PWA : enregistrement du service worker (hors environnement de dev Vite)
if ('serviceWorker' in navigator && !import.meta.env.DEV) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}
