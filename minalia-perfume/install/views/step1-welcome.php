<h2 class="step-title">Hoş Geldiniz! 🎉</h2>
<p class="step-description">
    MINALIA Parfüm E-Ticaret platformunu kurmak üzeresiniz. Kurulum süreci 5 dakikadan az sürecek!
</p>

<div class="feature-grid">
    <div class="feature-card">
        <div class="feature-card-icon">🎨</div>
        <h3>Modern Tasarım</h3>
        <p>Responsive ve kullanıcı dostu arayüz</p>
    </div>
    <div class="feature-card">
        <div class="feature-card-icon">⚡</div>
        <h3>Hızlı & Güvenli</h3>
        <p>Optimize edilmiş performans</p>
    </div>
    <div class="feature-card">
        <div class="feature-card-icon">🎛️</div>
        <h3>Kolay Yönetim</h3>
        <p>Her şey admin panelden</p>
    </div>
    <div class="feature-card">
        <div class="feature-card-icon">💳</div>
        <h3>Ödeme Entegrasyonu</h3>
        <p>iyzico, Stripe, PayPal</p>
    </div>
</div>

<h3 style="margin: 2rem 0 1rem 0; color: #333;">Sistem Gereksinimleri</h3>

<?php if (!$_SESSION['all_passed']): ?>
    <div class="alert alert-warning">
        <strong>⚠️ Uyarı:</strong> Bazı gereksinimler karşılanmıyor. Devam etmeden önce düzeltmeniz önerilir.
    </div>
<?php else: ?>
    <div class="alert alert-success">
        <strong>✅ Harika!</strong> Tüm sistem gereksinimleri karşılanıyor. Kuruluma devam edebilirsiniz.
    </div>
<?php endif; ?>

<table class="requirements-table">
    <?php foreach ($_SESSION['requirements'] as $requirement => $status): ?>
        <tr>
            <td><?= $requirement ?></td>
            <td style="text-align: right;">
                <?php if ($status): ?>
                    <span class="status-badge status-pass">✓ Tamam</span>
                <?php else: ?>
                    <span class="status-badge status-fail">✗ Eksik</span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<div class="btn-group">
    <a href="?step=2" class="btn btn-primary" style="flex: 1;">
        Kuruluma Başla →
    </a>
</div>
