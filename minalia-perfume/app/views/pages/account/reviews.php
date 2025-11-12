<div class="account-container">
    <div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1.5rem;">
        <h1 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; margin-bottom: 2rem;">
            Ürün Yorumlarım
        </h1>

        <div style="display: grid; grid-template-columns: 250px 1fr; gap: 2rem;">
            <div><?php include __DIR__ . '/../../components/account-sidebar.php'; ?></div>

            <div>
                <?php if (empty($reviews)): ?>
                    <div class="card" style="text-align: center; padding: 4rem 2rem;">
                        <i class="fas fa-comment-slash" style="font-size: 4rem; color: #ddd; margin-bottom: 1rem;"></i>
                        <h3 style="margin: 0 0 1rem;">Henüz Yorum Yapmadınız</h3>
                        <p style="color: #666; margin-bottom: 2rem;">Satın aldığınız ürünler hakkında yorum yaparak diğer müşterilere yardımcı olun.</p>
                        <a href="/account/orders" class="btn btn-primary">
                            <i class="fas fa-shopping-bag"></i> Siparişlerime Git
                        </a>
                    </div>
                <?php else: ?>
                    <div class="reviews-list">
                        <?php foreach ($reviews as $review): ?>
                            <div class="card" style="margin-bottom: 1.5rem;">
                                <div class="card-body" style="padding: 1.5rem;">
                                    <div style="display: flex; gap: 1.5rem;">
                                        <a href="/products/<?= htmlspecialchars($review['product_slug']) ?>" style="flex-shrink: 0;">
                                            <img src="<?= htmlspecialchars($review['product_image']) ?>" 
                                                 alt="<?= htmlspecialchars($review['product_name']) ?>"
                                                 style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                                        </a>
                                        <div style="flex: 1;">
                                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                                                <div>
                                                    <div style="color: #888; font-size: 0.875rem; margin-bottom: 0.25rem;">
                                                        <?= htmlspecialchars($review['brand_name']) ?>
                                                    </div>
                                                    <h4 style="margin: 0 0 0.5rem;">
                                                        <a href="/products/<?= htmlspecialchars($review['product_slug']) ?>" style="color: #333; text-decoration: none;">
                                                            <?= htmlspecialchars($review['product_name']) ?>
                                                        </a>
                                                    </h4>
                                                    <div style="color: #FFC107; margin-bottom: 0.5rem;">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <i class="<?= $i <= $review['rating'] ? 'fas' : 'far' ?> fa-star"></i>
                                                        <?php endfor; ?>
                                                        <span style="color: #666; margin-left: 0.5rem;"><?= $review['rating'] ?>/5</span>
                                                    </div>
                                                    <div style="color: #999; font-size: 0.875rem;">
                                                        <i class="far fa-calendar"></i> <?= date('d.m.Y', strtotime($review['created_at'])) ?>
                                                    </div>
                                                </div>
                                                <form method="POST" action="/account/reviews/delete/<?= $review['id'] ?>" 
                                                      onsubmit="return confirm('Yorumunuzu silmek istediğinize emin misiniz?');">
                                                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash-alt"></i> Sil
                                                    </button>
                                                </form>
                                            </div>
                                            <?php if ($review['title']): ?>
                                                <h5 style="margin: 0 0 0.5rem; font-size: 1rem; font-weight: 600;">
                                                    <?= htmlspecialchars($review['title']) ?>
                                                </h5>
                                            <?php endif; ?>
                                            <p style="margin: 0; color: #666; line-height: 1.6;">
                                                <?= nl2br(htmlspecialchars($review['comment'])) ?>
                                            </p>
                                            <?php if ($review['is_verified_purchase']): ?>
                                                <div style="margin-top: 1rem; display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.375rem 0.75rem; background: #E8F5E9; border-radius: 20px; font-size: 0.875rem; color: #2E7D32;">
                                                    <i class="fas fa-check-circle"></i> Onaylanmış Alışveriş
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
