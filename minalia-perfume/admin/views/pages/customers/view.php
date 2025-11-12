<div class="page-header">
    <div>
        <h1 class="page-title">Müşteri Detayı</h1>
        <div class="page-breadcrumb">
            <span>Müşteriler</span>
            <span>›</span>
            <span><?= htmlspecialchars(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? '')) ?></span>
        </div>
    </div>
    <div>
        <a href="<?= ADMIN_URL ?>/customers" class="btn">
            <i class="fas fa-arrow-left"></i> Geri
        </a>
    </div>
</div>

<!-- Customer Info -->
<div class="row" style="margin-bottom: 2rem;">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Müşteri Bilgileri</h3>
            </div>
            <div class="card-body">
                <div style="text-align: center; margin-bottom: 1.5rem;">
                    <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #D4AF37 0%, #C5A028 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 2rem; margin: 0 auto;">
                        <?= strtoupper(substr($customer['first_name'] ?? 'M', 0, 1)) ?>
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-weight: 600; color: #666; font-size: 0.85rem; margin-bottom: 0.25rem;">Ad Soyad</label>
                    <div><?= htmlspecialchars(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? '')) ?></div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-weight: 600; color: #666; font-size: 0.85rem; margin-bottom: 0.25rem;">Email</label>
                    <div><?= htmlspecialchars($customer['email']) ?></div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-weight: 600; color: #666; font-size: 0.85rem; margin-bottom: 0.25rem;">Telefon</label>
                    <div><?= htmlspecialchars($customer['phone'] ?? '-') ?></div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-weight: 600; color: #666; font-size: 0.85rem; margin-bottom: 0.25rem;">Kayıt Tarihi</label>
                    <div><?= date('d.m.Y H:i', strtotime($customer['created_at'])) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Orders -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <h3 class="card-title">Son Siparişler</h3>
            </div>
            <div class="card-body">
                <?php if (empty($orders)): ?>
                    <p style="text-align: center; color: #999; padding: 2rem;">
                        <i class="fas fa-shopping-bag" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; opacity: 0.3;"></i>
                        Henüz sipariş yok
                    </p>
                <?php else: ?>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Sipariş No</th>
                                    <th>Tutar</th>
                                    <th>Durum</th>
                                    <th>Tarih</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= ADMIN_URL ?>/orders/view?id=<?= $order['id'] ?>" style="color: var(--primary); font-weight: 600;">
                                                #<?= $order['order_number'] ?>
                                            </a>
                                        </td>
                                        <td><strong><?= formatPrice($order['total_amount']) ?></strong></td>
                                        <td>
                                            <?php
                                            $statusLabels = [
                                                'pending' => 'Bekliyor',
                                                'processing' => 'İşleniyor',
                                                'shipped' => 'Kargoda',
                                                'delivered' => 'Teslim Edildi',
                                                'cancelled' => 'İptal'
                                            ];
                                            $label = $statusLabels[$order['status']] ?? $order['status'];
                                            ?>
                                            <span><?= $label ?></span>
                                        </td>
                                        <td><?= date('d.m.Y', strtotime($order['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Loyalty Points -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Sadakat Puanları</h3>
            </div>
            <div class="card-body">
                <?php if (empty($points)): ?>
                    <p style="text-align: center; color: #999; padding: 2rem;">
                        <i class="fas fa-coins" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; opacity: 0.3;"></i>
                        Henüz puan yok
                    </p>
                <?php else: ?>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Puan Bakiyesi</th>
                                    <th>Toplam Kazanılan</th>
                                    <th>Son Güncelleme</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($points as $point): ?>
                                    <tr>
                                        <td>
                                            <span style="padding: 0.25rem 0.75rem; background: var(--admin-success); color: white; border-radius: 12px; font-weight: 600;">
                                                <?= number_format($point['points_balance']) ?> Puan
                                            </span>
                                        </td>
                                        <td><?= number_format($point['total_earned']) ?> Puan</td>
                                        <td><?= date('d.m.Y H:i', strtotime($point['updated_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
