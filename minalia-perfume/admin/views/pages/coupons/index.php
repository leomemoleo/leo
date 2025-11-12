<div class="page-header">
    <div>
        <h1 class="page-title">Kuponlar</h1>
        <div class="page-breadcrumb">
            <span>Pazarlama</span>
            <span>›</span>
            <span>Kuponlar</span>
        </div>
    </div>
    <div>
        <a href="<?= ADMIN_URL ?>/coupons/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Kupon Ekle
        </a>
    </div>
</div>

<!-- Coupons Table -->
<div class="card">
    <div class="card-body">
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kupon Kodu</th>
                        <th>Tip</th>
                        <th>Değer</th>
                        <th>Min. Sepet</th>
                        <th>Kullanım</th>
                        <th>Geçerlilik</th>
                        <th>Durum</th>
                        <th style="width: 150px;">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($coupons)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 3rem; color: #999;">
                                <i class="fas fa-ticket-alt" style="font-size: 3rem; margin-bottom: 1rem; display: block; opacity: 0.3;"></i>
                                Henüz kupon eklenmemiş
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($coupons as $coupon): ?>
                            <tr>
                                <td>
                                    <strong style="font-family: monospace; color: var(--primary); font-size: 1.1rem;">
                                        <?= htmlspecialchars($coupon['code']) ?>
                                    </strong>
                                </td>
                                <td>
                                    <?php if ($coupon['type'] === 'percentage'): ?>
                                        <span style="padding: 0.25rem 0.5rem; background: var(--admin-info); color: white; border-radius: 6px; font-size: 0.75rem;">
                                            <i class="fas fa-percent"></i> Yüzde
                                        </span>
                                    <?php else: ?>
                                        <span style="padding: 0.25rem 0.5rem; background: var(--admin-success); color: white; border-radius: 6px; font-size: 0.75rem;">
                                            <i class="fas fa-lira-sign"></i> Tutar
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong>
                                        <?= $coupon['type'] === 'percentage' ? $coupon['value'] . '%' : formatPrice($coupon['value']) ?>
                                    </strong>
                                </td>
                                <td><?= $coupon['min_purchase'] ? formatPrice($coupon['min_purchase']) : '-' ?></td>
                                <td>
                                    <?= $coupon['used_count'] ?? 0 ?> / <?= $coupon['max_uses'] ? $coupon['max_uses'] : '∞' ?>
                                </td>
                                <td>
                                    <?php if ($coupon['start_date'] && $coupon['end_date']): ?>
                                        <div style="font-size: 0.85rem;">
                                            <?= date('d.m.Y', strtotime($coupon['start_date'])) ?>
                                            <br>
                                            <?= date('d.m.Y', strtotime($coupon['end_date'])) ?>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: #999;">Süresiz</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($coupon['is_active']): ?>
                                        <span style="color: var(--admin-success); font-weight: 600;">
                                            <i class="fas fa-check-circle"></i> Aktif
                                        </span>
                                    <?php else: ?>
                                        <span style="color: var(--admin-danger); font-weight: 600;">
                                            <i class="fas fa-times-circle"></i> Pasif
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <a href="<?= ADMIN_URL ?>/coupons/edit?id=<?= $coupon['id'] ?>"
                                           class="btn btn-sm btn-primary"
                                           title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deleteCoupon(<?= $coupon['id'] ?>, '<?= htmlspecialchars($coupon['code']) ?>')"
                                                class="btn btn-sm btn-danger"
                                                title="Sil">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function deleteCoupon(id, code) {
    if (!confirm(`"${code}" kuponunu silmek istediğinize emin misiniz?`)) {
        return;
    }

    fetch('<?= ADMIN_URL ?>/coupons/delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + id
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        alert('Bir hata oluştu: ' + error);
    });
}
</script>
