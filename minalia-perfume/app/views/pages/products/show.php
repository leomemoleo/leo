<div class="product-detail-container">
    <div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1.5rem;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-bottom: 3rem;">
            <!-- Product Image -->
            <div>
                <div class="product-image-main" style="background: #f8f5f0; border-radius: 16px; overflow: hidden; margin-bottom: 1rem;">
                    <img src="<?= BASE_URL . ($product['main_image'] ?: '/images/no-image.jpg') ?>"
                         alt="<?= htmlspecialchars($product['name']) ?>"
                         style="width: 100%; height: 500px; object-fit: cover;">
                </div>

                <?php if (!empty($images)): ?>
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem;">
                        <?php foreach ($images as $img): ?>
                            <img src="<?= BASE_URL . $img['image_url'] ?>"
                                 style="width: 100%; height: 100px; object-fit: cover; border-radius: 8px; cursor: pointer;"
                                 onclick="document.querySelector('.product-image-main img').src = this.src">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Product Info -->
            <div>
                <div style="margin-bottom: 1rem;">
                    <span style="color: var(--gold); font-weight: 600; text-transform: uppercase; font-size: 0.9rem;">
                        <?= htmlspecialchars($product['brand_name'] ?? '') ?>
                    </span>
                </div>

                <h1 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; margin-bottom: 1rem; color: var(--text-dark);">
                    <?= htmlspecialchars($product['name']) ?>
                </h1>

                <!-- Rating -->
                <?php if ($product['review_count'] > 0): ?>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem;">
                        <div style="color: var(--gold);">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <i class="fas fa-star<?= $i < floor($product['rating']) ? '' : '-o' ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <span style="color: #666;"><?= number_format($product['rating'], 1) ?> (<?= $product['review_count'] ?> yorum)</span>
                    </div>
                <?php endif; ?>

                <!-- Price -->
                <div style="margin-bottom: 2rem;">
                    <?php if ($product['discount_price']): ?>
                        <div style="text-decoration: line-through; color: #999; font-size: 1.25rem; margin-bottom: 0.5rem;">
                            <?= formatPrice($product['price']) ?>
                        </div>
                        <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary);">
                            <?= formatPrice($product['discount_price']) ?>
                        </div>
                        <div style="display: inline-block; background: #f44336; color: white; padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.85rem; font-weight: 600; margin-top: 0.5rem;">
                            %<?= round((($product['price'] - $product['discount_price']) / $product['price']) * 100) ?> İndirim
                        </div>
                    <?php else: ?>
                        <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary);">
                            <?= formatPrice($product['price']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Stock Status -->
                <div style="margin-bottom: 2rem;">
                    <?php if ($product['stock_quantity'] > 0): ?>
                        <span style="color: #4CAF50; font-weight: 600;">
                            <i class="fas fa-check-circle"></i> Stokta Var (<?= $product['stock_quantity'] ?> adet)
                        </span>
                    <?php else: ?>
                        <span style="color: #f44336; font-weight: 600;">
                            <i class="fas fa-times-circle"></i> Stokta Yok
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Add to Cart -->
                <div style="display: flex; gap: 1rem; margin-bottom: 2rem;">
                    <div style="display: flex; align-items: center; border: 2px solid #e0e0e0; border-radius: 8px;">
                        <button onclick="decrementQty()" style="padding: 0.75rem 1rem; background: none; border: none; cursor: pointer; font-size: 1.25rem;">-</button>
                        <input type="number" id="quantity" value="1" min="1" max="<?= $product['stock_quantity'] ?>"
                               style="width: 60px; text-align: center; border: none; font-size: 1.1rem; font-weight: 600;">
                        <button onclick="incrementQty()" style="padding: 0.75rem 1rem; background: none; border: none; cursor: pointer; font-size: 1.25rem;">+</button>
                    </div>

                    <button onclick="addToCart(<?= $product['id'] ?>)"
                            class="btn btn-primary"
                            style="flex: 1; padding: 1rem 2rem; font-size: 1.1rem;"
                            <?= $product['stock_quantity'] <= 0 ? 'disabled' : '' ?>>
                        <i class="fas fa-shopping-cart"></i> Sepete Ekle
                    </button>

                    <button onclick="toggleWishlist(<?= $product['id'] ?>)"
                            class="btn btn-outline"
                            style="padding: 1rem 1.25rem;">
                        <i class="far fa-heart"></i>
                    </button>
                </div>

                <!-- Description -->
                <?php if ($product['description']): ?>
                    <div style="border-top: 1px solid #e0e0e0; padding-top: 2rem; margin-bottom: 2rem;">
                        <h3 style="font-size: 1.25rem; margin-bottom: 1rem;">Ürün Açıklaması</h3>
                        <p style="color: #666; line-height: 1.8;">
                            <?= nl2br(htmlspecialchars($product['description'])) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Fragrance Notes -->
                <?php if ($product['fragrance_notes']): ?>
                    <?php $notes = json_decode($product['fragrance_notes'], true); ?>
                    <div style="border-top: 1px solid #e0e0e0; padding-top: 2rem;">
                        <h3 style="font-size: 1.25rem; margin-bottom: 1rem;">Parfüm Notaları</h3>

                        <?php if (!empty($notes['ust'])): ?>
                            <div style="margin-bottom: 1rem;">
                                <strong style="color: var(--primary);">Üst Notalar:</strong>
                                <span style="color: #666;"><?= implode(', ', $notes['ust']) ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($notes['orta'])): ?>
                            <div style="margin-bottom: 1rem;">
                                <strong style="color: var(--primary);">Orta Notalar:</strong>
                                <span style="color: #666;"><?= implode(', ', $notes['orta']) ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($notes['alt'])): ?>
                            <div>
                                <strong style="color: var(--primary);">Alt Notalar:</strong>
                                <span style="color: #666;"><?= implode(', ', $notes['alt']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Product Meta -->
                <div style="background: #f8f5f0; padding: 1.5rem; border-radius: 8px; margin-top: 2rem;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.9rem;">
                        <div>
                            <strong>Kategori:</strong>
                            <span style="color: #666;"><?= htmlspecialchars($product['category_name'] ?? '-') ?></span>
                        </div>
                        <div>
                            <strong>Cinsiyet:</strong>
                            <span style="color: #666;"><?= htmlspecialchars($product['gender'] ?? '-') ?></span>
                        </div>
                        <?php if ($product['launch_year']): ?>
                            <div>
                                <strong>Çıkış Yılı:</strong>
                                <span style="color: #666;"><?= $product['launch_year'] ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($product['perfumer']): ?>
                            <div>
                                <strong>Parfümör:</strong>
                                <span style="color: #666;"><?= htmlspecialchars($product['perfumer']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($related_products)): ?>
            <div style="margin-top: 4rem;">
                <h2 style="font-family: 'Playfair Display', serif; font-size: 2rem; text-align: center; margin-bottom: 2rem;">
                    Benzer Ürünler
                </h2>

                <div class="products-grid">
                    <?php foreach (array_slice($related_products, 0, 4) as $related): ?>
                        <?php include __DIR__ . '/../../components/product-card.php'; $product = $related; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function incrementQty() {
    const input = document.getElementById('quantity');
    const max = parseInt(input.max);
    if (parseInt(input.value) < max) {
        input.value = parseInt(input.value) + 1;
    }
}

function decrementQty() {
    const input = document.getElementById('quantity');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

function addToCart(productId) {
    const quantity = parseInt(document.getElementById('quantity').value);
    // This function should be in main.js
    if (typeof addProductToCart === 'function') {
        addProductToCart(productId, quantity);
    }
}

function toggleWishlist(productId) {
    // This function should be in main.js
    if (typeof toggleProductWishlist === 'function') {
        toggleProductWishlist(productId);
    }
}
</script>

<style>
@media (max-width: 768px) {
    .product-detail-container .container > div {
        grid-template-columns: 1fr !important;
    }
}
</style>
