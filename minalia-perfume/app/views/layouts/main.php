<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?= isset($title) ? seoTitle($title) : SITE_NAME ?></title>

    <!-- Meta Tags -->
    <meta name="description" content="<?= $meta_description ?? SITE_TAGLINE ?>">
    <meta name="keywords" content="<?= $meta_keywords ?? 'parfüm, lüks parfüm, online parfüm satış' ?>">
    <meta name="author" content="<?= SITE_NAME ?>">

    <!-- Open Graph -->
    <meta property="og:title" content="<?= isset($title) ? seoTitle($title) : SITE_NAME ?>">
    <meta property="og:description" content="<?= $meta_description ?? SITE_TAGLINE ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= BASE_URL . $_SERVER['REQUEST_URI'] ?>">
    <meta property="og:site_name" content="<?= SITE_NAME ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>/images/favicon.ico">

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#7A8B5C">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="MINALIA">
    <link rel="manifest" href="<?= BASE_URL ?>/manifest.json">
    <link rel="apple-touch-icon" href="<?= BASE_URL ?>/images/icons/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?= BASE_URL ?>/images/icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= BASE_URL ?>/images/icons/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="167x167" href="<?= BASE_URL ?>/images/icons/icon-192x192.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/responsive.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/mobile-menu.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Header -->
    <?php include __DIR__ . '/../components/header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <?php
        // Display flash message
        $flashMessage = getFlashMessage();
        if ($flashMessage):
        ?>
        <div class="flash-message flash-<?= $flashMessage['type'] ?>">
            <div class="container">
                <p><?= $flashMessage['text'] ?></p>
                <button class="flash-close">&times;</button>
            </div>
        </div>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- JavaScript -->
    <script src="<?= BASE_URL ?>/js/main.js"></script>
    <?php if (isset($extra_js)): ?>
        <?php foreach ((array)$extra_js as $js): ?>
            <script src="<?= BASE_URL ?>/js/<?= $js ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((registration) => {
                        console.log('✅ PWA: Service Worker registered', registration.scope);

                        // Check for updates
                        registration.addEventListener('updatefound', () => {
                            const newWorker = registration.installing;
                            newWorker.addEventListener('statechange', () => {
                                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    // New version available
                                    if (confirm('🔄 Yeni bir sürüm mevcut! Güncellemek ister misiniz?')) {
                                        newWorker.postMessage({ type: 'SKIP_WAITING' });
                                        window.location.reload();
                                    }
                                }
                            });
                        });
                    })
                    .catch((error) => {
                        console.error('❌ PWA: Service Worker registration failed', error);
                    });

                // Reload page when new service worker takes over
                let refreshing = false;
                navigator.serviceWorker.addEventListener('controllerchange', () => {
                    if (!refreshing) {
                        refreshing = true;
                        window.location.reload();
                    }
                });
            });

            // Request push notification permission on first visit
            if (Notification.permission === 'default') {
                setTimeout(() => {
                    if (confirm('📢 MINALIA\'dan bildirim almak ister misiniz?\n(Özel indirimler, sipariş güncellemeleri)')) {
                        Notification.requestPermission().then((permission) => {
                            console.log('Notification permission:', permission);
                        });
                    }
                }, 5000); // 5 seconds after page load
            }
        }
    </script>

</body>
</html>
