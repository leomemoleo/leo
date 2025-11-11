<div class="page-header">
    <div>
        <h1 class="page-title">Ürünler</h1>
        <div class="page-breadcrumb">
            <span>E-Ticaret</span>
            <span>›</span>
            <span>Ürünler</span>
        </div>
    </div>
</div>

<!-- Filters & Actions -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
            <!-- Search -->
            <form method="GET" style="flex: 1; max-width: 400px;">
                <div style="position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #999;"></i>
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Ürün ara..."
                        value="<?= htmlspecialchars($search) ?>"
                        style="padding-left: 2.5rem;"
                    >
                </div>
            </form>

            <!-- Add Button -->
            <a href="<?= ADMIN_URL ?>/products/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Yeni Ürün Ekle
            </a>
        </div>
    </div>
</div>

<!-- Products Table -->
<div class="card">
    <div class="card-body">
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">Resim</th>
                        <th>Ürün Adı</th>
                        <th>Marka</th>
                        <th>Kategori</th>
                        <th>Fiyat</th>
                        <th>Stok</th>
                        <th>Durum</th>
                        <th style="width: 150px;">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 3rem; color: #999;">
                                <i class="fas fa-box" style="font-size: 3rem; margin-bottom: 1rem; display: block; opacity: 0.3;"></i>
                                Henüz ürün eklenmemiş
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <?php if ($product['main_image']): ?>
                                        <img src="<?= BASE_URL . $product['main_image'] ?>"
                                             alt="<?= htmlspecialchars($product['name']) ?>"
                                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                    <?php else: ?>
                                        <div style="width: 60px; height: 60px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-image" style="color: #ccc;"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="font-weight: 600; margin-bottom: 0.25rem;">
                                        <?= htmlspecialchars($product['name']) ?>
                                    </div>
                                    <?php if ($product['is_featured']): ?>
                                        <span style="background: #D4AF37; color: white; padding: 0.125rem 0.5rem; border-radius: 3px; font-size: 0.7rem; margin-right: 0.25rem;">
                                            Öne Çıkan
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($product['is_bestseller']): ?>
                                        <span style="background: #7A8B5C; color: white; padding: 0.125rem 0.5rem; border-radius: 3px; font-size: 0.7rem;">
                                            Çok Satan
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($product['brand_name'] ?? '') ?></td>
                                <td><?= htmlspecialchars($product['category_name'] ?? '') ?></td>
                                <td>
                                    <?php if ($product['discount_price']): ?>
                                        <div style="text-decoration: line-through; color: #999; font-size: 0.85rem;">
                                            <?= formatPrice($product['price']) ?>
                                        </div>
                                        <div style="font-weight: 600; color: #f44336;">
                                            <?= formatPrice($product['discount_price']) ?>
                                        </div>
                                    <?php else: ?>
                                        <div style="font-weight: 600;">
                                            <?= formatPrice($product['price']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $stockClass = 'success';
                                    if ($product['stock_quantity'] == 0) {
                                        $stockClass = 'danger';
                                    } elseif ($product['stock_quantity'] <= $product['low_stock_threshold']) {
                                        $stockClass = 'warning';
                                    }
                                    ?>
                                    <span style="padding: 0.25rem 0.75rem; background: var(--admin-<?= $stockClass ?>); color: white; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                                        <?= $product['stock_quantity'] ?> adet
                                    </span>
                                </td>
                                <td>
                                    <?php if ($product['stock_quantity'] > 0): ?>
                                        <span style="color: var(--admin-success); font-weight: 600;">
                                            <i class="fas fa-check-circle"></i> Aktif
                                        </span>
                                    <?php else: ?>
                                        <span style="color: var(--admin-danger); font-weight: 600;">
                                            <i class="fas fa-times-circle"></i> Stokta Yok
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <a href="<?= ADMIN_URL ?>/products/edit?id=<?= $product['id'] ?>"
                                           class="btn btn-sm btn-primary"
                                           title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deleteProduct(<?= $product['id'] ?>, '<?= htmlspecialchars($product['name']) ?>')"
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

<script>
function deleteProduct(id, name) {
    if (!confirm(`"${name}" ürününü silmek istediğinize emin misiniz?`)) {
        return;
    }

    fetch('<?= ADMIN_URL ?>/products/delete', {
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
