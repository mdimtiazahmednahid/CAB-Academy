const CACHE_NAME = 'acs-academy-pwa-v6';
const OFFLINE_ASSETS = [
    '/images/logo.png'
];

self.addEventListener('install', (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(OFFLINE_ASSETS).catch(() => {});
        })
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys.map((key) => {
                    if (key !== CACHE_NAME) {
                        return caches.delete(key);
                    }
                })
            )
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') {
        return;
    }

    // Never intercept HTML page navigations (/login, /dashboard, /admin, /cmd.php, etc.)
    // so CSRF tokens, auth redirects, and single-threaded local dev servers never stall or cache stale HTML.
    if (event.request.mode === 'navigate' || (event.request.headers.get('accept') || '').includes('text/html')) {
        return;
    }

    const url = new URL(event.request.url);
    const isStaticAsset = url.origin === self.location.origin && (
        url.pathname.startsWith('/build/') ||
        url.pathname.startsWith('/images/') ||
        url.pathname.startsWith('/uploads/') ||
        url.pathname.startsWith('/storage/')
    );

    if (!isStaticAsset) {
        return;
    }

    event.respondWith(
        fetch(event.request)
            .then((response) => {
                if (response && response.status === 200 && response.type === 'basic') {
                    const ct = (response.headers.get('content-type') || '').toLowerCase();
                    const isCss = url.pathname.endsWith('.css');
                    const isJs = url.pathname.endsWith('.js');
                    const validMime = isCss
                        ? ct.includes('text/css')
                        : (isJs ? ct.includes('javascript') : !ct.includes('text/html'));
                    if (validMime) {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone)).catch(() => {});
                    }
                }
                return response;
            })
            .catch(async () => {
                const cached = await caches.match(event.request);
                return cached || Response.error();
            })
    );
});
