<div class="account-container">
    <div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1.5rem;">
        <h1 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; margin-bottom: 2rem;">
            Favorilerim
        </h1>

        <div style="display: grid; grid-template-columns: 250px 1fr; gap: 2rem;">
            <!-- Sidebar Menu -->
            <div>
                <?php include __DIR__ . '/../../components/account-sidebar.php'; ?>
            </div>

            <!-- Main Content -->
            <div>
                <?php if (empty($items)): ?>
                    <!-- No Wishlist Items -->
                    <div class="card" style="text-align: center; padding: 4rem 2rem;">
                        <i class="fas fa-heart" style="font-size: 4rem; color: #ddd; margin-bottom: 1rem;"></i>
                        <h3 style="margin: 0 0 1rem;">Favori Listeniz Boş</h3>
                        <p style="color: #666; margin-bottom: 2rem;">Beğendiğiniz ürünleri favorilere ekleyerek daha sonra kolayca ulaşabilirsiniz.</p>
                        <a href="/products" class="btn btn-primary">
                            <i class="fas fa-shopping-bag"></i> Ürünleri Keşfet
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Wishlist Items -->
                    <div style="margin-bottom: 1.5rem; padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-heart" style="color: var(--primary);"></i>
                            <span style="font-weight: 500;"><?= count($items) ?> ürün favorilerinizde</span>
                        </div>
                    </div>

                    <div class="wishlist-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
                        <?php foreach ($items as $item): ?>
                            <?php
                            $finalPrice = $item['discount_price'] > 0 ? $item['discount_price'] : $item['price'];
                            $hasDiscount = $item['discount_price'] > 0 && $item['discount_price'] < $item['price'];
                            $inStock = $item['stock_quantity'] > 0;
                            ?>
                            <div class="wishlist-item-card" style="position: relative; border: 1px solid #e0e0e0; border-radius: 12px; overflow: hidden; background: #fff; transition: all 0.3s ease;">
                                <!-- Remove Button -->
                                <button class="remove-wishlist-btn"
                                        data-product-id="<?= $item['product_id'] ?>"
                                        style="position: absolute; top: 12px; right: 12px; z-index: 10; background: rgba(255,255,255,0.9); border: none; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;">
                                    <i class="fas fa-times" style="color: #666; font-size: 1.125rem;"></i>
                                </button>

                                <!-- Product Image -->
                                <a href="/products/<?= htmlspecialchars($item['slug']) ?>" style="display: block; position: relative;">
                                    <div style="aspect-ratio: 1; overflow: hidden; background: #f8f8f8;">
                                        <img src="<?= htmlspecialchars($item['main_image']) ?>"
                                             alt="<?= htmlspecialchars($item['name']) ?>"
                                             style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                                    </div>

                                    <!-- Stock Badge -->
                                    <?php if (!$inStock): ?>
                                        <div style="position: absolute; top: 12px; left: 12px; background: #f44336; color: white; padding: 0.375rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">
                                            Stokta Yok
                                        </div>
                                    <?php elseif ($hasDiscount): ?>
                                        <div style="position: absolute; top: 12px; left: 12px; background: var(--primary); color: white; padding: 0.375rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">
                                            <?= round((1 - $finalPrice / $item['price']) * 100) ?>% İndirim
                                        </div>
                                    <?php endif; ?>
                                </a>

                                <!-- Product Info -->
                                <div style="padding: 1.25rem;">
                                    <!-- Brand -->
                                    <div style="color: #888; font-size: 0.875rem; margin-bottom: 0.5rem;">
                                        <?= htmlspecialchars($item['brand_name']) ?>
                                    </div>

                                    <!-- Product Name -->
                                    <h3 style="margin: 0 0 1rem; font-size: 1rem; font-weight: 500; line-height: 1.4;">
                                        <a href="/products/<?= htmlspecialchars($item['slug']) ?>" style="color: #333; text-decoration: none;">
                                            <?= htmlspecialchars($item['name']) ?>
                                        </a>
                                    </h3>

                                    <!-- Price -->
                                    <div style="margin-bottom: 1rem;">
                                        <?php if ($hasDiscount): ?>
                                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                <span style="font-size: 1.25rem; font-weight: 700; color: var(--primary);">
                                                    <?= formatPrice($finalPrice) ?>
                                                </span>
                                                <span style="font-size: 0.9375rem; color: #999; text-decoration: line-through;">
                                                    <?= formatPrice($item['price']) ?>
                                                </span>
                                            </div>
                                        <?php else: ?>
                                            <span style="font-size: 1.25rem; font-weight: 700; color: #333;">
                                                <?= formatPrice($finalPrice) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Actions -->
                                    <div style="display: flex; gap: 0.75rem;">
                                        <?php if ($inStock): ?>
                                            <button class="btn btn-primary add-to-cart-btn"
                                                    data-product-id="<?= $item['product_id'] ?>"
                                                    style="flex: 1; padding: 0.75rem; font-size: 0.875rem;">
                                                <i class="fas fa-shopping-cart"></i> Sepete Ekle
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-outline-secondary stock-alert-btn"
                                                    data-product-id="<?= $item['product_id'] ?>"
                                                    style="flex: 1; padding: 0.75rem; font-size: 0.875rem;">
                                                <i class="fas fa-bell"></i> Stokta Olunca Haber Ver
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Bulk Actions -->
                    <div class="card" style="margin-top: 2rem; padding: 1.5rem; border: 2px dashed #ddd;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h3 style="margin: 0 0 0.5rem; font-size: 1.125rem;">Hepsini Sepete Ekle</h3>
                                <p style="margin: 0; color: #666; font-size: 0.9375rem;">Stokta olan tüm ürünleri tek seferde sepetinize ekleyin</p>
                            </div>
                            <button class="btn btn-success" id="addAllToCartBtn" style="padding: 0.875rem 2rem;">
                                <i class="fas fa-shopping-cart"></i> Tümünü Sepete Ekle
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.wishlist-item-card:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    transform: translateY(-4px);
}

.wishlist-item-card:hover img {
    transform: scale(1.05);
}

.remove-wishlist-btn:hover {
    background: rgba(244, 67, 54, 0.1) !important;
}

.remove-wishlist-btn:hover i {
    color: #f44336 !important;
}

@media (max-width: 768px) {
    .account-container .container > div {
        grid-template-columns: 1fr !important;
    }

    .wishlist-grid {
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)) !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Remove from wishlist
    document.querySelectorAll('.remove-wishlist-btn').forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;

            if (!confirm('Bu ürünü favorilerden çıkarmak istediğinize emin misiniz?')) {
                return;
            }

            try {
                const response = await fetch('/wishlist/remove', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'product_id=' + productId
                });

                const data = await response.json();

                if (data.success) {
                    // Remove card with animation
                    const card = this.closest('.wishlist-item-card');
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.8)';

                    setTimeout(() => {
                        card.remove();

                        // Check if wishlist is empty
                        const remaining = document.querySelectorAll('.wishlist-item-card').length;
                        if (remaining === 0) {
                            location.reload();
                        }
                    }, 300);

                    showNotification('Ürün favorilerden çıkarıldı', 'success');
                } else {
                    showNotification(data.message || 'Bir hata oluştu', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Bir hata oluştu', 'error');
            }
        });
    });

    // Add to cart
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            const originalText = this.innerHTML;

            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Ekleniyor...';

            try {
                const response = await fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'product_id=' + productId + '&quantity=1'
                });

                const data = await response.json();

                if (data.success) {
                    this.innerHTML = '<i class="fas fa-check"></i> Sepete Eklendi';
                    this.classList.remove('btn-primary');
                    this.classList.add('btn-success');

                    showNotification('Ürün sepete eklendi', 'success');

                    // Update cart count if exists
                    updateCartCount();
                } else {
                    this.innerHTML = originalText;
                    this.disabled = false;
                    showNotification(data.message || 'Bir hata oluştu', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.innerHTML = originalText;
                this.disabled = false;
                showNotification('Bir hata oluştu', 'error');
            }
        });
    });

    // Stock alert
    document.querySelectorAll('.stock-alert-btn').forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            const originalText = this.innerHTML;

            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Kaydediliyor...';

            try {
                const response = await fetch('/api/stock-alert', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'product_id=' + productId
                });

                const data = await response.json();

                if (data.success) {
                    this.innerHTML = '<i class="fas fa-check"></i> Bildirim Aktif';
                    this.classList.remove('btn-outline-secondary');
                    this.classList.add('btn-success');
                    showNotification('Ürün stoğa girdiğinde bildirim alacaksınız', 'success');
                } else {
                    this.innerHTML = originalText;
                    this.disabled = false;
                    showNotification(data.message || 'Bir hata oluştu', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.innerHTML = originalText;
                this.disabled = false;
                showNotification('Bir hata oluştu', 'error');
            }
        });
    });

    // Add all to cart
    const addAllBtn = document.getElementById('addAllToCartBtn');
    if (addAllBtn) {
        addAllBtn.addEventListener('click', async function() {
            const inStockBtns = document.querySelectorAll('.add-to-cart-btn:not(:disabled)');

            if (inStockBtns.length === 0) {
                showNotification('Sepete eklenecek ürün bulunamadı', 'warning');
                return;
            }

            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Ekleniyor...';

            let added = 0;
            for (const btn of inStockBtns) {
                try {
                    const productId = btn.dataset.productId;
                    const response = await fetch('/cart/add', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'product_id=' + productId + '&quantity=1'
                    });

                    const data = await response.json();
                    if (data.success) {
                        added++;
                        btn.innerHTML = '<i class="fas fa-check"></i> Sepette';
                        btn.classList.remove('btn-primary');
                        btn.classList.add('btn-success');
                        btn.disabled = true;
                    }
                } catch (error) {
                    console.error('Error adding product:', error);
                }
            }

            this.innerHTML = '<i class="fas fa-check"></i> ' + added + ' Ürün Sepete Eklendi';
            showNotification(added + ' ürün sepete eklendi', 'success');
            updateCartCount();
        });
    }
});

function showNotification(message, type = 'info') {
    // Simple notification implementation
    const toast = document.createElement('div');
    toast.className = 'toast-notification toast-' + type;
    toast.textContent = message;
    toast.style.cssText = 'position: fixed; top: 20px; right: 20px; padding: 1rem 1.5rem; background: ' +
                         (type === 'success' ? '#4CAF50' : type === 'error' ? '#f44336' : '#2196F3') +
                         '; color: white; border-radius: 8px; z-index: 9999; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

function updateCartCount() {
    // Update cart count in header if exists
    fetch('/api/cart/count', { method: 'POST' })
        .then(res => res.json())
        .then(data => {
            const cartBadge = document.querySelector('.cart-count');
            if (cartBadge && data.count !== undefined) {
                cartBadge.textContent = data.count;
            }
        })
        .catch(err => console.error('Error updating cart count:', err));
}
</script>
