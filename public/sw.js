const CACHE_NAME = 'e-presensi-cache-v1';
const OFFLINE_URL = '/offline';

const urlsToCache = [
  OFFLINE_URL,
  '/images/logo.png'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      // Fetch each URL individually and catch errors so the SW still installs
      return Promise.allSettled(
        urlsToCache.map(url => {
          return fetch(url).then(response => {
            if (!response.ok) {
              throw new Error(`Failed to fetch ${url}: ${response.status}`);
            }
            return cache.put(url, response);
          }).catch(error => {
            console.error('PWA Caching error for', url, error);
          });
        })
      );
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== CACHE_NAME) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  if (event.request.mode === 'navigate') {
    event.respondWith(
      fetch(event.request)
        .catch(() => {
          return caches.open(CACHE_NAME).then((cache) => {
            return cache.match(OFFLINE_URL);
          });
        })
    );
  } else {
    event.respondWith(
      caches.match(event.request)
        .then((response) => {
          return response || fetch(event.request);
        })
    );
  }
});
