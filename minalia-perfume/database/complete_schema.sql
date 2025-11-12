-- MINALIA Parfüm E-Ticaret Platformu
-- Veritabanı Şeması
-- Oluşturulma Tarihi: 2025-11-11

-- Veritabanı oluştur

-- 1. Kullanıcılar Tablosu
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    phone VARCHAR(20),
    birth_date DATE,
    gender ENUM('male', 'female', 'other'),
    newsletter_subscribed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    verification_token VARCHAR(255),
    reset_token VARCHAR(255),
    reset_token_expire TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Kategoriler
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    image_url VARCHAR(500),
    parent_id INT,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_parent_id (parent_id),
    INDEX idx_sort_order (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Markalar
CREATE TABLE brands (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    logo_url VARCHAR(500),
    description TEXT,
    country VARCHAR(100),
    is_featured BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_featured (is_featured)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Ürünler
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sku VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    brand_id INT,
    category_id INT,
    description TEXT,
    short_description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    sale_price DECIMAL(10, 2),
    stock_quantity INT DEFAULT 0,
    fragrance_notes JSON,
    gender ENUM('men', 'women', 'unisex'),
    launch_year INT,
    perfumer VARCHAR(255),
    is_featured BOOLEAN DEFAULT FALSE,
    is_new BOOLEAN DEFAULT FALSE,
    is_bestseller BOOLEAN DEFAULT FALSE,
    rating DECIMAL(2, 1) DEFAULT 0,
    review_count INT DEFAULT 0,
    view_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_brand_id (brand_id),
    INDEX idx_category_id (category_id),
    INDEX idx_featured (is_featured),
    INDEX idx_new (is_new),
    INDEX idx_bestseller (is_bestseller),
    INDEX idx_price (price),
    INDEX idx_rating (rating),
    FULLTEXT INDEX idx_fulltext (name, description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Ürün Varyantları (Boyutlar)
CREATE TABLE product_variants (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    size VARCHAR(20) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stock_quantity INT DEFAULT 0,
    sku_suffix VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product_id (product_id),
    UNIQUE KEY unique_product_size (product_id, size)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Ürün Görselleri
CREATE TABLE product_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    alt_text VARCHAR(255),
    is_primary BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product_id (product_id),
    INDEX idx_sort_order (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Sepet
CREATE TABLE cart_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    session_id VARCHAR(100),
    product_id INT NOT NULL,
    variant_id INT,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_session_id (session_id),
    INDEX idx_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Siparişler
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    user_id INT,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded') DEFAULT 'pending',
    subtotal DECIMAL(10, 2) NOT NULL,
    shipping_fee DECIMAL(10, 2) DEFAULT 0,
    tax DECIMAL(10, 2) DEFAULT 0,
    discount DECIMAL(10, 2) DEFAULT 0,
    total DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50),
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    shipping_address JSON,
    billing_address JSON,
    customer_notes TEXT,
    admin_notes TEXT,
    tracking_number VARCHAR(100),
    shipped_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_order_number (order_number),
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Sipariş Detayları
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT,
    variant_id INT,
    product_name VARCHAR(255) NOT NULL,
    product_sku VARCHAR(100),
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE SET NULL,
    INDEX idx_order_id (order_id),
    INDEX idx_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Yorumlar
CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK(rating >= 1 AND rating <= 5),
    title VARCHAR(255),
    comment TEXT,
    is_verified_purchase BOOLEAN DEFAULT FALSE,
    is_approved BOOLEAN DEFAULT FALSE,
    helpful_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_product_id (product_id),
    INDEX idx_user_id (user_id),
    INDEX idx_rating (rating),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Favoriler
CREATE TABLE wishlist (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist (user_id, product_id),
    INDEX idx_user_id (user_id),
    INDEX idx_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Adresler
CREATE TABLE addresses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    address_type ENUM('billing', 'shipping', 'both') DEFAULT 'both',
    full_name VARCHAR(200) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address_line1 VARCHAR(255) NOT NULL,
    address_line2 VARCHAR(255),
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100),
    postal_code VARCHAR(20) NOT NULL,
    country VARCHAR(100) NOT NULL DEFAULT 'Turkey',
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Kuponlar
CREATE TABLE coupons (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    discount_type ENUM('percentage', 'fixed') NOT NULL,
    discount_value DECIMAL(10, 2) NOT NULL,
    min_order_amount DECIMAL(10, 2) DEFAULT 0,
    max_discount DECIMAL(10, 2),
    usage_limit INT,
    usage_count INT DEFAULT 0,
    valid_from TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    valid_until TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Newsletter Aboneleri
CREATE TABLE newsletter_subscribers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    unsubscribed_at TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. Site Ayarları
CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type VARCHAR(50) DEFAULT 'text',
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 16. Admin Kullanıcıları
CREATE TABLE admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(200),
    role ENUM('admin', 'manager', 'editor') DEFAULT 'editor',
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 17. Aktivite Logları
CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    admin_id INT,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_admin_id (admin_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Varsayılan admin kullanıcısı (şifre: admin123)
INSERT INTO admin_users (username, email, password, full_name, role) VALUES
('admin', 'admin@minalia.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin');

-- Varsayılan site ayarları
INSERT INTO settings (setting_key, setting_value, setting_type, description) VALUES
('site_name', 'MINALIA Parfüm', 'text', 'Site adı'),
('site_tagline', 'Lüks Parfüm Deneyimi', 'text', 'Site sloganı'),
('site_email', 'info@minalia.com', 'email', 'Site iletişim emaili'),
('site_phone', '+90 212 XXX XX XX', 'text', 'Site telefon numarası'),
('currency', 'TRY', 'text', 'Para birimi'),
('tax_rate', '20', 'number', 'KDV oranı (%)'),
('free_shipping_threshold', '500', 'number', 'Ücretsiz kargo limiti'),
('primary_color', '#7A8B5C', 'color', 'Ana renk'),
('secondary_color', '#D4AF37', 'color', 'İkincil renk');
-- Additional Tables for New Features
-- MINALIA Parfüm E-Ticaret Platformu


-- Loyalty Points Table
CREATE TABLE IF NOT EXISTS loyalty_points (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    points INT NOT NULL,
    type ENUM('purchase', 'signup', 'birthday', 'review', 'referral', 'redemption') NOT NULL,
    description TEXT,
    reference_id INT,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_type (type),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Abandoned Cart Emails Log
CREATE TABLE IF NOT EXISTS abandoned_cart_emails (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    opened_at TIMESTAMP NULL,
    clicked_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_sent_at (sent_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- AI Recommendations Log
CREATE TABLE IF NOT EXISTS ai_recommendations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    product_id INT NOT NULL,
    reason TEXT,
    clicked BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Stock Alert Requests
CREATE TABLE IF NOT EXISTS stock_alerts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    product_id INT NOT NULL,
    email VARCHAR(255) NOT NULL,
    notified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product_id (product_id),
    INDEX idx_notified (notified)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User Preferences
CREATE TABLE IF NOT EXISTS user_preferences (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    preferred_notes JSON,
    preferred_brands JSON,
    budget_range VARCHAR(50),
    occasion_type VARCHAR(50),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Coupon Usage Log
CREATE TABLE IF NOT EXISTS coupon_usage (
    id INT PRIMARY KEY AUTO_INCREMENT,
    coupon_id INT NOT NULL,
    user_id INT,
    order_id INT,
    discount_amount DECIMAL(10, 2),
    used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
    INDEX idx_coupon_id (coupon_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add coupon fields to orders table
ALTER TABLE orders
ADD COLUMN IF NOT EXISTS coupon_code VARCHAR(50),
ADD COLUMN IF NOT EXISTS coupon_discount DECIMAL(10, 2) DEFAULT 0,
ADD COLUMN IF NOT EXISTS points_used INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS points_discount DECIMAL(10, 2) DEFAULT 0;

-- Sample Loyalty Points for test user
INSERT INTO loyalty_points (user_id, points, type, description) VALUES
(1, 100, 'signup', 'Hoş geldin bonusu'),
(1, 50, 'review', 'Ürün yorumu için puan'),
(1, 150, 'purchase', 'Sipariş için puan');

-- Sample Coupons
INSERT INTO coupons (code, description, discount_type, discount_value, min_order_amount, max_discount, usage_limit, valid_from, valid_until, is_active) VALUES
('HOSGELDIN15', 'Yeni üyelere özel %15 indirim', 'percentage', 15, 0, 500, 1000, NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR), 1),
('SEPET10', 'Terk edilmiş sepet için %10 indirim', 'percentage', 10, 300, 200, NULL, NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH), 1),
('YAZ2025', 'Yaz kampanyası', 'percentage', 20, 500, 1000, 500, NOW(), DATE_ADD(NOW(), INTERVAL 3 MONTH), 1),
('MINALIA100', '100 TL indirim kuponu', 'fixed', 100, 1000, NULL, 200, NOW(), DATE_ADD(NOW(), INTERVAL 6 MONTH), 1),
('DOGUMGUNU2025', 'Doğum günü indirimi', 'percentage', 20, 0, 300, NULL, NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR), 1);
-- Contact Messages Table
-- MINALIA Parfüm E-Ticaret Platformu


CREATE TABLE IF NOT EXISTS contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    replied_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Pages Table for Static Content Management
-- MINALIA Parfüm E-Ticaret Platformu


-- Create pages table
CREATE TABLE IF NOT EXISTS pages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content LONGTEXT,
    meta_title VARCHAR(255),
    meta_description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default pages
INSERT INTO pages (title, slug, content, meta_title, meta_description, is_active) VALUES
-- About Us Page
('Hakkımızda', 'hakkimizda', '<h2>MINALIA Hakkında</h2>

<p>MINALIA, lüks parfüm dünyasının seçkin markalarını Türkiye\'deki parfüm severlere ulaştırmak amacıyla kurulmuştur. 2020 yılından bu yana, prestijli parfüm markalarının resmi distribütörü olarak hizmet vermekteyiz.</p>

<h3>Misyonumuz</h3>
<p>En kaliteli, orijinal ve seçkin parfümleri müşterilerimize en uygun fiyatlarla sunmak ve her alışverişte unutulmaz bir deneyim yaşatmaktır.</p>

<h3>Vizyonumuz</h3>
<p>Türkiye\'nin en güvenilir ve tercih edilen online parfüm platformu olmak.</p>

<h3>Değerlerimiz</h3>
<ul>
    <li><strong>Orijinallik:</strong> Tüm ürünlerimiz %100 orijinal ve garantilidir</li>
    <li><strong>Kalite:</strong> Sadece prestijli ve kaliteli markalarla çalışırız</li>
    <li><strong>Güvenilirlik:</strong> Müşteri memnuniyeti her şeyden önce gelir</li>
    <li><strong>Hız:</strong> Siparişleriniz aynı gün kargoya verilir</li>
</ul>

<h3>Neden MINALIA?</h3>
<ul>
    <li>%100 Orijinal Ürün Garantisi</li>
    <li>Ücretsiz Kargo (500 TL ve üzeri)</li>
    <li>Hediye Paketleme Seçeneği</li>
    <li>Kolay İade ve Değişim</li>
    <li>Puan ve İndirim Kampanyaları</li>
    <li>AI Destekli Kişiselleştirilmiş Öneriler</li>
</ul>

<h3>İletişim</h3>
<p>Sorularınız için bizimle iletişime geçebilirsiniz:</p>
<ul>
    <li>Email: info@minalia.com.tr</li>
    <li>Telefon: +90 (212) 555 0123</li>
    <li>WhatsApp: +90 (555) 123 45 67</li>
</ul>',
'MINALIA Hakkında | Lüks Parfüm Mağazası',
'MINALIA, Türkiye\'nin önde gelen lüks parfüm platformu. 2020\'den beri orijinal ve prestijli parfümler sunuyoruz.',
1),

-- Contact Page
('İletişim', 'iletisim', '<h2>Bize Ulaşın</h2>

<p>Sorularınız, önerileriniz veya talepleriniz için bizimle iletişime geçmekten çekinmeyin. Müşteri memnuniyeti bizim önceliğimizdir ve size en kısa sürede geri dönüş yapmak için buradayız.</p>

<h3>İletişim Bilgileri</h3>

<div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0;">
    <p><strong>Telefon:</strong><br>+90 (212) 555 0123<br><em>Hafta içi 09:00 - 18:00</em></p>

    <p><strong>WhatsApp:</strong><br>+90 (555) 123 45 67<br><em>7/24 Destek</em></p>

    <p><strong>Email:</strong><br>
    Genel Sorular: info@minalia.com.tr<br>
    Sipariş Takibi: siparis@minalia.com.tr<br>
    Kurumsal: kurumsal@minalia.com.tr</p>

    <p><strong>Adres:</strong><br>
    MINALIA Parfümeri A.Ş.<br>
    Nispetiye Caddesi No: 45/3<br>
    Etiler, Beşiktaş / İstanbul<br>
    34340</p>
</div>

<h3>Çalışma Saatleri</h3>
<ul>
    <li><strong>Pazartesi - Cuma:</strong> 09:00 - 18:00</li>
    <li><strong>Cumartesi:</strong> 10:00 - 16:00</li>
    <li><strong>Pazar:</strong> Kapalı</li>
</ul>

<h3>Sıkça Sorulan Sorular</h3>

<p><strong>Kargo ne kadar sürede gelir?</strong><br>
İstanbul içi 1-2 iş günü, diğer iller için 2-3 iş günü içinde kargoya teslim edilir.</p>

<p><strong>Ürünler orijinal mi?</strong><br>
Evet, tüm ürünlerimiz %100 orijinal olup, yetkili distribütörlerden temin edilmektedir.</p>

<p><strong>İade ve değişim şartları nelerdir?</strong><br>
Ürünün teslim tarihinden itibaren 14 gün içinde, kullanılmamış ve ambalajı açılmamış ürünlerde iade kabul edilir.</p>

<p><strong>Kargo ücretsiz mi?</strong><br>
500 TL ve üzeri alışverişlerde kargo ücretsizdir.</p>

<h3>Sosyal Medya</h3>
<p>Bizi sosyal medya hesaplarımızdan takip edebilir, kampanyalardan haberdar olabilirsiniz:</p>
<ul>
    <li>Instagram: @minalia_parfum</li>
    <li>Facebook: /minaliaturkiye</li>
    <li>Twitter: @minalia_tr</li>
</ul>',
'İletişim | MINALIA',
'MINALIA ile iletişime geçin. Telefon, email, WhatsApp üzerinden 7/24 destek.',
1),

-- Privacy Policy
('Gizlilik Politikası', 'gizlilik-politikasi', '<h2>Gizlilik Politikası</h2>

<p><em>Son Güncelleme: 11 Kasım 2025</em></p>

<p>MINALIA olarak, kişisel verilerinizin gizliliğine saygı duyuyoruz. Bu politika, hangi verileri topladığımızı, nasıl kullandığımızı ve koruduğumuzu açıklamaktadır.</p>

<h3>1. Toplanan Veriler</h3>
<ul>
    <li>Ad, soyad, email adresi, telefon numarası</li>
    <li>Teslimat ve fatura adresleri</li>
    <li>Sipariş geçmişi ve tercihler</li>
    <li>Çerezler ve site kullanım verileri</li>
</ul>

<h3>2. Verilerin Kullanımı</h3>
<p>Topladığımız veriler şu amaçlarla kullanılır:</p>
<ul>
    <li>Sipariş işlemleri ve teslimat</li>
    <li>Müşteri hizmetleri desteği</li>
    <li>Kişiselleştirilmiş ürün önerileri</li>
    <li>Kampanya ve bilgilendirme e-postaları (izninizle)</li>
</ul>

<h3>3. Veri Güvenliği</h3>
<p>Kişisel verileriniz SSL şifrelemesi ile korunmaktadır. Ödeme bilgileriniz güvenli ödeme sağlayıcıları üzerinden işlenir ve sunucularımızda saklanmaz.</p>

<h3>4. Üçüncü Taraflarla Paylaşım</h3>
<p>Verileriniz yalnızca yasal zorunluluklar veya sipariş teslimatı için gerekli kargo firmaları ile paylaşılır. Pazarlama amaçlı üçüncü taraflara satılmaz.</p>

<h3>5. Çerezler</h3>
<p>Sitemiz, kullanıcı deneyimini iyileştirmek için çerezler kullanır. Tarayıcı ayarlarınızdan çerezleri yönetebilirsiniz.</p>

<h3>6. Haklarınız</h3>
<p>KVKK kapsamında aşağıdaki haklara sahipsiniz:</p>
<ul>
    <li>Verilerinize erişim</li>
    <li>Verilerin düzeltilmesi</li>
    <li>Verilerin silinmesi</li>
    <li>İşleme itiraz</li>
</ul>

<p>Sorularınız için: <a href="mailto:kvkk@minalia.com.tr">kvkk@minalia.com.tr</a></p>',
'Gizlilik Politikası | MINALIA',
'MINALIA kişisel veri gizlilik politikası. KVKK uyumlu veri koruma.',
1),

-- Terms and Conditions
('Kullanım Koşulları', 'kullanim-kosullari', '<h2>Kullanım Koşulları</h2>

<p><em>Son Güncelleme: 11 Kasım 2025</em></p>

<p>MINALIA web sitesini kullanarak aşağıdaki koşulları kabul etmiş sayılırsınız.</p>

<h3>1. Genel Koşullar</h3>
<ul>
    <li>18 yaşından büyük olmalısınız veya veli/vasi iznine sahip olmalısınız</li>
    <li>Doğru ve güncel bilgiler sağlamalısınız</li>
    <li>Hesap güvenliğiniz sizin sorumluluğunuzdadır</li>
</ul>

<h3>2. Sipariş ve Ödeme</h3>
<ul>
    <li>Fiyatlar TL cinsindendir ve KDV dahildir</li>
    <li>Stok durumuna göre siparişler iptal edilebilir</li>
    <li>Ödeme güvenli ödeme sağlayıcıları üzerinden alınır</li>
    <li>Fiyat hataları düzeltme hakkımız saklıdır</li>
</ul>

<h3>3. Teslimat</h3>
<ul>
    <li>Teslimat süreleri tahminidir ve garanti edilmez</li>
    <li>Kargo firması teslimat sırasında hasar durumunda sorumludur</li>
    <li>Yanlış adres nedeniyle oluşan gecikmelerden sorumlu değiliz</li>
</ul>

<h3>4. İade ve İptal</h3>
<ul>
    <li>14 gün içinde iade hakkınız vardır</li>
    <li>Ürün kullanılmamış ve ambalajı açılmamış olmalıdır</li>
    <li>İndirimli ürünlerde iade şartları farklılık gösterebilir</li>
    <li>Kargo ücreti müşteriye aittir (kusurlu ürün hariç)</li>
</ul>

<h3>5. Fikri Mülkiyet</h3>
<p>Site içeriği, logo, tasarım MINALIA\'ya aittir ve izinsiz kullanılamaz.</p>

<h3>6. Sorumluluk Sınırlaması</h3>
<p>Sitemizi olduğu gibi sunuyoruz. Teknik hatalar veya kesintilerden sorumlu değiliz.</p>

<h3>7. Değişiklikler</h3>
<p>Bu koşulları önceden haber vermeksizin değiştirme hakkımız saklıdır.</p>

<h3>8. İletişim</h3>
<p>Sorularınız için: <a href="mailto:info@minalia.com.tr">info@minalia.com.tr</a></p>',
'Kullanım Koşulları | MINALIA',
'MINALIA kullanım koşulları, iade politikası ve şartlar.',
1);
