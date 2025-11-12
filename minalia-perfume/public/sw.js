/**
 * MINALIA Service Worker
 * Enterprise PWA Implementation
 * Features: Offline Support, Caching Strategy, Push Notifications
 */

const CACHE_VERSION = 'minalia-v1.0.0';
const CACHE_NAME = `minalia-cache-${CACHE_VERSION}`;

// Assets to cache immediately on install
const PRECACHE_ASSETS = [
    '/',
    '/css/style.css',
    '/css/responsive.css',
    '/js/main.js',
    '/images/logo.png',
    '/manifest.json'
];

// Routes to cache with network-first strategy
const NETWORK_FIRST_ROUTES = [
    '/products',
    '/cart',
    '/api/',
    '/account'
];

// Routes to cache with cache-first strategy
const CACHE_FIRST_ROUTES = [
    '/images/',
    '/css/',
    '/js/',
    '/fonts/'
];

/**
 * Service Worker Install Event
 * Precache essential assets
 */
self.addEventListener('install', (event) => {
    console.log('[SW] Installing Service Worker version:', CACHE_VERSION);

    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('[SW] Precaching assets');
                return cache.addAll(PRECACHE_ASSETS);
            })
            .then(() => {
                console.log('[SW] Precaching complete');
                return self.skipWaiting();
            })
            .catch((error) => {
                console.error('[SW] Precaching failed:', error);
            })
    );
});

/**
 * Service Worker Activate Event
 * Clean up old caches
 */
self.addEventListener('activate', (event) => {
    console.log('[SW] Activating Service Worker version:', CACHE_VERSION);

    event.waitUntil(
        caches.keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames
                        .filter((cacheName) => {
                            // Delete old caches
                            return cacheName.startsWith('minalia-cache-') &&
                                   cacheName !== CACHE_NAME;
                        })
                        .map((cacheName) => {
                            console.log('[SW] Deleting old cache:', cacheName);
                            return caches.delete(cacheName);
                        })
                );
            })
            .then(() => {
                console.log('[SW] Activation complete');
                return self.clients.claim();
            })
    );
});

/**
 * Service Worker Fetch Event
 * Implement caching strategies
 */
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }

    // Skip chrome-extension and other non-http(s) requests
    if (!url.protocol.startsWith('http')) {
        return;
    }

    // Determine caching strategy
    const strategy = getCachingStrategy(url.pathname);

    event.respondWith(
        strategy(request)
            .catch((error) => {
                console.error('[SW] Fetch error:', error);

                // Return offline page for navigation requests
                if (request.mode === 'navigate') {
                    return caches.match('/offline.html');
                }

                // Return offline image for images
                if (request.destination === 'image') {
                    return caches.match('/images/offline-image.png');
                }
            })
    );
});

/**
 * Determine caching strategy based on route
 */
function getCachingStrategy(pathname) {
    // Network-first for dynamic content
    if (NETWORK_FIRST_ROUTES.some(route => pathname.startsWith(route))) {
        return networkFirst;
    }

    // Cache-first for static assets
    if (CACHE_FIRST_ROUTES.some(route => pathname.startsWith(route))) {
        return cacheFirst;
    }

    // Default: stale-while-revalidate
    return staleWhileRevalidate;
}

/**
 * Network-First Strategy
 * Try network, fallback to cache
 */
async function networkFirst(request) {
    try {
        const networkResponse = await fetch(request);

        // Cache successful responses
        if (networkResponse && networkResponse.status === 200) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, networkResponse.clone());
        }

        return networkResponse;

    } catch (error) {
        // Network failed, try cache
        const cachedResponse = await caches.match(request);

        if (cachedResponse) {
            console.log('[SW] Serving from cache (network failed):', request.url);
            return cachedResponse;
        }

        throw error;
    }
}

/**
 * Cache-First Strategy
 * Try cache, fallback to network
 */
async function cacheFirst(request) {
    const cachedResponse = await caches.match(request);

    if (cachedResponse) {
        console.log('[SW] Serving from cache:', request.url);
        return cachedResponse;
    }

    // Not in cache, fetch from network
    const networkResponse = await fetch(request);

    // Cache the response
    if (networkResponse && networkResponse.status === 200) {
        const cache = await caches.open(CACHE_NAME);
        cache.put(request, networkResponse.clone());
    }

    return networkResponse;
}

/**
 * Stale-While-Revalidate Strategy
 * Return cache immediately, update in background
 */
async function staleWhileRevalidate(request) {
    const cachedResponse = await caches.match(request);

    const fetchPromise = fetch(request).then((networkResponse) => {
        // Update cache in background
        if (networkResponse && networkResponse.status === 200) {
            caches.open(CACHE_NAME).then((cache) => {
                cache.put(request, networkResponse.clone());
            });
        }

        return networkResponse;
    });

    // Return cache immediately if available, otherwise wait for network
    return cachedResponse || fetchPromise;
}

/**
 * Push Notification Event
 * Handle incoming push notifications
 */
self.addEventListener('push', (event) => {
    console.log('[SW] Push notification received');

    let data = {
        title: 'MINALIA',
        body: 'Yeni bildiriminiz var',
        icon: '/images/icons/icon-192x192.png',
        badge: '/images/icons/badge-72x72.png',
        tag: 'minalia-notification',
        requireInteraction: false
    };

    if (event.data) {
        try {
            data = event.data.json();
        } catch (e) {
            data.body = event.data.text();
        }
    }

    const options = {
        body: data.body,
        icon: data.icon || '/images/icons/icon-192x192.png',
        badge: data.badge || '/images/icons/badge-72x72.png',
        tag: data.tag || 'minalia-notification',
        requireInteraction: data.requireInteraction || false,
        data: data.data || {},
        actions: data.actions || [],
        vibrate: [200, 100, 200]
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

/**
 * Notification Click Event
 * Handle notification clicks
 */
self.addEventListener('notificationclick', (event) => {
    console.log('[SW] Notification clicked:', event.notification.tag);

    event.notification.close();

    // Handle action button clicks
    if (event.action) {
        console.log('[SW] Notification action:', event.action);

        // Open specific URL based on action
        const actionUrls = {
            'view': event.notification.data.url || '/',
            'order': '/account/orders',
            'cart': '/cart'
        };

        const url = actionUrls[event.action] || '/';

        event.waitUntil(
            clients.openWindow(url)
        );

        return;
    }

    // Default action: focus or open app
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((clientList) => {
                // Focus existing window if available
                for (const client of clientList) {
                    if (client.url === '/' && 'focus' in client) {
                        return client.focus();
                    }
                }

                // Open new window
                if (clients.openWindow) {
                    return clients.openWindow(event.notification.data.url || '/');
                }
            })
    );
});

/**
 * Background Sync Event
 * Handle offline form submissions
 */
self.addEventListener('sync', (event) => {
    console.log('[SW] Background sync triggered:', event.tag);

    if (event.tag === 'sync-cart') {
        event.waitUntil(syncCart());
    }

    if (event.tag === 'sync-wishlist') {
        event.waitUntil(syncWishlist());
    }
});

/**
 * Sync cart data when back online
 */
async function syncCart() {
    try {
        // Get pending cart updates from IndexedDB
        const db = await openDB();
        const pendingUpdates = await db.getAll('pendingCartUpdates');

        for (const update of pendingUpdates) {
            await fetch('/api/cart/sync', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(update)
            });

            await db.delete('pendingCartUpdates', update.id);
        }

        console.log('[SW] Cart synced successfully');

    } catch (error) {
        console.error('[SW] Cart sync failed:', error);
        throw error;
    }
}

/**
 * Sync wishlist when back online
 */
async function syncWishlist() {
    try {
        const db = await openDB();
        const pendingUpdates = await db.getAll('pendingWishlistUpdates');

        for (const update of pendingUpdates) {
            await fetch('/api/wishlist/sync', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(update)
            });

            await db.delete('pendingWishlistUpdates', update.id);
        }

        console.log('[SW] Wishlist synced successfully');

    } catch (error) {
        console.error('[SW] Wishlist sync failed:', error);
        throw error;
    }
}

/**
 * Open IndexedDB for offline data storage
 */
function openDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('minalia-db', 1);

        request.onerror = () => reject(request.error);
        request.onsuccess = () => resolve(request.result);

        request.onupgradeneeded = (event) => {
            const db = event.target.result;

            if (!db.objectStoreNames.contains('pendingCartUpdates')) {
                db.createObjectStore('pendingCartUpdates', { keyPath: 'id', autoIncrement: true });
            }

            if (!db.objectStoreNames.contains('pendingWishlistUpdates')) {
                db.createObjectStore('pendingWishlistUpdates', { keyPath: 'id', autoIncrement: true });
            }
        };
    });
}

/**
 * Message Event
 * Handle messages from clients
 */
self.addEventListener('message', (event) => {
    console.log('[SW] Message received:', event.data);

    if (event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }

    if (event.data.type === 'GET_VERSION') {
        event.ports[0].postMessage({ version: CACHE_VERSION });
    }

    if (event.data.type === 'CLEAR_CACHE') {
        event.waitUntil(
            caches.delete(CACHE_NAME)
                .then(() => {
                    event.ports[0].postMessage({ success: true });
                })
        );
    }
});

console.log('[SW] Service Worker loaded successfully');
