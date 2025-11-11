<div class="page-header">
    <div>
        <h1 class="page-title">Yeni Ürün Ekle</h1>
        <div class="page-breadcrumb">
            <span>E-Ticaret</span>
            <span>›</span>
            <a href="<?= ADMIN_URL ?>/products" style="color: inherit; text-decoration: none;">Ürünler</a>
            <span>›</span>
            <span>Yeni Ürün</span>
        </div>
    </div>
</div>

<form method="POST" enctype="multipart/form-data">
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <!-- Main Info -->
        <div>
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h3 class="card-title">Temel Bilgiler</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Ürün Adı *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Açıklama</label>
                        <textarea name="description" class="form-control" rows="6"></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Marka *</label>
                            <select name="brand_id" class="form-control" required>
                                <option value="">Seçiniz...</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?= $brand['id'] ?>"><?= htmlspecialchars($brand['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Kategori *</label>
                            <select name="category_id" class="form-control" required>
                                <option value="">Seçiniz...</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Cinsiyet *</label>
                        <select name="gender" class="form-control" required>
                            <option value="unisex">Unisex</option>
                            <option value="erkek">Erkek</option>
                            <option value="kadın">Kadın</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Fragrance Notes -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h3 class="card-title">Parfüm Notaları</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Üst Notalar</label>
                        <div id="topNotes">
                            <input type="text" name="notes_top[]" class="form-control" placeholder="Örn: Bergamot" style="margin-bottom: 0.5rem;">
                        </div>
                        <button type="button" onclick="addNote('top')" class="btn btn-sm" style="background: #f0f0f0; margin-top: 0.5rem;">
                            <i class="fas fa-plus"></i> Nota Ekle
                        </button>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Orta Notalar</label>
                        <div id="middleNotes">
                            <input type="text" name="notes_middle[]" class="form-control" placeholder="Örn: Gül" style="margin-bottom: 0.5rem;">
                        </div>
                        <button type="button" onclick="addNote('middle')" class="btn btn-sm" style="background: #f0f0f0; margin-top: 0.5rem;">
                            <i class="fas fa-plus"></i> Nota Ekle
                        </button>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alt Notalar</label>
                        <div id="baseNotes">
                            <input type="text" name="notes_base[]" class="form-control" placeholder="Örn: Vanilya" style="margin-bottom: 0.5rem;">
                        </div>
                        <button type="button" onclick="addNote('base')" class="btn btn-sm" style="background: #f0f0f0; margin-top: 0.5rem;">
                            <i class="fas fa-plus"></i> Nota Ekle
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Image Upload -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h3 class="card-title">Ürün Resmi</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <input type="file" name="main_image" accept="image/*" id="imageInput" class="form-control">
                    </div>
                    <div id="imagePreview" style="margin-top: 1rem; display: none;">
                        <img id="preview" src="" style="width: 100%; border-radius: 8px;">
                    </div>
                </div>
            </div>

            <!-- Pricing & Stock -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h3 class="card-title">Fiyat ve Stok</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Fiyat (TL) *</label>
                        <input type="number" name="price" class="form-control" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">İndirimli Fiyat (TL)</label>
                        <input type="number" name="discount_price" class="form-control" step="0.01">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Stok Miktarı *</label>
                        <input type="number" name="stock_quantity" class="form-control" required value="0">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Düşük Stok Eşiği</label>
                        <input type="number" name="low_stock_threshold" class="form-control" value="10">
                    </div>
                </div>
            </div>

            <!-- Options -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h3 class="card-title">Özellikler</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="is_featured" value="1" style="margin-right: 0.5rem;">
                            <span>Öne Çıkan Ürün</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="is_bestseller" value="1" style="margin-right: 0.5rem;">
                            <span>Çok Satan Ürün</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-success" style="flex: 1;">
                    <i class="fas fa-save"></i> Kaydet
                </button>
                <a href="<?= ADMIN_URL ?>/products" class="btn" style="background: #f0f0f0;">
                    <i class="fas fa-times"></i> İptal
                </a>
            </div>
        </div>
    </div>
</form>

<script>
// Image preview
document.getElementById('imageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

// Add note input
function addNote(type) {
    const container = document.getElementById(type + 'Notes');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'notes_' + type + '[]';
    input.className = 'form-control';
    input.style.marginBottom = '0.5rem';
    container.appendChild(input);
}
</script>
