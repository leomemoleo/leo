<div class="card">
    <div class="card-header">
        <h3 class="card-title">Genel Ayarlar</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= ADMIN_URL ?>/settings/update-general" enctype="multipart/form-data">

            <!-- Site Information -->
            <div class="settings-group">
                <div class="settings-group-title">Site Bilgileri</div>

                <div class="form-group">
                    <label class="form-label">Site Adı *</label>
                    <input type="text" name="site_name" class="form-control"
                           value="<?= htmlspecialchars(getSetting($settings, 'general', 'site_name', 'MINALIA Parfüm')) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Site Sloganı</label>
                    <input type="text" name="site_slogan" class="form-control"
                           value="<?= htmlspecialchars(getSetting($settings, 'general', 'site_slogan', '')) ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Site Açıklaması</label>
                    <textarea name="site_description" class="form-control" rows="3"><?= htmlspecialchars(getSetting($settings, 'general', 'site_description', '')) ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Anahtar Kelimeler</label>
                    <input type="text" name="site_keywords" class="form-control"
                           value="<?= htmlspecialchars(getSetting($settings, 'general', 'site_keywords', '')) ?>"
                           placeholder="parfüm, lüks parfüm, kadın parfümü...">
                    <small class="text-muted">Virgül ile ayırın</small>
                </div>
            </div>

            <!-- Branding -->
            <div class="settings-group">
                <div class="settings-group-title">Marka Görselleri</div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Site Logosu</label>
                        <input type="file" name="site_logo" class="form-control" accept="image/*">
                        <?php $logoPath = getSetting($settings, 'general', 'site_logo', ''); ?>
                        <?php if ($logoPath): ?>
                            <div class="image-preview">
                                <img src="<?= BASE_URL . $logoPath ?>" alt="Current Logo">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Favicon</label>
                        <input type="file" name="site_favicon" class="form-control" accept="image/x-icon,image/png">
                        <?php $faviconPath = getSetting($settings, 'general', 'site_favicon', ''); ?>
                        <?php if ($faviconPath): ?>
                            <div class="image-preview">
                                <img src="<?= BASE_URL . $faviconPath ?>" alt="Current Favicon">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="settings-group">
                <div class="settings-group-title">İletişim Bilgileri</div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Email Adresi *</label>
                        <input type="email" name="contact_email" class="form-control"
                               value="<?= htmlspecialchars(getSetting($settings, 'general', 'contact_email', '')) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Telefon</label>
                        <input type="text" name="contact_phone" class="form-control"
                               value="<?= htmlspecialchars(getSetting($settings, 'general', 'contact_phone', '')) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Adres</label>
                    <textarea name="contact_address" class="form-control" rows="3"><?= htmlspecialchars(getSetting($settings, 'general', 'contact_address', '')) ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Çalışma Saatleri</label>
                    <input type="text" name="business_hours" class="form-control"
                           value="<?= htmlspecialchars(getSetting($settings, 'general', 'business_hours', '')) ?>"
                           placeholder="Pazartesi - Cumartesi: 09:00 - 18:00">
                </div>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Değişiklikleri Kaydet
                </button>
            </div>
        </form>
    </div>
</div>
