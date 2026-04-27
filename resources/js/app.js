// Sheza Laundry - Main JS entry point
// Alpine.js is bundled by Livewire v4 automatically.
// This file handles PWA service worker registration only.

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {
            // Service worker registration failed — non-fatal
        });
    });
}

