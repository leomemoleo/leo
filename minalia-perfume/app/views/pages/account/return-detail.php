<div class="account-container">
    <div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1.5rem;">
        <div style="margin-bottom: 2rem;">
            <a href="/account/returns" style="color: #666; text-decoration: none;">
                <i class="fas fa-arrow-left"></i> İadelerime Dön
            </a>
        </div>

        <h1 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; margin-bottom: 2rem;">
            İade Detayı #<?= htmlspecialchars($return['return_number']) ?>
        </h1>

        <?php
        $statusLabels = ['pending' => 'Beklemede', 'approved' => 'Onaylandı', 'rejected' => 'Reddedildi',
                        'processing' => 'İşleniyor', 'completed' => 'Tamamlandı', 'refunded' => 'İade Edildi'];
        $typeLabels = ['return' => 'İade', 'cancel' => 'İptal'];
        $reasonLabels = ['defective_product' => 'Ürün kusurlu', 'wrong_product' => 'Yanlış ürün',
                        'not_as_described' => 'Açıklamaya uymuyor', 'changed_mind' => 'Fikrim değişti',
                        'better_price' => 'Daha ucuz buldum', 'late_delivery' => 'Geç teslimat',
                        'damaged_package' => 'Hasarlı paket', 'other' => 'Diğer'];
        ?>

        <div class="card" style="margin-bottom: 2rem;">
            <div class="card-body" style="padding: 2rem;">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; margin-bottom: 2rem;">
                    <div>
                        <div style="color: #666; font-size: 0.875rem; margin-bottom: 0.5rem;">Durum</div>
                        <div style="font-weight: 600; font-size: 1.125rem;">
                            <?= $statusLabels[$return['status']] ?? $return['status'] ?>
                        </div>
                    </div>
                    <div>
                        <div style="color: #666; font-size: 0.875rem; margin-bottom: 0.5rem;">Talep Tipi</div>
                        <div style="font-weight: 600; font-size: 1.125rem;">
                            <?= $typeLabels[$return['return_type']] ?>
                        </div>
                    </div>
                    <div>
                        <div style="color: #666; font-size: 0.875rem; margin-bottom: 0.5rem;">İade Sebebi</div>
                        <div style="font-weight: 600; font-size: 1.125rem;">
                            <?= $reasonLabels[$return['reason']] ?? $return['reason'] ?>
                        </div>
                    </div>
                </div>

                <?php if ($return['reason_details']): ?>
                    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px;">
                        <strong>Açıklama:</strong><br>
                        <?= nl2br(htmlspecialchars($return['reason_details'])) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($return['refund_amount'] > 0): ?>
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-body" style="padding: 2rem;">
                    <h3 style="margin: 0 0 1rem;">İade Bilgisi</h3>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem;">
                        <div>
                            <div style="color: #666; font-size: 0.875rem; margin-bottom: 0.5rem;">İade Tutarı</div>
                            <div style="font-weight: 700; font-size: 1.5rem; color: var(--primary);">
                                <?= formatPrice($return['refund_amount']) ?>
                            </div>
                        </div>
                        <div>
                            <div style="color: #666; font-size: 0.875rem; margin-bottom: 0.5rem;">İade Durumu</div>
                            <div style="font-weight: 600;">
                                <?= $return['refund_status'] === 'completed' ? 'İade Yapıldı' : 'İşleniyor' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
