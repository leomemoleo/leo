<div class="page-header">
    <div>
        <h1 class="page-title">Sadakat Programı</h1>
        <div class="page-breadcrumb">
            <span>Müşteriler</span>
            <span>›</span>
            <span>Sadakat Programı</span>
        </div>
    </div>
</div>

<!-- Statistics -->
<div class="row" style="margin-bottom: 2rem;">
    <div class="col-md-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #D4AF37 0%, #C5A028 100%);">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?= number_format($stats['total_members'] ?? 0) ?></div>
                <div class="stat-label">Toplam Üye</div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #7A8B5C 0%, #6B7C4D 100%);">
                <i class="fas fa-coins"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?= number_format($stats['total_points'] ?? 0) ?></div>
                <div class="stat-label">Toplam Puan</div>
            </div>
        </div>
    </div>
</div>

<!-- Members Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Sadakat Üyeleri</h3>
    </div>
    <div class="card-body">
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Müşteri</th>
                        <th>Email</th>
                        <th>Puan Bakiyesi</th>
                        <th>Toplam Kazanılan</th>
                        <th>Son Güncelleme</th>
                        <th style="width: 120px;">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($members)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: #999;">
                                <i class="fas fa-award" style="font-size: 3rem; margin-bottom: 1rem; display: block; opacity: 0.3;"></i>
                                Henüz sadakat üyesi yok
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($members as $member): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600;">
                                        <?= htmlspecialchars(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? '')) ?>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($member['email'] ?? '') ?></td>
                                <td>
                                    <span style="padding: 0.25rem 0.75rem; background: var(--admin-primary); color: white; border-radius: 12px; font-weight: 600;">
                                        <?= number_format($member['points_balance']) ?> Puan
                                    </span>
                                </td>
                                <td><?= number_format($member['total_earned']) ?> Puan</td>
                                <td><?= date('d.m.Y H:i', strtotime($member['updated_at'])) ?></td>
                                <td>
                                    <button onclick="adjustPoints(<?= $member['user_id'] ?>)"
                                            class="btn btn-sm btn-primary"
                                            title="Puan Ayarla">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Adjust Points Modal -->
<div id="adjustPointsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; padding: 2rem; max-width: 500px; width: 90%;">
        <h3 style="margin-bottom: 1.5rem;">Puan Ayarla</h3>
        <form method="POST" action="<?= ADMIN_URL ?>/loyalty/adjust-points">
            <input type="hidden" name="user_id" id="adjust_user_id">

            <div class="form-group">
                <label class="form-label">Puan (+ eklemek için pozitif, - çıkarmak için negatif)</label>
                <input type="number" name="points" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Açıklama</label>
                <textarea name="reason" class="form-control" rows="3" required></textarea>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
                <button type="button" onclick="closeModal()" class="btn">İptal</button>
                <button type="submit" class="btn btn-primary">Kaydet</button>
            </div>
        </form>
    </div>
</div>

<script>
function adjustPoints(userId) {
    document.getElementById('adjust_user_id').value = userId;
    document.getElementById('adjustPointsModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('adjustPointsModal').style.display = 'none';
}
</script>
