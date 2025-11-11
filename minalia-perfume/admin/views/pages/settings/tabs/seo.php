<div class="card">
    <div class="card-header">
        <h3 class="card-title">SEO Ayarları</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= ADMIN_URL ?>/settings/update-seo" enctype="multipart/form-data">

            <!-- General SEO -->
            <div class="settings-group">
                <div class="settings-group-title">Genel SEO Ayarları</div>

                <div class="form-switch">
                    <input type="checkbox" name="seo_enabled" id="seo_enabled" <?= isChecked($settings, 'seo', 'seo_enabled') ?>>
                    <div class="form-switch-label">
                        <strong>SEO Optimizasyonunu Etkinleştir</strong>
                        <small>Arama motorları için site optimizasyonu</small>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Meta Title (Başlık) *</label>
                    <input type="text" name="meta_title" class="form-control"
                           value="<?= htmlspecialchars(getSetting($settings, 'seo', 'meta_title', '')) ?>"
                           maxlength="60" required>
                    <small class="text-muted">Maksimum 60 karakter önerilir</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Meta Description (Açıklama)</label>
                    <textarea name="meta_description" class="form-control" rows="3"
                              maxlength="160"><?= htmlspecialchars(getSetting($settings, 'seo', 'meta_description', '')) ?></textarea>
                    <small class="text-muted">Maksimum 160 karakter önerilir</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Meta Keywords (Anahtar Kelimeler)</label>
                    <input type="text" name="meta_keywords" class="form-control"
                           value="<?= htmlspecialchars(getSetting($settings, 'seo', 'meta_keywords', '')) ?>"
                           placeholder="parfüm, lüks parfüm, kadın parfümü...">
                    <small class="text-muted">Virgül ile ayırın</small>
                </div>
            </div>

            <!-- Open Graph (Social Media) -->
            <div class="settings-group">
                <div class="settings-group-title">Open Graph / Sosyal Medya Paylaşımı</div>

                <div class="form-group">
                    <label class="form-label">OG Title</label>
                    <input type="text" name="og_title" class="form-control"
                           value="<?= htmlspecialchars(getSetting($settings, 'seo', 'og_title', '')) ?>"
                           placeholder="MINALIA Parfüm">
                    <small class="text-muted">Facebook, Twitter gibi sosyal medyalarda görünecek başlık</small>
                </div>

                <div class="form-group">
                    <label class="form-label">OG Description</label>
                    <textarea name="og_description" class="form-control" rows="2"><?= htmlspecialchars(getSetting($settings, 'seo', 'og_description', '')) ?></textarea>
                    <small class="text-muted">Sosyal medya paylaşımlarında görünecek açıklama</small>
                </div>

                <div class="form-group">
                    <label class="form-label">OG Image (Paylaşım Görseli)</label>
                    <input type="file" name="og_image" class="form-control" accept="image/*">
                    <small class="text-muted">Önerilen boyut: 1200x630 piksel</small>
                    <?php $ogImagePath = getSetting($settings, 'seo', 'og_image', ''); ?>
                    <?php if ($ogImagePath): ?>
                        <div class="image-preview" style="max-width: 300px; margin-top: 1rem;">
                            <img src="<?= BASE_URL . $ogImagePath ?>" alt="OG Image" style="width: 100%; border-radius: 8px;">
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Verification Codes -->
            <div class="settings-group">
                <div class="settings-group-title">Site Doğrulama Kodları</div>

                <div class="form-group">
                    <label class="form-label">Google Site Verification</label>
                    <input type="text" name="google_site_verification" class="form-control"
                           value="<?= htmlspecialchars(getSetting($settings, 'seo', 'google_site_verification', '')) ?>"
                           placeholder="xxxxxxxxxxxxxxxxxxxxx">
                    <small class="text-muted">
                        <a href="https://search.google.com/search-console" target="_blank">Google Search Console</a>'dan alabilirsiniz
                    </small>
                </div>
            </div>

            <!-- SEO Tips -->
            <div style="padding: 1.5rem; background: linear-gradient(135deg, #e3f2fd 0%, #e8eaf6 100%); border-radius: 12px; margin-top: 1.5rem;">
                <h4 style="margin: 0 0 1rem 0; color: #1565c0;">
                    <i class="fas fa-lightbulb"></i> SEO İpuçları
                </h4>
                <ul style="margin: 0; padding-left: 1.5rem; font-size: 0.9375rem; color: #555;">
                    <li style="margin-bottom: 0.5rem;">
                        <strong>Title:</strong> Markanızı ve ana hizmetinizi içermeli (50-60 karakter)
                    </li>
                    <li style="margin-bottom: 0.5rem;">
                        <strong>Description:</strong> Arama sonuçlarında görünür, cazip ve bilgilendirici olmalı (150-160 karakter)
                    </li>
                    <li style="margin-bottom: 0.5rem;">
                        <strong>Keywords:</strong> En önemli 5-10 anahtar kelimeyi kullanın
                    </li>
                    <li style="margin-bottom: 0.5rem;">
                        <strong>OG Image:</strong> Sosyal medya paylaşımlarında profesyonel görünmek için kaliteli görsel kullanın
                    </li>
                    <li>
                        <strong>Google Search Console:</strong> Sitenizi Google'a kaydedin ve performansını takip edin
                    </li>
                </ul>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Değişiklikleri Kaydet
                </button>
            </div>
        </form>
    </div>
</div>
