<h2 class="step-title">Kurulum Yapılıyor... ⚙️</h2>
<p class="step-description">
    MINALIA platformu kuruluyor. Bu işlem birkaç saniye sürecek.
</p>

<div style="padding: 2rem; background: #f8f9fa; border-radius: 12px; text-align: center;">
    <div style="margin: 2rem 0;">
        <div style="width: 60px; height: 60px; border: 6px solid #e0e0e0; border-top-color: #7a8b5c; border-radius: 50%; margin: 0 auto; animation: spin 1s linear infinite;"></div>
    </div>

    <h3 style="color: #333; margin-bottom: 1rem;">Kurulum İşlemleri</h3>

    <div style="text-align: left; max-width: 500px; margin: 0 auto;">
        <div style="padding: 0.75rem; margin: 0.5rem 0; background: white; border-radius: 6px; display: flex; align-items: center; gap: 0.75rem;">
            <span style="color: #4CAF50;">✓</span>
            <span>Database bağlantısı</span>
        </div>
        <div style="padding: 0.75rem; margin: 0.5rem 0; background: white; border-radius: 6px; display: flex; align-items: center; gap: 0.75rem;">
            <span style="color: #4CAF50;">✓</span>
            <span>Tabloları oluştur</span>
        </div>
        <div style="padding: 0.75rem; margin: 0.5rem 0; background: white; border-radius: 6px; display: flex; align-items: center; gap: 0.75rem;">
            <span style="color: #4CAF50;">✓</span>
            <span>Ayarları yükle</span>
        </div>
        <div style="padding: 0.75rem; margin: 0.5rem 0; background: white; border-radius: 6px; display: flex; align-items: center; gap: 0.75rem;">
            <span style="color: #4CAF50;">✓</span>
            <span>Admin hesabı oluştur</span>
        </div>
        <?php if (isset($_SESSION['site_config']['install_demo']) && $_SESSION['site_config']['install_demo']): ?>
        <div style="padding: 0.75rem; margin: 0.5rem 0; background: white; border-radius: 6px; display: flex; align-items: center; gap: 0.75rem;">
            <span style="color: #4CAF50;">✓</span>
            <span>Demo verileri yükle</span>
        </div>
        <?php endif; ?>
        <div style="padding: 0.75rem; margin: 0.5rem 0; background: white; border-radius: 6px; display: flex; align-items: center; gap: 0.75rem;">
            <span style="color: #4CAF50;">✓</span>
            <span>Yapılandırma dosyası oluştur</span>
        </div>
    </div>
</div>

<style>
@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

<form method="POST" id="installForm">
    <div class="btn-group">
        <button type="submit" class="btn btn-primary" style="flex: 1;">
            Kurulumu Başlat 🚀
        </button>
    </div>
</form>

<script>
// Auto-submit after 2 seconds
setTimeout(function() {
    // Show user feedback
    document.querySelector('.step-description').textContent = 'Kurulum başlatılıyor...';
}, 1000);
</script>
