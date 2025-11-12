<h2 class="step-title">Veritabanı Ayarları 💾</h2>
<p class="step-description">
    Veritabanı bağlantı bilgilerinizi girin. Database yoksa otomatik oluşturulacaktır.
</p>

<form method="POST">
    <div class="form-group">
        <label class="form-label">Database Host</label>
        <input type="text" name="db_host" class="form-control" value="localhost" required>
        <small class="form-help">Genelde 'localhost' veya '127.0.0.1'</small>
    </div>

    <div class="form-group">
        <label class="form-label">Database Adı</label>
        <input type="text" name="db_name" class="form-control" value="minalia_perfume" required>
        <small class="form-help">Veritabanı adı (varsa kullanılır, yoksa oluşturulur)</small>
    </div>

    <div class="form-group">
        <label class="form-label">Database Kullanıcı Adı</label>
        <input type="text" name="db_user" class="form-control" value="root" required>
        <small class="form-help">MySQL kullanıcı adı</small>
    </div>

    <div class="form-group">
        <label class="form-label">Database Şifresi</label>
        <input type="password" name="db_pass" class="form-control">
        <small class="form-help">MySQL şifresi (boş bırakılabilir)</small>
    </div>

    <div style="padding: 1rem; background: #e3f2fd; border-radius: 8px; border-left: 4px solid #2196F3; margin: 1.5rem 0;">
        <strong>💡 Bilgi:</strong>
        <ul style="margin: 0.5rem 0 0 1.5rem; font-size: 0.9375rem;">
            <li>Sistem database bağlantısını test edecek</li>
            <li>Database yoksa otomatik oluşturulacak</li>
            <li>Tablolar otomatik yüklenecek</li>
        </ul>
    </div>

    <div class="btn-group">
        <a href="?step=1" class="btn btn-secondary">
            ← Geri
        </a>
        <button type="submit" class="btn btn-primary" style="flex: 1;">
            Bağlantıyı Test Et & Devam →
        </button>
    </div>
</form>
