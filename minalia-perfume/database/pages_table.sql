-- Pages Table for Static Content Management
-- MINALIA Parfüm E-Ticaret Platformu

USE minalia_perfume;

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
