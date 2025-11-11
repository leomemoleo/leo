<div class="page-header">
    <div>
        <h1 class="page-title">Markalar</h1>
        <div class="page-breadcrumb">
            <span>E-Ticaret</span>
            <span>›</span>
            <span>Markalar</span>
        </div>
    </div>
    <a href="<?= ADMIN_URL ?>/brands/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Yeni Marka
    </a>
</div>

<div class="card">
    <div class="card-body">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Marka Adı</th>
                    <th>Ülke</th>
                    <th>Ürün Sayısı</th>
                    <th>Öne Çıkan</th>
                    <th>Durum</th>
                    <th style="width: 150px;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($brands)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 3rem; color: #999;">
                            Henüz marka eklenmemiş
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($brands as $brand): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($brand['name']) ?></strong></td>
                            <td><?= htmlspecialchars($brand['country'] ?: '-') ?></td>
                            <td><?= $brand['product_count'] ?> ürün</td>
                            <td>
                                <?php if ($brand['is_featured']): ?>
                                    <span style="color: var(--gold);"><i class="fas fa-star"></i> Öne Çıkan</span>
                                <?php else: ?>
                                    <span style="color: #999;">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="color: var(--admin-success);"><i class="fas fa-check-circle"></i> Aktif</span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="<?= ADMIN_URL ?>/brands/edit?id=<?= $brand['id'] ?>"
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="deleteBrand(<?= $brand['id'] ?>, '<?= htmlspecialchars($brand['name']) ?>')"
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
function deleteBrand(id, name) {
    if (!confirm(`"${name}" markasını silmek istediğinize emin misiniz?`)) {
        return;
    }

    fetch('<?= ADMIN_URL ?>/brands/delete', {
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
