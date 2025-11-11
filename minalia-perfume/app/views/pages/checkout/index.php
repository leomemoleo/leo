<div class="checkout-container">
    <div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1.5rem;">
        <h1 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; margin-bottom: 2rem; text-align: center;">
            Ödeme
        </h1>

        <form method="POST" action="<?= BASE_URL ?>/checkout/process" id="checkoutForm">
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
                <!-- Left Column: Checkout Form -->
                <div>
                    <!-- Delivery Address -->
                    <div class="card" style="margin-bottom: 2rem;">
                        <div class="card-header">
                            <h3 class="card-title">Teslimat Adresi</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($addresses)): ?>
                                <?php foreach ($addresses as $address): ?>
                                    <label style="display: block; padding: 1rem; border: 2px solid #e0e0e0; border-radius: 8px; margin-bottom: 1rem; cursor: pointer; transition: all 0.3s;">
                                        <input type="radio" name="address_id" value="<?= $address['id'] ?>"
                                               <?= $address['is_default'] ? 'checked' : '' ?>
                                               style="margin-right: 0.75rem;">
                                        <div style="display: inline-block;">
                                            <strong><?= htmlspecialchars($address['title'] ?? 'Adres') ?></strong>
                                            <p style="margin: 0.5rem 0 0; color: #666; font-size: 0.9rem;">
                                                <?= htmlspecialchars($address['address']) ?><br>
                                                <?= htmlspecialchars($address['city']) ?> / <?= htmlspecialchars($address['state']) ?><br>
                                                <?= htmlspecialchars($address['postal_code']) ?>
                                            </p>
                                        </div>
                                    </label>
                                <?php endforeach; ?>

                                <a href="<?= BASE_URL ?>/account/addresses/add" class="btn btn-outline">
                                    <i class="fas fa-plus"></i> Yeni Adres Ekle
                                </a>
                            <?php else: ?>
                                <p style="color: #999; text-align: center; padding: 2rem;">
                                    Kayıtlı adresiniz yok.
                                </p>
                                <a href="<?= BASE_URL ?>/account/addresses/add" class="btn btn-primary" style="width: 100%;">
                                    <i class="fas fa-plus"></i> Adres Ekle
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="card" style="margin-bottom: 2rem;">
                        <div class="card-header">
                            <h3 class="card-title">Ödeme Yöntemi</h3>
                        </div>
                        <div class="card-body">
                            <label style="display: block; padding: 1rem; border: 2px solid #e0e0e0; border-radius: 8px; margin-bottom: 1rem; cursor: pointer;">
                                <input type="radio" name="payment_method" value="credit_card" checked style="margin-right: 0.75rem;">
                                <i class="fas fa-credit-card" style="margin-right: 0.5rem; color: var(--primary);"></i>
                                <strong>Kredi Kartı</strong>
                                <div style="margin-top: 0.5rem; font-size: 0.85rem; color: #666;">
                                    Visa, Mastercard, American Express
                                </div>
                            </label>

                            <label style="display: block; padding: 1rem; border: 2px solid #e0e0e0; border-radius: 8px; margin-bottom: 1rem; cursor: pointer;">
                                <input type="radio" name="payment_method" value="bank_transfer" style="margin-right: 0.75rem;">
                                <i class="fas fa-university" style="margin-right: 0.5rem; color: var(--primary);"></i>
                                <strong>Banka Havalesi</strong>
                                <div style="margin-top: 0.5rem; font-size: 0.85rem; color: #666;">
                                    Havale/EFT ile ödeme
                                </div>
                            </label>

                            <label style="display: block; padding: 1rem; border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer;">
                                <input type="radio" name="payment_method" value="cash_on_delivery" style="margin-right: 0.75rem;">
                                <i class="fas fa-money-bill-wave" style="margin-right: 0.5rem; color: var(--primary);"></i>
                                <strong>Kapıda Ödeme</strong>
                                <div style="margin-top: 0.5rem; font-size: 0.85rem; color: #666;">
                                    Teslimat sırasında nakit veya kartla
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Order Notes -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Sipariş Notu (Opsiyonel)</h3>
                        </div>
                        <div class="card-body">
                            <textarea name="notes" class="form-control" rows="4"
                                      placeholder="Siparişiniz hakkında özel notlarınız..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Order Summary -->
                <div>
                    <div class="card" style="position: sticky; top: 20px;">
                        <div class="card-header">
                            <h3 class="card-title">Sipariş Özeti</h3>
                        </div>
                        <div class="card-body">
                            <!-- Order Items -->
                            <div style="margin-bottom: 1.5rem; max-height: 300px; overflow-y: auto;">
                                <?php foreach ($items as $item): ?>
                                    <div style="display: flex; gap: 1rem; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #f0f0f0;">
                                        <img src="<?= BASE_URL . $item['product']['main_image'] ?>"
                                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                        <div style="flex: 1;">
                                            <div style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.25rem;">
                                                <?= htmlspecialchars($item['product']['name']) ?>
                                            </div>
                                            <div style="font-size: 0.85rem; color: #999;">
                                                <?= $item['quantity'] ?> x <?= formatPrice($item['price']) ?>
                                            </div>
                                        </div>
                                        <div style="font-weight: 600; color: var(--primary);">
                                            <?= formatPrice($item['total']) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Coupon Code -->
                            <div style="margin-bottom: 1.5rem;">
                                <label class="form-label">Kupon Kodu</label>
                                <div style="display: flex; gap: 0.5rem;">
                                    <input type="text" name="coupon_code" class="form-control" placeholder="Kupon kodunuz">
                                    <button type="button" class="btn btn-outline" onclick="applyCoupon()">
                                        Uygula
                                    </button>
                                </div>
                            </div>

                            <!-- Price Breakdown -->
                            <div style="border-top: 1px solid #e0e0e0; padding-top: 1rem; margin-bottom: 1rem;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                                    <span>Ara Toplam:</span>
                                    <span><?= formatPrice($subtotal) ?></span>
                                </div>

                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                                    <span>Kargo:</span>
                                    <span><?= $shipping > 0 ? formatPrice($shipping) : '<span style="color: #4CAF50;">Ücretsiz</span>' ?></span>
                                </div>

                                <?php if ($subtotal < 500 && $shipping > 0): ?>
                                    <div style="background: #e3f2fd; padding: 0.75rem; border-radius: 4px; margin-bottom: 0.75rem; font-size: 0.85rem;">
                                        <i class="fas fa-info-circle" style="color: #2196F3;"></i>
                                        <?= formatPrice(500 - $subtotal) ?> daha ekleyerek <strong>ücretsiz kargo</strong> kazanın!
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Total -->
                            <div style="background: #f9f9f9; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <strong style="font-size: 1.1rem;">Toplam:</strong>
                                    <strong style="font-size: 1.5rem; color: var(--primary);"><?= formatPrice($total) ?></strong>
                                </div>
                            </div>

                            <!-- Terms Checkbox -->
                            <label style="display: flex; align-items: start; margin-bottom: 1rem; cursor: pointer; font-size: 0.9rem;">
                                <input type="checkbox" name="terms" required style="margin-top: 0.25rem; margin-right: 0.5rem;">
                                <span>
                                    <a href="<?= BASE_URL ?>/page/kullanim-kosullari" target="_blank" style="color: var(--primary);">Kullanım koşullarını</a>
                                    ve
                                    <a href="<?= BASE_URL ?>/page/gizlilik-politikasi" target="_blank" style="color: var(--primary);">gizlilik politikasını</a>
                                    okudum ve kabul ediyorum.
                                </span>
                            </label>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem;">
                                <i class="fas fa-lock"></i> Siparişi Tamamla
                            </button>

                            <!-- Security Badge -->
                            <div style="text-align: center; margin-top: 1rem; font-size: 0.85rem; color: #999;">
                                <i class="fas fa-shield-alt" style="color: #4CAF50;"></i>
                                Güvenli Ödeme
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden field for cart data -->
            <input type="hidden" name="cart_data" id="cartData" value="">
        </form>
    </div>
</div>

<script>
// Load cart from localStorage and set to hidden field
document.addEventListener('DOMContentLoaded', function() {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    document.getElementById('cartData').value = JSON.stringify(cart);
});

function applyCoupon() {
    const code = document.querySelector('input[name="coupon_code"]').value;

    if (!code) {
        alert('Lütfen bir kupon kodu girin.');
        return;
    }

    fetch('<?= BASE_URL ?>/api/coupon/validate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'code=' + encodeURIComponent(code) + '&cart_total=<?= $subtotal ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.valid) {
            alert('Kupon uygulandı! İndirim: ' + data.discount + ' TL');
            location.reload();
        } else {
            alert(data.message || 'Geçersiz kupon kodu.');
        }
    })
    .catch(error => {
        alert('Bir hata oluştu: ' + error);
    });
}

// Highlight selected address/payment
document.querySelectorAll('input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const parent = this.closest('label');
        const siblings = parent.parentElement.querySelectorAll('label');

        siblings.forEach(s => s.style.borderColor = '#e0e0e0');
        parent.style.borderColor = 'var(--primary)';
    });
});
</script>

<style>
@media (max-width: 768px) {
    .checkout-container .container > form > div {
        grid-template-columns: 1fr !important;
    }
}
</style>
