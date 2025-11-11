<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Panel' ?> | MINALIA Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= ADMIN_URL ?>/assets/css/admin.css">
</head>
<body>
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <h2>MINALIA</h2>
            <p>Admin Panel</p>
        </div>

        <nav class="sidebar-nav">
            <a href="<?= ADMIN_URL ?>" class="nav-item <?= $active_menu === 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>

            <div class="nav-group">
                <div class="nav-group-title">E-Ticaret</div>

                <a href="<?= ADMIN_URL ?>/products" class="nav-item <?= $active_menu === 'products' ? 'active' : '' ?>">
                    <i class="fas fa-box"></i>
                    <span>Ürünler</span>
                </a>

                <a href="<?= ADMIN_URL ?>/categories" class="nav-item <?= $active_menu === 'categories' ? 'active' : '' ?>">
                    <i class="fas fa-tags"></i>
                    <span>Kategoriler</span>
                </a>

                <a href="<?= ADMIN_URL ?>/brands" class="nav-item <?= $active_menu === 'brands' ? 'active' : '' ?>">
                    <i class="fas fa-trademark"></i>
                    <span>Markalar</span>
                </a>

                <a href="<?= ADMIN_URL ?>/orders" class="nav-item <?= $active_menu === 'orders' ? 'active' : '' ?>">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Siparişler</span>
                    <span class="badge">5</span>
                </a>
            </div>

            <div class="nav-group">
                <div class="nav-group-title">Müşteriler</div>

                <a href="<?= ADMIN_URL ?>/customers" class="nav-item <?= $active_menu === 'customers' ? 'active' : '' ?>">
                    <i class="fas fa-users"></i>
                    <span>Müşteriler</span>
                </a>

                <a href="<?= ADMIN_URL ?>/reviews" class="nav-item <?= $active_menu === 'reviews' ? 'active' : '' ?>">
                    <i class="fas fa-star"></i>
                    <span>Yorumlar</span>
                </a>
            </div>

            <div class="nav-group">
                <div class="nav-group-title">Kampanyalar</div>

                <a href="<?= ADMIN_URL ?>/coupons" class="nav-item <?= $active_menu === 'coupons' ? 'active' : '' ?>">
                    <i class="fas fa-ticket-alt"></i>
                    <span>Kuponlar</span>
                </a>

                <a href="<?= ADMIN_URL ?>/loyalty" class="nav-item <?= $active_menu === 'loyalty' ? 'active' : '' ?>">
                    <i class="fas fa-gift"></i>
                    <span>Puan Sistemi</span>
                </a>

                <a href="<?= ADMIN_URL ?>/newsletter" class="nav-item <?= $active_menu === 'newsletter' ? 'active' : '' ?>">
                    <i class="fas fa-envelope"></i>
                    <span>Newsletter</span>
                </a>
            </div>

            <div class="nav-group">
                <div class="nav-group-title">İçerik</div>

                <a href="<?= ADMIN_URL ?>/pages" class="nav-item <?= $active_menu === 'pages' ? 'active' : '' ?>">
                    <i class="fas fa-file-alt"></i>
                    <span>Sayfalar</span>
                </a>

                <a href="<?= ADMIN_URL ?>/settings" class="nav-item <?= $active_menu === 'settings' ? 'active' : '' ?>">
                    <i class="fas fa-cog"></i>
                    <span>Ayarlar</span>
                </a>
            </div>

            <div class="nav-group">
                <div class="nav-group-title">Raporlar</div>

                <a href="<?= ADMIN_URL ?>/reports" class="nav-item <?= $active_menu === 'reports' ? 'active' : '' ?>">
                    <i class="fas fa-chart-line"></i>
                    <span>Satış Raporları</span>
                </a>

                <a href="<?= ADMIN_URL ?>/ai-reports" class="nav-item <?= $active_menu === 'ai-reports' ? 'active' : '' ?>">
                    <i class="fas fa-robot"></i>
                    <span>AI Raporları</span>
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="admin-main">
        <!-- Top Bar -->
        <header class="admin-topbar">
            <button class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </button>

            <div class="topbar-search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Ara...">
            </div>

            <div class="topbar-actions">
                <a href="<?= BASE_URL ?>" target="_blank" class="topbar-btn" title="Siteyi Görüntüle">
                    <i class="fas fa-external-link-alt"></i>
                </a>

                <button class="topbar-btn" id="notificationBtn">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </button>

                <div class="admin-user-menu">
                    <img src="<?= ADMIN_URL ?>/assets/images/admin-avatar.jpg" alt="Admin" onerror="this.src='https://ui-avatars.com/api/?name=Admin&background=7A8B5C&color=fff'">
                    <span><?= $_SESSION[SESSION_ADMIN_ROLE] ?? 'Admin' ?></span>
                    <i class="fas fa-chevron-down"></i>
                    <div class="user-dropdown">
                        <a href="<?= ADMIN_URL ?>/profile"><i class="fas fa-user"></i> Profil</a>
                        <a href="<?= ADMIN_URL ?>/logout"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="admin-content">
            <?php
            $flashMessage = getFlashMessage();
            if ($flashMessage):
            ?>
            <div class="alert alert-<?= $flashMessage['type'] ?>">
                <?= $flashMessage['text'] ?>
                <button class="alert-close">&times;</button>
            </div>
            <?php endif; ?>

            <?= $content ?? '' ?>
        </div>
    </div>

    <script src="<?= ADMIN_URL ?>/assets/js/admin.js"></script>
</body>
</html>
