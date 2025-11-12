<div class="page-header">
    <div>
        <h1 class="page-title">Yorumlar</h1>
        <div class="page-breadcrumb">
            <span>İçerik</span>
            <span>›</span>
            <span>Yorumlar</span>
        </div>
    </div>
</div>

<!-- Reviews Table -->
<div class="card">
    <div class="card-body">
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Ürün</th>
                        <th>Müşteri</th>
                        <th>Puan</th>
                        <th>Yorum</th>
                        <th>Tarih</th>
                        <th>Durum</th>
                        <th style="width: 150px;">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reviews)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: #999;">
                                <i class="fas fa-comments" style="font-size: 3rem; margin-bottom: 1rem; display: block; opacity: 0.3;"></i>
                                Henüz yorum yapılmamış
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600;">
                                        <?= htmlspecialchars($review['product_name'] ?? 'Ürün bulunamadı') ?>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 600;">
                                        <?= htmlspecialchars($review['first_name'] . ' ' . $review['last_name']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div style="color: #D4AF37;">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star<?= $i <= $review['rating'] ? '' : '-o' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                </td>
                                <td>
                                    <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?= htmlspecialchars($review['comment']) ?>
                                    </div>
                                </td>
                                <td>
                                    <?= date('d.m.Y H:i', strtotime($review['created_at'])) ?>
                                </td>
                                <td>
                                    <?php if ($review['is_approved']): ?>
                                        <span style="color: var(--admin-success); font-weight: 600;">
                                            <i class="fas fa-check-circle"></i> Onaylı
                                        </span>
                                    <?php else: ?>
                                        <span style="color: var(--admin-warning); font-weight: 600;">
                                            <i class="fas fa-clock"></i> Beklemede
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <?php if (!$review['is_approved']): ?>
                                            <form method="POST" action="<?= ADMIN_URL ?>/reviews/approve" style="display: inline;">
                                                <input type="hidden" name="id" value="<?= $review['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-primary" title="Onayla">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="POST" action="<?= ADMIN_URL ?>/reviews/delete"
                                              onsubmit="return confirm('Bu yorumu silmek istediğinize emin misiniz?');"
                                              style="display: inline;">
                                            <input type="hidden" name="id" value="<?= $review['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Sil">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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
