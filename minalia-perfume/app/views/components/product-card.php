<div class="product-card">
    <?php if (!empty($product['is_new'])): ?>
        <span class="product-badge badge-new">Yeni</span>
    <?php endif; ?>

    <?php if (!empty($product['sale_price'])): ?>
        <span class="product-badge badge-sale">İndirim</span>
    <?php endif; ?>

    <div class="product-image">
        <a href="<?= BASE_URL ?>/products/<?= $product['slug'] ?>">
            <img src="<?= BASE_URL ?>/images/products/<?= $product['slug'] ?? 'default' ?>.jpg"
                 alt="<?= htmlspecialchars($product['name']) ?>"
                 loading="lazy"
                 onerror="this.src='<?= BASE_URL ?>/images/placeholder.jpg'">
        </a>

        <!-- Quick Actions -->
        <div class="product-actions">
            <button class="action-btn wishlist-btn" data-product-id="<?= $product['id'] ?>" title="Favorilere Ekle">
                <i class="far fa-heart"></i>
            </button>
            <button class="action-btn quick-view-btn" data-product-id="<?= $product['id'] ?>" title="Hızlı Görünüm">
                <i class="far fa-eye"></i>
            </button>
        </div>
    </div>

    <div class="product-info">
        <?php if (!empty($product['brand_name'])): ?>
            <p class="product-brand"><?= htmlspecialchars($product['brand_name']) ?></p>
        <?php endif; ?>

        <h3 class="product-name">
            <a href="<?= BASE_URL ?>/products/<?= $product['slug'] ?>">
                <?= htmlspecialchars($product['name']) ?>
            </a>
        </h3>

        <?php if (!empty($product['rating']) && $product['rating'] > 0): ?>
            <div class="product-rating">
                <?php
                $rating = floatval($product['rating']);
                $fullStars = floor($rating);
                $halfStar = ($rating - $fullStars) >= 0.5;
                $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                ?>

                <?php for ($i = 0; $i < $fullStars; $i++): ?>
                    <i class="fas fa-star"></i>
                <?php endfor; ?>

                <?php if ($halfStar): ?>
                    <i class="fas fa-star-half-alt"></i>
                <?php endif; ?>

                <?php for ($i = 0; $i < $emptyStars; $i++): ?>
                    <i class="far fa-star"></i>
                <?php endfor; ?>

                <span class="rating-count">(<?= $product['review_count'] ?? 0 ?>)</span>
            </div>
        <?php endif; ?>

        <div class="product-price">
            <?php if (!empty($product['sale_price'])): ?>
                <span class="price-old"><?= formatPrice($product['price']) ?></span>
                <span class="price-current"><?= formatPrice($product['sale_price']) ?></span>
            <?php else: ?>
                <span class="price-current"><?= formatPrice($product['price']) ?></span>
            <?php endif; ?>
        </div>

        <button class="btn btn-primary btn-block add-to-cart-btn" data-product-id="<?= $product['id'] ?>">
            <i class="fas fa-shopping-bag"></i> Sepete Ekle
        </button>
    </div>
</div>
