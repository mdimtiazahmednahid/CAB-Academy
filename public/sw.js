const CACHE_VERSION = 'acs-pwa-v1';
const OFFLINE_URL = '/offline';
const PRECACHE_ASSETS = [
  OFFLINE_URL,
  '/manifest.json',
  '/images/logo.png',
  '/images/icons/icon-192x192.png'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_VERSION)
      .then((cache) => cache.addAll(PRECACHE_ASSETS))
      .then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys()
      .then((keys) => Promise.all(
        keys.filter((k) => k !== CACHE_VERSION).map((k) => caches.delete(k))
      ))
      .then(() => self.clients.claim())
  );
});

function shouldBypass(url) {
  const path = url.pathname;
  return (
    path.startsWith('/admin') ||
    path.startsWith('/login') ||
    path.startsWith('/register') ||
    path.startsWith('/logout') ||
    path.startsWith('/cron') ||
    path.includes('/checkout') ||
    path.includes('/express-enroll')
  );
}

self.addEventListener('fetch', (event) => {
  const req = event.request;
  if (req.method !== 'GET') return;

  const url = new URL(req.url);
  if (url.origin !== self.location.origin) return;
  if (shouldBypass(url)) return;

  // Navigation requests: Network-First with offline fallback
  if (req.mode === 'navigate' || (req.headers.get('accept') || '').includes('text/html')) {
    event.respondWith(
      fetch(req)
        .then((res) => {
          if (res && res.status === 200) {
            const copy = res.clone();
            caches.open(CACHE_VERSION).then((cache) => cache.put(req, copy));
          }
          return res;
        })
        .catch(() => caches.match(req).then((cached) => cached || caches.match(OFFLINE_URL)))
    );
    return;
  }

  // Static assets: Stale-While-Revalidate
  if (/\.(?:css|js|png|jpg|jpeg|webp|svg|woff2?)$/i.test(url.pathname)) {
    event.respondWith(
      caches.match(req).then((cached) => {
        const networkFetch = fetch(req).then((res) => {
          if (res && res.status === 200) {
            const copy = res.clone();
            caches.open(CACHE_VERSION).then((cache) => cache.put(req, copy));
          }
          return res;
        }).catch(() => cached);
        return cached || networkFetch;
      })
    );
  }
});
