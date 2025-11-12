<div class="account-container">
    <div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1.5rem;">
        <h1 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; margin-bottom: 2rem;">
            İadelerim & İptallerim
        </h1>

        <div style="display: grid; grid-template-columns: 250px 1fr; gap: 2rem;">
            <!-- Sidebar Menu -->
            <div>
                <?php include __DIR__ . '/../../components/account-sidebar.php'; ?>
            </div>

            <!-- Main Content -->
            <div>
                <?php if (empty($returns)): ?>
                    <!-- No Returns -->
                    <div class="card" style="text-align: center; padding: 4rem 2rem;">
                        <i class="fas fa-undo-alt" style="font-size: 4rem; color: #ddd; margin-bottom: 1rem;"></i>
                        <h3 style="margin: 0 0 1rem;">Henüz İade Talebiniz Yok</h3>
                        <p style="color: #666; margin-bottom: 2rem;">
                            Sipariş iade ve iptal taleplerinizi buradan takip edebilirsiniz.
                        </p>
                        <a href="/account/orders" class="btn btn-primary">
                            <i class="fas fa-shopping-bag"></i> Siparişlerime Dön
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Returns List -->
                    <div class="returns-list">
                        <?php foreach ($returns as $return): ?>
                            <?php
                            $statusColors = [
                                'pending' => 'warning',
                                'approved' => 'info',
                                'rejected' => 'danger',
                                'processing' => 'primary',
                                'completed' => 'success',
                                'refunded' => 'success'
                            ];
                            $statusLabels = [
                                'pending' => 'Beklemede',
                                'approved' => 'Onaylandı',
                                'rejected' => 'Reddedildi',
                                'processing' => 'İşleniyor',
                                'completed' => 'Tamamlandı',
                                'refunded' => 'İade Edildi'
                            ];
                            $typeLabels = [
                                'return' => 'İade',
                                'cancel' => 'İptal'
                            ];
                            $reasonLabels = [
                                'defective_product' => 'Ürün kusurlu',
                                'wrong_product' => 'Yanlış ürün',
                                'not_as_described' => 'Açıklamaya uymuyor',
                                'changed_mind' => 'Fikrim değişti',
                                'better_price' => 'Daha ucuz buldum',
                                'late_delivery' => 'Geç teslimat',
                                'damaged_package' => 'Hasarlı paket',
                                'other' => 'Diğer'
                            ];

                            $statusClass = $statusColors[$return['status']] ?? 'secondary';
                            $statusLabel = $statusLabels[$return['status']] ?? $return['status'];
                            ?>
                            <div class="card" style="margin-bottom: 1.5rem;">
                                <div class="card-body" style="padding: 1.5rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                                        <div>
                                            <h3 style="margin: 0 0 0.5rem;">
                                                <?= $typeLabels[$return['return_type']] ?> #<?= htmlspecialchars($return['return_number']) ?>
                                            </h3>
                                            <div style="color: #666; font-size: 0.9375rem; margin-bottom: 0.25rem;">
                                                Sipariş #<?= htmlspecialchars($return['order_number']) ?>
                                            </div>
                                            <div style="color: #888; font-size: 0.875rem;">
                                                <i class="far fa-calendar"></i>
                                                <?= date('d.m.Y H:i', strtotime($return['created_at'])) ?>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="badge badge-<?= $statusClass ?>" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                                                <?= $statusLabel ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div style="border-top: 1px solid #eee; padding-top: 1rem;">
                                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1rem;">
                                            <div>
                                                <div style="color: #666; font-size: 0.875rem; margin-bottom: 0.25rem;">İade Sebebi</div>
                                                <div style="font-weight: 500;">
                                                    <?= $reasonLabels[$return['reason']] ?? $return['reason'] ?>
                                                </div>
                                            </div>
                                            <?php if ($return['refund_amount'] > 0): ?>
                                                <div>
                                                    <div style="color: #666; font-size: 0.875rem; margin-bottom: 0.25rem;">İade Tutarı</div>
                                                    <div style="font-weight: 600; color: var(--primary);">
                                                        <?= formatPrice($return['refund_amount']) ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <?php if ($return['reason_details']): ?>
                                            <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                                                <div style="font-size: 0.875rem; color: #666; margin-bottom: 0.25rem;">Açıklama:</div>
                                                <div style="font-size: 0.9375rem;"><?= nl2br(htmlspecialchars($return['reason_details'])) ?></div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($return['rejection_reason']): ?>
                                            <div class="alert alert-danger" style="margin-bottom: 1rem;">
                                                <strong><i class="fas fa-exclamation-circle"></i> Red Sebebi:</strong><br>
                                                <?= nl2br(htmlspecialchars($return['rejection_reason'])) ?>
                                            </div>
                                        <?php endif; ?>

                                        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                                            <a href="/account/returns/<?= $return['id'] ?>" class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i> Detayları Gör
                                            </a>

                                            <?php if ($return['status'] === 'pending'): ?>
                                                <form method="POST" action="/account/returns/cancel/<?= $return['id'] ?>"
                                                      onsubmit="return confirm('Bu iade talebini iptal etmek istediğinize emin misiniz?');"
                                                      style="display: inline;">
                                                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="fas fa-times"></i> İptal Et
                                                    </button>
                                                </form>
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

<style>
@media (max-width: 768px) {
    .account-container .container > div {
        grid-template-columns: 1fr !important;
    }
}
</style>
