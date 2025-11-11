<!-- Hero Slider -->
<section class="hero-slider">
    <div class="slider-container">
        <div class="slide active" style="background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('<?= BASE_URL ?>/images/hero-1.jpg');">
            <div class="container">
                <div class="slide-content">
                    <h2 class="slide-title">Yeni Sezon Parfümleri</h2>
                    <p class="slide-desc">En seçkin markaların parfümleri şimdi MINALIA'da</p>
                    <a href="<?= BASE_URL ?>/products?new=1" class="btn btn-primary btn-lg">Keşfet</a>
                </div>
            </div>
        </div>
        <div class="slide" style="background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('<?= BASE_URL ?>/images/hero-2.jpg');">
            <div class="container">
                <div class="slide-content">
                    <h2 class="slide-title">Özel Kampanyalar</h2>
                    <p class="slide-desc">Seçili ürünlerde %40'a varan indirim fırsatı</p>
                    <a href="<?= BASE_URL ?>/products?featured=1" class="btn btn-primary btn-lg">Alışverişe Başla</a>
                </div>
            </div>
        </div>
        <div class="slide" style="background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('<?= BASE_URL ?>/images/hero-3.jpg');">
            <div class="container">
                <div class="slide-content">
                    <h2 class="slide-title">Niş Parfüm Koleksiyonu</h2>
                    <p class="slide-desc">Kendinizi özel hissettiren parfümler</p>
                    <a href="<?= BASE_URL ?>/category/nis-parfum" class="btn btn-primary btn-lg">İncele</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Slider Controls -->
    <button class="slider-arrow slider-prev"><i class="fas fa-chevron-left"></i></button>
    <button class="slider-arrow slider-next"><i class="fas fa-chevron-right"></i></button>

    <!-- Slider Indicators -->
    <div class="slider-indicators">
        <span class="indicator active" data-slide="0"></span>
        <span class="indicator" data-slide="1"></span>
        <span class="indicator" data-slide="2"></span>
    </div>
</section>

<!-- Category Cards -->
<section class="category-section">
    <div class="container">
        <div class="category-grid">
            <div class="category-card">
                <a href="<?= BASE_URL ?>/category/erkek-parfum">
                    <div class="category-image">
                        <img src="<?= BASE_URL ?>/images/category-men.jpg" alt="Erkek Parfüm" loading="lazy">
                        <div class="category-overlay">
                            <h3>Erkek Parfüm</h3>
                            <span class="category-link">Keşfet <i class="fas fa-arrow-right"></i></span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="category-card">
                <a href="<?= BASE_URL ?>/category/kadin-parfum">
                    <div class="category-image">
                        <img src="<?= BASE_URL ?>/images/category-women.jpg" alt="Kadın Parfüm" loading="lazy">
                        <div class="category-overlay">
                            <h3>Kadın Parfüm</h3>
                            <span class="category-link">Keşfet <i class="fas fa-arrow-right"></i></span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="category-card">
                <a href="<?= BASE_URL ?>/category/nis-parfum">
                    <div class="category-image">
                        <img src="<?= BASE_URL ?>/images/category-niche.jpg" alt="Niş Parfüm" loading="lazy">
                        <div class="category-overlay">
                            <h3>Niş Parfüm</h3>
                            <span class="category-link">Keşfet <i class="fas fa-arrow-right"></i></span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<?php if (!empty($featured_products)): ?>
<section class="products-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Öne Çıkan Ürünler</h2>
            <a href="<?= BASE_URL ?>/products?featured=1" class="section-link">Tümünü Gör <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="products-grid">
            <?php foreach ($featured_products as $product): ?>
                <?php include __DIR__ . '/../../components/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Best Sellers Banner -->
<?php if (!empty($bestsellers)): ?>
<section class="bestsellers-banner">
    <div class="container">
        <div class="banner-content">
            <div class="banner-text">
                <h2>Çok Satanlar</h2>
                <p>En çok tercih edilen parfümler burada!</p>
            </div>
            <div class="banner-products">
                <?php foreach (array_slice($bestsellers, 0, 4) as $product): ?>
                <div class="banner-product-item">
                    <img src="<?= BASE_URL ?>/images/products/<?= $product['slug'] ?? 'default' ?>.jpg"
                         alt="<?= htmlspecialchars($product['name']) ?>"
                         onerror="this.src='<?= BASE_URL ?>/images/placeholder.jpg'">
                    <div class="banner-product-info">
                        <h4><?= htmlspecialchars($product['name']) ?></h4>
                        <p class="price"><?= formatPrice($product['price']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- New Products -->
<?php if (!empty($new_products)): ?>
<section class="products-section bg-cream">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Yeni Ürünler</h2>
            <a href="<?= BASE_URL ?>/products?new=1" class="section-link">Tümünü Gör <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="products-grid">
            <?php foreach ($new_products as $product): ?>
                <?php include __DIR__ . '/../../components/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <h3>Hızlı Kargo</h3>
                <p>Siparişleriniz aynı gün kargoda</p>
            </div>

            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <h3>Güvenli Ödeme</h3>
                <p>256-bit SSL sertifikası ile güvenli alışveriş</p>
            </div>

            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-undo"></i>
                </div>
                <h3>Kolay İade</h3>
                <p>14 gün içinde ücretsiz iade hakkı</p>
            </div>

            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>7/24 Destek</h3>
                <p>Müşteri hizmetlerimiz her zaman yanınızda</p>
            </div>
        </div>
    </div>
</section>
