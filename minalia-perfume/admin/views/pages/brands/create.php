<div class="page-header">
    <h1 class="page-title">Yeni Marka Ekle</h1>
    <div class="page-breadcrumb">
        <a href="<?= ADMIN_URL ?>/brands">Markalar</a>
        <span>/</span>
        <span>Yeni Ekle</span>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Marka Bilgileri</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= ADMIN_URL ?>/brands/create" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Marka Adı *</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="slug">SEO URL (Slug) *</label>
                    <input type="text" class="form-control" id="slug" name="slug" required>
                    <small class="form-text">Otomatik oluşturulur, özelleştirebilirsiniz</small>
                </div>

                <div class="form-group">
                    <label for="country">Ülke</label>
                    <input type="text" class="form-control" id="country" name="country"
                           placeholder="Örn: Fransa, İtalya">
                </div>

                <div class="form-group">
                    <label for="sort_order">Sıralama</label>
                    <input type="number" class="form-control" id="sort_order" name="sort_order"
                           value="0" min="0">
                    <small class="form-text">Markaların gösterim sırası</small>
                </div>

                <div class="form-group">
                    <label for="is_featured">Öne Çıkan Marka</label>
                    <select class="form-control" id="is_featured" name="is_featured">
                        <option value="0">Hayır</option>
                        <option value="1">Evet</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="is_active">Durum</label>
                    <select class="form-control" id="is_active" name="is_active">
                        <option value="1" selected>Aktif</option>
                        <option value="0">Pasif</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Açıklama</label>
                <textarea class="form-control" id="description" name="description" rows="4"></textarea>
            </div>

            <div class="form-group">
                <label for="logo">Marka Logosu</label>
                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                <small class="form-text">PNG veya JPG formatında, maksimum 2MB</small>
            </div>

            <div class="form-group">
                <label for="meta_title">Meta Başlık (SEO)</label>
                <input type="text" class="form-control" id="meta_title" name="meta_title">
            </div>

            <div class="form-group">
                <label for="meta_description">Meta Açıklama (SEO)</label>
                <textarea class="form-control" id="meta_description" name="meta_description" rows="3"></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Kaydet
                </button>
                <a href="<?= ADMIN_URL ?>/brands" class="btn btn-secondary">
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
