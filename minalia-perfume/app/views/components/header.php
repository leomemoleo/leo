<header class="header">
    <!-- Top Bar -->
    <div class="header-top">
        <div class="container">
            <div class="header-top-content">
                <div class="header-top-left">
                    <span><i class="fas fa-phone"></i> <?= $_ENV['SITE_PHONE'] ?? '+90 212 XXX XX XX' ?></span>
                    <span><i class="fas fa-envelope"></i> <?= $_ENV['SITE_EMAIL'] ?? 'info@minalia.com' ?></span>
                </div>
                <div class="header-top-right">
                    <?php if (isLoggedIn()): ?>
                        <a href="<?= BASE_URL ?>/account"><i class="fas fa-user"></i> Hesabım</a>
                        <a href="<?= BASE_URL ?>/logout"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/login"><i class="fas fa-sign-in-alt"></i> Giriş</a>
                        <a href="<?= BASE_URL ?>/register"><i class="fas fa-user-plus"></i> Üye Ol</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <div class="header-main">
        <div class="container">
            <div class="header-main-content">
                <!-- Logo -->
                <div class="header-logo">
                    <a href="<?= BASE_URL ?>">
                        <h1 class="logo-text">MINALIA</h1>
                        <p class="logo-tagline"><?= SITE_TAGLINE ?></p>
                    </a>
                </div>

                <!-- Search Bar -->
                <div class="header-search">
                    <form action="<?= BASE_URL ?>/search" method="GET" class="search-form">
                        <input type="text" name="q" placeholder="Ürün, marka ara..." class="search-input" autocomplete="off">
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    <div class="search-suggestions" style="display: none;"></div>
                </div>

                <!-- Header Actions -->
                <div class="header-actions">
                    <!-- Wishlist -->
                    <a href="<?= BASE_URL ?>/wishlist" class="header-action-item">
                        <i class="fas fa-heart"></i>
                        <span class="action-label">Favoriler</span>
                        <span class="badge" id="wishlist-count">0</span>
                    </a>

                    <!-- Cart -->
                    <div class="header-action-item cart-trigger">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="action-label">Sepet</span>
                        <span class="badge" id="cart-count">0</span>
                    </div>

                    <!-- Mini Cart Dropdown -->
                    <div class="mini-cart" style="display: none;">
                        <div class="mini-cart-header">
                            <h3>Sepetim</h3>
                            <button class="mini-cart-close">&times;</button>
                        </div>
                        <div class="mini-cart-items">
                            <!-- Cart items will be loaded here via JavaScript -->
                        </div>
                        <div class="mini-cart-footer">
                            <div class="mini-cart-total">
                                <span>Toplam:</span>
                                <strong id="cart-total"><?= formatPrice(0) ?></strong>
                            </div>
                            <a href="<?= BASE_URL ?>/cart" class="btn btn-outline">Sepete Git</a>
                            <a href="<?= BASE_URL ?>/checkout" class="btn btn-primary">Alışverişi Tamamla</a>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="header-nav">
        <div class="container">
            <ul class="nav-menu">
                <li><a href="<?= BASE_URL ?>">Ana Sayfa</a></li>
                <li class="has-submenu">
                    <a href="<?= BASE_URL ?>/products">Ürünler</a>
                    <ul class="submenu">
                        <li><a href="<?= BASE_URL ?>/category/erkek-parfum">Erkek Parfüm</a></li>
                        <li><a href="<?= BASE_URL ?>/category/kadin-parfum">Kadın Parfüm</a></li>
                        <li><a href="<?= BASE_URL ?>/category/unisex-parfum">Unisex Parfüm</a></li>
                        <li><a href="<?= BASE_URL ?>/category/nis-parfum">Niş Parfüm</a></li>
                    </ul>
                </li>
                <li><a href="<?= BASE_URL ?>/products?featured=1">Öne Çıkanlar</a></li>
                <li><a href="<?= BASE_URL ?>/products?new=1">Yeni Ürünler</a></li>
                <li><a href="<?= BASE_URL ?>/products?bestseller=1">Çok Satanlar</a></li>
                <li><a href="<?= BASE_URL ?>/about">Hakkımızda</a></li>
                <li><a href="<?= BASE_URL ?>/contact">İletişim</a></li>
            </ul>
        </div>
    </nav>
</header>
