const CACHE_NAME = 'supply4me-v1';
const STATIC_CACHE = 'supply4me-static-v1';

const STATIC_ASSETS = [
    '/',
    '/shop',
    '/manifest.json',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE).then((cache) => {
            return cache.addAll(STATIC_ASSETS);
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((name) => name !== CACHE_NAME && name !== STATIC_CACHE)
                    .map((name) => caches.delete(name))
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;

    if (event.request.url.includes('/api/') || event.request.url.includes('/sanctum/')) {
        return;
    }

    event.respondWith(
        caches.match(event.request).then((cached) => {
            const fetchPromise = fetch(event.request)
                .then((response) => {
                    if (response && response.status === 200) {
                        const responseClone = response.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(event.request, responseClone);
                        });
                    }
                    return response;
                })
                .catch(() => cached);

            return cached || fetchPromise;
        })
    );
});

self.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    self.deferredPrompt = event;
    self.clients.matchAll().then((clients) => {
        clients.forEach((client) => {
            client.postMessage({ type: 'INSTALL_PROMPT_READY' });
        });
    });
});
