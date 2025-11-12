<div class="account-container">
    <div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1.5rem;">
        <h1 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; margin-bottom: 2rem;">
            Kuponlarım
        </h1>

        <div style="display: grid; grid-template-columns: 250px 1fr; gap: 2rem;">
            <!-- Sidebar Menu -->
            <div>
                <?php include __DIR__ . '/../../components/account-sidebar.php'; ?>
            </div>

            <!-- Main Content -->
            <div>
                <?php if (empty($coupons)): ?>
                    <!-- No Coupons -->
                    <div class="card" style="text-align: center; padding: 4rem 2rem;">
                        <i class="fas fa-ticket-alt" style="font-size: 4rem; color: #ddd; margin-bottom: 1rem;"></i>
                        <h3 style="margin: 0 0 1rem;">Henüz Kuponunuz Yok</h3>
                        <p style="color: #666; margin-bottom: 2rem;">Kampanyalarımızı takip ederek özel indirim kuponları kazanabilirsiniz.</p>
                        <a href="/products" class="btn btn-primary">
                            <i class="fas fa-shopping-cart"></i> Alışverişe Başla
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Filter Tabs -->
                    <div style="margin-bottom: 2rem; display: flex; gap: 1rem; border-bottom: 2px solid #eee;">
                        <button class="filter-tab active" data-status="all" style="padding: 1rem 1.5rem; border: none; background: none; cursor: pointer; font-weight: 600; color: var(--primary); border-bottom: 3px solid var(--primary);">
                            Tümü (<?= count($coupons) ?>)
                        </button>
                        <button class="filter-tab" data-status="available" style="padding: 1rem 1.5rem; border: none; background: none; cursor: pointer; font-weight: 600; color: #666; border-bottom: 3px solid transparent;">
                            Kullanılabilir (<?= count(array_filter($coupons, fn($c) => $c['status'] === 'available')) ?>)
                        </button>
                        <button class="filter-tab" data-status="used" style="padding: 1rem 1.5rem; border: none; background: none; cursor: pointer; font-weight: 600; color: #666; border-bottom: 3px solid transparent;">
                            Kullanıldı (<?= count(array_filter($coupons, fn($c) => $c['status'] === 'used')) ?>)
                        </button>
                        <button class="filter-tab" data-status="expired" style="padding: 1rem 1.5rem; border: none; background: none; cursor: pointer; font-weight: 600; color: #666; border-bottom: 3px solid transparent;">
                            Süresi Doldu (<?= count(array_filter($coupons, fn($c) => $c['status'] === 'expired')) ?>)
                        </button>
                    </div>

                    <!-- Coupons List -->
                    <div class="coupons-list">
                        <?php foreach ($coupons as $coupon): ?>
                            <?php
                            $statusClasses = [
                                'available' => 'coupon-available',
                                'used' => 'coupon-used',
                                'expired' => 'coupon-expired'
                            ];
                            $statusLabels = [
                                'available' => 'Kullanılabilir',
                                'used' => 'Kullanıldı',
                                'expired' => 'Süresi Doldu'
                            ];
                            $statusClass = $statusClasses[$coupon['status']] ?? '';
                            $statusLabel = $statusLabels[$coupon['status']] ?? $coupon['status'];

                            // Calculate discount display
                            $discountDisplay = '';
                            if ($coupon['type'] === 'percentage') {
                                $discountDisplay = '%' . number_format($coupon['discount_value'], 0);
                            } elseif ($coupon['type'] === 'fixed_amount') {
                                $discountDisplay = number_format($coupon['discount_value'], 0) . ' TL';
                            } elseif ($coupon['type'] === 'free_shipping') {
                                $discountDisplay = 'Ücretsiz Kargo';
                            }

                            $expiryDate = $coupon['custom_expiry_date'] ?? $coupon['valid_until'];
                            $daysLeft = max(0, floor((strtotime($expiryDate) - time()) / 86400));
                            ?>
                            <div class="coupon-card <?= $statusClass ?>" data-status="<?= $coupon['status'] ?>">
                                <div class="coupon-left">
                                    <div class="coupon-discount">
                                        <?= $discountDisplay ?>
                                    </div>
                                    <div class="coupon-type">
                                        <?php if ($coupon['type'] === 'percentage'): ?>
                                            İNDİRİM
                                        <?php elseif ($coupon['type'] === 'fixed_amount'): ?>
                                            İNDİRİM
                                        <?php else: ?>
                                            KARGO
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="coupon-right">
                                    <div style="flex: 1;">
                                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                                            <h3 style="margin: 0; font-size: 1.25rem;"><?= htmlspecialchars($coupon['title']) ?></h3>
                                            <span class="coupon-status-badge status-<?= $coupon['status'] ?>">
                                                <?= $statusLabel ?>
                                            </span>
                                        </div>

                                        <div class="coupon-code">
                                            <i class="fas fa-tag"></i> <strong><?= htmlspecialchars($coupon['code']) ?></strong>
                                            <button onclick="copyCode('<?= htmlspecialchars($coupon['code']) ?>')" class="copy-btn" title="Kopyala">
                                                <i class="far fa-copy"></i>
                                            </button>
                                        </div>

                                        <p style="color: #666; font-size: 0.9375rem; margin: 0.75rem 0;">
                                            <?= nl2br(htmlspecialchars($coupon['description'] ?? '')) ?>
                                        </p>

                                        <div style="display: flex; gap: 2rem; font-size: 0.875rem; color: #666; margin-top: 1rem;">
                                            <?php if ($coupon['minimum_order_amount'] > 0): ?>
                                                <div>
                                                    <i class="fas fa-shopping-cart"></i>
                                                    Min. <?= formatPrice($coupon['minimum_order_amount']) ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($coupon['maximum_discount_amount']): ?>
                                                <div>
                                                    <i class="fas fa-arrow-down"></i>
                                                    Maks. <?= formatPrice($coupon['maximum_discount_amount']) ?> indirim
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($coupon['status'] === 'available'): ?>
                                                <div style="color: <?= $daysLeft <= 3 ? '#f44336' : '#666' ?>;">
                                                    <i class="far fa-clock"></i>
                                                    <?= $daysLeft ?> gün kaldı
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($coupon['usage_limit_per_user'] > 1): ?>
                                                <div>
                                                    <i class="fas fa-redo"></i>
                                                    <?= $coupon['times_used'] ?>/<?= $coupon['usage_limit_per_user'] ?> kullanım
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <?php if ($coupon['status'] === 'available'): ?>
                                            <div style="margin-top: 1rem;">
                                                <a href="/cart" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-shopping-cart"></i> Kullan
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Usage History -->
                    <?php if (!empty($usage_history)): ?>
                        <div class="card" style="margin-top: 2rem;">
                            <div class="card-header" style="padding: 1rem 1.5rem; border-bottom: 1px solid #eee;">
                                <h3 style="margin: 0;"><i class="fas fa-history"></i> Kullanım Geçmişi</h3>
                            </div>
                            <div class="card-body" style="padding: 0;">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <thead style="background: #f8f9fa;">
                                        <tr>
                                            <th style="padding: 1rem; text-align: left; border-bottom: 2px solid #eee;">Tarih</th>
                                            <th style="padding: 1rem; text-align: left; border-bottom: 2px solid #eee;">Kupon</th>
                                            <th style="padding: 1rem; text-align: left; border-bottom: 2px solid #eee;">Sipariş</th>
                                            <th style="padding: 1rem; text-align: right; border-bottom: 2px solid #eee;">İndirim</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($usage_history as $history): ?>
                                            <tr style="border-bottom: 1px solid #eee;">
                                                <td style="padding: 1rem;">
                                                    <?= date('d.m.Y H:i', strtotime($history['created_at'])) ?>
                                                </td>
                                                <td style="padding: 1rem;">
                                                    <strong><?= htmlspecialchars($history['code']) ?></strong><br>
                                                    <small style="color: #666;"><?= htmlspecialchars($history['title']) ?></small>
                                                </td>
                                                <td style="padding: 1rem;">
                                                    <a href="/account/orders/<?= $history['order_number'] ?>" style="color: var(--primary); text-decoration: none;">
                                                        #<?= htmlspecialchars($history['order_number']) ?>
                                                    </a>
                                                </td>
                                                <td style="padding: 1rem; text-align: right; color: #4CAF50; font-weight: 600;">
                                                    -<?= formatPrice($history['discount_amount']) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.coupon-card {
    display: flex;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.coupon-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.coupon-left {
    width: 180px;
    background: linear-gradient(135deg, var(--primary) 0%, #B87333 100%);
    color: white;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
    position: relative;
}

.coupon-left::after {
    content: '';
    position: absolute;
    right: -10px;
    top: 50%;
    transform: translateY(-50%);
    width: 20px;
    height: 20px;
    background: white;
    border-radius: 50%;
}

.coupon-discount {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.coupon-type {
    font-size: 0.875rem;
    font-weight: 600;
    letter-spacing: 1px;
}

.coupon-right {
    flex: 1;
    padding: 1.5rem;
    display: flex;
}

.coupon-code {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: #f8f9fa;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    border: 2px dashed #ddd;
    font-family: 'Courier New', monospace;
    font-size: 1rem;
    margin-bottom: 0.75rem;
}

.copy-btn {
    background: none;
    border: none;
    color: var(--primary);
    cursor: pointer;
    padding: 0.25rem;
    transition: color 0.3s;
}

.copy-btn:hover {
    color: #B87333;
}

.coupon-status-badge {
    padding: 0.375rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
}

.status-available {
    background: #E8F5E9;
    color: #4CAF50;
}

.status-used {
    background: #E3F2FD;
    color: #2196F3;
}

.status-expired {
    background: #FFEBEE;
    color: #f44336;
}

.coupon-used, .coupon-expired {
    opacity: 0.6;
}

.coupon-used .coupon-left, .coupon-expired .coupon-left {
    background: linear-gradient(135deg, #999 0%, #666 100%);
}

.filter-tab.active {
    color: var(--primary) !important;
    border-bottom-color: var(--primary) !important;
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}
</style>

<script>
// Filter functionality
document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        // Update active tab
        document.querySelectorAll('.filter-tab').forEach(t => {
            t.classList.remove('active');
            t.style.color = '#666';
            t.style.borderBottomColor = 'transparent';
        });
        this.classList.add('active');
        this.style.color = 'var(--primary)';
        this.style.borderBottomColor = 'var(--primary)';

        // Filter coupons
        const status = this.getAttribute('data-status');
        document.querySelectorAll('.coupon-card').forEach(card => {
            if (status === 'all' || card.getAttribute('data-status') === status) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    });
});

// Copy coupon code
function copyCode(code) {
    navigator.clipboard.writeText(code).then(() => {
        // Show temporary success message
        const btn = event.target.closest('.copy-btn');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i>';
        btn.style.color = '#4CAF50';

        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.style.color = '';
        }, 2000);
    });
}
</script>
