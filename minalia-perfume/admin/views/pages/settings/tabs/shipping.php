<div class="card">
    <div class="card-header">
        <h3 class="card-title">Kargo Ayarları</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= ADMIN_URL ?>/settings/update-shipping">

            <!-- General Shipping Settings -->
            <div class="settings-group">
                <div class="settings-group-title">Genel Kargo Ayarları</div>

                <div class="form-switch">
                    <input type="checkbox" name="shipping_enabled" id="shipping_enabled" <?= isChecked($settings, 'shipping', 'shipping_enabled') ?>>
                    <div class="form-switch-label">
                        <strong>Kargo Hizmetini Etkinleştir</strong>
                        <small>Ürün teslimatını aktif et</small>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Ücretsiz Kargo Eşiği (₺)</label>
                    <input type="number" name="free_shipping_threshold" class="form-control" step="0.01"
                           value="<?= getSetting($settings, 'shipping', 'free_shipping_threshold', '500') ?>">
                    <small class="text-muted">Bu tutarın üzerindeki siparişlerde kargo ücretsiz olur</small>
                </div>
            </div>

            <!-- Shipping Costs -->
            <div class="settings-group">
                <div class="settings-group-title">Kargo Ücretleri</div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Standart Kargo Ücreti (₺)</label>
                        <input type="number" name="shipping_cost" class="form-control" step="0.01"
                               value="<?= getSetting($settings, 'shipping', 'shipping_cost', '29.90') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Hızlı Kargo Ücreti (₺)</label>
                        <input type="number" name="express_shipping_cost" class="form-control" step="0.01"
                               value="<?= getSetting($settings, 'shipping', 'express_shipping_cost', '49.90') ?>">
                    </div>
                </div>
            </div>

            <!-- Shipping Companies -->
            <div class="settings-group">
                <div class="settings-group-title">Kargo Firmaları</div>

                <div class="form-group">
                    <label class="form-label">Anlaşmalı Kargo Firmaları</label>
                    <input type="text" name="shipping_companies" class="form-control"
                           value="<?= htmlspecialchars(getSetting($settings, 'shipping', 'shipping_companies', 'Aras Kargo,MNG Kargo,Yurtiçi Kargo,PTT Kargo')) ?>">
                    <small class="text-muted">Virgül ile ayırın (Örn: Aras Kargo,MNG Kargo,Yurtiçi Kargo)</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Tahmini Teslimat Süresi</label>
                    <input type="text" name="estimated_delivery_days" class="form-control"
                           value="<?= htmlspecialchars(getSetting($settings, 'shipping', 'estimated_delivery_days', '2-3')) ?>"
                           placeholder="2-3 gün, 3-5 iş günü, vb.">
                </div>
            </div>

            <!-- Shipping Info -->
            <div style="padding: 1.5rem; background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%); border-radius: 12px; margin-top: 1.5rem;">
                <h4 style="margin: 0 0 1rem 0; color: #2e7d32;">
                    <i class="fas fa-info-circle"></i> Kargo Bilgilendirmesi
                </h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.9375rem;">
                    <div>
                        <strong>📦 Standart Kargo:</strong>
                        <p style="margin: 0.25rem 0 0 0; color: #555;">
                            <?= getSetting($settings, 'shipping', 'estimated_delivery_days', '2-3') ?> iş günü içinde teslim
                        </p>
                    </div>
                    <div>
                        <strong>🚀 Hızlı Kargo:</strong>
                        <p style="margin: 0.25rem 0 0 0; color: #555;">
                            Ertesi gün teslimat (çalışma günleri)
                        </p>
                    </div>
                    <div>
                        <strong>✅ Ücretsiz Kargo:</strong>
                        <p style="margin: 0.25rem 0 0 0; color: #555;">
                            <?= getSetting($settings, 'shipping', 'free_shipping_threshold', '500') ?> ₺ ve üzeri siparişlerde
                        </p>
                    </div>
                    <div>
                        <strong>🏢 Kargo Firmaları:</strong>
                        <p style="margin: 0.25rem 0 0 0; color: #555;">
                            <?= str_replace(',', ', ', getSetting($settings, 'shipping', 'shipping_companies', '')) ?>
                        </p>
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
