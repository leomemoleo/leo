<div class="account-container">
    <div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1.5rem;">
        <h1 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; margin-bottom: 2rem;">
            Hesabım
        </h1>

        <div style="display: grid; grid-template-columns: 250px 1fr; gap: 2rem;">
            <!-- Sidebar Menu -->
            <div>
                <?php include __DIR__ . '/../../components/account-sidebar.php'; ?>
            </div>

            <!-- Main Content -->
            <div>
                <!-- Welcome Message -->
                <div class="card" style="background: linear-gradient(135deg, var(--primary) 0%, #6a7a4f 100%); color: white; margin-bottom: 2rem;">
                    <div class="card-body" style="padding: 2rem;">
                        <h2 style="margin: 0 0 0.5rem; font-size: 1.75rem;">
                            Hoş Geldiniz, <?= htmlspecialchars(getCurrentUserName()) ?>!
                        </h2>
                        <p style="margin: 0; opacity: 0.9;">
                            Hesap bilgilerinizi ve siparişlerinizi buradan yönetebilirsiniz.
                        </p>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
                    <div class="card">
                        <div class="card-body" style="text-align: center; padding: 2rem;">
                            <div style="font-size: 2.5rem; color: var(--primary); margin-bottom: 0.5rem;">
                                <?= $order_stats['total_orders'] ?>
                            </div>
                            <div style="color: #666; font-weight: 600;">Toplam Sipariş</div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body" style="text-align: center; padding: 2rem;">
                            <div style="font-size: 2.5rem; color: var(--gold); margin-bottom: 0.5rem;">
                                <?= formatPrice($order_stats['total_spent']) ?>
                            </div>
                            <div style="color: #666; font-weight: 600;">Toplam Harcama</div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body" style="text-align: center; padding: 2rem;">
                            <div style="font-size: 2.5rem; color: #f44336; margin-bottom: 0.5rem;">
                                <?= $loyalty_stats['total_points'] ?? 0 ?>
                            </div>
                            <div style="color: #666; font-weight: 600;">Sadakat Puanı</div>
                        </div>
                    </div>
                </div>

                <!-- Loyalty Level -->
                <?php if (isset($loyalty_stats['level'])): ?>
                    <div class="card" style="margin-bottom: 2rem;">
                        <div class="card-header">
                            <h3 class="card-title">VIP Seviyeniz</h3>
                        </div>
                        <div class="card-body">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div>
                                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--gold); margin-bottom: 0.5rem;">
                                        <?= $loyalty_stats['level']['name'] ?>
                                    </div>
                                    <div style="color: #666;">
                                        Puan Çarpanı: x<?= $loyalty_stats['level']['multiplier'] ?>
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-size: 0.9rem; color: #999; margin-bottom: 0.25rem;">
                                        Kullanılabilir Puan
                                    </div>
                                    <div style="font-size: 2rem; font-weight: 700; color: var(--primary);">
                                        <?= $loyalty_stats['available_points'] ?? 0 ?>
                                    </div>
                                </div>
                            </div>

                            <div style="margin-top: 1rem;">
                                <a href="<?= BASE_URL ?>/account/loyalty" class="btn btn-primary">
                                    <i class="fas fa-star"></i> Puan Detayları
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Recent Orders -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Son Siparişlerim</h3>
                        <a href="<?= BASE_URL ?>/account/orders" style="color: var(--primary); text-decoration: none; font-weight: 600;">
                            Tümünü Gör <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recent_orders)): ?>
                            <p style="text-align: center; color: #999; padding: 2rem;">
                                Henüz sipariş vermemişsiniz.
                            </p>
                            <div style="text-align: center;">
                                <a href="<?= BASE_URL ?>" class="btn btn-primary">
                                    <i class="fas fa-shopping-bag"></i> Alışverişe Başla
                                </a>
                            </div>
                        <?php else: ?>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Sipariş No</th>
                                        <th>Tarih</th>
                                        <th>Tutar</th>
                                        <th>Durum</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_orders as $order): ?>
                                        <tr>
                                            <td><strong>#<?= $order['order_number'] ?></strong></td>
                                            <td><?= date('d.m.Y', strtotime($order['created_at'])) ?></td>
                                            <td><?= formatPrice($order['total_amount']) ?></td>
                                            <td>
                                                <?php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'processing' => 'info',
                                                    'shipped' => 'primary',
                                                    'delivered' => 'success',
                                                    'cancelled' => 'danger'
                                                ];
                                                $statusLabels = [
                                                    'pending' => 'Bekliyor',
                                                    'processing' => 'İşleniyor',
                                                    'shipped' => 'Kargoda',
                                                    'delivered' => 'Teslim Edildi',
                                                    'cancelled' => 'İptal'
                                                ];
                                                $color = $statusColors[$order['status']] ?? 'info';
                                                $label = $statusLabels[$order['status']] ?? $order['status'];
                                                ?>
                                                <span style="padding: 0.25rem 0.75rem; background: var(--<?= $color ?>); color: white; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                                                    <?= $label ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= BASE_URL ?>/account/orders/<?= $order['id'] ?>" class="btn btn-sm" style="background: #f0f0f0;">
                                                    <i class="fas fa-eye"></i> Detay
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media (max-width: 768px) {
    .account-container .container > div {
        grid-template-columns: 1fr !important;
    }

    .account-container .card-body > div[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
