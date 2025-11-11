<div class="page-header">
    <h1 class="page-title">Yeni Kategori Ekle</h1>
</div>

<form method="POST">
    <div style="max-width: 800px;">
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Kategori Adı *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Slug (URL)</label>
                    <input type="text" name="slug" class="form-control" placeholder="Otomatik oluşturulur">
                </div>

                <div class="form-group">
                    <label class="form-label">Açıklama</label>
                    <textarea name="description" class="form-control" rows="4"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Sıralama</label>
                    <input type="number" name="sort_order" class="form-control" value="0">
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1" checked style="margin-right: 0.5rem;">
                        <span>Aktif</span>
                    </label>
                </div>

                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Kaydet
                    </button>
                    <a href="<?= ADMIN_URL ?>/categories" class="btn" style="background: #f0f0f0;">İptal</a>
                </div>
            </div>
        </div>
    </div>
</form>
