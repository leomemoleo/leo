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

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/responsive.css">

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

</body>
</html>
