# MINALIA Parfüm E-Ticaret Platformu

Modern ve lüks bir parfüm e-ticaret platformu. MINALIA tarzında tasarlanmış, tam fonksiyonel bir online alışveriş deneyimi sunar.

## 🎨 Özellikler

### Frontend Özellikleri
- ✨ Modern ve şık tasarım
- 📱 Tam responsive (mobil, tablet, desktop)
- 🎯 Hero slider ile dikkat çekici ana sayfa
- 🛍️ Gelişmiş ürün listeleme ve filtreleme
- 🔍 Canlı arama önerileri
- ❤️ Favori listesi (wishlist)
- 🛒 Dinamik alışveriş sepeti
- ⭐ Ürün değerlendirme sistemi
- 🎨 Smooth animasyonlar ve geçişler

### Backend Özellikleri
- 🔐 Güvenli kullanıcı authentication sistemi
- 👤 Kullanıcı profil yönetimi
- 📦 Sipariş takip sistemi
- 💳 Ödeme entegrasyonu hazır altyapı
- 📧 E-posta bildirimleri
- 🏷️ Kupon ve kampanya sistemi
- 📊 Admin paneli (dashboard, ürün/sipariş yönetimi)
- 🔒 CSRF, XSS, SQL Injection koruması

### Teknik Özellikler
- **Backend:** PHP (OOP, MVC Architecture)
- **Database:** MySQL
- **Frontend:** Vanilla JavaScript, CSS3
- **Security:** Password hashing, Prepared statements, Input sanitization
- **SEO:** SEO-friendly URLs, Meta tags, Schema markup
- **Performance:** Lazy loading, Browser caching, Optimized queries

## 🚀 Kurulum

### Gereksinimler
- PHP 7.4 veya üzeri
- MySQL 5.7 veya üzeri
- Apache/Nginx web server
- mod_rewrite etkin

### Adım 1: Projeyi İndirin
```bash
git clone https://github.com/yourusername/minalia-perfume.git
cd minalia-perfume
```

### Adım 2: Veritabanı Kurulumu
1. MySQL'de yeni bir veritabanı oluşturun:
```sql
CREATE DATABASE minalia_perfume CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. SQL şemasını import edin:
```bash
mysql -u root -p minalia_perfume < database/schema.sql
```

### Adım 3: Yapılandırma
1. `.env.example` dosyasını `.env` olarak kopyalayın:
```bash
cp .env.example .env
```

2. `.env` dosyasını düzenleyip veritabanı bilgilerinizi girin:
```env
DB_HOST=localhost
DB_NAME=minalia_perfume
DB_USER=root
DB_PASS=your_password
SITE_URL=http://localhost/minalia-perfume/public
```

### Adım 4: Klasör İzinleri
```bash
chmod -R 755 public/uploads
chmod -R 755 logs
chmod -R 755 cache
```

### Adım 5: Web Sunucusu Ayarları

#### Apache
`.htaccess` dosyası zaten hazır. `mod_rewrite` modülünün etkin olduğundan emin olun.

#### Nginx
```nginx
server {
    listen 80;
    server_name minalia.local;
    root /path/to/minalia-perfume/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 📁 Proje Yapısı

```
minalia-perfume/
├── app/
│   ├── controllers/       # Controller sınıfları
│   ├── models/           # Model sınıfları
│   ├── views/            # View template'leri
│   │   ├── layouts/      # Ana layout dosyaları
│   │   ├── pages/        # Sayfa view'ları
│   │   └── components/   # Yeniden kullanılabilir component'ler
│   ├── helpers/          # Helper fonksiyonlar
│   └── Router.php        # Router sınıfı
├── config/
│   ├── database.php      # Veritabanı bağlantısı
│   └── constants.php     # Sabitler ve yapılandırma
├── database/
│   └── schema.sql        # Veritabanı şeması
├── public/               # Public dizin (web root)
│   ├── css/             # CSS dosyaları
│   ├── js/              # JavaScript dosyaları
│   ├── images/          # Resim dosyaları
│   ├── uploads/         # Yüklenen dosyalar
│   ├── .htaccess        # Apache rewrite kuralları
│   └── index.php        # Ana giriş noktası
├── admin/               # Admin paneli (geliştirilecek)
├── logs/                # Log dosyaları
└── .env                 # Environment değişkenleri
```

## 🎨 Tasarım Sistemi

### Renk Paleti
- **Primary Green:** `#7A8B5C` - Ana vurgu rengi
- **Dark:** `#1A1A1A` - Newsletter ve footer
- **White:** `#FFFFFF` - Ana arka plan
- **Cream:** `#F8F5F0` - Alternatif arka plan
- **Gold:** `#D4AF37` - Premium vurgu
- **Text Primary:** `#2C2C2C`
- **Text Secondary:** `#666666`

### Tipografi
- **Başlıklar:** Playfair Display (serif)
- **Body Text:** Montserrat (sans-serif)

## 🔐 Güvenlik

### Uygulanan Güvenlik Önlemleri
- ✅ Password hashing (bcrypt)
- ✅ SQL Injection koruması (PDO Prepared Statements)
- ✅ XSS koruması (Input sanitization)
- ✅ CSRF token doğrulama
- ✅ Rate limiting (brute force koruması)
- ✅ Secure session handling
- ✅ Input validation
- ✅ File upload güvenliği

### Güvenlik Tavsiyeleri
1. Üretim ortamında `DEBUG_MODE=false` olarak ayarlayın
2. `.env` dosyasını `.gitignore`'a ekleyin
3. HTTPS kullanın
4. Düzenli olarak güvenlik güncellemelerini takip edin
5. Güçlü şifreler kullanın

## 📝 Varsayılan Admin Kullanıcısı

İlk kurulumda aşağıdaki admin kullanıcısı oluşturulur:

```
Kullanıcı Adı: admin
E-posta: admin@minalia.com
Şifre: admin123
```

**ÖNEMLİ:** Üretim ortamına geçmeden önce bu şifreyi mutlaka değiştirin!

## 🧪 Test Verileri

Test verileri eklemek için:

```sql
-- Örnek kategoriler
INSERT INTO categories (name, slug, description) VALUES
('Erkek Parfüm', 'erkek-parfum', 'Erkekler için özel parfümler'),
('Kadın Parfüm', 'kadin-parfum', 'Kadınlar için özel parfümler'),
('Niş Parfüm', 'nis-parfum', 'Özel ve niş parfümler');

-- Örnek markalar
INSERT INTO brands (name, slug, description) VALUES
('Chanel', 'chanel', 'Fransız lüks marka'),
('Dior', 'dior', 'Prestijli parfüm markası'),
('Tom Ford', 'tom-ford', 'Modern lüks parfümler');
```

## 🚧 Geliştirme Durumu

### Tamamlanan Özellikler ✅
- [x] Proje yapısı ve MVC mimarisi
- [x] Veritabanı şeması
- [x] Authentication sistemi
- [x] Ana sayfa ve ürün listeleme
- [x] Responsive tasarım
- [x] Sepet ve wishlist (frontend)
- [x] JavaScript interaktif özellikler

### Devam Eden Geliştirmeler 🔄
- [ ] Ürün detay sayfası
- [ ] Checkout süreci
- [ ] Kullanıcı hesap paneli
- [ ] Admin paneli
- [ ] Ödeme entegrasyonu
- [ ] E-posta servisi
- [ ] Kargo entegrasyonu

## 📄 Lisans

Bu proje MIT lisansı altında lisanslanmıştır.

## 👥 Katkıda Bulunma

1. Fork edin
2. Feature branch oluşturun (`git checkout -b feature/AmazingFeature`)
3. Değişikliklerinizi commit edin (`git commit -m 'Add some AmazingFeature'`)
4. Branch'inizi push edin (`git push origin feature/AmazingFeature`)
5. Pull Request açın

## 📞 İletişim

Proje Linki: [https://github.com/yourusername/minalia-perfume](https://github.com/yourusername/minalia-perfume)

## 🙏 Teşekkürler

Bu proje MINALIA tarzında tasarlanmış modern bir e-ticaret platformudur. Kullanılan tüm açık kaynak projelere teşekkür ederiz.

---

**MINALIA Parfüm** - Lüks Parfüm Deneyimi 🌟
