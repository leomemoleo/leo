<div class="account-container">
    <div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1.5rem;">
        <div style="margin-bottom: 2rem;">
            <a href="/account/orders" style="color: var(--primary); text-decoration: none; font-weight: 500;">
                <i class="fas fa-arrow-left"></i> Siparişlerime Dön
            </a>
        </div>

        <h1 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; margin-bottom: 2rem;">
            Sipariş #<?= htmlspecialchars($order['order_number']) ?>
        </h1>

        <div style="display: grid; grid-template-columns: 250px 1fr; gap: 2rem;">
            <!-- Sidebar Menu -->
            <div>
                <?php include __DIR__ . '/../../components/account-sidebar.php'; ?>
            </div>

            <!-- Main Content -->
            <div>
                <!-- Order Status -->
                <div class="card" style="margin-bottom: 1.5rem;">
                    <div class="card-body" style="padding: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h3 style="margin: 0 0 0.5rem;">Sipariş Durumu</h3>
                                <p style="margin: 0; color: #666;">
                                    Sipariş Tarihi: <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?>
                                </p>
                            </div>
                            <div>
                                <?php
                                $statusColors = [
                                    'pending' => 'warning',
                                    'processing' => 'info',
                                    'shipped' => 'primary',
                                    'delivered' => 'success',
                                    'cancelled' => 'danger'
                                ];
                                $statusLabels = [
                                    'pending' => 'Beklemede',
                                    'processing' => 'Hazırlanıyor',
                                    'shipped' => 'Kargoda',
                                    'delivered' => 'Teslim Edildi',
                                    'cancelled' => 'İptal Edildi'
                                ];
                                $statusClass = $statusColors[$order['status']] ?? 'secondary';
                                $statusLabel = $statusLabels[$order['status']] ?? $order['status'];
                                ?>
                                <span class="badge badge-<?= $statusClass ?>" style="padding: 0.75rem 1.5rem; font-size: 1rem;">
                                    <?= $statusLabel ?>
                                </span>
                            </div>
                        </div>

                        <!-- Order Timeline -->
                        <div style="margin-top: 2rem;">
                            <div class="order-timeline">
                                <div class="timeline-step <?= in_array($order['status'], ['pending', 'processing', 'shipped', 'delivered']) ? 'completed' : '' ?>">
                                    <div class="timeline-icon"><i class="fas fa-check"></i></div>
                                    <div class="timeline-content">
                                        <strong>Sipariş Alındı</strong>
                                        <div><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></div>
                                    </div>
                                </div>
                                <div class="timeline-step <?= in_array($order['status'], ['processing', 'shipped', 'delivered']) ? 'completed' : '' ?>">
                                    <div class="timeline-icon"><i class="fas fa-box"></i></div>
                                    <div class="timeline-content">
                                        <strong>Hazırlanıyor</strong>
                                        <?php if (in_array($order['status'], ['processing', 'shipped', 'delivered'])): ?>
                                            <div><?= date('d.m.Y H:i', strtotime($order['updated_at'])) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="timeline-step <?= in_array($order['status'], ['shipped', 'delivered']) ? 'completed' : '' ?>">
                                    <div class="timeline-icon"><i class="fas fa-truck"></i></div>
                                    <div class="timeline-content">
                                        <strong>Kargoya Verildi</strong>
                                        <?php if (in_array($order['status'], ['shipped', 'delivered']) && !empty($order['tracking_number'])): ?>
                                            <div>Takip: <?= htmlspecialchars($order['tracking_number']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="timeline-step <?= $order['status'] === 'delivered' ? 'completed' : '' ?>">
                                    <div class="timeline-icon"><i class="fas fa-check-circle"></i></div>
                                    <div class="timeline-content">
                                        <strong>Teslim Edildi</strong>
                                        <?php if ($order['status'] === 'delivered'): ?>
                                            <div><?= date('d.m.Y H:i', strtotime($order['delivered_at'] ?? $order['updated_at'])) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="card" style="margin-bottom: 1.5rem;">
                    <div class="card-header" style="padding: 1rem 1.5rem; border-bottom: 1px solid #eee;">
                        <h3 style="margin: 0;">Sipariş Ürünleri</h3>
                    </div>
                    <div class="card-body" style="padding: 1.5rem;">
                        <?php foreach ($items as $item): ?>
                            <div style="display: flex; gap: 1.5rem; padding: 1rem 0; border-bottom: 1px solid #eee;">
                                <div style="flex-shrink: 0;">
                                    <img src="<?= BASE_URL ?>/images/products/<?= htmlspecialchars($item['main_image']) ?>"
                                         alt="<?= htmlspecialchars($item['name']) ?>"
                                         style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                                </div>
                                <div style="flex: 1;">
                                    <h4 style="margin: 0 0 0.5rem; font-size: 1.125rem;">
                                        <?= htmlspecialchars($item['name']) ?>
                                    </h4>
                                    <div style="color: #666; font-size: 0.9375rem; margin-bottom: 0.5rem;">
                                        <?= htmlspecialchars($item['brand_name']) ?>
                                    </div>
                                    <div style="font-weight: 500;">
                                        Adet: <?= $item['quantity'] ?>
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-size: 1.25rem; font-weight: 600; color: var(--primary);">
                                        <?= formatPrice($item['price']) ?>
                                    </div>
                                    <?php if ($item['quantity'] > 1): ?>
                                        <div style="color: #666; font-size: 0.875rem;">
                                            Toplam: <?= formatPrice($item['price'] * $item['quantity']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                    <!-- Shipping Address -->
                    <div class="card">
                        <div class="card-header" style="padding: 1rem 1.5rem; border-bottom: 1px solid #eee;">
                            <h3 style="margin: 0;"><i class="fas fa-map-marker-alt"></i> Teslimat Adresi</h3>
                        </div>
                        <div class="card-body" style="padding: 1.5rem;">
                            <strong><?= htmlspecialchars($order['shipping_name']) ?></strong><br>
                            <?= nl2br(htmlspecialchars($order['shipping_address'])) ?><br>
                            <?= htmlspecialchars($order['shipping_city']) ?>, <?= htmlspecialchars($order['shipping_state']) ?><br>
                            <?= htmlspecialchars($order['shipping_zip']) ?><br>
                            <?= htmlspecialchars($order['shipping_country']) ?><br><br>
                            <strong>Telefon:</strong> <?= htmlspecialchars($order['shipping_phone']) ?><br>
                            <?php if (!empty($order['shipping_email'])): ?>
                                <strong>Email:</strong> <?= htmlspecialchars($order['shipping_email']) ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="card">
                        <div class="card-header" style="padding: 1rem 1.5rem; border-bottom: 1px solid #eee;">
                            <h3 style="margin: 0;"><i class="fas fa-receipt"></i> Sipariş Özeti</h3>
                        </div>
                        <div class="card-body" style="padding: 1.5rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                                <span>Ara Toplam</span>
                                <span><?= formatPrice($order['subtotal']) ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                                <span>Kargo</span>
                                <span><?= $order['shipping_cost'] > 0 ? formatPrice($order['shipping_cost']) : 'Ücretsiz' ?></span>
                            </div>
                            <?php if ($order['tax_amount'] > 0): ?>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                                    <span>KDV</span>
                                    <span><?= formatPrice($order['tax_amount']) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($order['discount_amount'] > 0): ?>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; color: #4CAF50;">
                                    <span>İndirim</span>
                                    <span>-<?= formatPrice($order['discount_amount']) ?></span>
                                </div>
                            <?php endif; ?>
                            <div style="border-top: 2px solid #eee; margin: 1rem 0; padding-top: 1rem; display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 700;">
                                <span>Toplam</span>
                                <span style="color: var(--primary);"><?= formatPrice($order['total_amount']) ?></span>
                            </div>

                            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #eee;">
                                <div style="margin-bottom: 0.75rem;">
                                    <strong>Ödeme Yöntemi:</strong><br>
                                    <?php
                                    $paymentLabels = [
                                        'credit_card' => 'Kredi Kartı',
                                        'bank_transfer' => 'Havale/EFT',
                                        'cash_on_delivery' => 'Kapıda Ödeme'
                                    ];
                                    echo $paymentLabels[$order['payment_method']] ?? $order['payment_method'];
                                    ?>
                                </div>
                                <div>
                                    <strong>Ödeme Durumu:</strong><br>
                                    <?php if ($order['payment_status'] === 'paid'): ?>
                                        <span style="color: #4CAF50;">
                                            <i class="fas fa-check-circle"></i> Ödendi
                                        </span>
                                    <?php else: ?>
                                        <span style="color: #ff9800;">
                                            <i class="far fa-clock"></i> Beklemede
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <?php if ($order['status'] !== 'cancelled' && $order['status'] !== 'delivered'): ?>
                    <div style="margin-top: 1.5rem; text-align: center;">
                        <button onclick="if(confirm('Siparişi iptal etmek istediğinizden emin misiniz?')) { cancelOrder('<?= $order['id'] ?>'); }"
                                class="btn btn-outline" style="padding: 0.75rem 2rem;">
                            <i class="fas fa-times"></i> Siparişi İptal Et
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.order-timeline {
    display: flex;
    justify-content: space-between;
    position: relative;
    padding: 2rem 0;
}

.order-timeline::before {
    content: '';
    position: absolute;
    top: 2.5rem;
    left: 2rem;
    right: 2rem;
    height: 2px;
    background: #eee;
    z-index: 0;
}

.timeline-step {
    position: relative;
    z-index: 1;
    text-align: center;
    flex: 1;
}

.timeline-icon {
    width: 50px;
    height: 50px;
    margin: 0 auto 1rem;
    background: #eee;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #999;
    font-size: 1.25rem;
    transition: all 0.3s;
}

.timeline-step.completed .timeline-icon {
    background: var(--primary);
    color: white;
}

.timeline-content {
    font-size: 0.875rem;
}

.timeline-content strong {
    display: block;
    margin-bottom: 0.25rem;
}

.timeline-content div {
    color: #666;
    font-size: 0.8125rem;
}

.badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-weight: 500;
}
.badge-success { background: #4CAF50; color: white; }
.badge-warning { background: #ff9800; color: white; }
.badge-info { background: #2196F3; color: white; }
.badge-primary { background: var(--primary); color: white; }
.badge-danger { background: #f44336; color: white; }
</style>

<script>
function cancelOrder(orderId) {
    // Implement cancel order via AJAX
    console.log('Cancel order:', orderId);
}
</script>
