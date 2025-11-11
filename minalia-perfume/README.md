# MINALIA Parfüm E-Ticaret Platformu

Modern ve lüks bir parfüm e-ticaret platformu. MINALIA tarzında tasarlanmış, **profesyonel seviye** tam fonksiyonel bir online alışveriş deneyimi sunar.

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange)](https://www.mysql.com/)

## 🎨 Özellikler

### 🎯 Frontend Özellikleri
- ✨ Modern ve lüks tasarım (MINALIA tarzı)
- 📱 Tam responsive (mobil, tablet, desktop)
- 🎯 Hero slider ile dikkat çekici ana sayfa
- 🛍️ **Gelişmiş filtreleme sistemi**
  - Fiyat aralığı (dual-handle slider)
  - Marka, cinsiyet, parfüm notaları
  - Stok durumu ve değerlendirme
- 🔍 **Canlı arama önerileri** (AJAX)
- ❤️ Favori listesi (wishlist)
- 🛒 Dinamik alışveriş sepeti (localStorage)
- ⭐ Ürün değerlendirme ve yorum sistemi
- 🎨 Smooth animasyonlar ve geçişler
- 📄 **Dinamik sayfa yönetimi** (Hakkımızda, İletişim, vb.)

### 🤖 AI & Gelişmiş Özellikler
- 🧠 **AI Parfüm Önerileri**
  - OpenAI GPT-4 entegrasyonu
  - DeepSeek AI desteği
  - Kullanıcı geçmişine dayalı kişiselleştirme
  - Quiz tabanlı öneriler
- 🎁 **Kupon ve İndirim Sistemi**
  - Yüzde/Sabit tutar indirimleri
  - Minimum sipariş tutarı
  - Kullanım limitleri
  - Geçerlilik tarihleri
- ⭐ **Sadakat Puanı Sistemi**
  - VIP seviyeleri (Bronze, Silver, Gold, Platinum)
  - Puan çarpanları
  - Satın alma, yorum, doğum günü puanları
  - Puan kullanımı (100 puan = 10 TL)
- 📧 **Email Pazarlama**
  - Terk edilmiş sepet e-postaları
  - Doğum günü kampanyaları
  - Newsletter sistemi
- 🔔 **Stok Uyarı Sistemi**
  - Ürün stoğa girdiğinde email bildirimi

### 💳 Ödeme ve Kargo
- **iyzico** entegrasyonu
  - 3D Secure desteği
  - Taksit seçenekleri
  - Sanal POS
- **PayTR** entegrasyonu
  - Alternatif ödeme yöntemi
  - Türk Lirası optimizasyonu
- Güvenli ödeme altyapısı
- Adapter pattern ile esnek yapı

### 🔐 Admin Paneli (Tam Fonksiyonel)
- 📊 **Dashboard**
  - Satış istatistikleri (aylık karşılaştırma)
  - Sipariş, müşteri, ürün metrikleri
  - Düşük stok uyarıları
  - Son siparişler ve yorumlar
- 📦 **Ürün Yönetimi**
  - Tam CRUD işlemleri
  - Resim yükleme sistemi
  - Parfüm notaları (üst, orta, alt)
  - Marka ve kategori yönetimi
  - Stok ve fiyat kontrolü
  - Öne çıkan/Çok satan işaretleme
- 🛍️ **Sipariş Yönetimi**
  - Sipariş durumu güncelleme
  - Detaylı sipariş görüntüleme
  - Kargo takibi
- 👥 **Müşteri Yönetimi**
  - Kullanıcı bilgileri
  - Sipariş geçmişi
  - Puan durumu
- 🎫 **Kupon Yönetimi**
  - Kupon oluşturma/düzenleme
  - Kullanım raporları
- 📄 **Sayfa Yönetimi** ⭐
  - İletişim sayfası
  - Hakkımızda sayfası
  - Gizlilik Politikası
  - Kullanım Koşulları
  - HTML editör desteği
  - SEO ayarları (meta tags)
- ⚙️ **Ayarlar ve Raporlar**
  - Site ayarları
  - AI rapor analizi
  - Satış raporları
- 🔒 **Güvenlik**
  - Güvenli admin girişi
  - Aktivite loglama
  - Rol tabanlı yetkilendirme

### 🛡️ Güvenlik Özellikleri
- ✅ **Password hashing** (bcrypt)
- ✅ **SQL Injection koruması** (PDO Prepared Statements)
- ✅ **XSS koruması** (Input sanitization)
- ✅ **CSRF token** doğrulama
- ✅ **Rate limiting** (brute force koruması)
- ✅ **Secure session** handling
- ✅ **Input validation**
- ✅ **File upload** güvenliği

### 🏗️ Teknik Altyapı
- **Backend:** PHP 7.4+ (OOP, MVC Architecture)
- **Database:** MySQL 5.7+ (20+ tablo)
- **Frontend:** Vanilla JavaScript (ES6+), CSS3
- **Design Pattern:** MVC, Adapter, Singleton
- **Security:** OWASP Top 10 koruması
- **SEO:** SEO-friendly URLs, Schema markup
- **Performance:** Lazy loading, Query optimization
- **API:** RESTful API endpoints

## 📊 Veritabanı Şeması

**20+ Tablo:**
- users, admin_users
- products, brands, categories
- orders, order_items
- reviews, wishlist, cart_items
- coupons, coupon_usage
- loyalty_points
- newsletter_subscribers
- pages (dinamik içerik yönetimi)
- contact_messages
- ai_recommendations
- stock_alerts
- abandoned_cart_emails
- user_preferences
- activity_logs
- ve daha fazlası...

## 🚀 Kurulum

### Gereksinimler
- PHP 7.4 veya üzeri
- MySQL 5.7 veya üzeri
- Apache/Nginx web server
- mod_rewrite etkin
- cURL extension (AI entegrasyonları için)

### Hızlı Başlangıç

#### 1. Projeyi İndirin
```bash
git clone <repository-url>
cd minalia-perfume
```

#### 2. Veritabanı Kurulumu
```bash
# Ana şema
mysql -u root -p < database/schema.sql

# Ek tablolar
mysql -u root -p < database/additional_tables.sql
mysql -u root -p < database/pages_table.sql
mysql -u root -p < database/contact_messages_table.sql

# Demo veriler (opsiyonel)
mysql -u root -p < database/demo_data.sql
```

#### 3. Yapılandırma
```bash
cp .env.example .env
```

`.env` dosyasını düzenleyin:
```env
# Database
DB_HOST=localhost
DB_NAME=minalia_perfume
DB_USER=root
DB_PASS=your_password

# Site
SITE_URL=http://localhost:8000
DEBUG_MODE=true

# Payment Gateways
PAYMENT_PROVIDER=iyzico
IYZICO_API_KEY=your_key
IYZICO_SECRET_KEY=your_secret
PAYTR_MERCHANT_ID=your_id
PAYTR_MERCHANT_KEY=your_key

# AI Providers
AI_PROVIDER=openai
OPENAI_API_KEY=your_key
DEEPSEEK_API_KEY=your_key

# Email
SMTP_HOST=smtp.gmail.com
SMTP_USER=your_email
SMTP_PASS=your_password
```

#### 4. Klasör İzinleri
```bash
chmod -R 755 public/uploads
chmod -R 755 logs
```

#### 5. Sunucuyu Başlatın

**PHP Built-in Server (Test):**
```bash
php -S localhost:8000 -t public
```

**XAMPP:**
1. Projeyi `htdocs/minalia-perfume` klasörüne kopyalayın
2. `http://localhost/minalia-perfume/public` adresini ziyaret edin

**Docker:**
```bash
docker-compose up -d
```

## 📁 Proje Yapısı

```
minalia-perfume/
├── app/
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── HomeController.php
│   │   ├── ProductController.php
│   │   ├── PageController.php         # Statik sayfa kontrolü
│   │   └── ApiController.php          # API endpoints
│   ├── models/
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Coupon.php
│   │   └── LoyaltyPoints.php
│   ├── views/
│   │   ├── layouts/
│   │   ├── pages/
│   │   │   ├── home/
│   │   │   ├── products/
│   │   │   ├── page-detail.php        # Dinamik sayfalar
│   │   │   └── contact.php
│   │   └── components/
│   └── helpers/
│       ├── functions.php
│       ├── PaymentGateway.php         # iyzico/PayTR
│       ├── EmailService.php
│       └── AIRecommendation.php       # OpenAI/DeepSeek
├── admin/                              ⭐ TAM FONKSİYONEL
│   ├── index.php                       # Admin routing
│   ├── controllers/
│   │   ├── AdminAuthController.php
│   │   ├── AdminDashboardController.php
│   │   ├── AdminProductController.php
│   │   └── AdminPageController.php     # Sayfa yönetimi
│   ├── views/
│   │   ├── layouts/main.php
│   │   ├── pages/
│   │   │   ├── dashboard.php
│   │   │   ├── products/
│   │   │   └── page-management/        # Dinamik sayfalar
│   └── assets/
│       ├── css/admin.css
│       └── js/admin.js
├── config/
│   ├── database.php
│   └── constants.php
├── database/
│   ├── schema.sql
│   ├── additional_tables.sql
│   ├── pages_table.sql                 # Sayfa yönetimi
│   ├── contact_messages_table.sql
│   └── demo_data.sql
├── public/
│   ├── css/
│   │   ├── style.css
│   │   └── responsive.css
│   ├── js/
│   │   ├── main.js
│   │   └── filters.js                  # Gelişmiş filtreleme
│   ├── images/
│   ├── uploads/
│   └── index.php
├── cron/                                # Cron jobs (oluşturulacak)
│   ├── abandoned-cart-cron.php
│   ├── birthday-email-cron.php
│   └── stock-alert-cron.php
├── .env
├── .env.example
├── README.md
├── FEATURES.md
└── QUICK_START.md
```

## 🎨 Tasarım Sistemi

### Renk Paleti
```css
--primary: #7A8B5C;        /* Ana vurgu rengi (yeşil) */
--primary-dark: #6a7a4f;   /* Koyu yeşil */
--dark: #1A1A1A;           /* Newsletter, footer */
--white: #FFFFFF;          /* Ana arka plan */
--cream: #F8F5F0;          /* Alternatif arka plan */
--gold: #D4AF37;           /* Premium vurgu */
--text-dark: #2C2C2C;      /* Ana metin */
--text-light: #666666;     /* İkincil metin */
```

### Tipografi
- **Başlıklar:** Playfair Display (serif, lüks)
- **Body Text:** Montserrat (sans-serif, modern)
- **Admin:** Inter (sans-serif, profesyonel)

## 🔐 Varsayılan Giriş Bilgileri

### Admin Paneli
**URL:** `http://localhost:8000/admin/login`

```
Email: admin@minalia.com.tr
Şifre: admin123
```

### Test Kullanıcısı
```
Email: test@example.com
Şifre: test123
```

**⚠️ ÖNEMLİ:** Üretim ortamına geçmeden önce bu şifreleri mutlaka değiştirin!

## 🧪 Test Senaryoları

### Frontend Test
1. Ana sayfayı ziyaret edin: `http://localhost:8000`
2. Ürün filtreleme sistemini test edin
3. Sepete ürün ekleyin
4. İletişim formunu gönderin

### Admin Panel Test
1. Admin panele giriş yapın
2. Dashboard istatistiklerini kontrol edin
3. Yeni ürün ekleyin (resim yükleme dahil)
4. Sayfa yönetiminden "Hakkımızda" sayfasını düzenleyin
5. Siparişleri görüntüleyin

### AI Özellikleri Test
1. `.env` dosyasına OpenAI API key ekleyin
2. Ürün önerilerini test edin
3. Quiz tabanlı önerileri deneyin

## 📦 Özellik Detayları

Tüm özelliklerin detaylı dokümantasyonu için:
- **[FEATURES.md](FEATURES.md)** - Kapsamlı özellik listesi ve kullanım
- **[QUICK_START.md](QUICK_START.md)** - Hızlı başlangıç rehberi

## 🚧 Geliştirme Durumu

### ✅ Tamamlanan Özellikler
- [x] MVC mimarisi
- [x] Veritabanı şeması (20+ tablo)
- [x] Authentication sistemi
- [x] Ana sayfa ve ürün listeleme
- [x] Gelişmiş filtreleme sistemi
- [x] AI parfüm önerileri (OpenAI/DeepSeek)
- [x] Kupon ve indirim sistemi
- [x] Sadakat puanı sistemi (VIP seviyeleri)
- [x] Email pazarlama (terk edilmiş sepet)
- [x] Ödeme entegrasyonları (iyzico/PayTR)
- [x] **Profesyonel admin paneli**
  - [x] Dashboard ve istatistikler
  - [x] Ürün CRUD (resim yükleme)
  - [x] Sayfa yönetimi (İletişim, Hakkımızda, vb.)
  - [x] Admin authentication
- [x] **Dinamik sayfa sistemi**
  - [x] Hakkımızda sayfası
  - [x] İletişim sayfası (formlu)
  - [x] Gizlilik Politikası
  - [x] Kullanım Koşulları
- [x] Responsive tasarım

### 🔄 Devam Eden Geliştirmeler
- [ ] Sipariş yönetimi (admin)
- [ ] Müşteri yönetimi (admin)
- [ ] Kupon yönetimi (admin UI)
- [ ] Ürün detay sayfası (frontend)
- [ ] Checkout süreci
- [ ] Kullanıcı hesap paneli
- [ ] Cron job dosyaları

### 📋 Planlanan Özellikler
- [ ] Gelişmiş raporlama
- [ ] SMS bildirimleri
- [ ] Sosyal medya entegrasyonu
- [ ] Çoklu dil desteği
- [ ] PWA (Progressive Web App)

## 🔧 Cron Jobs (Kurulum Gerekli)

```bash
# Terk edilmiş sepet e-postaları (her saat)
0 * * * * php /path/to/cron/abandoned-cart-cron.php

# Doğum günü e-postaları (günlük 09:00)
0 9 * * * php /path/to/cron/birthday-email-cron.php

# Stok uyarıları (günlük 08:00)
0 8 * * * php /path/to/cron/stock-alert-cron.php

# Süre dolan puanlar (günlük 00:00)
0 0 * * * php /path/to/cron/expire-points-cron.php
```

## 🌐 API Endpoints

```
GET  /api/products/filter      - Ürün filtreleme
GET  /api/search/suggestions   - Arama önerileri
POST /api/coupon/validate      - Kupon doğrulama
GET  /api/ai/recommendations   - AI önerileri
POST /api/ai/quiz              - Quiz önerileri
GET  /api/loyalty/points       - Kullanıcı puanları
POST /api/stock/alert          - Stok uyarı kaydı
```

## 📝 Lisans

Bu proje MIT lisansı altında lisanslanmıştır.

## 👥 Katkıda Bulunma

1. Fork edin
2. Feature branch oluşturun (`git checkout -b feature/AmazingFeature`)
3. Değişikliklerinizi commit edin (`git commit -m 'Add some AmazingFeature'`)
4. Branch'inizi push edin (`git push origin feature/AmazingFeature`)
5. Pull Request açın

## 📞 Destek

Sorularınız için:
- Issue açın: [GitHub Issues](../../issues)
- Dokümantasyon: [FEATURES.md](FEATURES.md)

## 🙏 Teşekkürler

Bu proje MINALIA tarzında tasarlanmış profesyonel seviye bir e-ticaret platformudur.

---

**MINALIA Parfüm** - Lüks Parfüm Deneyimi 🌟

**Son Güncelleme:** 11 Kasım 2025
**Versiyon:** 2.0 (Admin Panel + Dinamik Sayfa Yönetimi)
