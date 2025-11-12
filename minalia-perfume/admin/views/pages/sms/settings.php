<?php $content = ob_start(); ?>

<div class="content-header">
    <h1><i class="fas fa-cog"></i> SMS Ayarları</h1>
    <div class="header-actions">
        <a href="/admin/sms" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Geri
        </a>
    </div>
</div>

<form id="sms-settings-form">
    <div class="row">
        <div class="col-lg-8">
            <!-- General Settings -->
            <div class="card">
                <div class="card-header">
                    <h3>Genel Ayarlar</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-switch">
                            <input type="checkbox" name="sms_enabled"
                                   <?= ($settings['SMS_ENABLED'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <span class="switch-slider"></span>
                            <span class="switch-label">SMS Bildirimlerini Etkinleştir</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="form-switch">
                            <input type="checkbox" name="order_notifications"
                                   <?= ($settings['SMS_ORDER_NOTIFICATIONS'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <span class="switch-slider"></span>
                            <span class="switch-label">Sipariş Bildirimleri</span>
                        </label>
                        <small class="form-text">Sipariş durumu değiştiğinde müşteriye SMS gönder</small>
                    </div>

                    <div class="form-group">
                        <label class="form-switch">
                            <input type="checkbox" name="shipping_notifications"
                                   <?= ($settings['SMS_SHIPPING_NOTIFICATIONS'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <span class="switch-slider"></span>
                            <span class="switch-label">Kargo Bildirimleri</span>
                        </label>
                        <small class="form-text">Kargo kodu oluşturulduğunda SMS gönder</small>
                    </div>
                </div>
            </div>

            <!-- Provider Selection -->
            <div class="card">
                <div class="card-header">
                    <h3>SMS Sağlayıcı</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Sağlayıcı Seçin *</label>
                        <select name="provider" class="form-control" id="provider-select" required>
                            <option value="netgsm" <?= ($settings['SMS_PROVIDER'] ?? 'netgsm') === 'netgsm' ? 'selected' : '' ?>>
                                Netgsm - En Popüler
                            </option>
                            <option value="iletimerkezi" <?= ($settings['SMS_PROVIDER'] ?? '') === 'iletimerkezi' ? 'selected' : '' ?>>
                                İletimerkezi
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Netgsm Settings -->
            <div class="card provider-settings" id="netgsm-settings">
                <div class="card-header">
                    <h3><img src="https://www.netgsm.com.tr/img/logo.png" height="20" alt="Netgsm"> Netgsm Ayarları</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Hesap Bilgileri:</strong>
                        Netgsm panel giriş bilgilerinizi kullanın.
                        <a href="https://www.netgsm.com.tr" target="_blank">Hesap oluştur →</a>
                    </div>

                    <div class="form-group">
                        <label>Kullanıcı Adı (User Code) *</label>
                        <input type="text" name="netgsm_username" class="form-control"
                               value="<?= htmlspecialchars($settings['NETGSM_USERNAME'] ?? '') ?>"
                               placeholder="850XXXXXXXXX">
                    </div>

                    <div class="form-group">
                        <label>Şifre (Password) *</label>
                        <input type="password" name="netgsm_password" class="form-control"
                               value="<?= htmlspecialchars($settings['NETGSM_PASSWORD'] ?? '') ?>"
                               placeholder="••••••••">
                    </div>

                    <div class="form-group">
                        <label>Başlık (Header)</label>
                        <input type="text" name="netgsm_header" class="form-control"
                               value="<?= htmlspecialchars($settings['NETGSM_HEADER'] ?? 'MINALIA') ?>"
                               placeholder="MINALIA" maxlength="11">
                        <small class="form-text">SMS'lerde gözükecek gönderici adı (max 11 karakter)</small>
                    </div>
                </div>
            </div>

            <!-- İletimerkezi Settings -->
            <div class="card provider-settings" id="iletimerkezi-settings" style="display: none;">
                <div class="card-header">
                    <h3>İletimerkezi Ayarları</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>API Bilgileri:</strong>
                        İletimerkezi panelden API Key ve Hash değerlerini alın.
                        <a href="https://www.iletimerkezi.com" target="_blank">Hesap oluştur →</a>
                    </div>

                    <div class="form-group">
                        <label>API Key *</label>
                        <input type="text" name="iletimerkezi_key" class="form-control"
                               value="<?= htmlspecialchars($settings['ILETIMERKEZI_API_KEY'] ?? '') ?>"
                               placeholder="xxxxxxxxxxxxxxxx">
                    </div>

                    <div class="form-group">
                        <label>API Hash *</label>
                        <input type="password" name="iletimerkezi_hash" class="form-control"
                               value="<?= htmlspecialchars($settings['ILETIMERKEZI_API_HASH'] ?? '') ?>"
                               placeholder="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                    </div>

                    <div class="form-group">
                        <label>Gönderici Adı (Sender)</label>
                        <input type="text" name="iletimerkezi_sender" class="form-control"
                               value="<?= htmlspecialchars($settings['ILETIMERKEZI_SENDER'] ?? 'MINALIA') ?>"
                               placeholder="MINALIA">
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Ayarları Kaydet
                </button>
                <button type="button" class="btn btn-outline" onclick="testConnection()">
                    <i class="fas fa-plug"></i> Bağlantıyı Test Et
                </button>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3>Kurulum Rehberi</h3>
                </div>
                <div class="card-body">
                    <div class="setup-steps">
                        <div class="step">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <strong>Hesap Oluştur</strong>
                                <p>SMS sağlayıcıdan hesap açın</p>
                            </div>
                        </div>

                        <div class="step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <strong>Başlık Tanımla</strong>
                                <p>Gönderici adınızı onaylayın</p>
                            </div>
                        </div>

                        <div class="step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <strong>API Bilgilerini Al</strong>
                                <p>Kullanıcı adı ve şifre/key</p>
                            </div>
                        </div>

                        <div class="step">
                            <div class="step-number">4</div>
                            <div class="step-content">
                                <strong>Ayarları Kaydet</strong>
                                <p>Bağlantıyı test edin</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Karşılaştırma</h3>
                </div>
                <div class="card-body">
                    <table class="comparison-table">
                        <tr>
                            <th></th>
                            <th>Netgsm</th>
                            <th>İletimerkezi</th>
                        </tr>
                        <tr>
                            <td><strong>Popülerlik</strong></td>
                            <td>⭐⭐⭐⭐⭐</td>
                            <td>⭐⭐⭐⭐</td>
                        </tr>
                        <tr>
                            <td><strong>Fiyat</strong></td>
                            <td>Uygun</td>
                            <td>Uygun</td>
                        </tr>
                        <tr>
                            <td><strong>API</strong></td>
                            <td>Basit</td>
                            <td>XML</td>
                        </tr>
                        <tr>
                            <td><strong>Destek</strong></td>
                            <td>7/24</td>
                            <td>7/24</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
.provider-settings {
    transition: all 0.3s;
}

.setup-steps .step {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.step-number {
    width: 32px;
    height: 32px;
    background: var(--admin-primary);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    flex-shrink: 0;
}

.step-content strong {
    display: block;
    margin-bottom: 0.25rem;
}

.step-content p {
    margin: 0;
    font-size: 0.875rem;
    color: #666;
}

.comparison-table {
    width: 100%;
    font-size: 0.875rem;
}

.comparison-table th,
.comparison-table td {
    padding: 0.5rem;
    text-align: center;
    border-bottom: 1px solid #eee;
}

.comparison-table th:first-child,
.comparison-table td:first-child {
    text-align: left;
}

.form-switch {
    display: flex;
    align-items: center;
    gap: 1rem;
    cursor: pointer;
}

.switch-slider {
    position: relative;
    width: 50px;
    height: 26px;
    background: #ccc;
    border-radius: 13px;
    transition: 0.3s;
}

.switch-slider::before {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    left: 3px;
    top: 3px;
    background: white;
    border-radius: 50%;
    transition: 0.3s;
}

input[type="checkbox"]:checked + .switch-slider {
    background: var(--admin-success);
}

input[type="checkbox"]:checked + .switch-slider::before {
    transform: translateX(24px);
}

.form-switch input[type="checkbox"] {
    display: none;
}
</style>

<script>
const providerSelect = document.getElementById('provider-select');
const netgsmSettings = document.getElementById('netgsm-settings');
const iletimerkeziSettings = document.getElementById('iletimerkezi-settings');

// Show/hide provider settings
providerSelect.addEventListener('change', () => {
    const provider = providerSelect.value;

    if (provider === 'netgsm') {
        netgsmSettings.style.display = 'block';
        iletimerkeziSettings.style.display = 'none';
    } else {
        netgsmSettings.style.display = 'none';
        iletimerkeziSettings.style.display = 'block';
    }
});

// Initialize on load
providerSelect.dispatchEvent(new Event('change'));

// Save settings
document.getElementById('sms-settings-form').addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);
    const submitBtn = e.target.querySelector('button[type="submit"]');

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Kaydediliyor...';

    try {
        const response = await fetch('/admin/sms/settings', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            showToast('Ayarlar kaydedildi!', 'success');
        } else {
            showToast(result.message, 'error');
        }

    } catch (error) {
        showToast('Bir hata oluştu', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save"></i> Ayarları Kaydet';
    }
});

// Test connection
async function testConnection() {
    const btn = event.target;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Test ediliyor...';

    try {
        const response = await fetch('/admin/sms/test-connection');
        const result = await response.json();

        if (result.success) {
            showToast('Bağlantı başarılı! Bakiye: ' + (result.data.credits || result.data.sms || 'N/A'), 'success');
        } else {
            showToast('Bağlantı hatası: ' + result.message, 'error');
        }

    } catch (error) {
        showToast('Test başarısız', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-plug"></i> Bağlantıyı Test Et';
    }
}

function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'}`;
    toast.textContent = message;
    toast.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999;';

    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>

<?php $content = ob_get_clean(); include __DIR__ . '/../../layouts/admin.php'; ?>
