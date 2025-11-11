<div class="page-container">
    <div class="container" style="max-width: 1200px; margin: 3rem auto; padding: 0 1.5rem;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">
            <!-- Contact Info -->
            <div>
                <h1 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; margin-bottom: 1.5rem; color: var(--text-dark);">
                    İletişim
                </h1>

                <?php if ($page): ?>
                    <div class="page-content" style="line-height: 1.8; color: #666; margin-bottom: 2rem;">
                        <?= $page['content'] ?>
                    </div>
                <?php endif; ?>

                <!-- Contact Cards -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem; margin-top: 2rem;">
                    <div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); display: flex; align-items: start; gap: 1rem;">
                        <div style="width: 50px; height: 50px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-phone" style="font-size: 1.25rem;"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 0.5rem; font-size: 1.1rem;">Telefon</h3>
                            <p style="margin: 0; color: #666;">+90 (212) 555 0123</p>
                            <p style="margin: 0; font-size: 0.9rem; color: #999;">Hafta içi 09:00 - 18:00</p>
                        </div>
                    </div>

                    <div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); display: flex; align-items: start; gap: 1rem;">
                        <div style="width: 50px; height: 50px; background: var(--gold); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fab fa-whatsapp" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 0.5rem; font-size: 1.1rem;">WhatsApp</h3>
                            <p style="margin: 0; color: #666;">+90 (555) 123 45 67</p>
                            <p style="margin: 0; font-size: 0.9rem; color: #999;">7/24 Destek</p>
                        </div>
                    </div>

                    <div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); display: flex; align-items: start; gap: 1rem;">
                        <div style="width: 50px; height: 50px; background: #333; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-envelope" style="font-size: 1.25rem;"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 0.5rem; font-size: 1.1rem;">Email</h3>
                            <p style="margin: 0; color: #666;">info@minalia.com.tr</p>
                            <p style="margin: 0; font-size: 0.9rem; color: #999;">24 saat içinde yanıt</p>
                        </div>
                    </div>

                    <div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); display: flex; align-items: start; gap: 1rem;">
                        <div style="width: 50px; height: 50px; background: #e74c3c; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-map-marker-alt" style="font-size: 1.25rem;"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 0.5rem; font-size: 1.1rem;">Adres</h3>
                            <p style="margin: 0; color: #666;">
                                Nispetiye Caddesi No: 45/3<br>
                                Etiler, Beşiktaş / İstanbul
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div>
                <div style="background: white; padding: 2.5rem; border-radius: 16px; box-shadow: 0 4px 16px rgba(0,0,0,0.08);">
                    <h2 style="font-family: 'Playfair Display', serif; font-size: 1.75rem; margin-bottom: 1.5rem; color: var(--text-dark);">
                        Mesaj Gönderin
                    </h2>

                    <form method="POST" style="display: flex; flex-direction: column; gap: 1.5rem;">
                        <div class="form-group">
                            <label class="form-label">Adınız Soyadınız *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email Adresiniz *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Konu *</label>
                            <select name="subject" class="form-control" required>
                                <option value="">Seçiniz...</option>
                                <option value="Sipariş">Sipariş Hakkında</option>
                                <option value="Ürün">Ürün Hakkında</option>
                                <option value="İade">İade/Değişim</option>
                                <option value="Teknik">Teknik Destek</option>
                                <option value="Öneri">Öneri/Şikayet</option>
                                <option value="Diğer">Diğer</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Mesajınız *</label>
                            <textarea name="message" class="form-control" rows="6" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            <i class="fas fa-paper-plane"></i> Mesaj Gönder
                        </button>
                    </form>
                </div>

                <!-- Social Media -->
                <div style="margin-top: 2rem; text-align: center;">
                    <h3 style="font-size: 1.1rem; margin-bottom: 1rem; color: #666;">Bizi Takip Edin</h3>
                    <div style="display: flex; gap: 1rem; justify-content: center;">
                        <a href="#" style="width: 45px; height: 45px; background: #E1306C; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: transform 0.3s;">
                            <i class="fab fa-instagram" style="font-size: 1.25rem;"></i>
                        </a>
                        <a href="#" style="width: 45px; height: 45px; background: #1877F2; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: transform 0.3s;">
                            <i class="fab fa-facebook" style="font-size: 1.25rem;"></i>
                        </a>
                        <a href="#" style="width: 45px; height: 45px; background: #1DA1F2; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: transform 0.3s;">
                            <i class="fab fa-twitter" style="font-size: 1.25rem;"></i>
                        </a>
                        <a href="#" style="width: 45px; height: 45px; background: #25D366; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: transform 0.3s;">
                            <i class="fab fa-whatsapp" style="font-size: 1.5rem;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media (max-width: 768px) {
    .page-container > .container > div {
        grid-template-columns: 1fr !important;
    }
}
</style>
