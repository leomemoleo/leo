<footer class="footer">
    <!-- Newsletter Section -->
    <div class="newsletter-section">
        <div class="container">
            <div class="newsletter-content">
                <div class="newsletter-text">
                    <h2>E-Bültene Abone Ol</h2>
                    <p>Kampanya ve yeni ürünlerden ilk sen haberdar ol!</p>
                </div>
                <form class="newsletter-form" id="newsletter-form">
                    <input type="email" name="email" placeholder="E-posta adresiniz" required>
                    <button type="submit" class="btn btn-primary">Abone Ol</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer Main -->
    <div class="footer-main">
        <div class="container">
            <div class="footer-grid">
                <!-- About -->
                <div class="footer-col">
                    <h3 class="footer-title">MINALIA</h3>
                    <p class="footer-desc">
                        Lüks parfüm deneyiminin adresi. En seçkin markaların parfümleri,
                        kampanyalı fiyatlarla MINALIA'da.
                    </p>
                    <div class="footer-social">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="footer-col">
                    <h4 class="footer-heading">Hızlı Linkler</h4>
                    <ul class="footer-links">
                        <li><a href="<?= BASE_URL ?>/about">Hakkımızda</a></li>
                        <li><a href="<?= BASE_URL ?>/contact">İletişim</a></li>
                        <li><a href="<?= BASE_URL ?>/products">Tüm Ürünler</a></li>
                        <li><a href="<?= BASE_URL ?>/products?new=1">Yeni Ürünler</a></li>
                        <li><a href="<?= BASE_URL ?>/products?bestseller=1">Çok Satanlar</a></li>
                    </ul>
                </div>

                <!-- Customer Service -->
                <div class="footer-col">
                    <h4 class="footer-heading">Müşteri Hizmetleri</h4>
                    <ul class="footer-links">
                        <li><a href="<?= BASE_URL ?>/account">Hesabım</a></li>
                        <li><a href="<?= BASE_URL ?>/account/orders">Siparişlerim</a></li>
                        <li><a href="#">Kargo Takip</a></li>
                        <li><a href="#">İade ve Değişim</a></li>
                        <li><a href="#">SSS</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="footer-col">
                    <h4 class="footer-heading">İletişim</h4>
                    <ul class="footer-contact">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>İstanbul, Türkiye</span>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <span><?= $_ENV['SITE_PHONE'] ?? '+90 212 XXX XX XX' ?></span>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <span><?= $_ENV['SITE_EMAIL'] ?? 'info@minalia.com' ?></span>
                        </li>
                        <li>
                            <i class="fas fa-clock"></i>
                            <span>Pzt-Cmt: 09:00 - 18:00</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-content">
                <p>&copy; <?= date('Y') ?> MINALIA Parfüm. Tüm hakları saklıdır.</p>
                <div class="footer-bottom-links">
                    <a href="<?= BASE_URL ?>/privacy-policy">Gizlilik Politikası</a>
                    <a href="<?= BASE_URL ?>/terms-conditions">Kullanım Koşulları</a>
                </div>
                <div class="payment-methods">
                    <i class="fab fa-cc-visa"></i>
                    <i class="fab fa-cc-mastercard"></i>
                    <i class="fab fa-cc-amex"></i>
                    <i class="fab fa-cc-paypal"></i>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Scroll to Top Button -->
<button class="scroll-to-top" id="scrollToTop">
    <i class="fas fa-arrow-up"></i>
</button>
