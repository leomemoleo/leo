<!-- Products Listing Page with Faceted Search -->
<div class="products-page">
    <!-- Breadcrumb -->
    <div class="container">
        <nav class="breadcrumb">
            <a href="<?= BASE_URL ?>">Ana Sayfa</a>
            <span>/</span>
            <span>Ürünler</span>
        </nav>
    </div>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1 class="page-title"><?= $title ?? 'Tüm Ürünler' ?></h1>
            <p class="page-subtitle">
                <?= $result_count ?? 0 ?> ürün bulundu
                <?php if (!empty($filters)): ?>
                    <span class="active-filters-count">(<?= count($filters) ?> filtre aktif)</span>
                <?php endif; ?>
            </p>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container">
        <div class="products-container">
            <!-- Filters Sidebar -->
            <aside class="filter-sidebar">
                <div class="filter-header">
                    <h2>Filtreler</h2>
                    <?php if (!empty($filters)): ?>
                        <button class="btn-clear-filters" onclick="window.location.href='<?= BASE_URL ?>/products'">
                            Temizle
                        </button>
                    <?php endif; ?>
                </div>

                <form method="GET" action="<?= BASE_URL ?>/products" class="filters-form">
                    <!-- Category Filter -->
                    <?php if (!empty($filter_options['categories'])): ?>
                    <div class="filter-group">
                        <h3 class="filter-title">Kategori</h3>
                        <div class="filter-options">
                            <?php foreach ($filter_options['categories'] as $category): ?>
                                <label class="filter-checkbox">
                                    <input type="radio" name="category" value="<?= $category['id'] ?>"
                                        <?= ($filters['category_id'] ?? '') == $category['id'] ? 'checked' : '' ?>>
                                    <span><?= htmlspecialchars($category['name']) ?></span>
                                    <span class="filter-count">(<?= $category['product_count'] ?>)</span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Brand Filter -->
                    <?php if (!empty($filter_options['brands'])): ?>
                    <div class="filter-group">
                        <h3 class="filter-title">Marka</h3>
                        <div class="filter-options scrollable">
                            <?php foreach ($filter_options['brands'] as $brand): ?>
                                <label class="filter-checkbox">
                                    <input type="radio" name="brand" value="<?= $brand['id'] ?>"
                                        <?= ($filters['brand_id'] ?? '') == $brand['id'] ? 'checked' : '' ?>>
                                    <span><?= htmlspecialchars($brand['name']) ?></span>
                                    <span class="filter-count">(<?= $brand['product_count'] ?>)</span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Gender Filter -->
                    <div class="filter-group">
                        <h3 class="filter-title">Cinsiyet</h3>
                        <div class="filter-options">
                            <label class="filter-checkbox">
                                <input type="radio" name="gender" value="erkek" <?= ($filters['gender'] ?? '') == 'erkek' ? 'checked' : '' ?>>
                                <span>Erkek</span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="radio" name="gender" value="kadın" <?= ($filters['gender'] ?? '') == 'kadın' ? 'checked' : '' ?>>
                                <span>Kadın</span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="radio" name="gender" value="unisex" <?= ($filters['gender'] ?? '') == 'unisex' ? 'checked' : '' ?>>
                                <span>Unisex</span>
                            </label>
                        </div>
                    </div>

                    <!-- Price Filter -->
                    <div class="filter-group">
                        <h3 class="filter-title">Fiyat Aralığı</h3>
                        <div class="price-inputs">
                            <input type="number" name="min_price" placeholder="Min"
                                value="<?= $filters['min_price'] ?? '' ?>" min="0" step="100">
                            <span>-</span>
                            <input type="number" name="max_price" placeholder="Max"
                                value="<?= $filters['max_price'] ?? '' ?>" min="0" step="100">
                        </div>
                    </div>

                    <!-- Product Type Filters -->
                    <div class="filter-group">
                        <h3 class="filter-title">Özellikler</h3>
                        <div class="filter-options">
                            <label class="filter-checkbox">
                                <input type="checkbox" name="featured" value="1" <?= !empty($filters['is_featured']) ? 'checked' : '' ?>>
                                <span>Öne Çıkanlar</span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="bestseller" value="1" <?= !empty($filters['is_bestseller']) ? 'checked' : '' ?>>
                                <span>Çok Satanlar</span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="new" value="1" <?= !empty($filters['is_new']) ? 'checked' : '' ?>>
                                <span>Yeni Ürünler</span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="in_stock" value="1" <?= !empty($filters['in_stock']) ? 'checked' : '' ?>>
                                <span>Stokta Olanlar</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Filtrele</button>
                </form>
            </aside>

            <!-- Products Grid -->
            <div class="products-main">
                <!-- Sort & View Options -->
                <div class="products-toolbar">
                    <div class="products-info">
                        <p><?= $result_count ?? 0 ?> ürün gösteriliyor</p>
                    </div>
                    <div class="products-sort">
                        <label>Sırala:</label>
                        <select name="sort" onchange="window.location.href='<?= BASE_URL ?>/products?sort=' + this.value + '<?= !empty($filters) ? '&' . http_build_query($filters) : '' ?>'">
                            <option value="newest" <?= ($filters['order_by'] ?? '') == 'newest' ? 'selected' : '' ?>>En Yeni</option>
                            <option value="price_low" <?= ($filters['order_by'] ?? '') == 'price_low' ? 'selected' : '' ?>>Fiyat: Düşük-Yüksek</option>
                            <option value="price_high" <?= ($filters['order_by'] ?? '') == 'price_high' ? 'selected' : '' ?>>Fiyat: Yüksek-Düşük</option>
                            <option value="popular" <?= ($filters['order_by'] ?? '') == 'popular' ? 'selected' : '' ?>>En Popüler</option>
                            <option value="rating" <?= ($filters['order_by'] ?? '') == 'rating' ? 'selected' : '' ?>>En Yüksek Puan</option>
                        </select>
                    </div>
                </div>

                <!-- Products Grid -->
                <?php if (!empty($products)): ?>
                    <div class="products-grid">
                        <?php foreach ($products as $product): ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <a href="<?= BASE_URL ?>/products/<?= $product['slug'] ?>">
                                        <img src="<?= getImageOrPlaceholder($product['image'] ?? null, $product['name'], 400, 400) ?>"
                                             alt="<?= htmlspecialchars($product['name']) ?>"
                                             loading="lazy">
                                    </a>
                                    <?php if ($product['is_new']): ?>
                                        <span class="badge badge-new">Yeni</span>
                                    <?php endif; ?>
                                    <?php if ($product['is_featured']): ?>
                                        <span class="badge badge-featured">Öne Çıkan</span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <div class="product-brand"><?= htmlspecialchars($product['brand_name'] ?? 'MINALIA') ?></div>
                                    <h3 class="product-title">
                                        <a href="<?= BASE_URL ?>/products/<?= $product['slug'] ?>">
                                            <?= htmlspecialchars($product['name']) ?>
                                        </a>
                                    </h3>
                                    <div class="product-rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?= $i <= ($product['rating'] ?? 0) ? 'active' : '' ?>"></i>
                                        <?php endfor; ?>
                                        <span class="rating-count">(<?= $product['review_count'] ?? 0 ?>)</span>
                                    </div>
                                    <div class="product-price">
                                        <?= formatPrice($product['price']) ?>
                                    </div>
                                    <button class="btn btn-primary btn-add-to-cart" data-product-id="<?= $product['id'] ?>">
                                        <i class="fas fa-shopping-cart"></i> Sepete Ekle
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($pagination['total_pages'] > 1): ?>
                        <div class="pagination">
                            <?php if ($pagination['has_prev']): ?>
                                <a href="?page=<?= $pagination['current_page'] - 1 ?><?= !empty($filters) ? '&' . http_build_query($filters) : '' ?>" class="page-link">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                <a href="?page=<?= $i ?><?= !empty($filters) ? '&' . http_build_query($filters) : '' ?>"
                                   class="page-link <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($pagination['has_next']): ?>
                                <a href="?page=<?= $pagination['current_page'] + 1 ?><?= !empty($filters) ? '&' . http_build_query($filters) : '' ?>" class="page-link">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- No Products Found -->
                    <div class="no-products">
                        <i class="fas fa-search fa-3x"></i>
                        <h3>Ürün Bulunamadı</h3>
                        <p>Arama kriterlerinize uygun ürün bulunamadı. Lütfen filtrelerinizi değiştirerek tekrar deneyin.</p>
                        <a href="<?= BASE_URL ?>/products" class="btn btn-primary">Tüm Ürünleri Göster</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Load Faceted Search JS -->
<script src="<?= BASE_URL ?>/js/faceted-search.js"></script>
