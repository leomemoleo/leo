<div class="page-header">
    <div>
        <h1 class="page-title">Kategoriler</h1>
        <div class="page-breadcrumb">
            <span>E-Ticaret</span>
            <span>›</span>
            <span>Kategoriler</span>
        </div>
    </div>
    <a href="<?= ADMIN_URL ?>/categories/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Yeni Kategori
    </a>
</div>

<div class="card">
    <div class="card-body">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kategori Adı</th>
                    <th>Slug</th>
                    <th>Ürün Sayısı</th>
                    <th>Sıralama</th>
                    <th>Durum</th>
                    <th style="width: 150px;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 3rem; color: #999;">
                            Henüz kategori eklenmemiş
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($category['name']) ?></strong></td>
                            <td><code><?= htmlspecialchars($category['slug']) ?></code></td>
                            <td><?= $category['product_count'] ?> ürün</td>
                            <td><?= $category['sort_order'] ?></td>
                            <td>
                                <?php if ($category['is_active']): ?>
                                    <span style="color: var(--admin-success);"><i class="fas fa-check-circle"></i> Aktif</span>
                                <?php else: ?>
                                    <span style="color: #999;"><i class="fas fa-times-circle"></i> Pasif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="<?= ADMIN_URL ?>/categories/edit?id=<?= $category['id'] ?>"
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="deleteCategory(<?= $category['id'] ?>, '<?= htmlspecialchars($category['name']) ?>')"
                                            class="btn btn-sm btn-danger">
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

<script>
function deleteCategory(id, name) {
    if (!confirm(`"${name}" kategorisini silmek istediğinize emin misiniz?`)) {
        return;
    }

    fetch('<?= ADMIN_URL ?>/categories/delete', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + id
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    });
}
</script>
