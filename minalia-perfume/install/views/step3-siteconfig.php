<h2 class="step-title">Site Yapılandırması 🎨</h2>
<p class="step-description">
    Site bilgilerinizi ve admin hesabınızı oluşturun.
</p>

<form method="POST">
    <h3 style="margin: 0 0 1rem 0; color: #333;">Site Bilgileri</h3>

    <div class="form-group">
        <label class="form-label">Site Adı</label>
        <input type="text" name="site_name" class="form-control" value="MINALIA Parfüm" required>
    </div>

    <div class="form-group">
        <label class="form-label">Site URL</label>
        <input type="url" name="site_url" class="form-control"
               value="<?= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . str_replace('/install/InstallController.php', '', $_SERVER['SCRIPT_NAME']) ?>"
               required>
        <small class="form-help">Sitenizin tam URL'i (http:// veya https:// ile)</small>
    </div>

    <h3 style="margin: 2rem 0 1rem 0; color: #333;">Admin Hesabı</h3>

    <div class="form-group">
        <label class="form-label">Admin Email</label>
        <input type="email" name="admin_email" class="form-control" placeholder="admin@minalia.com.tr" required>
        <small class="form-help">Admin paneline giriş için kullanılacak</small>
    </div>

    <div class="form-group">
        <label class="form-label">Admin Kullanıcı Adı</label>
        <input type="text" name="admin_username" class="form-control" value="admin" required>
        <small class="form-help">Minimum 3 karakter</small>
    </div>

    <div class="form-group">
        <label class="form-label">Admin Şifresi</label>
        <input type="password" name="admin_password" class="form-control" required minlength="6">
        <small class="form-help">Minimum 6 karakter, güçlü bir şifre kullanın</small>
    </div>

    <h3 style="margin: 2rem 0 1rem 0; color: #333;">Demo Veriler</h3>

    <label class="checkbox-group">
        <input type="checkbox" name="install_demo" value="1" checked>
        <div>
            <strong>Demo verileri yükle (Önerilir)</strong>
            <div style="font-size: 0.875rem; color: #666; margin-top: 0.25rem;">
                Örnek ürünler, kategoriler ve markalar yüklenecek. Sistemi hemen test edebilirsiniz.
            </div>
        </div>
    </label>

    <div style="padding: 1rem; background: #fff3e0; border-radius: 8px; border-left: 4px solid #ff9800; margin: 1.5rem 0;">
        <strong>💡 Demo Veriler:</strong>
        <ul style="margin: 0.5rem 0 0 1.5rem; font-size: 0.9375rem;">
            <li>10+ örnek parfüm ürünü</li>
            <li>5 kategori (Kadın, Erkek, Unisex, vb.)</li>
            <li>5 lüks marka</li>
            <li>Örnek banner ve içerikler</li>
        </ul>
        <p style="margin: 0.5rem 0 0 0; font-size: 0.875rem; color: #856404;">
            İsterseniz kurulumdan sonra admin panelden silebilirsiniz.
        </p>
    </div>

    <div class="btn-group">
        <a href="?step=2" class="btn btn-secondary">
            ← Geri
        </a>
        <button type="submit" class="btn btn-primary" style="flex: 1;">
            Kuruluma Başla →
        </button>
    </div>
</form>
