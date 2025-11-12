<div style="text-align: center;">
    <div class="success-icon">✓</div>

    <h2 class="step-title">Kurulum Tamamlandı! 🎉</h2>
    <p class="step-description">
        MINALIA Parfüm E-Ticaret platformu başarıyla kuruldu ve kullanıma hazır!
    </p>
</div>

<div style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); padding: 2rem; border-radius: 12px; margin: 2rem 0;">
    <h3 style="margin: 0 0 1rem 0; color: #155724; text-align: center;">Sonraki Adımlar</h3>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1.5rem;">
        <div style="background: white; padding: 1.5rem; border-radius: 8px; text-align: center;">
            <div style="font-size: 2rem; margin-bottom: 0.5rem;">🎨</div>
            <strong style="display: block; margin-bottom: 0.5rem; color: #333;">Admin Paneli</strong>
            <p style="font-size: 0.875rem; color: #666; margin-bottom: 1rem;">
                Ürünleri, ayarları ve tüm sistemi yönetin
            </p>
            <a href="../admin" class="btn btn-primary">
                Admin Paneline Git →
            </a>
        </div>

        <div style="background: white; padding: 1.5rem; border-radius: 8px; text-align: center;">
            <div style="font-size: 2rem; margin-bottom: 0.5rem;">🛍️</div>
            <strong style="display: block; margin-bottom: 0.5rem; color: #333;">Site Önizleme</strong>
            <p style="font-size: 0.875rem; color: #666; margin-bottom: 1rem;">
                Müşteri görünümünü inceleyin
            </p>
            <a href="../" class="btn btn-success">
                Siteyi Görüntüle →
            </a>
        </div>
    </div>
</div>

<div style="background: #fff3e0; padding: 1.5rem; border-radius: 12px; border-left: 4px solid #ff9800;">
    <h4 style="margin: 0 0 1rem 0; color: #856404;">📝 Giriş Bilgileriniz</h4>
    <div style="background: white; padding: 1rem; border-radius: 6px; font-family: monospace; margin-bottom: 0.5rem;">
        <strong>Admin Panel:</strong> <?= htmlspecialchars($_SESSION['site_config']['site_url'] ?? '') ?>/admin
    </div>
    <div style="background: white; padding: 1rem; border-radius: 6px; font-family: monospace; margin-bottom: 0.5rem;">
        <strong>Kullanıcı Adı:</strong> <?= htmlspecialchars($_SESSION['site_config']['admin_username'] ?? '') ?>
    </div>
    <div style="background: white; padding: 1rem; border-radius: 6px; font-family: monospace;">
        <strong>Email:</strong> <?= htmlspecialchars($_SESSION['site_config']['admin_email'] ?? '') ?>
    </div>
    <p style="margin: 1rem 0 0 0; font-size: 0.875rem; color: #856404;">
        💡 Bu bilgileri not alın. Şifrenizi unutursanız database üzerinden sıfırlayabilirsiniz.
    </p>
</div>

<div style="background: #e3f2fd; padding: 1.5rem; border-radius: 12px; border-left: 4px solid #2196F3; margin-top: 1.5rem;">
    <h4 style="margin: 0 0 1rem 0; color: #0d47a1;">🎯 Yapmanız Gerekenler</h4>
    <ol style="margin: 0 0 0 1.5rem; color: #1565c0; font-size: 0.9375rem; line-height: 1.8;">
        <li><strong>Ayarlar:</strong> Admin → Settings → Tüm sekmeleri yapılandırın</li>
        <li><strong>Email:</strong> SMTP ayarlarını yapın ve test edin</li>
        <li><strong>Ödeme:</strong> iyzico/Stripe API anahtarlarını ekleyin</li>
        <li><strong>SEO:</strong> Meta tagları ve sosyal medya ayarlarını düzenleyin</li>
        <?php if (isset($_SESSION['site_config']['install_demo']) && $_SESSION['site_config']['install_demo']): ?>
        <li><strong>Demo Veriler:</strong> İsterseniz admin panelden silin veya düzenleyin</li>
        <?php else: ?>
        <li><strong>İçerik:</strong> Ürün, kategori ve marka ekleyin</li>
        <?php endif; ?>
        <li><strong>Güvenlik:</strong> <code>install</code> klasörünü sunucudan silin</li>
    </ol>
</div>

<div style="background: #f8d7da; padding: 1.5rem; border-radius: 12px; border-left: 4px solid #d32f2f; margin-top: 1.5rem;">
    <h4 style="margin: 0 0 0.5rem 0; color: #721c24;">⚠️ ÖNEMLİ GÜVENLİK UYARISI</h4>
    <p style="margin: 0; color: #721c24; font-size: 0.9375rem;">
        Kurulum tamamlandı. Güvenlik için <strong>install</strong> klasörünü sunucudan silmeniz veya taşımanız önemle önerilir!
    </p>
</div>

<div style="text-align: center; margin-top: 3rem; padding-top: 2rem; border-top: 2px solid #e0e0e0;">
    <p style="color: #666; margin-bottom: 1rem;">MINALIA Parfüm E-Ticaret Platformu</p>
    <div style="display: flex; gap: 1rem; justify-content: center;">
        <a href="../admin" class="btn btn-primary">Admin Paneli</a>
        <a href="../" class="btn btn-success">Siteyi Görüntüle</a>
    </div>
</div>

<script>
// Clear session storage
sessionStorage.clear();
</script>
