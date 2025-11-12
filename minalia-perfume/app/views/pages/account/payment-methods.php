<div class="account-container">
    <div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1.5rem;">
        <h1 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; margin-bottom: 2rem;">
            Ödeme Yöntemlerim
        </h1>

        <div style="display: grid; grid-template-columns: 250px 1fr; gap: 2rem;">
            <!-- Sidebar Menu -->
            <div>
                <?php include __DIR__ . '/../../components/account-sidebar.php'; ?>
            </div>

            <!-- Main Content -->
            <div>
                <!-- Info Banner -->
                <div class="card" style="margin-bottom: 2rem; padding: 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                    <div style="display: flex; align-items: start; gap: 1rem;">
                        <i class="fas fa-shield-alt" style="font-size: 2rem;"></i>
                        <div>
                            <h3 style="margin: 0 0 0.5rem; font-size: 1.125rem;">Güvenli Ödeme</h3>
                            <p style="margin: 0; opacity: 0.9; font-size: 0.9375rem;">
                                Kart bilgileriniz PCI-DSS standartlarına uygun şekilde şifrelenmiş olarak saklanır.
                                Gerçek kart numaranız hiçbir zaman sistemimizde tutulmaz.
                            </p>
                        </div>
                    </div>
                </div>

                <?php if (empty($payment_methods)): ?>
                    <!-- No Payment Methods -->
                    <div class="card" style="text-align: center; padding: 4rem 2rem;">
                        <i class="fas fa-credit-card" style="font-size: 4rem; color: #ddd; margin-bottom: 1rem;"></i>
                        <h3 style="margin: 0 0 1rem;">Kayıtlı Kart Bulunamadı</h3>
                        <p style="color: #666; margin-bottom: 2rem;">Hızlı ödeme için kartınızı güvenli bir şekilde kaydedebilirsiniz.</p>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#addCardModal">
                            <i class="fas fa-plus"></i> Yeni Kart Ekle
                        </button>
                    </div>
                <?php else: ?>
                    <!-- Add Card Button -->
                    <div style="margin-bottom: 2rem; text-align: right;">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#addCardModal">
                            <i class="fas fa-plus"></i> Yeni Kart Ekle
                        </button>
                    </div>

                    <!-- Payment Methods Grid -->
                    <div style="display: grid; gap: 1.5rem;">
                        <?php foreach ($payment_methods as $method): ?>
                            <?php
                            $cardBrandIcons = [
                                'visa' => 'fab fa-cc-visa',
                                'mastercard' => 'fab fa-cc-mastercard',
                                'amex' => 'fab fa-cc-amex',
                                'troy' => 'fas fa-credit-card'
                            ];
                            $cardBrandColors = [
                                'visa' => '#1A1F71',
                                'mastercard' => '#EB001B',
                                'amex' => '#006FCF',
                                'troy' => '#00A651'
                            ];
                            $icon = $cardBrandIcons[$method['card_brand']] ?? 'fas fa-credit-card';
                            $brandColor = $cardBrandColors[$method['card_brand']] ?? '#333';

                            $expiryDate = sprintf('%02d/%d', $method['expiry_month'], $method['expiry_year']);
                            $isExpired = ($method['expiry_year'] < date('Y')) ||
                                        ($method['expiry_year'] == date('Y') && $method['expiry_month'] < date('n'));
                            ?>
                            <div class="card" style="position: relative; overflow: hidden;">
                                <!-- Default Badge -->
                                <?php if ($method['is_default']): ?>
                                    <div style="position: absolute; top: 12px; right: 12px; background: #4CAF50; color: white; padding: 0.375rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; z-index: 10;">
                                        <i class="fas fa-check"></i> Varsayılan
                                    </div>
                                <?php endif; ?>

                                <!-- Expired Badge -->
                                <?php if ($isExpired): ?>
                                    <div style="position: absolute; top: 12px; left: 12px; background: #f44336; color: white; padding: 0.375rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; z-index: 10;">
                                        <i class="fas fa-exclamation-triangle"></i> Süresi Dolmuş
                                    </div>
                                <?php endif; ?>

                                <div class="card-body" style="padding: 2rem;">
                                    <div style="display: grid; grid-template-columns: auto 1fr auto; gap: 2rem; align-items: center;">
                                        <!-- Card Brand Icon -->
                                        <div style="font-size: 3rem; color: <?= $brandColor ?>;">
                                            <i class="<?= $icon ?>"></i>
                                        </div>

                                        <!-- Card Info -->
                                        <div>
                                            <h3 style="margin: 0 0 0.5rem; font-size: 1.125rem; font-weight: 600;">
                                                <?= htmlspecialchars($method['card_alias']) ?>
                                            </h3>
                                            <div style="color: #666; font-size: 0.9375rem; margin-bottom: 0.5rem;">
                                                <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                                                <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                                                <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                                                <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                                                <?= htmlspecialchars($method['last_four_digits']) ?>
                                            </div>
                                            <div style="color: #888; font-size: 0.875rem;">
                                                <i class="far fa-calendar"></i>
                                                <?= $expiryDate ?> •
                                                <i class="far fa-user"></i>
                                                <?= htmlspecialchars($method['cardholder_name']) ?>
                                            </div>
                                            <?php if ($method['last_used_at']): ?>
                                                <div style="color: #aaa; font-size: 0.8125rem; margin-top: 0.25rem;">
                                                    Son kullanım: <?= date('d.m.Y', strtotime($method['last_used_at'])) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Actions -->
                                        <div style="display: flex; gap: 0.75rem;">
                                            <?php if (!$method['is_default'] && !$isExpired): ?>
                                                <form method="POST" action="/account/payment-methods/set-default/<?= $method['id'] ?>" style="display: inline;">
                                                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-primary" title="Varsayılan Yap">
                                                        <i class="fas fa-star"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                            <form method="POST" action="/account/payment-methods/delete/<?= $method['id'] ?>"
                                                  onsubmit="return confirm('Bu kartı silmek istediğinize emin misiniz?');"
                                                  style="display: inline;">
                                                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Kartı Sil">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- PCI DSS Info -->
                    <div class="card" style="margin-top: 2rem; padding: 1.5rem; background: #f8f9fa; border: 1px solid #e0e0e0;">
                        <div style="display: flex; align-items: start; gap: 1rem;">
                            <i class="fas fa-info-circle" style="color: #2196F3; font-size: 1.5rem; margin-top: 0.25rem;"></i>
                            <div>
                                <h4 style="margin: 0 0 0.5rem; font-size: 1rem;">Güvenlik Bilgisi</h4>
                                <p style="margin: 0; color: #666; font-size: 0.9375rem; line-height: 1.6;">
                                    Kart bilgileriniz yalnızca güvenli ödeme gateway'imiz (Iyzico) üzerinde şifrelenmiş token olarak saklanır.
                                    Gerçek kart numaranız, CVV kodunuz veya tam vade bilgileriniz hiçbir zaman sunucularımızda tutulmaz.
                                    Tüm işlemler 256-bit SSL şifreleme ile korunmaktadır.
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add Card Modal -->
<div class="modal fade" id="addCardModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="max-width: 500px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-credit-card"></i> Yeni Kart Ekle
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <!-- Warning: This is a demo form -->
                <div class="alert alert-warning" style="margin-bottom: 1.5rem;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Demo Uyarısı:</strong> Gerçek kart bilgilerini girmeyiniz! Bu form demo amaçlıdır.
                    Canlı ortamda Iyzico payment gateway entegrasyonu kullanılacaktır.
                </div>

                <form id="addCardForm" method="POST" action="/account/payment-methods/add">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    <input type="hidden" name="card_token" id="cardToken" value="">
                    <input type="hidden" name="card_brand" id="cardBrand" value="">
                    <input type="hidden" name="last_four_digits" id="lastFourDigits" value="">

                    <div class="form-group">
                        <label>Kart İsmi *</label>
                        <input type="text" name="card_alias" class="form-control"
                               placeholder="Örn: İş Bankası Kredi Kartım" required>
                        <small class="form-text text-muted">Kartınızı hatırlamanız için bir isim verin</small>
                    </div>

                    <div class="form-group">
                        <label>Kart Sahibi *</label>
                        <input type="text" name="cardholder_name" id="cardholderName" class="form-control"
                               placeholder="Kartta yazan ad soyad" required>
                    </div>

                    <div class="form-group">
                        <label>Kart Numarası * (Demo)</label>
                        <input type="text" id="cardNumber" class="form-control"
                               placeholder="0000 0000 0000 0000" maxlength="19" required>
                        <small class="form-text text-muted">Test: 4242 4242 4242 4242</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Son Kullanma Ay *</label>
                                <select name="expiry_month" class="form-control" required>
                                    <option value="">Ay Seç</option>
                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                        <option value="<?= $i ?>"><?= sprintf('%02d', $i) ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Son Kullanma Yıl *</label>
                                <select name="expiry_year" class="form-control" required>
                                    <option value="">Yıl Seç</option>
                                    <?php for ($i = date('Y'); $i <= date('Y') + 15; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_default" value="1">
                            Bu kartı varsayılan yap
                        </label>
                    </div>

                    <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" style="flex: 1;">
                            İptal
                        </button>
                        <button type="submit" class="btn btn-primary" style="flex: 1;">
                            <i class="fas fa-save"></i> Kartı Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
@media (max-width: 768px) {
    .account-container .container > div {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cardNumberInput = document.getElementById('cardNumber');

    // Format card number
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;

            // Detect card brand
            if (value.length >= 4) {
                const firstDigit = value.charAt(0);
                const firstTwoDigits = value.substring(0, 2);

                let brand = 'unknown';
                if (firstDigit === '4') {
                    brand = 'visa';
                } else if (['51', '52', '53', '54', '55'].includes(firstTwoDigits)) {
                    brand = 'mastercard';
                } else if (['34', '37'].includes(firstTwoDigits)) {
                    brand = 'amex';
                } else if (firstDigit === '9') {
                    brand = 'troy';
                }

                document.getElementById('cardBrand').value = brand;
            }

            // Set last 4 digits
            if (value.length >= 16) {
                document.getElementById('lastFourDigits').value = value.slice(-4);
            }
        });
    }

    // Form submission
    const addCardForm = document.getElementById('addCardForm');
    if (addCardForm) {
        addCardForm.addEventListener('submit', function(e) {
            // Generate demo token (in production, this would come from Iyzico)
            const timestamp = Date.now();
            const randomPart = Math.random().toString(36).substring(7);
            const demoToken = 'demo_token_' + timestamp + '_' + randomPart;

            document.getElementById('cardToken').value = demoToken;
        });
    }
});
</script>
