<div class="page-header">
    <div>
        <h1 class="page-title">Sipariş #<?= $order['order_number'] ?></h1>
        <div class="page-breadcrumb">
            <span>E-Ticaret</span>
            <span>›</span>
            <a href="<?= ADMIN_URL ?>/orders" style="color: inherit; text-decoration: none;">Siparişler</a>
            <span>›</span>
            <span>Detay</span>
        </div>
    </div>
    <div>
        <a href="<?= ADMIN_URL ?>/orders" class="btn" style="background: #f0f0f0;">
            <i class="fas fa-arrow-left"></i> Geri
        </a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
    <!-- Order Items -->
    <div>
        <div class="card" style="margin-bottom: 2rem;">
            <div class="card-header">
                <h3 class="card-title">Sipariş Ürünleri</h3>
            </div>
            <div class="card-body">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Ürün</th>
                            <th>Birim Fiyat</th>
                            <th>Adet</th>
                            <th>Toplam</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <?php if ($item['main_image']): ?>
                                            <img src="<?= BASE_URL . $item['main_image'] ?>"
                                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                        <?php endif; ?>
                                        <div>
                                            <div style="font-weight: 600;"><?= htmlspecialchars($item['product_name']) ?></div>
                                            <div style="font-size: 0.85rem; color: #999;"><?= htmlspecialchars($item['brand_name']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?= formatPrice($item['price']) ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td><strong><?= formatPrice($item['price'] * $item['quantity']) ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align: right;"><strong>Ara Toplam:</strong></td>
                            <td><strong><?= formatPrice($order['subtotal']) ?></strong></td>
                        </tr>
                        <?php if ($order['discount_amount'] > 0): ?>
                            <tr>
                                <td colspan="3" style="text-align: right; color: #f44336;">İndirim:</td>
                                <td style="color: #f44336;">-<?= formatPrice($order['discount_amount']) ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td colspan="3" style="text-align: right;">Kargo:</td>
                            <td><?= formatPrice($order['shipping_cost']) ?></td>
                        </tr>
                        <tr style="background: #f9f9f9;">
                            <td colspan="3" style="text-align: right; font-size: 1.1rem;"><strong>Genel Toplam:</strong></td>
                            <td style="font-size: 1.1rem;"><strong><?= formatPrice($order['total_amount']) ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Shipping Address -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Teslimat Adresi</h3>
            </div>
            <div class="card-body">
                <?php
                $address = json_decode($order['shipping_address'], true);
                if ($address):
                ?>
                    <p style="margin: 0; line-height: 1.8;">
                        <strong><?= htmlspecialchars($address['name'] ?? '') ?></strong><br>
                        <?= htmlspecialchars($address['address'] ?? '') ?><br>
                        <?= htmlspecialchars($address['city'] ?? '') ?> / <?= htmlspecialchars($address['state'] ?? '') ?><br>
                        <?= htmlspecialchars($address['postal_code'] ?? '') ?><br>
                        Tel: <?= htmlspecialchars($address['phone'] ?? '') ?>
                    </p>
                <?php else: ?>
                    <p style="color: #999;">Adres bilgisi yok</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Order Info Sidebar -->
    <div>
        <!-- Order Status -->
        <div class="card" style="margin-bottom: 2rem;">
            <div class="card-header">
                <h3 class="card-title">Sipariş Durumu</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Durum</label>
                    <select class="form-control" id="orderStatus">
                        <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Bekliyor</option>
                        <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>İşleniyor</option>
                        <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Kargoda</option>
                        <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Teslim Edildi</option>
                        <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>İptal</option>
                    </select>
                </div>

                <button onclick="updateStatus()" class="btn btn-success" style="width: 100%;">
                    <i class="fas fa-save"></i> Durumu Güncelle
                </button>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="card" style="margin-bottom: 2rem;">
            <div class="card-header">
                <h3 class="card-title">Müşteri Bilgileri</h3>
            </div>
            <div class="card-body">
                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.85rem; color: #999; margin-bottom: 0.25rem;">Ad Soyad:</div>
                    <div style="font-weight: 600;"><?= htmlspecialchars($order['customer_name'] ?? 'Misafir') ?></div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.85rem; color: #999; margin-bottom: 0.25rem;">Email:</div>
                    <div><?= htmlspecialchars($order['customer_email'] ?? '-') ?></div>
                </div>

                <div>
                    <div style="font-size: 0.85rem; color: #999; margin-bottom: 0.25rem;">Telefon:</div>
                    <div><?= htmlspecialchars($order['customer_phone'] ?? '-') ?></div>
                </div>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Ödeme Bilgileri</h3>
            </div>
            <div class="card-body">
                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.85rem; color: #999; margin-bottom: 0.25rem;">Ödeme Yöntemi:</div>
                    <div style="font-weight: 600;"><?= htmlspecialchars($order['payment_method'] ?? 'Kredi Kartı') ?></div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.85rem; color: #999; margin-bottom: 0.25rem;">Ödeme Durumu:</div>
                    <?php
                    $paymentColors = [
                        'pending' => 'warning',
                        'completed' => 'success',
                        'failed' => 'danger'
                    ];
                    $paymentLabels = [
                        'pending' => 'Bekliyor',
                        'completed' => 'Tamamlandı',
                        'failed' => 'Başarısız'
                    ];
                    $pColor = $paymentColors[$order['payment_status']] ?? 'info';
                    $pLabel = $paymentLabels[$order['payment_status']] ?? $order['payment_status'];
                    ?>
                    <span style="padding: 0.5rem 1rem; background: var(--admin-<?= $pColor ?>); color: white; border-radius: 8px; display: inline-block; font-weight: 600;">
                        <?= $pLabel ?>
                    </span>
                </div>

                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.85rem; color: #999; margin-bottom: 0.25rem;">Sipariş Tarihi:</div>
                    <div><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></div>
                </div>

                <?php if ($order['notes']): ?>
                    <div>
                        <div style="font-size: 0.85rem; color: #999; margin-bottom: 0.25rem;">Sipariş Notu:</div>
                        <div style="background: #f9f9f9; padding: 0.75rem; border-radius: 4px; font-size: 0.9rem;">
                            <?= nl2br(htmlspecialchars($order['notes'])) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function updateStatus() {
    const newStatus = document.getElementById('orderStatus').value;

    if (!confirm('Sipariş durumunu değiştirmek istediğinize emin misiniz?')) {
        return;
    }

    fetch('<?= ADMIN_URL ?>/orders/update-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=<?= $order['id'] ?>&status=' + newStatus
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        alert('Bir hata oluştu: ' + error);
    });
}
</script>
