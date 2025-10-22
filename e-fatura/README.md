# 🧾 e-Fatura Sistemi

Türkiye e-Fatura standartlarına uyumlu, profesyonel fatura yönetim sistemi.

## 📋 Özellikler

- ✅ **Tam Özellikli Fatura Yönetimi** - Fatura oluşturma, düzenleme, listeleme
- ✅ **Müşteri Yönetimi** - Bireysel ve kurumsal müşteri kayıtları
- ✅ **Ürün/Hizmet Kataloğu** - Ürün ve hizmet tanımları
- ✅ **KDV Hesaplama** - Türkiye KDV oranları (0%, 1%, 10%, 20%)
- ✅ **UBL-TR XML Export** - e-Fatura standart formatı
- ✅ **Modern Arayüz** - Responsive ve kullanıcı dostu tasarım
- ✅ **Otomatik Fatura Numaralandırma** - Yıllık sıra takibi
- ✅ **Vergi Numarası Doğrulama** - Türk vergi numarası kontrolü
- ✅ **TC Kimlik No Doğrulama** - TC kimlik numarası kontrolü

## 🚀 Kurulum

### Gereksinimler

- PHP 7.4 veya üzeri
- MySQL 5.7 veya üzeri / MariaDB 10.3 veya üzeri
- Web sunucusu (Apache/Nginx)
- PDO PHP Extension

### Adım 1: Dosyaları Yükleyin

Tüm dosyaları web sunucunuzun root dizinine kopyalayın.

```bash
cd /var/www/html
# veya XAMPP kullanıyorsanız
cd C:/xampp/htdocs
```

### Adım 2: Veritabanını Oluşturun

MySQL/MariaDB'ye bağlanın ve veritabanını oluşturun:

```bash
mysql -u root -p < e-fatura/database.sql
```

veya phpMyAdmin üzerinden `database.sql` dosyasını import edin.

### Adım 3: Veritabanı Ayarlarını Yapın

`e-fatura/includes/config.php` dosyasını düzenleyin:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');           // Veritabanı kullanıcı adı
define('DB_PASS', '');               // Veritabanı şifresi
define('DB_NAME', 'efatura_db');     // Veritabanı adı
```

### Adım 4: Dizin İzinlerini Ayarlayın

Export klasörlerine yazma izni verin:

```bash
chmod -R 755 e-fatura/exports
chmod -R 777 e-fatura/exports/pdf
chmod -R 777 e-fatura/exports/xml
```

Windows'ta bu adım gerekli değildir.

### Adım 5: Sisteme Giriş Yapın

Tarayıcınızdan şu adrese gidin:

```
http://localhost/e-fatura
```

## 📖 Kullanım Kılavuzu

### İlk Kurulum Sonrası

1. **Şirket Bilgilerini Güncelleyin**
   - Ayarlar > Şirket Bilgileri bölümünden şirket bilgilerinizi güncelleyin
   - Veya doğrudan veritabanında `company` tablosunu düzenleyin

2. **Müşteri Ekleyin**
   - Müşteriler sekmesinden "Yeni Müşteri" butonuna tıklayın
   - Bireysel veya kurumsal müşteri bilgilerini girin

3. **Ürün/Hizmet Ekleyin**
   - Ürünler sekmesinden "Yeni Ürün" butonuna tıklayın
   - Ürün adı, fiyat ve KDV oranını belirleyin

4. **Fatura Oluşturun**
   - Faturalar sekmesinden "Yeni Fatura" butonuna tıklayın
   - Müşteri seçin ve fatura kalemlerini ekleyin
   - Fatura otomatik olarak hesaplanır ve kaydedilir

### Fatura Durumları

- **Taslak (Draft)**: Düzenlenebilir, silinebilir
- **Kesildi (Issued)**: Fatura kesilmiş, değişiklik yapılamaz
- **Gönderildi (Sent)**: Müşteriye gönderilmiş
- **İptal (Cancelled)**: İptal edilmiş fatura

### XML Export

Fatura detaylarından "XML İndir" butonuna tıklayarak UBL-TR formatında XML dosyası oluşturabilirsiniz. Bu dosya GIB e-Fatura sistemine yüklenebilir.

## 🔧 Yapılandırma

### KDV Oranları

KDV oranlarını değiştirmek için `includes/config.php` dosyasını düzenleyin:

```php
define('KDV_RATES', [
    '0' => 0,
    '1' => 1,
    '10' => 10,
    '20' => 20
]);
```

### Fatura Numarası Formatı

Fatura numarası formatı: `PREFIX + YIL + SIRA`

Örnek: `FTR2024000001`

Prefix'i değiştirmek için veritabanında `settings` tablosunu düzenleyin.

### Para Birimleri

Sistem TRY, USD, EUR ve GBP para birimlerini destekler. Yeni para birimi eklemek için `includes/config.php` dosyasını düzenleyin.

## 📊 Veritabanı Yapısı

### Ana Tablolar

- `company` - Şirket bilgileri
- `customers` - Müşteri bilgileri
- `products` - Ürün/hizmet kataloğu
- `invoices` - Fatura kayıtları
- `invoice_items` - Fatura kalemleri
- `invoice_tax_summary` - KDV özeti
- `invoice_sequences` - Fatura sıra numaraları
- `settings` - Sistem ayarları

## 🔐 Güvenlik

- Tüm kullanıcı girdileri sanitize edilir
- SQL Injection'a karşı PDO prepared statements kullanılır
- XSS koruması için htmlspecialchars kullanılır
- Vergi numarası ve TC kimlik no doğrulaması yapılır

### Üretim Ortamı İçin Öneriler

1. `config.php` dosyasında `SECRET_KEY` değerini değiştirin
2. Hata raporlamayı kapatın: `error_reporting(0)`
3. HTTPS kullanın
4. Düzenli veritabanı yedeklemesi alın
5. Güçlü veritabanı şifresi kullanın

## 🛠️ Teknik Detaylar

### Frontend
- HTML5, CSS3, JavaScript (Vanilla)
- Responsive tasarım (Mobile-friendly)
- AJAX ile API iletişimi

### Backend
- PHP 7.4+
- MySQL/MariaDB
- RESTful API yapısı
- PDO veritabanı katmanı

### e-Fatura Standartları
- UBL-TR 1.2
- UBLTR Invoice 2.1
- GIB e-Fatura formatı

## 📝 API Endpoints

### Faturalar
- `GET /api/invoices.php` - Fatura listesi
- `GET /api/invoices.php?id={id}` - Fatura detayı
- `POST /api/invoices.php` - Yeni fatura
- `PUT /api/invoices.php` - Fatura güncelle
- `DELETE /api/invoices.php?id={id}` - Fatura sil

### Müşteriler
- `GET /api/customers.php` - Müşteri listesi
- `POST /api/customers.php` - Yeni müşteri
- `PUT /api/customers.php` - Müşteri güncelle
- `DELETE /api/customers.php?id={id}` - Müşteri sil

### Ürünler
- `GET /api/products.php` - Ürün listesi
- `POST /api/products.php` - Yeni ürün
- `PUT /api/products.php` - Ürün güncelle
- `DELETE /api/products.php?id={id}` - Ürün sil

### Export
- `GET /api/export-xml.php?id={id}` - XML indir

## 🐛 Sorun Giderme

### Veritabanı Bağlantı Hatası

```
Veritabanı bağlantı hatası
```

**Çözüm**: `config.php` dosyasındaki veritabanı bilgilerini kontrol edin.

### Dosya Yazma Hatası

```
Permission denied
```

**Çözüm**: `exports` klasörüne yazma izni verin (chmod 777).

### API Çalışmıyor

**Çözüm**:
1. PHP'nin çalıştığından emin olun
2. `mod_rewrite` aktif mi kontrol edin
3. `.htaccess` dosyasını kontrol edin

## 🚧 Geliştirme Aşamasında

- [ ] PDF export özelliği
- [ ] Toplu fatura oluşturma
- [ ] E-posta ile fatura gönderme
- [ ] Ödeme takibi
- [ ] Raporlama modülü
- [ ] GIB e-Fatura entegrasyonu
- [ ] Kullanıcı yetkilendirme sistemi

## 📄 Lisans

Bu proje MIT lisansı altında lisanslanmıştır.

## 🤝 Katkıda Bulunma

1. Fork yapın
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Commit yapın (`git commit -m 'Add some amazing feature'`)
4. Push yapın (`git push origin feature/amazing-feature`)
5. Pull Request açın

## 📞 İletişim

Sorularınız için: info@42dil.com

## ⚠️ Önemli Notlar

- Bu sistem e-Fatura formatında XML üretir ancak GIB'e doğrudan entegre değildir
- GIB entegrasyonu için ayrı bir e-Fatura entegratörü kullanmanız gerekir
- Üretim ortamında kullanmadan önce mutlaka test edin
- Yerel yönetmeliklere uygunluğu kontrol edin

---

**🎯 42 DİL TERCÜME BÜROSU** - Profesyonel belge yönetim çözümleri
