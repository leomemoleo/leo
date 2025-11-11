<div class="page-header">
    <div>
        <h1 class="page-title">Ürün Düzenle</h1>
        <div class="page-breadcrumb">
            <span>E-Ticaret</span>
            <span>›</span>
            <a href="<?= ADMIN_URL ?>/products" style="color: inherit; text-decoration: none;">Ürünler</a>
            <span>›</span>
            <span>Düzenle</span>
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
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Açıklama</label>
                        <textarea name="description" class="form-control" rows="6"><?= htmlspecialchars($product['description']) ?></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Marka *</label>
                            <select name="brand_id" class="form-control" required>
                                <option value="">Seçiniz...</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?= $brand['id'] ?>" <?= $brand['id'] == $product['brand_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($brand['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Kategori *</label>
                            <select name="category_id" class="form-control" required>
                                <option value="">Seçiniz...</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= $category['id'] == $product['category_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Cinsiyet *</label>
                        <select name="gender" class="form-control" required>
                            <option value="unisex" <?= $product['gender'] == 'unisex' ? 'selected' : '' ?>>Unisex</option>
                            <option value="erkek" <?= $product['gender'] == 'erkek' ? 'selected' : '' ?>>Erkek</option>
                            <option value="kadın" <?= $product['gender'] == 'kadın' ? 'selected' : '' ?>>Kadın</option>
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
                            <?php
                            $topNotes = $product['fragrance_notes']['ust'] ?? [''];
                            foreach ($topNotes as $note):
                            ?>
                                <input type="text" name="notes_top[]" class="form-control" value="<?= htmlspecialchars($note) ?>" placeholder="Örn: Bergamot" style="margin-bottom: 0.5rem;">
                            <?php endforeach; ?>
                        </div>
                        <button type="button" onclick="addNote('top')" class="btn btn-sm" style="background: #f0f0f0; margin-top: 0.5rem;">
                            <i class="fas fa-plus"></i> Nota Ekle
                        </button>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Orta Notalar</label>
                        <div id="middleNotes">
                            <?php
                            $middleNotes = $product['fragrance_notes']['orta'] ?? [''];
                            foreach ($middleNotes as $note):
                            ?>
                                <input type="text" name="notes_middle[]" class="form-control" value="<?= htmlspecialchars($note) ?>" placeholder="Örn: Gül" style="margin-bottom: 0.5rem;">
                            <?php endforeach; ?>
                        </div>
                        <button type="button" onclick="addNote('middle')" class="btn btn-sm" style="background: #f0f0f0; margin-top: 0.5rem;">
                            <i class="fas fa-plus"></i> Nota Ekle
                        </button>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alt Notalar</label>
                        <div id="baseNotes">
                            <?php
                            $baseNotes = $product['fragrance_notes']['alt'] ?? [''];
                            foreach ($baseNotes as $note):
                            ?>
                                <input type="text" name="notes_base[]" class="form-control" value="<?= htmlspecialchars($note) ?>" placeholder="Örn: Vanilya" style="margin-bottom: 0.5rem;">
                            <?php endforeach; ?>
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
                    <?php if ($product['main_image']): ?>
                        <div style="margin-bottom: 1rem;">
                            <img src="<?= BASE_URL . $product['main_image'] ?>" style="width: 100%; border-radius: 8px;">
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label class="form-label">Yeni Resim Yükle</label>
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
                        <input type="number" name="price" class="form-control" step="0.01" value="<?= $product['price'] ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">İndirimli Fiyat (TL)</label>
                        <input type="number" name="discount_price" class="form-control" step="0.01" value="<?= $product['discount_price'] ?? '' ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Stok Miktarı *</label>
                        <input type="number" name="stock_quantity" class="form-control" value="<?= $product['stock_quantity'] ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Düşük Stok Eşiği</label>
                        <input type="number" name="low_stock_threshold" class="form-control" value="<?= $product['low_stock_threshold'] ?>">
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
                            <input type="checkbox" name="is_featured" value="1" <?= $product['is_featured'] ? 'checked' : '' ?> style="margin-right: 0.5rem;">
                            <span>Öne Çıkan Ürün</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="is_bestseller" value="1" <?= $product['is_bestseller'] ? 'checked' : '' ?> style="margin-right: 0.5rem;">
                            <span>Çok Satan Ürün</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-success" style="flex: 1;">
                    <i class="fas fa-save"></i> Güncelle
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
