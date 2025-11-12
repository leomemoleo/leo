<div class="account-container">
    <div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1.5rem;">
        <h1 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; margin-bottom: 2rem;">
            Siparişlerim
        </h1>

        <div style="display: grid; grid-template-columns: 250px 1fr; gap: 2rem;">
            <!-- Sidebar Menu -->
            <div>
                <?php include __DIR__ . '/../../components/account-sidebar.php'; ?>
            </div>

            <!-- Main Content -->
            <div>
                <?php if (empty($orders)): ?>
                    <!-- No Orders -->
                    <div class="card" style="text-align: center; padding: 4rem 2rem;">
                        <i class="fas fa-shopping-bag" style="font-size: 4rem; color: #ddd; margin-bottom: 1rem;"></i>
                        <h3 style="margin: 0 0 1rem;">Henüz Siparişiniz Yok</h3>
                        <p style="color: #666; margin-bottom: 2rem;">İlk siparişinizi vermek için ürünlerimize göz atın.</p>
                        <a href="/products" class="btn btn-primary">
                            <i class="fas fa-shopping-cart"></i> Alışverişe Başla
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Orders List -->
                    <div class="orders-list">
                        <?php foreach ($orders as $order): ?>
                            <div class="card" style="margin-bottom: 1.5rem;">
                                <div class="card-body" style="padding: 1.5rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                                        <div>
                                            <h3 style="margin: 0 0 0.5rem;">
                                                Sipariş #<?= htmlspecialchars($order['order_number']) ?>
                                            </h3>
                                            <div style="color: #666; font-size: 0.9375rem;">
                                                <i class="far fa-calendar"></i>
                                                <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?>
                                            </div>
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
                                                'cancelled' => 'İptal'
                                            ];
                                            $statusClass = $statusColors[$order['status']] ?? 'secondary';
                                            $statusLabel = $statusLabels[$order['status']] ?? $order['status'];
                                            ?>
                                            <span class="badge badge-<?= $statusClass ?>" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                                                <?= $statusLabel ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div style="border-top: 1px solid #eee; padding-top: 1rem; margin-bottom: 1rem;">
                                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
                                            <div>
                                                <div style="color: #666; font-size: 0.875rem; margin-bottom: 0.25rem;">Toplam Tutar</div>
                                                <div style="font-weight: 600; font-size: 1.125rem; color: var(--primary);">
                                                    <?= formatPrice($order['total_amount']) ?>
                                                </div>
                                            </div>
                                            <div>
                                                <div style="color: #666; font-size: 0.875rem; margin-bottom: 0.25rem;">Ödeme</div>
                                                <div style="font-weight: 500;">
                                                    <?php
                                                    $paymentLabels = [
                                                        'credit_card' => 'Kredi Kartı',
                                                        'bank_transfer' => 'Havale/EFT',
                                                        'cash_on_delivery' => 'Kapıda Ödeme'
                                                    ];
                                                    echo $paymentLabels[$order['payment_method']] ?? $order['payment_method'];
                                                    ?>
                                                </div>
                                            </div>
                                            <div>
                                                <div style="color: #666; font-size: 0.875rem; margin-bottom: 0.25rem;">Teslimat</div>
                                                <div style="font-weight: 500;">
                                                    <?= htmlspecialchars($order['shipping_method']) ?>
                                                </div>
                                            </div>
                                            <div>
                                                <div style="color: #666; font-size: 0.875rem; margin-bottom: 0.25rem;">Ödeme Durumu</div>
                                                <div>
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

                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <div style="color: #666; font-size: 0.9375rem;">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <?= htmlspecialchars($order['shipping_city']) ?>, <?= htmlspecialchars($order['shipping_country']) ?>
                                        </div>
                                        <div style="display: flex; gap: 0.75rem;">
                                            <form method="POST" action="/account/orders/reorder/<?= $order['id'] ?>" style="display: inline;">
                                                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                                <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1.25rem;">
                                                    <i class="fas fa-redo"></i> Tekrar Sipariş Ver
                                                </button>
                                            </form>
                                            <a href="/account/orders/<?= $order['order_number'] ?>" class="btn btn-outline" style="padding: 0.5rem 1.5rem;">
                                                <i class="fas fa-eye"></i> Detayları Gör
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if (isset($total_pages) && $total_pages > 1): ?>
                        <div style="text-align: center; margin-top: 2rem;">
                            <div class="pagination">
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <a href="?page=<?= $i ?>" class="<?= $i == $current_page ? 'active' : '' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-weight: 500;
    font-size: 0.875rem;
}
.badge-success { background: #4CAF50; color: white; }
.badge-warning { background: #ff9800; color: white; }
.badge-info { background: #2196F3; color: white; }
.badge-primary { background: var(--primary); color: white; }
.badge-danger { background: #f44336; color: white; }
.badge-secondary { background: #666; color: white; }

.pagination {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
}
.pagination a {
    padding: 0.5rem 1rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-decoration: none;
    color: #333;
    transition: all 0.3s;
}
.pagination a:hover {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}
.pagination a.active {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}
</style>
