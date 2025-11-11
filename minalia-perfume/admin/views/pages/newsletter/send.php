<div class="page-header">
    <h1 class="page-title">Newsletter Gönder</h1>
    <div class="page-breadcrumb">
        <a href="<?= ADMIN_URL ?>/newsletter">Newsletter</a>
        <span>/</span>
        <span>Gönder</span>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Newsletter Oluştur</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= ADMIN_URL ?>/newsletter/send" id="newsletterForm">
            <!-- Recipient Info -->
            <div style="background: #e3f2fd; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                    <i class="fas fa-info-circle" style="color: #1976d2;"></i>
                    <strong style="color: #1976d2;">Alıcı Bilgisi</strong>
                </div>
                <p style="margin: 0; color: #666; font-size: 0.9rem;">
                    Bu email <strong><?= $active_subscribers ?? 0 ?></strong> aktif aboneye gönderilecektir.
                </p>
            </div>

            <div class="form-group">
                <label for="subject">Email Konusu *</label>
                <input type="text" class="form-control" id="subject" name="subject" required
                       placeholder="Örn: Özel İndirim Fırsatları">
            </div>

            <div class="form-group">
                <label for="template">Şablon Seç</label>
                <select class="form-control" id="template" name="template">
                    <option value="">Özel Mesaj (Elle Yazılacak)</option>
                    <option value="discount">İndirim Kampanyası</option>
                    <option value="new_products">Yeni Ürünler</option>
                    <option value="seasonal">Mevsimsel Kampanya</option>
                </select>
            </div>

            <div class="form-group">
                <label for="message">Mesaj İçeriği *</label>
                <textarea class="form-control" id="message" name="message" rows="12" required
                          placeholder="Email içeriğinizi buraya yazın..."><?php
// Default template content
echo "Merhaba,\n\n";
echo "MINALIA Parfüm'den özel haberler ve fırsatlar sizlerle!\n\n";
echo "[İçeriğinizi buraya ekleyin]\n\n";
echo "Sevgilerimizle,\n";
echo "MINALIA Parfüm Ekibi\n\n";
echo "---\n";
echo "Bu emaili almak istemiyorsanız abonelikten çıkabilirsiniz.";
?></textarea>
                <small class="form-text">HTML formatını destekler. Temel HTML etiketlerini kullanabilirsiniz.</small>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="include_coupon" id="include_coupon">
                    <span style="margin-left: 0.5rem;">Email'e özel indirim kuponu ekle</span>
                </label>
            </div>

            <div id="couponOptions" style="display: none; padding: 1rem; background: #f5f5f5; border-radius: 8px; margin-top: 1rem;">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="coupon_code">Kupon Kodu</label>
                        <input type="text" class="form-control" id="coupon_code" name="coupon_code"
                               placeholder="Örn: NEWSLETTER20">
                    </div>
                    <div class="form-group">
                        <label for="discount_amount">İndirim Miktarı (%)</label>
                        <input type="number" class="form-control" id="discount_amount" name="discount_amount"
                               min="5" max="50" value="10">
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-top: 1.5rem;">
                <label>
                    <input type="checkbox" name="send_test" id="send_test">
                    <span style="margin-left: 0.5rem;">Önce test emaili gönder (sadece bana)</span>
                </label>
            </div>

            <div class="form-actions" style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" id="sendBtn">
                    <i class="fas fa-paper-plane"></i> Gönder
                </button>
                <button type="button" onclick="previewNewsletter()" class="btn btn-secondary">
                    <i class="fas fa-eye"></i> Önizleme
                </button>
                <a href="<?= ADMIN_URL ?>/newsletter" class="btn btn-secondary">
                    <i class="fas fa-times"></i> İptal
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Preview Modal -->
<div id="previewModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); z-index: 9999; padding: 2rem; overflow: auto;">
    <div style="max-width: 700px; margin: 0 auto; background: white; border-radius: 12px; padding: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="margin: 0;">Email Önizlemesi</h3>
            <button onclick="closePreview()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <div style="border: 1px solid #ddd; padding: 1.5rem; border-radius: 8px; background: #fafafa;">
            <div style="margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 2px solid #8B9A66;">
                <strong>Konu:</strong> <span id="previewSubject"></span>
            </div>
            <div id="previewContent" style="white-space: pre-wrap; line-height: 1.6;"></div>
        </div>
    </div>
</div>

<style>
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Show/hide coupon options
document.getElementById('include_coupon').addEventListener('change', function() {
    document.getElementById('couponOptions').style.display = this.checked ? 'block' : 'none';
});

// Template selection
document.getElementById('template').addEventListener('change', function() {
    const template = this.value;
    const messageField = document.getElementById('message');

    const templates = {
        'discount': `Merhaba,

MINALIA Parfüm'den özel indirim fırsatı!

Sadece sizin için hazırladığımız %20 indirim kuponu ile tüm premium parfümlerimizde geçerli özel fırsat!

Kupon Kodu: NEWSLETTER20

Hemen alışverişe başlayın ve favori parfümünüzü indirimli fiyatlarla edin.

Sevgilerimizle,
MINALIA Parfüm Ekibi`,

        'new_products': `Merhaba,

MINALIA Parfüm koleksiyonumuza yeni ürünler eklendi!

Bu ay koleksiyonumuza katılan özel parfümlerimizi keşfedin. Benzersiz kokular, kalıcı notalar...

Yeni ürünlerimizi görmek için hemen sitemizi ziyaret edin.

Sevgilerimizle,
MINALIA Parfüm Ekibi`,

        'seasonal': `Merhaba,

MINALIA Parfüm'den mevsimsel özel fırsatlar!

Yaz koleksiyonumuzda özel indirimler başladı. Ferah, hafif ve kalıcı kokularımızla yazın tadını çıkarın.

Hemen alışverişe başlayın!

Sevgilerimizle,
MINALIA Parfüm Ekibi`
    };

    if (templates[template]) {
        messageField.value = templates[template];
    }
});

// Preview newsletter
function previewNewsletter() {
    const subject = document.getElementById('subject').value;
    const message = document.getElementById('message').value;

    if (!subject || !message) {
        alert('Lütfen konu ve mesaj alanlarını doldurun.');
        return;
    }

    document.getElementById('previewSubject').textContent = subject;
    document.getElementById('previewContent').innerHTML = message;
    document.getElementById('previewModal').style.display = 'block';
}

function closePreview() {
    document.getElementById('previewModal').style.display = 'none';
}

// Form submission
document.getElementById('newsletterForm').addEventListener('submit', function(e) {
    const sendBtn = document.getElementById('sendBtn');
    const subscriberCount = <?= $active_subscribers ?? 0 ?>;
    const isTest = document.getElementById('send_test').checked;

    if (!isTest && subscriberCount > 0) {
        if (!confirm(`${subscriberCount} aboneye newsletter göndermek istediğinize emin misiniz? Bu işlem geri alınamaz.`)) {
            e.preventDefault();
            return;
        }
    }

    sendBtn.disabled = true;
    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Gönderiliyor...';
});
</script>
