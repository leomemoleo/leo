/**
 * MINALIA PWA Initialization
 * Service Worker registration, Install prompt, Push notifications
 */

(function() {
    'use strict';

    let deferredPrompt = null;
    let swRegistration = null;

    // ===================================
    // SERVICE WORKER REGISTRATION
    // ===================================

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            registerServiceWorker();
        });
    }

    async function registerServiceWorker() {
        try {
            swRegistration = await navigator.serviceWorker.register('/sw.js', {
                scope: '/'
            });

            console.log('[PWA] Service Worker registered:', swRegistration.scope);

            // Check for updates
            swRegistration.addEventListener('updatefound', () => {
                const newWorker = swRegistration.installing;

                newWorker.addEventListener('statechange', () => {
                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                        // New service worker available
                        showUpdateNotification();
                    }
                });
            });

            // Request push notification permission
            if ('PushManager' in window) {
                requestNotificationPermission();
            }

        } catch (error) {
            console.error('[PWA] Service Worker registration failed:', error);
        }
    }

    /**
     * Show update notification when new version available
     */
    function showUpdateNotification() {
        const updateBanner = document.createElement('div');
        updateBanner.className = 'pwa-update-banner';
        updateBanner.innerHTML = `
            <div class="update-content">
                <i class="fas fa-sync-alt"></i>
                <span>Yeni sürüm mevcut!</span>
                <button onclick="window.updatePWA()" class="btn-update">Güncelle</button>
                <button onclick="this.parentElement.parentElement.remove()" class="btn-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        document.body.appendChild(updateBanner);
    }

    /**
     * Update PWA to new version
     */
    window.updatePWA = function() {
        if (!swRegistration || !swRegistration.waiting) return;

        swRegistration.waiting.postMessage({ type: 'SKIP_WAITING' });

        navigator.serviceWorker.addEventListener('controllerchange', () => {
            window.location.reload();
        });
    };

    // ===================================
    // INSTALL PROMPT (Add to Home Screen)
    // ===================================

    window.addEventListener('beforeinstallprompt', (e) => {
        console.log('[PWA] Install prompt triggered');

        // Prevent default mini-infobar
        e.preventDefault();

        // Store event for later use
        deferredPrompt = e;

        // Show custom install button
        showInstallPrompt();
    });

    /**
     * Show custom install prompt
     */
    function showInstallPrompt() {
        const installBanner = document.createElement('div');
        installBanner.className = 'pwa-install-banner';
        installBanner.id = 'pwa-install-banner';
        installBanner.innerHTML = `
            <div class="install-content">
                <div class="install-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <div class="install-text">
                    <strong>MINALIA Uygulamasını Yükle</strong>
                    <p>Daha hızlı ve kolay alışveriş için cihazınıza yükleyin</p>
                </div>
                <div class="install-actions">
                    <button onclick="window.installPWA()" class="btn-install">
                        <i class="fas fa-download"></i> Yükle
                    </button>
                    <button onclick="window.dismissInstallPrompt()" class="btn-dismiss">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(installBanner);

        // Show banner with animation
        setTimeout(() => {
            installBanner.classList.add('show');
        }, 100);
    }

    /**
     * Install PWA
     */
    window.installPWA = async function() {
        if (!deferredPrompt) {
            console.log('[PWA] Install prompt not available');
            return;
        }

        // Show native install prompt
        deferredPrompt.prompt();

        // Wait for user response
        const { outcome } = await deferredPrompt.userChoice;

        console.log('[PWA] Install prompt outcome:', outcome);

        if (outcome === 'accepted') {
            // User accepted
            showToast('Uygulama yükleniyor...', 'success');

            // Track installation
            trackPWAInstall();
        }

        // Clear the prompt
        deferredPrompt = null;

        // Hide install banner
        window.dismissInstallPrompt();
    };

    /**
     * Dismiss install prompt
     */
    window.dismissInstallPrompt = function() {
        const banner = document.getElementById('pwa-install-banner');
        if (banner) {
            banner.classList.remove('show');
            setTimeout(() => banner.remove(), 300);
        }

        // Remember dismissal (show again after 7 days)
        localStorage.setItem('pwa_install_dismissed', Date.now());
    };

    /**
     * Check if should show install prompt
     */
    function shouldShowInstallPrompt() {
        const dismissed = localStorage.getItem('pwa_install_dismissed');

        if (!dismissed) return true;

        const daysSinceDismissed = (Date.now() - dismissed) / (1000 * 60 * 60 * 24);

        return daysSinceDismissed > 7;
    }

    /**
     * Track PWA installation
     */
    function trackPWAInstall() {
        if (typeof gtag !== 'undefined') {
            gtag('event', 'pwa_install', {
                event_category: 'PWA',
                event_label: 'App Installed'
            });
        }

        // Send to backend
        fetch('/api/track-pwa-install', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        });
    }

    /**
     * Detect if app is installed
     */
    window.addEventListener('appinstalled', () => {
        console.log('[PWA] App installed successfully');

        showToast('Uygulama başarıyla yüklendi!', 'success');

        // Hide install banner if visible
        window.dismissInstallPrompt();

        // Track installation
        trackPWAInstall();
    });

    // ===================================
    // PUSH NOTIFICATIONS
    // ===================================

    /**
     * Request notification permission
     */
    async function requestNotificationPermission() {
        if (!('Notification' in window)) {
            console.log('[PWA] Notifications not supported');
            return;
        }

        if (Notification.permission === 'granted') {
            subscribeToPushNotifications();
            return;
        }

        if (Notification.permission !== 'denied') {
            // Show custom permission prompt
            showNotificationPermissionPrompt();
        }
    }

    /**
     * Show custom notification permission prompt
     */
    function showNotificationPermissionPrompt() {
        // Only show after user has been on site for 30 seconds
        setTimeout(() => {
            const permissionPrompt = document.createElement('div');
            permissionPrompt.className = 'notification-permission-prompt';
            permissionPrompt.innerHTML = `
                <div class="permission-content">
                    <i class="fas fa-bell"></i>
                    <div class="permission-text">
                        <strong>Bildirimlere İzin Ver</strong>
                        <p>Sipariş güncellemeleri ve özel tekliflerden haberdar olun</p>
                    </div>
                    <div class="permission-actions">
                        <button onclick="window.enableNotifications()" class="btn-enable">
                            İzin Ver
                        </button>
                        <button onclick="this.parentElement.parentElement.parentElement.remove()" class="btn-cancel">
                            Şimdi Değil
                        </button>
                    </div>
                </div>
            `;

            document.body.appendChild(permissionPrompt);
        }, 30000);
    }

    /**
     * Enable push notifications
     */
    window.enableNotifications = async function() {
        try {
            const permission = await Notification.requestPermission();

            if (permission === 'granted') {
                console.log('[PWA] Notification permission granted');

                subscribeToPushNotifications();

                showToast('Bildirimler aktif edildi!', 'success');

                // Remove permission prompt
                const prompt = document.querySelector('.notification-permission-prompt');
                if (prompt) prompt.remove();

            } else {
                console.log('[PWA] Notification permission denied');
                showToast('Bildirim izni reddedildi', 'error');
            }

        } catch (error) {
            console.error('[PWA] Notification permission error:', error);
        }
    };

    /**
     * Subscribe to push notifications
     */
    async function subscribeToPushNotifications() {
        if (!swRegistration) {
            console.error('[PWA] Service Worker not registered');
            return;
        }

        try {
            // Check if already subscribed
            let subscription = await swRegistration.pushManager.getSubscription();

            if (!subscription) {
                // Subscribe to push notifications
                const vapidPublicKey = 'YOUR_VAPID_PUBLIC_KEY_HERE';

                subscription = await swRegistration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(vapidPublicKey)
                });

                console.log('[PWA] Push subscription created');
            }

            // Send subscription to server
            await sendSubscriptionToServer(subscription);

        } catch (error) {
            console.error('[PWA] Push subscription failed:', error);
        }
    }

    /**
     * Send push subscription to server
     */
    async function sendSubscriptionToServer(subscription) {
        try {
            await fetch('/api/push-subscription', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(subscription)
            });

            console.log('[PWA] Subscription sent to server');

        } catch (error) {
            console.error('[PWA] Failed to send subscription:', error);
        }
    }

    /**
     * Convert VAPID key to Uint8Array
     */
    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }

        return outputArray;
    }

    // ===================================
    // OFFLINE DETECTION
    // ===================================

    window.addEventListener('online', () => {
        console.log('[PWA] Back online');
        showToast('İnternet bağlantınız geri geldi', 'success');

        // Sync pending data
        if (swRegistration && swRegistration.sync) {
            swRegistration.sync.register('sync-cart');
            swRegistration.sync.register('sync-wishlist');
        }
    });

    window.addEventListener('offline', () => {
        console.log('[PWA] Gone offline');
        showToast('İnternet bağlantısı yok. Offline modda çalışıyorsunuz.', 'warning');
    });

    // ===================================
    // HELPER FUNCTIONS
    // ===================================

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `pwa-toast pwa-toast-${type}`;
        toast.textContent = message;

        document.body.appendChild(toast);

        setTimeout(() => toast.classList.add('show'), 100);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    console.log('[PWA] Initialization complete');

})();
