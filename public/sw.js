const VERSION = 'v3';

const PRECACHE_CACHE = `supply4me-precache-${VERSION}`;
const DATA_CACHE = `supply4me-data-${VERSION}`;
const STATIC_CACHE = `supply4me-static-${VERSION}`;

const CURRENT_CACHES = [PRECACHE_CACHE, DATA_CACHE, STATIC_CACHE];

const PRECACHE_URLS = ['/', '/shop', '/manifest.json', '/images/logo_dark.png'];

const STATIC_PREFIXES = ['/build/', '/images/', '/fonts/', '/icons/'];

const OFFLINE_PATHS = ['/', '/shop'];
const OFFLINE_PREFIXES = ['/product/'];

const NETWORK_ONLY_PREFIXES = ['/api/', '/sanctum/', '/livewire/', '/broadcasting/'];

const isStaticAsset = (url) =>
    STATIC_PREFIXES.some((prefix) => url.pathname.startsWith(prefix));

const isOfflineCachable = (url) =>
    OFFLINE_PATHS.includes(url.pathname) ||
    OFFLINE_PREFIXES.some((prefix) => url.pathname.startsWith(prefix));

const isStoreable = (response) => {
    if (!response || response.status !== 200 || response.type !== 'basic') {
        return false;
    }

    if (response.headers.get('Vary') === '*') {
        return false;
    }

    const control = response.headers.get('Cache-Control') || '';

    return !control.includes('no-store');
};

const putInCache = (cacheName, request, response) => {
    const clone = response.clone();
    caches.open(cacheName).then((cache) => cache.put(request, clone));
};

const serveStatic = (request) =>
    caches.match(request).then((cached) => {
        if (cached) {
            return cached;
        }

        return fetch(request).then((response) => {
            if (isStoreable(response)) {
                putInCache(STATIC_CACHE, request, response);
            }
            return response;
        });
    });

const serveDynamic = (request) =>
    fetch(request)
        .then((response) => {
            if (isStoreable(response) && isOfflineCachable(new URL(request.url))) {
                putInCache(DATA_CACHE, request, response);
            }
            return response;
        })
        .catch(() =>
            caches
                .match(request)
                .then((cached) => cached || caches.match('/shop'))
        );

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(PRECACHE_CACHE).then((cache) => cache.addAll(PRECACHE_URLS))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((key) => !CURRENT_CACHES.includes(key))
                    .map((key) => caches.delete(key))
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

    if (NETWORK_ONLY_PREFIXES.some((prefix) => url.pathname.startsWith(prefix))) {
        return;
    }

    if (isStaticAsset(url)) {
        event.respondWith(serveStatic(request));
        return;
    }

    event.respondWith(serveDynamic(request));
});

self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});