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

                <!-- Danger Zone -->
                <div class="card" style="border: 2px solid #f44336;">
                    <div class="card-body" style="padding: 2rem;">
                        <h3 style="margin: 0 0 1rem; color: #f44336;"><i class="fas fa-exclamation-triangle"></i> Tehlikeli Bölge</h3>
                        <p style="color: #666; margin-bottom: 1.5rem;">
                            Hesabınızı silmek kalıcıdır ve geri alınamaz. Tüm siparişleriniz, puanlarınız ve verileriniz silinecektir.
                        </p>
                        <button class="btn btn-outline-danger" onclick="alert('Hesap silme işlemi için lütfen müşteri hizmetleri ile iletişime geçin.')">
                            <i class="fas fa-trash-alt"></i> Hesabımı Sil
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
