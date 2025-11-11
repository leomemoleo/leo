<div class="page-header">
    <div>
        <h1 class="page-title">Siparişler</h1>
        <div class="page-breadcrumb">
            <span>E-Ticaret</span>
            <span>›</span>
            <span>Siparişler</span>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body">
        <form method="GET" style="display: flex; gap: 1rem; align-items: end; flex-wrap: wrap;">
            <!-- Search -->
            <div style="flex: 1; min-width: 250px;">
                <label class="form-label">Ara</label>
                <input type="text" name="search" class="form-control"
                       placeholder="Sipariş no, müşteri adı veya email..."
                       value="<?= htmlspecialchars($search) ?>">
            </div>

            <!-- Status Filter -->
            <div style="min-width: 200px;">
                <label class="form-label">Durum</label>
                <select name="status" class="form-control">
                    <option value="">Tümü</option>
                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Bekliyor</option>
                    <option value="processing" <?= $status === 'processing' ? 'selected' : '' ?>>İşleniyor</option>
                    <option value="shipped" <?= $status === 'shipped' ? 'selected' : '' ?>>Kargoda</option>
                    <option value="delivered" <?= $status === 'delivered' ? 'selected' : '' ?>>Teslim Edildi</option>
                    <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>İptal</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Filtrele
            </button>

            <?php if ($search || $status): ?>
                <a href="<?= ADMIN_URL ?>/orders" class="btn" style="background: #f0f0f0;">
                    <i class="fas fa-times"></i> Temizle
                </a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="card">
    <div class="card-body">
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Sipariş No</th>
                        <th>Müşteri</th>
                        <th>Tutar</th>
                        <th>Durum</th>
                        <th>Ödeme</th>
                        <th>Tarih</th>
                        <th style="width: 120px;">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: #999;">
                                <i class="fas fa-shopping-bag" style="font-size: 3rem; margin-bottom: 1rem; display: block; opacity: 0.3;"></i>
                                Sipariş bulunamadı
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>
                                    <strong style="color: var(--primary);">#<?= $order['order_number'] ?></strong>
                                </td>
                                <td>
                                    <div style="font-weight: 600;"><?= htmlspecialchars($order['customer_name'] ?? 'Misafir') ?></div>
                                    <div style="font-size: 0.85rem; color: #999;"><?= htmlspecialchars($order['customer_email'] ?? '') ?></div>
                                </td>
                                <td>
                                    <strong><?= formatPrice($order['total_amount']) ?></strong>
                                </td>
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
                                    <select class="form-control" style="font-size: 0.85rem; padding: 0.25rem 0.5rem; background: var(--admin-<?= $color ?>); color: white; border: none; font-weight: 600;"
                                            onchange="updateOrderStatus(<?= $order['id'] ?>, this.value)">
                                        <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Bekliyor</option>
                                        <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>İşleniyor</option>
                                        <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Kargoda</option>
                                        <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Teslim Edildi</option>
                                        <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>İptal</option>
                                    </select>
                                </td>
                                <td>
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
                                    <span style="padding: 0.25rem 0.5rem; background: var(--admin-<?= $pColor ?>); color: white; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                                        <?= $pLabel ?>
                                    </span>
                                </td>
                                <td>
                                    <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?>
                                </td>
                                <td>
                                    <a href="<?= ADMIN_URL ?>/orders/view?id=<?= $order['id'] ?>"
                                       class="btn btn-sm btn-primary"
                                       title="Detaylar">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['total_pages'] > 1): ?>
            <div style="display: flex; justify-content: center; margin-top: 2rem; gap: 0.5rem;">
                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                    <a href="?page=<?= $i ?><?= $status ? '&status=' . $status : '' ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
                       class="btn btn-sm <?= $i === $pagination['current_page'] ? 'btn-primary' : '' ?>"
                       style="min-width: 40px;">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function updateOrderStatus(orderId, newStatus) {
    if (!confirm('Sipariş durumunu değiştirmek istediğinize emin misiniz?')) {
        location.reload();
        return;
    }

    fetch('<?= ADMIN_URL ?>/orders/update-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + orderId + '&status=' + newStatus
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            alert(data.message);
            location.reload();
        }
    })
    .catch(error => {
        alert('Bir hata oluştu: ' + error);
        location.reload();
    });
}
</script>
