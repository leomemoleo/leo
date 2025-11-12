<div class="card">
    <div class="card-header">
        <h3 class="card-title">Ödeme Ayarları</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= ADMIN_URL ?>/settings/update-payment">

            <!-- General Payment Settings -->
            <div class="settings-group">
                <div class="settings-group-title">Genel Ödeme Ayarları</div>

                <div class="form-switch">
                    <input type="checkbox" name="payment_enabled" id="payment_enabled" <?= isChecked($settings, 'payment', 'payment_enabled') ?>>
                    <div class="form-switch-label">
                        <strong>Online Ödemeyi Etkinleştir</strong>
                        <small>Kredi kartı/banka kartı ile ödeme almayı aktif et</small>
                    </div>
                </div>

                <div class="form-switch">
                    <input type="checkbox" name="payment_test_mode" id="payment_test_mode" <?= isChecked($settings, 'payment', 'payment_test_mode') ?>>
                    <div class="form-switch-label">
                        <strong>Test Modu</strong>
                        <small>Gerçek para hareketi olmadan ödeme testleri yap</small>
                    </div>
                </div>

                <div class="form-switch">
                    <input type="checkbox" name="cash_on_delivery_enabled" id="cash_on_delivery_enabled" <?= isChecked($settings, 'payment', 'cash_on_delivery_enabled') ?>>
                    <div class="form-switch-label">
                        <strong>Kapıda Ödeme</strong>
                        <small>Kargo ile ödeme seçeneğini aktif et</small>
                    </div>
                </div>
            </div>

            <!-- iyzico Settings -->
            <div class="settings-group">
                <div class="settings-group-title">
                    <i class="fas fa-credit-card"></i> iyzico Ayarları
                </div>

                <div class="form-switch">
                    <input type="checkbox" name="iyzico_enabled" id="iyzico_enabled" <?= isChecked($settings, 'payment', 'iyzico_enabled') ?>>
                    <div class="form-switch-label">
                        <strong>iyzico'yu Etkinleştir</strong>
                        <small>Türkiye'nin önde gelen ödeme altyapısı</small>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">iyzico API Key</label>
                    <input type="text" name="iyzico_api_key" class="form-control"
                           value="<?= htmlspecialchars(getSetting($settings, 'payment', 'iyzico_api_key', '')) ?>"
                           placeholder="sandbox-xxxxx veya xxxxx">
                </div>

                <div class="form-group">
                    <label class="form-label">iyzico Secret Key</label>
                    <input type="password" name="iyzico_secret_key" class="form-control"
                           value="<?= htmlspecialchars(getSetting($settings, 'payment', 'iyzico_secret_key', '')) ?>"
                           placeholder="sandbox-xxxxx veya xxxxx">
                </div>

                <div style="padding: 1rem; background: #e7f3ff; border-left: 4px solid #2196F3; border-radius: 4px; margin-top: 1rem;">
                    <strong>💡 iyzico Entegrasyonu:</strong>
                    <p style="margin: 0.5rem 0 0 0; font-size: 0.875rem;">
                        <a href="https://merchant.iyzipay.com/" target="_blank" style="color: #2196F3;">iyzico Merchant Panel</a>'den API anahtarlarınızı alabilirsiniz.
                    </p>
                </div>
            </div>

            <!-- Stripe Settings -->
            <div class="settings-group">
                <div class="settings-group-title">
                    <i class="fab fa-stripe"></i> Stripe Ayarları
                </div>

                <div class="form-switch">
                    <input type="checkbox" name="stripe_enabled" id="stripe_enabled" <?= isChecked($settings, 'payment', 'stripe_enabled') ?>>
                    <div class="form-switch-label">
                        <strong>Stripe'ı Etkinleştir</strong>
                        <small>Uluslararası ödeme altyapısı</small>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Stripe Publishable Key</label>
                    <input type="text" name="stripe_publishable_key" class="form-control"
                           value="<?= htmlspecialchars(getSetting($settings, 'payment', 'stripe_publishable_key', '')) ?>"
                           placeholder="pk_test_xxxxx veya pk_live_xxxxx">
                </div>

                <div class="form-group">
                    <label class="form-label">Stripe Secret Key</label>
                    <input type="password" name="stripe_secret_key" class="form-control"
                           value="<?= htmlspecialchars(getSetting($settings, 'payment', 'stripe_secret_key', '')) ?>"
                           placeholder="sk_test_xxxxx veya sk_live_xxxxx">
                </div>

                <div style="padding: 1rem; background: #f3e5f5; border-left: 4px solid #9c27b0; border-radius: 4px; margin-top: 1rem;">
                    <strong>💡 Stripe Entegrasyonu:</strong>
                    <p style="margin: 0.5rem 0 0 0; font-size: 0.875rem;">
                        <a href="https://dashboard.stripe.com/apikeys" target="_blank" style="color: #9c27b0;">Stripe Dashboard</a>'dan API anahtarlarınızı alabilirsiniz.
                    </p>
                </div>
            </div>

            <!-- PayPal Settings -->
            <div class="settings-group">
                <div class="settings-group-title">
                    <i class="fab fa-paypal"></i> PayPal Ayarları
                </div>

                <div class="form-switch">
                    <input type="checkbox" name="paypal_enabled" id="paypal_enabled" <?= isChecked($settings, 'payment', 'paypal_enabled') ?>>
                    <div class="form-switch-label">
                        <strong>PayPal'ı Etkinleştir</strong>
                        <small>PayPal ile ödeme almayı aktif et</small>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">PayPal Client ID</label>
                    <input type="text" name="paypal_client_id" class="form-control"
                           value="<?= htmlspecialchars(getSetting($settings, 'payment', 'paypal_client_id', '')) ?>"
                           placeholder="xxxxx">
                </div>

                <div class="form-group">
                    <label class="form-label">PayPal Secret</label>
                    <input type="password" name="paypal_secret" class="form-control"
                           value="<?= htmlspecialchars(getSetting($settings, 'payment', 'paypal_secret', '')) ?>"
                           placeholder="xxxxx">
                </div>

                <div style="padding: 1rem; background: #fff3e0; border-left: 4px solid #ff9800; border-radius: 4px; margin-top: 1rem;">
                    <strong>💡 PayPal Entegrasyonu:</strong>
                    <p style="margin: 0.5rem 0 0 0; font-size: 0.875rem;">
                        <a href="https://developer.paypal.com/dashboard/" target="_blank" style="color: #ff9800;">PayPal Developer</a>'dan API anahtarlarınızı alabilirsiniz.
                    </p>
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
