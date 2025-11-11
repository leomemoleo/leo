<div class="page-header">
    <div>
        <h1 class="page-title">Sayfa Yönetimi</h1>
        <div class="page-breadcrumb">
            <span>İçerik</span>
            <span>›</span>
            <span>Sayfalar</span>
        </div>
    </div>
</div>

<!-- Info Alert -->
<div class="alert" style="background: #e3f2fd; color: #1976d2; border-left: 4px solid #1976d2; margin-bottom: 2rem;">
    <div>
        <i class="fas fa-info-circle"></i>
        <strong>Sayfa Yönetimi:</strong> İletişim, Hakkımızda, Gizlilik Politikası gibi statik sayfalarınızı buradan yönetebilirsiniz.
    </div>
</div>

<!-- Actions -->
<div style="margin-bottom: 2rem;">
    <a href="<?= ADMIN_URL ?>/pages/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Yeni Sayfa Ekle
    </a>
</div>

<!-- Pages List -->
<div class="card">
    <div class="card-body">
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Sayfa Başlığı</th>
                        <th>URL (Slug)</th>
                        <th>Durum</th>
                        <th>Son Güncelleme</th>
                        <th style="width: 150px;">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pages)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 3rem; color: #999;">
                                <i class="fas fa-file-alt" style="font-size: 3rem; margin-bottom: 1rem; display: block; opacity: 0.3;"></i>
                                Henüz sayfa eklenmemiş
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pages as $page): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600;">
                                        <?= htmlspecialchars($page['title']) ?>
                                    </div>
                                </td>
                                <td>
                                    <code style="background: #f0f0f0; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.85rem;">
                                        /page/<?= htmlspecialchars($page['slug']) ?>
                                    </code>
                                </td>
                                <td>
                                    <?php if ($page['is_active']): ?>
                                        <span style="padding: 0.25rem 0.75rem; background: var(--admin-success); color: white; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                                            <i class="fas fa-check-circle"></i> Yayında
                                        </span>
                                    <?php else: ?>
                                        <span style="padding: 0.25rem 0.75rem; background: #999; color: white; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                                            <i class="fas fa-eye-slash"></i> Taslak
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= date('d.m.Y H:i', strtotime($page['updated_at'])) ?>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <a href="<?= BASE_URL ?>/page/<?= $page['slug'] ?>"
                                           target="_blank"
                                           class="btn btn-sm"
                                           style="background: #e3f2fd; color: #1976d2;"
                                           title="Görüntüle">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        <a href="<?= ADMIN_URL ?>/pages/edit?id=<?= $page['id'] ?>"
                                           class="btn btn-sm btn-primary"
                                           title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deletePage(<?= $page['id'] ?>, '<?= htmlspecialchars($page['title']) ?>')"
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
function deletePage(id, title) {
    if (!confirm(`"${title}" sayfasını silmek istediğinize emin misiniz?`)) {
        return;
    }

    fetch('<?= ADMIN_URL ?>/pages/delete', {
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
