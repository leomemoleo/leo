<div class="account-container">
    <div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1.5rem;">
        <h1 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; margin-bottom: 2rem;">
            Profil Bilgilerim
        </h1>

        <div style="display: grid; grid-template-columns: 250px 1fr; gap: 2rem;">
            <!-- Sidebar Menu -->
            <div>
                <?php include __DIR__ . '/../../components/account-sidebar.php'; ?>
            </div>

            <!-- Main Content -->
            <div>
                <div class="card">
                    <div class="card-header" style="padding: 1.5rem; border-bottom: 1px solid #eee;">
                        <h3 style="margin: 0;">Kişisel Bilgiler</h3>
                    </div>
                    <div class="card-body" style="padding: 2rem;">
                        <form method="POST" action="/account/profile">
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                                <!-- First Name -->
                                <div class="form-group">
                                    <label>Ad *</label>
                                    <input type="text" name="first_name" class="form-control"
                                           value="<?= htmlspecialchars($user['first_name'] ?? '') ?>"
                                           required>
                                </div>

                                <!-- Last Name -->
                                <div class="form-group">
                                    <label>Soyad *</label>
                                    <input type="text" name="last_name" class="form-control"
                                           value="<?= htmlspecialchars($user['last_name'] ?? '') ?>"
                                           required>
                                </div>

                                <!-- Email -->
                                <div class="form-group">
                                    <label>E-posta Adresi *</label>
                                    <input type="email" name="email" class="form-control"
                                           value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                                           required>
                                    <small class="form-text">E-posta adresiniz değiştirilebilir ancak doğrulama gerektirir.</small>
                                </div>

                                <!-- Phone -->
                                <div class="form-group">
                                    <label>Telefon</label>
                                    <input type="tel" name="phone" class="form-control"
                                           value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                                           placeholder="05XX XXX XX XX">
                                </div>

                                <!-- Birth Date -->
                                <div class="form-group">
                                    <label>Doğum Tarihi</label>
                                    <input type="date" name="birth_date" class="form-control"
                                           value="<?= htmlspecialchars($user['birth_date'] ?? '') ?>">
                                    <small class="form-text">Doğum günü kampanyalarından faydalanabilirsiniz.</small>
                                </div>

                                <!-- Gender -->
                                <div class="form-group">
                                    <label>Cinsiyet</label>
                                    <select name="gender" class="form-control">
                                        <option value="">Belirtmek İstemiyorum</option>
                                        <option value="male" <?= ($user['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Erkek</option>
                                        <option value="female" <?= ($user['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Kadın</option>
                                        <option value="other" <?= ($user['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Diğer</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Newsletter -->
                            <div class="form-group" style="margin-top: 1.5rem;">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="newsletter_subscription"
                                           <?= ($user['newsletter_subscription'] ?? 0) == 1 ? 'checked' : '' ?>>
                                    <span>Kampanya ve yeniliklerden haberdar olmak istiyorum</span>
                                </label>
                            </div>

                            <!-- SMS Notifications -->
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="sms_notifications"
                                           <?= ($user['sms_notifications'] ?? 0) == 1 ? 'checked' : '' ?>>
                                    <span>SMS ile sipariş bildirimleri almak istiyorum</span>
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #eee;">
                                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">
                                    <i class="fas fa-save"></i> Değişiklikleri Kaydet
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Change Password Section -->
                <div class="card" style="margin-top: 1.5rem;">
                    <div class="card-header" style="padding: 1.5rem; border-bottom: 1px solid #eee;">
                        <h3 style="margin: 0;">Şifre Değiştir</h3>
                    </div>
                    <div class="card-body" style="padding: 2rem;">
                        <form method="POST" action="/account/change-password">
                            <div style="max-width: 500px;">
                                <!-- Current Password -->
                                <div class="form-group">
                                    <label>Mevcut Şifre *</label>
                                    <input type="password" name="current_password" class="form-control"
                                           required>
                                </div>

                                <!-- New Password -->
                                <div class="form-group">
                                    <label>Yeni Şifre *</label>
                                    <input type="password" name="new_password" class="form-control"
                                           minlength="6" required>
                                    <small class="form-text">En az 6 karakter olmalıdır.</small>
                                </div>

                                <!-- Confirm New Password -->
                                <div class="form-group">
                                    <label>Yeni Şifre (Tekrar) *</label>
                                    <input type="password" name="confirm_password" class="form-control"
                                           minlength="6" required>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-outline" style="padding: 0.75rem 2rem;">
                                    <i class="fas fa-lock"></i> Şifreyi Değiştir
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Account Info -->
                <div class="card" style="margin-top: 1.5rem;">
                    <div class="card-header" style="padding: 1.5rem; border-bottom: 1px solid #eee;">
                        <h3 style="margin: 0;">Hesap Bilgileri</h3>
                    </div>
                    <div class="card-body" style="padding: 2rem;">
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                            <div>
                                <div style="color: #666; font-size: 0.875rem; margin-bottom: 0.25rem;">Kayıt Tarihi</div>
                                <div style="font-weight: 500;">
                                    <?= date('d.m.Y', strtotime($user['created_at'] ?? 'now')) ?>
                                </div>
                            </div>
                            <div>
                                <div style="color: #666; font-size: 0.875rem; margin-bottom: 0.25rem;">Son Giriş</div>
                                <div style="font-weight: 500;">
                                    <?= $user['last_login'] ? date('d.m.Y H:i', strtotime($user['last_login'])) : 'İlk giriş' ?>
                                </div>
                            </div>
                            <div>
                                <div style="color: #666; font-size: 0.875rem; margin-bottom: 0.25rem;">Hesap Tipi</div>
                                <div style="font-weight: 500;">
                                    <?= ucfirst($user['auth_provider'] ?? 'local') ?>
                                    <?php if (($user['auth_provider'] ?? 'local') !== 'local'): ?>
                                        <span style="color: #4CAF50;">
                                            <i class="fas fa-shield-alt"></i>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div>
                                <div style="color: #666; font-size: 0.875rem; margin-bottom: 0.25rem;">E-posta Durumu</div>
                                <div style="font-weight: 500;">
                                    <?php if (($user['email_verified'] ?? 0) == 1): ?>
                                        <span style="color: #4CAF50;">
                                            <i class="fas fa-check-circle"></i> Doğrulanmış
                                        </span>
                                    <?php else: ?>
                                        <span style="color: #ff9800;">
                                            <i class="fas fa-exclamation-triangle"></i> Doğrulanmamış
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Account -->
                        <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #eee;">
                            <button onclick="if(confirm('Hesabınızı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!')) { deleteAccount(); }"
                                    class="btn btn-outline" style="color: #f44336; border-color: #f44336;">
                                <i class="fas fa-trash"></i> Hesabı Sil
                            </button>
                            <small style="display: block; margin-top: 0.5rem; color: #999;">
                                Hesabınızı silmek tüm verilerinizi kalıcı olarak siler.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 0.9375rem;
    transition: all 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(122, 139, 92, 0.1);
}

.form-text {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #666;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.checkbox-label span {
    font-weight: 500;
}
</style>

<script>
function deleteAccount() {
    // Implement delete account via AJAX
    console.log('Delete account');
}
</script>
