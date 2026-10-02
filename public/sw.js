const VERSION = 'v2';
const STATIC_CACHE = `supply4me-static-${VERSION}`;
const RUNTIME_CACHE = `supply4me-runtime-${VERSION}`;

const OFFLINE_PATHS = ['/', '/shop'];

const OFFLINE_ASSETS = [
    '/',
    '/shop',
    '/manifest.json',
    '/images/logo_dark.png',
];

const isStaticAsset = (url) =>
    url.pathname.startsWith('/build/') ||
    url.pathname.startsWith('/images/') ||
    url.pathname.startsWith('/fonts/');

const isCacheable = (response) => {
    const control = response.headers.get('Cache-Control') || '';
    return !control.includes('no-store') && !control.includes('private');
};

const cacheFirst = (request) =>
    caches.match(request).then((cached) => {
        if (cached) {
            return cached;
        }

        return fetch(request).then((response) => {
            if (response && response.status === 200 && response.type === 'basic') {
                const clone = response.clone();
                caches.open(RUNTIME_CACHE).then((cache) => cache.put(request, clone));
            }
            return response;
        });
    });

const networkFirst = (request) =>
    fetch(request)
        .then((response) => {
            if (
                response &&
                response.status === 200 &&
                response.type === 'basic' &&
                isCacheable(response) &&
                OFFLINE_PATHS.includes(new URL(request.url).pathname)
            ) {
                const clone = response.clone();
                caches.open(RUNTIME_CACHE).then((cache) => cache.put(request, clone));
            }
            return response;
        })
        .catch(() => caches.match(request).then((cached) => cached || caches.match('/shop')));

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE).then((cache) => cache.addAll(OFFLINE_ASSETS))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((name) => name !== STATIC_CACHE && name !== RUNTIME_CACHE)
                    .map((name) => caches.delete(name))
            )
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    if (url.origin !== self.location.origin) {
        return;
    }

    if (url.pathname.startsWith('/api/') || url.pathname.startsWith('/sanctum/')) {
        return;
    }

    if (request.headers.get('X-Inertia')) {
        return;
    }

    if (request.mode === 'navigate') {
        event.respondWith(networkFirst(request));
        return;
    }

    if (isStaticAsset(url)) {
        event.respondWith(cacheFirst(request));
    }
});

self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});