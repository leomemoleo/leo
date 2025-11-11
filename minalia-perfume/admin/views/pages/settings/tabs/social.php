<div class="card">
    <div class="card-header">
        <h3 class="card-title">Sosyal Medya Ayarları</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= ADMIN_URL ?>/settings/update-social">

            <div class="settings-group">
                <div class="settings-group-title">Sosyal Medya Hesapları</div>
                <p style="color: #666; margin-bottom: 1.5rem;">
                    Sosyal medya hesap linklerinizi ekleyin. Footer ve diğer alanlarda otomatik olarak görünecektir.
                </p>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fab fa-facebook" style="color: #1877f2; width: 24px;"></i> Facebook
                    </label>
                    <input type="url" name="social_facebook" class="form-control"
                           value="<?= htmlspecialchars(getSetting($settings, 'social', 'social_facebook', '')) ?>"
                           placeholder="https://facebook.com/minaliaperfume">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fab fa-instagram" style="color: #e4405f; width: 24px;"></i> Instagram
                    </label>
                    <input type="url" name="social_instagram" class="form-control"
                           value="<?= htmlspecialchars(getSetting($settings, 'social', 'social_instagram', '')) ?>"
                           placeholder="https://instagram.com/minaliaperfume">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fab fa-twitter" style="color: #1da1f2; width: 24px;"></i> Twitter / X
                    </label>
                    <input type="url" name="social_twitter" class="form-control"
                           value="<?= htmlspecialchars(getSetting($settings, 'social', 'social_twitter', '')) ?>"
                           placeholder="https://twitter.com/minaliaperfume">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fab fa-youtube" style="color: #ff0000; width: 24px;"></i> YouTube
                    </label>
                    <input type="url" name="social_youtube" class="form-control"
                           value="<?= htmlspecialchars(getSetting($settings, 'social', 'social_youtube', '')) ?>"
                           placeholder="https://youtube.com/@minaliaperfume">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fab fa-pinterest" style="color: #e60023; width: 24px;"></i> Pinterest
                    </label>
                    <input type="url" name="social_pinterest" class="form-control"
                           value="<?= htmlspecialchars(getSetting($settings, 'social', 'social_pinterest', '')) ?>"
                           placeholder="https://pinterest.com/minaliaperfume">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fab fa-tiktok" style="color: #000000; width: 24px;"></i> TikTok
                    </label>
                    <input type="url" name="social_tiktok" class="form-control"
                           value="<?= htmlspecialchars(getSetting($settings, 'social', 'social_tiktok', '')) ?>"
                           placeholder="https://tiktok.com/@minaliaperfume">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fab fa-linkedin" style="color: #0077b5; width: 24px;"></i> LinkedIn
                    </label>
                    <input type="url" name="social_linkedin" class="form-control"
                           value="<?= htmlspecialchars(getSetting($settings, 'social', 'social_linkedin', '')) ?>"
                           placeholder="https://linkedin.com/company/minaliaperfume">
                </div>
            </div>

            <!-- Social Media Preview -->
            <div style="padding: 1.5rem; background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%); border-radius: 12px; margin-top: 1.5rem;">
                <h4 style="margin: 0 0 1rem 0; color: #e65100;">
                    <i class="fas fa-eye"></i> Önizleme
                </h4>
                <p style="margin-bottom: 1rem; color: #666; font-size: 0.9375rem;">
                    Doldurduğunuz sosyal medya hesapları site footer'ında otomatik olarak görünecektir:
                </p>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <?php
                    $socialLinks = [
                        'facebook' => ['icon' => 'fab fa-facebook', 'color' => '#1877f2', 'name' => 'Facebook'],
                        'instagram' => ['icon' => 'fab fa-instagram', 'color' => '#e4405f', 'name' => 'Instagram'],
                        'twitter' => ['icon' => 'fab fa-twitter', 'color' => '#1da1f2', 'name' => 'Twitter'],
                        'youtube' => ['icon' => 'fab fa-youtube', 'color' => '#ff0000', 'name' => 'YouTube'],
                        'pinterest' => ['icon' => 'fab fa-pinterest', 'color' => '#e60023', 'name' => 'Pinterest'],
                        'tiktok' => ['icon' => 'fab fa-tiktok', 'color' => '#000000', 'name' => 'TikTok'],
                        'linkedin' => ['icon' => 'fab fa-linkedin', 'color' => '#0077b5', 'name' => 'LinkedIn']
                    ];

                    foreach ($socialLinks as $key => $info) {
                        $value = getSetting($settings, 'social', 'social_' . $key, '');
                        if (!empty($value)) {
                            echo '<div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: white; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">';
                            echo '<i class="' . $info['icon'] . '" style="color: ' . $info['color'] . '; font-size: 1.25rem;"></i>';
                            echo '<span style="font-size: 0.875rem; font-weight: 500;">' . $info['name'] . '</span>';
                            echo '</div>';
                        }
                    }
                    ?>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Değişiklikleri Kaydet
                </button>
            </div>
        </form>
    </div>
</div>
