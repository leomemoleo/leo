<div class="page-header">
    <div>
        <h1 class="page-title">Sayfa Düzenle</h1>
        <div class="page-breadcrumb">
            <span>İçerik</span>
            <span>›</span>
            <a href="<?= ADMIN_URL ?>/pages" style="color: inherit; text-decoration: none;">Sayfalar</a>
            <span>›</span>
            <span>Düzenle</span>
        </div>
    </div>
</div>

<form method="POST">
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <!-- Main Content -->
        <div>
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h3 class="card-title">Sayfa İçeriği</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Sayfa Başlığı *</label>
                        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($page['title']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">URL (Slug)</label>
                        <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($page['slug']) ?>">
                        <small style="color: #999; font-size: 0.85rem;">Boş bırakılırsa başlıktan otomatik oluşturulur</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">İçerik *</label>
                        <textarea name="content" id="content" class="form-control" rows="20" required><?= htmlspecialchars($page['content']) ?></textarea>
                        <small style="color: #999; font-size: 0.85rem;">
                            HTML kullanabilirsiniz. Önerilen: &lt;h2&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- SEO Settings -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h3 class="card-title">SEO Ayarları</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Meta Başlık</label>
                        <input type="text" name="meta_title" class="form-control" value="<?= htmlspecialchars($page['meta_title']) ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Meta Açıklama</label>
                        <textarea name="meta_description" class="form-control" rows="3"><?= htmlspecialchars($page['meta_description']) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Publish Settings -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h3 class="card-title">Yayın Ayarları</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="is_active" value="1" <?= $page['is_active'] ? 'checked' : '' ?> style="margin-right: 0.5rem;">
                            <span>Sayfayı Yayınla</span>
                        </label>
                        <small style="color: #999; font-size: 0.85rem; display: block; margin-top: 0.5rem;">
                            İşaretli değilse sayfa sadece taslak olarak kaydedilir
                        </small>
                    </div>

                    <div style="margin-top: 1rem; padding: 1rem; background: #f5f5f5; border-radius: 8px;">
                        <div style="font-size: 0.85rem; color: #666; margin-bottom: 0.5rem;">Son Güncelleme:</div>
                        <div style="font-weight: 600;"><?= date('d.m.Y H:i', strtotime($page['updated_at'])) ?></div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-success" style="flex: 1;">
                    <i class="fas fa-save"></i> Güncelle
                </button>
                <a href="<?= ADMIN_URL ?>/pages" class="btn" style="background: #f0f0f0;">
                    <i class="fas fa-times"></i> İptal
                </a>
            </div>
        </div>
    </div>
</form>
