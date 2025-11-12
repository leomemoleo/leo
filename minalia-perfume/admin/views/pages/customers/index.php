<div class="page-header">
    <div>
        <h1 class="page-title">Müşteriler</h1>
        <div class="page-breadcrumb">
            <span>Müşteriler</span>
            <span>›</span>
            <span>Müşteri Listesi</span>
        </div>
    </div>
</div>

<!-- Search & Filters -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body">
        <form method="GET" style="display: flex; gap: 1rem; align-items: end; flex-wrap: wrap;">
            <!-- Search -->
            <div style="flex: 1; min-width: 250px;">
                <label class="form-label">Ara</label>
                <input type="text" name="search" class="form-control"
                       placeholder="İsim veya email..."
                       value="<?= htmlspecialchars($search) ?>">
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Ara
            </button>

            <?php if ($search): ?>
                <a href="<?= ADMIN_URL ?>/customers" class="btn" style="background: #f0f0f0;">
                    <i class="fas fa-times"></i> Temizle
                </a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Customers Table -->
<div class="card">
    <div class="card-body">
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Müşteri</th>
                        <th>Email</th>
                        <th>Telefon</th>
                        <th>Sipariş Sayısı</th>
                        <th>Toplam Harcama</th>
                        <th>Kayıt Tarihi</th>
                        <th style="width: 120px;">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($customers)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: #999;">
                                <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 1rem; display: block; opacity: 0.3;"></i>
                                Müşteri bulunamadı
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #D4AF37 0%, #C5A028 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                            <?= strtoupper(substr($customer['first_name'] ?? 'M', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600;">
                                                <?= htmlspecialchars(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? '')) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($customer['email']) ?></td>
                                <td><?= htmlspecialchars($customer['phone'] ?? '-') ?></td>
                                <td>
                                    <span style="padding: 0.25rem 0.75rem; background: var(--admin-primary); color: white; border-radius: 12px; font-weight: 600; font-size: 0.85rem;">
                                        <?= number_format($customer['order_count'] ?? 0) ?> sipariş
                                    </span>
                                </td>
                                <td>
                                    <strong><?= formatPrice($customer['total_spent'] ?? 0) ?></strong>
                                </td>
                                <td>
                                    <?= date('d.m.Y', strtotime($customer['created_at'])) ?>
                                </td>
                                <td>
                                    <a href="<?= ADMIN_URL ?>/customers/view?id=<?= $customer['id'] ?>"
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
                    <a href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
                       class="btn btn-sm <?= $i === $pagination['current_page'] ? 'btn-primary' : '' ?>"
                       style="min-width: 40px;">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
