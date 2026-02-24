import './bootstrap';
import './echo';
import Alpine from 'alpinejs';

import { posision, showError, showGameWinner } from './game.js';
window.posision = posision;
window.showError = showError;
window.showGameWinner = showGameWinner;

// Expose Alpine globally (required for production)
window.Alpine = Alpine;

// Wait for DOM to be ready before starting Alpine
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => Alpine.start());
} else {
    Alpine.start();
}
