<div class="card">
    <div class="card-header">
        <h3 class="card-title">Gelişmiş Ayarlar</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= ADMIN_URL ?>/settings/update-advanced">

            <!-- Maintenance Mode -->
            <div class="settings-group">
                <div class="settings-group-title">
                    <i class="fas fa-tools"></i> Bakım Modu
                </div>

                <div class="form-switch">
                    <input type="checkbox" name="maintenance_mode" id="maintenance_mode" <?= isChecked($settings, 'advanced', 'maintenance_mode') ?>>
                    <div class="form-switch-label">
                        <strong>Bakım Modunu Etkinleştir</strong>
                        <small style="color: #d32f2f;">⚠️ Site ziyaretçilere kapalı olacak, sadece adminler erişebilecek</small>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Bakım Modu Mesajı</label>
                    <textarea name="maintenance_message" class="form-control" rows="3"><?= htmlspecialchars(getSetting($settings, 'advanced', 'maintenance_message', '')) ?></textarea>
                </div>

                <?php if (getSetting($settings, 'advanced', 'maintenance_mode', '0') == '1'): ?>
                <div style="padding: 1rem; background: #ffebee; border-left: 4px solid #d32f2f; border-radius: 4px; margin-top: 1rem;">
                    <strong style="color: #d32f2f;">⚠️ Bakım Modu Aktif!</strong>
                    <p style="margin: 0.5rem 0 0 0; font-size: 0.875rem; color: #c62828;">
                        Site şu anda bakım modunda. Normal ziyaretçiler siteye erişemiyor.
                    </p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Currency & Tax -->
            <div class="settings-group">
                <div class="settings-group-title">
                    <i class="fas fa-money-bill-wave"></i> Para Birimi & Vergi
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Para Birimi Kodu</label>
                        <select name="currency" class="form-control">
                            <?php $currency = getSetting($settings, 'advanced', 'currency', 'TRY'); ?>
                            <option value="TRY" <?= $currency === 'TRY' ? 'selected' : '' ?>>TRY (Türk Lirası)</option>
                            <option value="USD" <?= $currency === 'USD' ? 'selected' : '' ?>>USD (ABD Doları)</option>
                            <option value="EUR" <?= $currency === 'EUR' ? 'selected' : '' ?>>EUR (Euro)</option>
                            <option value="GBP" <?= $currency === 'GBP' ? 'selected' : '' ?>>GBP (İngiliz Sterlini)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Para Birimi Sembolü</label>
                        <input type="text" name="currency_symbol" class="form-control" maxlength="3"
                               value="<?= htmlspecialchars(getSetting($settings, 'advanced', 'currency_symbol', '₺')) ?>">
                    </div>
                </div>

                <div class="form-switch">
                    <input type="checkbox" name="tax_enabled" id="tax_enabled" <?= isChecked($settings, 'advanced', 'tax_enabled') ?>>
                    <div class="form-switch-label">
                        <strong>KDV/Vergi Hesaplamayı Etkinleştir</strong>
                        <small>Ürün fiyatlarına vergi dahil edilsin</small>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Vergi Oranı (%)</label>
                    <input type="number" name="tax_rate" class="form-control" step="0.01"
                           value="<?= getSetting($settings, 'advanced', 'tax_rate', '20') ?>">
                    <small class="text-muted">Türkiye için standart KDV oranı: %20</small>
                </div>
            </div>

            <!-- Stock Management -->
            <div class="settings-group">
                <div class="settings-group-title">
                    <i class="fas fa-box"></i> Stok Yönetimi
                </div>

                <div class="form-switch">
                    <input type="checkbox" name="stock_alert_enabled" id="stock_alert_enabled" <?= isChecked($settings, 'advanced', 'stock_alert_enabled') ?>>
                    <div class="form-switch-label">
                        <strong>Stok Uyarılarını Etkinleştir</strong>
                        <small>Düşük stok durumlarında email bildirimi al</small>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Düşük Stok Eşiği</label>
                    <input type="number" name="stock_alert_threshold" class="form-control"
                           value="<?= getSetting($settings, 'advanced', 'stock_alert_threshold', '10') ?>">
                    <small class="text-muted">Bu miktarın altındaki ürünler için uyarı al</small>
                </div>
            </div>

            <!-- Features -->
            <div class="settings-group">
                <div class="settings-group-title">
                    <i class="fas fa-toggle-on"></i> Özellikler
                </div>

                <div class="form-switch">
                    <input type="checkbox" name="enable_reviews" id="enable_reviews" <?= isChecked($settings, 'advanced', 'enable_reviews') ?>>
                    <div class="form-switch-label">
                        <strong>Ürün Yorumlarını Etkinleştir</strong>
                        <small>Müşteriler ürünlere yorum yapabilsin</small>
                    </div>
                </div>

                <div class="form-switch">
                    <input type="checkbox" name="enable_wishlist" id="enable_wishlist" <?= isChecked($settings, 'advanced', 'enable_wishlist') ?>>
                    <div class="form-switch-label">
                        <strong>Favoriler Listesini Etkinleştir</strong>
                        <small>Müşteriler ürünleri favorilere ekleyebilsin</small>
                    </div>
                </div>

                <div class="form-switch">
                    <input type="checkbox" name="enable_loyalty" id="enable_loyalty" <?= isChecked($settings, 'advanced', 'enable_loyalty') ?>>
                    <div class="form-switch-label">
                        <strong>Sadakat Programını Etkinleştir</strong>
                        <small>Müşteri puan sistemi</small>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Puan Kazanma Oranı</label>
                    <input type="number" name="loyalty_points_rate" class="form-control"
                           value="<?= getSetting($settings, 'advanced', 'loyalty_points_rate', '10') ?>">
                    <small class="text-muted">Her ₺1 harcama için kaç puan verilsin (Örn: 10 = Her ₺1'e 10 puan)</small>
                </div>
            </div>

            <!-- Order Settings -->
            <div class="settings-group">
                <div class="settings-group-title">
                    <i class="fas fa-shopping-cart"></i> Sipariş Ayarları
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Minimum Sipariş Tutarı (₺)</label>
                        <input type="number" name="min_order_amount" class="form-control" step="0.01"
                               value="<?= getSetting($settings, 'advanced', 'min_order_amount', '50') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Maksimum Sepet Ürün Sayısı</label>
                        <input type="number" name="max_cart_items" class="form-control"
                               value="<?= getSetting($settings, 'advanced', 'max_cart_items', '50') ?>">
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="settings-group">
                <div class="settings-group-title">
                    <i class="fas fa-shield-alt"></i> Güvenlik Ayarları
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Oturum Zaman Aşımı (saniye)</label>
                        <input type="number" name="session_timeout" class="form-control"
                               value="<?= getSetting($settings, 'advanced', 'session_timeout', '3600') ?>">
                        <small class="text-muted">3600 = 1 saat</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Minimum Şifre Uzunluğu</label>
                        <input type="number" name="password_min_length" class="form-control"
                               value="<?= getSetting($settings, 'advanced', 'password_min_length', '6') ?>">
                    </div>
                </div>
            </div>

            <!-- System Tools -->
            <div class="settings-group">
                <div class="settings-group-title">
                    <i class="fas fa-wrench"></i> Sistem Araçları
                </div>

                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <button type="button" class="btn-secondary" onclick="if(confirm('Sistem önbelleğini temizlemek istediğinizden emin misiniz?')) { window.location.href='<?= ADMIN_URL ?>/settings/clear-cache'; }">
                        <i class="fas fa-broom"></i> Önbelleği Temizle
                    </button>
                </div>

                <div style="padding: 1rem; background: #fff3e0; border-left: 4px solid #ff9800; border-radius: 4px; margin-top: 1rem;">
                    <strong>💡 Önbellek Temizleme:</strong>
                    <p style="margin: 0.5rem 0 0 0; font-size: 0.875rem;">
                        Ayarlarda değişiklik yaptıktan sonra önbelleği temizlemeniz önerilir. Bu, tüm ayarların anında etkili olmasını sağlar.
                    </p>
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
