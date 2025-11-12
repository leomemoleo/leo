<div class="account-container">
    <div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1.5rem;">
        <h1 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; margin-bottom: 2rem;">Hesap Güvenliği</h1>

        <div style="display: grid; grid-template-columns: 250px 1fr; gap: 2rem;">
            <div><?php include __DIR__ . '/../../components/account-sidebar.php'; ?></div>

            <div>
                <!-- Password Section -->
                <div class="card" style="margin-bottom: 2rem;">
                    <div class="card-body" style="padding: 2rem;">
                        <h3 style="margin: 0 0 1rem;"><i class="fas fa-lock"></i> Şifre</h3>
                        <p style="color: #666; margin-bottom: 1.5rem;">Son değişiklik: <?= $user['updated_at'] ? date('d.m.Y', strtotime($user['updated_at'])) : 'Bilinmiyor' ?></p>
                        <a href="/account/profile#password" class="btn btn-outline-primary">
                            <i class="fas fa-key"></i> Şifremi Değiştir
                        </a>
                    </div>
                </div>

                <!-- Social Accounts -->
                <div class="card" style="margin-bottom: 2rem;">
                    <div class="card-body" style="padding: 2rem;">
                        <h3 style="margin: 0 0 1.5rem;"><i class="fas fa-link"></i> Bağlı Hesaplar</h3>

                        <?php if (empty($social_accounts)): ?>
                            <div style="text-align: center; padding: 2rem; background: #f8f9fa; border-radius: 8px;">
                                <i class="fas fa-unlink" style="font-size: 3rem; color: #ddd; margin-bottom: 1rem;"></i>
                                <p style="color: #666; margin: 0;">Henüz bağlı sosyal hesap yok</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($social_accounts as $account): ?>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; background: #f8f9fa; border-radius: 8px; margin-bottom: 1rem;">
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <?php if ($account['provider'] === 'google'): ?>
                                            <div style="width: 48px; height: 48px; background: #4285F4; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                <i class="fab fa-google" style="color: white; font-size: 1.5rem;"></i>
                                            </div>
                                            <div>
                                                <div style="font-weight: 600;">Google</div>
                                                <div style="color: #666; font-size: 0.875rem;"><?= htmlspecialchars($account['provider_email'] ?? 'Bağlı') ?></div>
                                            </div>
                                        <?php elseif ($account['provider'] === 'facebook'): ?>
                                            <div style="width: 48px; height: 48px; background: #1877F2; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                <i class="fab fa-facebook-f" style="color: white; font-size: 1.5rem;"></i>
                                            </div>
                                            <div>
                                                <div style="font-weight: 600;">Facebook</div>
                                                <div style="color: #666; font-size: 0.875rem;"><?= htmlspecialchars($account['provider_email'] ?? 'Bağlı') ?></div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <form method="POST" action="/auth/disconnect" onsubmit="return confirm('Bu hesap bağlantısını kaldırmak istediğinize emin misiniz?');">
                                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                        <input type="hidden" name="provider" value="<?= $account['provider'] ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-unlink"></i> Bağlantıyı Kaldır
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #ddd;">
                            <p style="color: #666; font-size: 0.9375rem; margin-bottom: 1rem;">Yeni hesap bağla:</p>
                            <div style="display: flex; gap: 1rem;">
                                <?php
                                $hasGoogle = false;
                                $hasFacebook = false;
                                foreach ($social_accounts as $acc) {
                                    if ($acc['provider'] === 'google') $hasGoogle = true;
                                    if ($acc['provider'] === 'facebook') $hasFacebook = true;
                                }
                                ?>
                                <?php if (!$hasGoogle): ?>
                                    <a href="/auth/google" class="btn btn-outline-secondary">
                                        <i class="fab fa-google"></i> Google ile Bağlan
                                    </a>
                                <?php endif; ?>
                                <?php if (!$hasFacebook): ?>
                                    <a href="/auth/facebook" class="btn btn-outline-secondary">
                                        <i class="fab fa-facebook-f"></i> Facebook ile Bağlan
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Info -->
                <div class="card" style="margin-bottom: 2rem;">
                    <div class="card-body" style="padding: 2rem;">
                        <h3 style="margin: 0 0 1.5rem;"><i class="fas fa-info-circle"></i> Hesap Bilgileri</h3>
                        <div style="display: grid; gap: 1rem;">
                            <div style="display: flex; justify-content: space-between; padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                                <span style="color: #666;">Kayıt Tarihi</span>
                                <strong><?= date('d.m.Y', strtotime($user['created_at'])) ?></strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                                <span style="color: #666;">Son Giriş</span>
                                <strong><?= $user['last_login'] ? date('d.m.Y H:i', strtotime($user['last_login'])) : 'Bilinmiyor' ?></strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                                <span style="color: #666;">Email Durumu</span>
                                <strong style="color: <?= $user['email_verified'] ? '#4CAF50' : '#FF9800' ?>;">
                                    <?= $user['email_verified'] ? '✓ Doğrulanmış' : '⚠ Doğrulanmamış' ?>
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KVKK Data Privacy -->
                <div class="card" id="kvkk" style="margin-bottom: 2rem;">
                    <div class="card-body" style="padding: 2rem;">
                        <h3 style="margin: 0 0 1rem;"><i class="fas fa-shield-alt"></i> KVKK ve Veri Gizliliği</h3>
                        <p style="color: #666; margin-bottom: 1.5rem;">
                            6698 sayılı Kişisel Verilerin Korunması Kanunu kapsamındaki haklarınızı yönetin.
                            <a href="/kvkk" target="_blank" style="color: var(--primary);">KVKK Aydınlatma Metni</a>
                        </p>

                        <!-- KVKK Consents Form -->
                        <form method="POST" action="/account/security/kvkk">
                            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                            <?php
                            // Get user preferences
                            global $db;
                            $prefSql = "SELECT * FROM user_preferences WHERE user_id = ?";
                            $prefStmt = $db->prepare($prefSql);
                            $prefStmt->execute([getCurrentUserId()]);
                            $prefs = $prefStmt->fetch(PDO::FETCH_ASSOC);

                            $marketingConsent = $prefs['marketing_consent'] ?? 0;
                            $personalizedAdsConsent = $prefs['personalized_ads_consent'] ?? 0;
                            $dataSharingConsent = $prefs['data_sharing_consent'] ?? 0;
                            $profilingConsent = $prefs['profiling_consent'] ?? 0;
                            ?>

                            <div style="display: grid; gap: 1rem; margin-bottom: 1.5rem;">
                                <label style="display: flex; align-items: start; gap: 1rem; padding: 1rem; background: #f8f9fa; border-radius: 8px; cursor: pointer;">
                                    <input type="checkbox" name="marketing_consent" <?= $marketingConsent ? 'checked' : '' ?> style="margin-top: 0.25rem;">
                                    <div>
                                        <strong>Pazarlama İletişimi</strong>
                                        <p style="color: #666; font-size: 0.875rem; margin: 0.25rem 0 0;">Kampanya, indirim ve yeni ürün bildirimleri almak istiyorum.</p>
                                    </div>
                                </label>

                                <label style="display: flex; align-items: start; gap: 1rem; padding: 1rem; background: #f8f9fa; border-radius: 8px; cursor: pointer;">
                                    <input type="checkbox" name="personalized_ads_consent" <?= $personalizedAdsConsent ? 'checked' : '' ?> style="margin-top: 0.25rem;">
                                    <div>
                                        <strong>Kişiselleştirilmiş Reklamlar</strong>
                                        <p style="color: #666; font-size: 0.875rem; margin: 0.25rem 0 0;">Alışveriş geçmişime göre özelleştirilmiş reklamlar görebilirim.</p>
                                    </div>
                                </label>

                                <label style="display: flex; align-items: start; gap: 1rem; padding: 1rem; background: #f8f9fa; border-radius: 8px; cursor: pointer;">
                                    <input type="checkbox" name="data_sharing_consent" <?= $dataSharingConsent ? 'checked' : '' ?> style="margin-top: 0.25rem;">
                                    <div>
                                        <strong>Veri Paylaşımı</strong>
                                        <p style="color: #666; font-size: 0.875rem; margin: 0.25rem 0 0;">Verilerimin iş ortaklarıyla (kargo, ödeme) paylaşılmasına izin veriyorum.</p>
                                    </div>
                                </label>

                                <label style="display: flex; align-items: start; gap: 1rem; padding: 1rem; background: #f8f9fa; border-radius: 8px; cursor: pointer;">
                                    <input type="checkbox" name="profiling_consent" <?= $profilingConsent ? 'checked' : '' ?> style="margin-top: 0.25rem;">
                                    <div>
                                        <strong>Profilleme ve Analiz</strong>
                                        <p style="color: #666; font-size: 0.875rem; margin: 0.25rem 0 0;">Alışveriş alışkanlıklarımın analiz edilmesine izin veriyorum.</p>
                                    </div>
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> İzinleri Kaydet
                            </button>
                        </form>

                        <hr style="margin: 2rem 0; border: none; border-top: 1px solid #eee;">

                        <!-- KVKK Rights -->
                        <div style="display: grid; gap: 1rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                                <div>
                                    <strong>Verilerimi İndir</strong>
                                    <p style="color: #666; font-size: 0.875rem; margin: 0.25rem 0 0;">Tüm kişisel verilerinizi JSON formatında indirin.</p>
                                </div>
                                <a href="/account/data/download" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-download"></i> İndir
                                </a>
                            </div>

                            <?php if ($prefs && $prefs['right_to_access_exercised_at']): ?>
                                <div style="padding: 1rem; background: #E8F5E9; border-radius: 8px; font-size: 0.875rem; color: #4CAF50;">
                                    <i class="fas fa-check-circle"></i> Son veri erişimi: <?= date('d.m.Y H:i', strtotime($prefs['right_to_access_exercised_at'])) ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($prefs && $prefs['right_to_portability_exercised_at']): ?>
                                <div style="padding: 1rem; background: #E8F5E9; border-radius: 8px; font-size: 0.875rem; color: #4CAF50;">
                                    <i class="fas fa-check-circle"></i> Son veri indirme: <?= date('d.m.Y H:i', strtotime($prefs['right_to_portability_exercised_at'])) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="card" style="border: 2px solid #f44336;">
                    <div class="card-body" style="padding: 2rem;">
                        <h3 style="margin: 0 0 1rem; color: #f44336;"><i class="fas fa-exclamation-triangle"></i> Tehlikeli Bölge</h3>
                        <p style="color: #666; margin-bottom: 1.5rem;">
                            Hesabınızı silmek kalıcıdır ve geri alınamaz. KVKK gereği hesap silme talebiniz 30 gün içinde işleme alınacaktır.
                            Bu süre içinde fikrinizi değiştirirseniz müşteri hizmetleri ile iletişime geçebilirsiniz.
                        </p>

                        <?php
                        // Check for pending deletion request
                        global $db;
                        $deletionSql = "SELECT * FROM account_deletion_requests WHERE user_id = ? AND status = 'pending' ORDER BY requested_at DESC LIMIT 1";
                        $deletionStmt = $db->prepare($deletionSql);
                        $deletionStmt->execute([getCurrentUserId()]);
                        $pendingDeletion = $deletionStmt->fetch(PDO::FETCH_ASSOC);
                        ?>

                        <?php if ($pendingDeletion): ?>
                            <div style="padding: 1.5rem; background: #FFEBEE; border-radius: 8px; margin-bottom: 1rem;">
                                <h4 style="margin: 0 0 0.5rem; color: #f44336;"><i class="fas fa-exclamation-circle"></i> Hesap Silme Talebi Beklemede</h4>
                                <p style="margin: 0.5rem 0; font-size: 0.9375rem;">
                                    Hesabınız <strong><?= date('d.m.Y H:i', strtotime($pendingDeletion['deletion_scheduled_at'])) ?></strong> tarihinde silinecek.
                                </p>
                                <p style="margin: 0.5rem 0; font-size: 0.875rem; color: #666;">
                                    Talebinizi iptal etmek için müşteri hizmetleri ile iletişime geçin.
                                </p>
                            </div>
                        <?php else: ?>
                            <button class="btn btn-outline-danger" onclick="showDeletionModal()">
                                <i class="fas fa-trash-alt"></i> Hesabımı Sil
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Account Deletion Modal -->
<div id="deletionModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; padding: 2rem;">
        <h3 style="margin: 0 0 1rem; color: #f44336;">
            <i class="fas fa-exclamation-triangle"></i> Hesap Silme Onayı
        </h3>

        <div style="background: #FFEBEE; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
            <p style="margin: 0; font-size: 0.9375rem; color: #666;">
                <strong>KVKK Uyarısı:</strong> Hesabınız 30 gün sonra kalıcı olarak silinecektir.
                Bu süre içinde talebinizi iptal edebilirsiniz.
            </p>
        </div>

        <form method="POST" action="/account/security/delete-account">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                    Hesabınızı neden silmek istiyorsunuz? (İsteğe bağlı)
                </label>
                <textarea name="deletion_reason" rows="4" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px; font-family: inherit;" placeholder="Gerekçenizi yazabilirsiniz..."></textarea>
            </div>

            <div style="background: #FFF3CD; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.875rem;">
                <p style="margin: 0;"><strong>Silinecek veriler:</strong></p>
                <ul style="margin: 0.5rem 0 0; padding-left: 1.5rem;">
                    <li>Profil bilgileriniz</li>
                    <li>Adres ve iletişim bilgileriniz</li>
                    <li>Sipariş geçmişiniz (son 30 gün hariç - yasal saklama süresi)</li>
                    <li>Favori ürünler ve kuponlarınız</li>
                    <li>Yorumlarınız</li>
                    <li>Sadakat puanlarınız</li>
                </ul>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="hideDeletionModal()" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i> İptal
                </button>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash-alt"></i> Silme Talebi Oluştur
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showDeletionModal() {
    document.getElementById('deletionModal').style.display = 'flex';
}

function hideDeletionModal() {
    document.getElementById('deletionModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('deletionModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        hideDeletionModal();
    }
});
</script>
