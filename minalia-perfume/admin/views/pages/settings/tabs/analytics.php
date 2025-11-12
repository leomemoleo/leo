<div class="card">
    <div class="card-header">
        <h3 class="card-title">Analytics & Tracking</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= ADMIN_URL ?>/settings/update-analytics">

            <!-- Google Analytics -->
            <div class="settings-group">
                <div class="settings-group-title">
                    <i class="fab fa-google"></i> Google Analytics
                </div>

                <div class="form-group">
                    <label class="form-label">Google Analytics ID</label>
                    <input type="text" name="google_analytics_id" class="form-control"
                           value="<?= htmlspecialchars(getSetting($settings, 'analytics', 'google_analytics_id', '')) ?>"
                           placeholder="G-XXXXXXXXXX veya UA-XXXXXXXXX-X">
                    <small class="text-muted">
                        GA4 (G-XXXXXXXXXX) veya Universal Analytics (UA-XXXXXXXXX-X) ID'nizi girin
                    </small>
                </div>

                <div style="padding: 1rem; background: #e8f5e9; border-left: 4px solid #4caf50; border-radius: 4px; margin-top: 1rem;">
                    <strong>💡 Google Analytics Kurulumu:</strong>
                    <ol style="margin: 0.5rem 0 0 0; padding-left: 1.5rem; font-size: 0.875rem;">
                        <li><a href="https://analytics.google.com/" target="_blank">Google Analytics</a> hesabınızı açın</li>
                        <li>Yeni bir özellik (property) oluşturun</li>
                        <li>Ölçüm ID'nizi (G-XXXXXXXXXX) buraya girin</li>
                        <li>Sistem otomatik olarak tracking kodunu sayfalarınıza ekleyecektir</li>
                    </ol>
                </div>
            </div>

            <!-- Facebook Pixel -->
            <div class="settings-group">
                <div class="settings-group-title">
                    <i class="fab fa-facebook"></i> Facebook Pixel
                </div>

                <div class="form-group">
                    <label class="form-label">Facebook Pixel ID</label>
                    <input type="text" name="facebook_pixel_id" class="form-control"
                           value="<?= htmlspecialchars(getSetting($settings, 'analytics', 'facebook_pixel_id', '')) ?>"
                           placeholder="XXXXXXXXXXXXXXX">
                    <small class="text-muted">
                        Facebook Ads yöneticisinden pixel ID'nizi alın
                    </small>
                </div>

                <div style="padding: 1rem; background: #e3f2fd; border-left: 4px solid #2196f3; border-radius: 4px; margin-top: 1rem;">
                    <strong>💡 Facebook Pixel Faydaları:</strong>
                    <ul style="margin: 0.5rem 0 0 0; padding-left: 1.5rem; font-size: 0.875rem;">
                        <li>Site ziyaretçilerinizi takip edin</li>
                        <li>Dönüşüm optimizasyonu yapın</li>
                        <li>Remarketing kampanyaları oluşturun</li>
                        <li>Müşteri davranışlarını analiz edin</li>
                    </ul>
                    <p style="margin: 0.5rem 0 0 0; font-size: 0.875rem;">
                        <a href="https://business.facebook.com/events_manager" target="_blank">Events Manager</a>'dan pixel oluşturabilirsiniz.
                    </p>
                </div>
            </div>

            <!-- WhatsApp Integration -->
            <div class="settings-group">
                <div class="settings-group-title">
                    <i class="fab fa-whatsapp"></i> WhatsApp Entegrasyonu
                </div>

                <div class="form-switch">
                    <input type="checkbox" name="whatsapp_enabled" id="whatsapp_enabled" <?= isChecked($settings, 'analytics', 'whatsapp_enabled') ?>>
                    <div class="form-switch-label">
                        <strong>WhatsApp Destek Butonunu Etkinleştir</strong>
                        <small>Site üzerinde sabit WhatsApp destek butonu göster</small>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">WhatsApp Telefon Numarası</label>
                    <input type="text" name="whatsapp_number" class="form-control"
                           value="<?= htmlspecialchars(getSetting($settings, 'analytics', 'whatsapp_number', '')) ?>"
                           placeholder="905xxxxxxxxx (Ülke kodu ile birlikte, + işareti olmadan)">
                    <small class="text-muted">
                        Örnek: 905551234567 (Türkiye için 90 ülke kodu ile başlamalı)
                    </small>
                </div>

                <?php if (getSetting($settings, 'analytics', 'whatsapp_enabled', '0') == '1' && !empty(getSetting($settings, 'analytics', 'whatsapp_number', ''))): ?>
                <div style="padding: 1rem; background: #e8f5e9; border-left: 4px solid #25d366; border-radius: 4px; margin-top: 1rem;">
                    <strong style="color: #25d366;">✓ WhatsApp Butonu Aktif</strong>
                    <p style="margin: 0.5rem 0 0 0; font-size: 0.875rem;">
                        Site sağ alt köşesinde WhatsApp destek butonu görünecektir. Müşteriler direkt size mesaj atabilecek.
                    </p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Analytics Overview -->
            <div style="padding: 1.5rem; background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%); border-radius: 12px; margin-top: 1.5rem;">
                <h4 style="margin: 0 0 1rem 0; color: #7b1fa2;">
                    <i class="fas fa-chart-line"></i> Tracking Özeti
                </h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div style="background: white; padding: 1rem; border-radius: 8px;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                            <i class="fab fa-google" style="color: #4285f4; font-size: 1.5rem;"></i>
                            <strong>Google Analytics</strong>
                        </div>
                        <span style="font-size: 0.875rem; color: <?= !empty(getSetting($settings, 'analytics', 'google_analytics_id', '')) ? '#4caf50' : '#999' ?>;">
                            <?= !empty(getSetting($settings, 'analytics', 'google_analytics_id', '')) ? '✓ Aktif' : '○ Pasif' ?>
                        </span>
                    </div>

                    <div style="background: white; padding: 1rem; border-radius: 8px;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                            <i class="fab fa-facebook" style="color: #1877f2; font-size: 1.5rem;"></i>
                            <strong>Facebook Pixel</strong>
                        </div>
                        <span style="font-size: 0.875rem; color: <?= !empty(getSetting($settings, 'analytics', 'facebook_pixel_id', '')) ? '#4caf50' : '#999' ?>;">
                            <?= !empty(getSetting($settings, 'analytics', 'facebook_pixel_id', '')) ? '✓ Aktif' : '○ Pasif' ?>
                        </span>
                    </div>

                    <div style="background: white; padding: 1rem; border-radius: 8px;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                            <i class="fab fa-whatsapp" style="color: #25d366; font-size: 1.5rem;"></i>
                            <strong>WhatsApp</strong>
                        </div>
                        <span style="font-size: 0.875rem; color: <?= getSetting($settings, 'analytics', 'whatsapp_enabled', '0') == '1' ? '#4caf50' : '#999' ?>;">
                            <?= getSetting($settings, 'analytics', 'whatsapp_enabled', '0') == '1' ? '✓ Aktif' : '○ Pasif' ?>
                        </span>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Değişiklikleri Kaydet
                </button>
            </div>
        </form>
    </div>
</div>
