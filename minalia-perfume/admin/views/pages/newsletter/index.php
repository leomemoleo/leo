<div class="page-header">
    <h1 class="page-title">Newsletter Yönetimi</h1>
    <div class="page-breadcrumb">
        <span>Ana Sayfa</span>
        <span>/</span>
        <span>Newsletter</span>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-label">Toplam Abone</div>
                <div class="stat-value"><?= $stats['total_subscribers'] ?? 0 ?></div>
            </div>
            <div class="stat-icon success">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-label">Aktif Abone</div>
                <div class="stat-value"><?= $stats['active_subscribers'] ?? 0 ?></div>
            </div>
            <div class="stat-icon info">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-label">Bu Ay Kayıt</div>
                <div class="stat-value"><?= $stats['this_month_subscribers'] ?? 0 ?></div>
            </div>
            <div class="stat-icon warning">
                <i class="fas fa-user-plus"></i>
            </div>
        </div>
    </div>
</div>

<!-- Actions -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <h3 class="card-title">Hızlı İşlemler</h3>
    </div>
    <div class="card-body">
        <div style="display: flex; gap: 1rem;">
            <a href="<?= ADMIN_URL ?>/newsletter/send" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Newsletter Gönder
            </a>
            <button onclick="exportSubscribers()" class="btn btn-success">
                <i class="fas fa-download"></i> Aboneleri Dışa Aktar
            </button>
        </div>
    </div>
</div>

<!-- Subscribers List -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Abone Listesi</h3>
        <div class="card-actions">
            <input type="text" id="searchInput" placeholder="Email ara..." style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 6px;">
        </div>
    </div>
    <div class="card-body">
        <div class="table-container">
            <table class="data-table" id="subscribersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Durum</th>
                        <th>Kayıt Tarihi</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($subscribers)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem; color: #999;">
                                Henüz abone yok
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($subscribers as $subscriber): ?>
                            <tr>
                                <td><?= $subscriber['id'] ?></td>
                                <td><strong><?= htmlspecialchars($subscriber['email']) ?></strong></td>
                                <td>
                                    <?php if ($subscriber['is_active']): ?>
                                        <span style="padding: 0.25rem 0.75rem; background: var(--admin-success); color: white; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                                            Aktif
                                        </span>
                                    <?php else: ?>
                                        <span style="padding: 0.25rem 0.75rem; background: var(--admin-secondary); color: white; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                                            Pasif
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d.m.Y H:i', strtotime($subscriber['created_at'])) ?></td>
                                <td>
                                    <button onclick="toggleStatus(<?= $subscriber['id'] ?>, <?= $subscriber['is_active'] ?>)"
                                            class="btn btn-sm <?= $subscriber['is_active'] ? 'btn-warning' : 'btn-success' ?>">
                                        <i class="fas fa-<?= $subscriber['is_active'] ? 'ban' : 'check' ?>"></i>
                                        <?= $subscriber['is_active'] ? 'Pasifleştir' : 'Aktifleştir' ?>
                                    </button>
                                    <button onclick="deleteSubscriber(<?= $subscriber['id'] ?>, '<?= htmlspecialchars($subscriber['email']) ?>')"
                                            class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                    <a href="?page=<?= $i ?>"
                       class="<?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Search functionality
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#subscribersTable tbody tr');

    rows.forEach(row => {
        const email = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        row.style.display = email.includes(searchTerm) ? '' : 'none';
    });
});

// Toggle subscriber status
function toggleStatus(id, currentStatus) {
    const action = currentStatus ? 'pasifleştirmek' : 'aktifleştirmek';
    if (!confirm(`Bu aboneyi ${action} istediğinize emin misiniz?`)) return;

    fetch('<?= ADMIN_URL ?>/newsletter/toggle-status', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + id
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Bir hata oluştu');
        }
    });
}

// Delete subscriber
function deleteSubscriber(id, email) {
    if (!confirm(`"${email}" adresini silmek istediğinize emin misiniz?`)) return;

    fetch('<?= ADMIN_URL ?>/newsletter/delete', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + id
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Bir hata oluştu');
        }
    });
}

// Export subscribers
function exportSubscribers() {
    window.location.href = '<?= ADMIN_URL ?>/newsletter/export';
}
</script>
