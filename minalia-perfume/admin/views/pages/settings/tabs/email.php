<div class="card">
    <div class="card-header">
        <h3 class="card-title">Email Ayarları</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= ADMIN_URL ?>/settings/update-email">

            <!-- SMTP Settings -->
            <div class="settings-group">
                <div class="settings-group-title">SMTP Yapılandırması</div>

                <div class="form-switch">
                    <input type="checkbox" name="smtp_enabled" id="smtp_enabled" <?= isChecked($settings, 'email', 'smtp_enabled') ?>>
                    <div class="form-switch-label">
                        <strong>SMTP'yi Etkinleştir</strong>
                        <small>Email gönderimi için SMTP kullan</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">SMTP Host</label>
                        <input type="text" name="smtp_host" class="form-control"
                               value="<?= htmlspecialchars(getSetting($settings, 'email', 'smtp_host', 'smtp.gmail.com')) ?>"
                               placeholder="smtp.gmail.com">
                    </div>

                    <div class="form-group">
                        <label class="form-label">SMTP Port</label>
                        <input type="number" name="smtp_port" class="form-control"
                               value="<?= getSetting($settings, 'email', 'smtp_port', '587') ?>"
                               placeholder="587">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">SMTP Kullanıcı Adı</label>
                    <input type="text" name="smtp_username" class="form-control"
                           value="<?= htmlspecialchars(getSetting($settings, 'email', 'smtp_username', '')) ?>"
                           placeholder="ornek@gmail.com">
                </div>

                <div class="form-group">
                    <label class="form-label">SMTP Şifre</label>
                    <input type="password" name="smtp_password" class="form-control"
                           placeholder="Şifreyi değiştirmek için girin">
                    <small class="text-muted">Boş bırakırsanız mevcut şifre korunur</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Şifreleme</label>
                    <select name="smtp_encryption" class="form-control">
                        <?php $encryption = getSetting($settings, 'email', 'smtp_encryption', 'tls'); ?>
                        <option value="tls" <?= $encryption === 'tls' ? 'selected' : '' ?>>TLS</option>
                        <option value="ssl" <?= $encryption === 'ssl' ? 'selected' : '' ?>>SSL</option>
                        <option value="" <?= empty($encryption) ? 'selected' : '' ?>>Yok</option>
                    </select>
                </div>
            </div>

            <!-- From Settings -->
            <div class="settings-group">
                <div class="settings-group-title">Gönderen Bilgileri</div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Gönderen İsim</label>
                        <input type="text" name="email_from_name" class="form-control"
                               value="<?= htmlspecialchars(getSetting($settings, 'email', 'email_from_name', 'MINALIA Parfüm')) ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Gönderen Email</label>
                        <input type="email" name="email_from_address" class="form-control"
                               value="<?= htmlspecialchars(getSetting($settings, 'email', 'email_from_address', '')) ?>">
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Değişiklikleri Kaydet
                </button>
                <button type="button" class="btn-secondary" onclick="showTestEmailModal()">
                    <i class="fas fa-envelope"></i> Email Testi Gönder
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Test Email Modal -->
<div id="testEmailModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; padding: 2rem; border-radius: 12px; max-width: 500px; width: 90%;">
        <h3 style="margin-bottom: 1rem;">Email Testi</h3>
        <form method="POST" action="<?= ADMIN_URL ?>/settings/test-email">
            <div class="form-group">
                <label class="form-label">Test Email Adresi</label>
                <input type="email" name="test_email" class="form-control" required placeholder="test@example.com">
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" class="btn-save">Gönder</button>
                <button type="button" class="btn-secondary" onclick="hideTestEmailModal()">İptal</button>
            </div>
        </form>
    </div>
</div>

<script>
function showTestEmailModal() {
    document.getElementById('testEmailModal').style.display = 'flex';
}

function hideTestEmailModal() {
    document.getElementById('testEmailModal').style.display = 'none';
}
</script>
