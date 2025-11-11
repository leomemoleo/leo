<div class="page-header">
    <h1 class="page-title">Kategori Düzenle</h1>
    <div class="page-breadcrumb">
        <a href="<?= ADMIN_URL ?>/categories">Kategoriler</a>
        <span>/</span>
        <span>Düzenle</span>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Kategori Bilgileri</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= ADMIN_URL ?>/categories/edit/<?= $category['id'] ?>" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Kategori Adı *</label>
                    <input type="text" class="form-control" id="name" name="name"
                           value="<?= htmlspecialchars($category['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="slug">SEO URL (Slug) *</label>
                    <input type="text" class="form-control" id="slug" name="slug"
                           value="<?= htmlspecialchars($category['slug']) ?>" required>
                    <small class="form-text">Otomatik oluşturulur, özelleştirebilirsiniz</small>
                </div>

                <div class="form-group">
                    <label for="sort_order">Sıralama</label>
                    <input type="number" class="form-control" id="sort_order" name="sort_order"
                           value="<?= $category['sort_order'] ?? 0 ?>" min="0">
                    <small class="form-text">Kategorilerin gösterim sırası</small>
                </div>

                <div class="form-group">
                    <label for="is_active">Durum</label>
                    <select class="form-control" id="is_active" name="is_active">
                        <option value="1" <?= $category['is_active'] == 1 ? 'selected' : '' ?>>Aktif</option>
                        <option value="0" <?= $category['is_active'] == 0 ? 'selected' : '' ?>>Pasif</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Açıklama</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="meta_title">Meta Başlık (SEO)</label>
                <input type="text" class="form-control" id="meta_title" name="meta_title"
                       value="<?= htmlspecialchars($category['meta_title'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="meta_description">Meta Açıklama (SEO)</label>
                <textarea class="form-control" id="meta_description" name="meta_description" rows="3"><?= htmlspecialchars($category['meta_description'] ?? '') ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Güncelle
                </button>
                <a href="<?= ADMIN_URL ?>/categories" class="btn btn-secondary">
                    <i class="fas fa-times"></i> İptal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function() {
    const name = this.value;
    const slug = name.toLowerCase()
        .replace(/ı/g, 'i')
        .replace(/ğ/g, 'g')
        .replace(/ü/g, 'u')
        .replace(/ş/g, 's')
        .replace(/ö/g, 'o')
        .replace(/ç/g, 'c')
        .replace(/[^a-z0-9-]/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '');
    document.getElementById('slug').value = slug;
});
</script>
