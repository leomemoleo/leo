# 🚀 MINALIA Parfüm - Hızlı Başlangıç Kılavuzu

## Seçenek 1: Docker ile Hızlı Kurulum (Önerilen) 🐳

Docker yüklüyse en kolay yöntem budur:

```bash
cd /home/user/leo/minalia-perfume

# Docker Compose ile başlat
docker-compose up -d

# Veritabanını kur
docker-compose exec web mysql -u root -proot minalia_perfume < database/schema.sql
docker-compose exec web mysql -u root -proot minalia_perfume < database/demo_data.sql

# Tarayıcıda aç
# http://localhost:8000
```

## Seçenek 2: XAMPP/WAMP ile Kurulum 💻

### Windows için:

1. **XAMPP İndir ve Kur:**
   - https://www.apachefriends.org/download.html
   - PHP 7.4+ ve MySQL seç

2. **Projeyi Kopyala:**
   ```bash
   # XAMPP htdocs klasörüne kopyala
   C:\xampp\htdocs\minalia-perfume
   ```

3. **Veritabanını Kur:**
   - XAMPP Control Panel'den MySQL'i başlat
   - Tarayıcıda `http://localhost/phpmyadmin` aç
   - "New" butonuna tıkla, veritabanı adı: `minalia_perfume`
   - Import sekmesine gel
   - `database/schema.sql` dosyasını import et
   - `database/demo_data.sql` dosyasını import et

4. **Tarayıcıda Aç:**
   ```
   http://localhost/minalia-perfume/public
   ```

### macOS için:

1. **MAMP İndir ve Kur:**
   - https://www.mamp.info/en/downloads/

2. Yukarıdaki Windows adımlarını takip et (htdocs klasörü: `/Applications/MAMP/htdocs/`)

## Seçenek 3: PHP Built-in Server (Basit Test) ⚡

MySQL olmadan hızlı bir önizleme için:

```bash
cd /home/user/leo/minalia-perfume/public

# PHP sunucusunu başlat
php -S localhost:8000

# Tarayıcıda aç
# http://localhost:8000
```

⚠️ **Not:** Bu yöntemle veritabanı çalışmaz, sadece frontend görünümü test edilebilir.

## Seçenek 4: Linux/Mac için Manuel Kurulum 🐧

### 1. MySQL Kurulumu:

**Ubuntu/Debian:**
```bash
sudo apt update
sudo apt install mysql-server
sudo systemctl start mysql
```

**macOS (Homebrew):**
```bash
brew install mysql
brew services start mysql
```

### 2. Veritabanını Oluştur:

```bash
# MySQL'e giriş yap
mysql -u root -p

# Veritabanını oluştur
CREATE DATABASE minalia_perfume CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Şemaları import et
mysql -u root -p minalia_perfume < /home/user/leo/minalia-perfume/database/schema.sql
mysql -u root -p minalia_perfume < /home/user/leo/minalia-perfume/database/demo_data.sql
```

### 3. PHP Sunucusunu Başlat:

```bash
cd /home/user/leo/minalia-perfume/public
php -S localhost:8000
```

### 4. Tarayıcıda Aç:
```
http://localhost:8000
```

## 🎯 Test Hesapları

### Müşteri Hesabı:
- **E-posta:** test@minalia.com
- **Şifre:** test123

### Admin Hesabı:
- **E-posta:** admin@minalia.com
- **Şifre:** admin123
- **URL:** http://localhost:8000/admin (geliştirilecek)

## 📦 Demo Verileri

Platform şu demo verilerle gelir:

- **4 Kategori:** Erkek, Kadın, Unisex, Niş Parfüm
- **5 Marka:** Chanel, Dior, Tom Ford, Creed, Jo Malone
- **10 Ürün:** Popüler parfümlerle dolu
- **3 Yorum:** Örnek müşteri yorumları
- **1 Test Kullanıcı:** Giriş yapıp test edebilirsiniz

## 🔧 Yapılandırma

`.env` dosyası zaten hazır. Özelleştirmek isterseniz:

```env
# Veritabanı
DB_HOST=localhost
DB_NAME=minalia_perfume
DB_USER=root
DB_PASS=your_password

# Site URL
SITE_URL=http://localhost:8000
```

## 🎨 Özellikler

### Çalışan Özellikler:
✅ Ana sayfa hero slider
✅ Kategori kartları
✅ Ürün listeleme
✅ Ürün detay (geliştiriliyor)
✅ Sepete ekleme (localStorage)
✅ Favoriler (localStorage)
✅ Arama önerileri
✅ Responsive tasarım
✅ Kullanıcı kayıt/giriş

### Geliştirme Aşamasında:
🔄 Checkout süreci
🔄 Sipariş yönetimi
🔄 Admin paneli
🔄 Ödeme entegrasyonu

## 🐛 Sorun Giderme

### "Connection refused" hatası:
- MySQL servisinin çalıştığından emin olun
- `.env` dosyasındaki DB bilgilerini kontrol edin

### "Permission denied" hatası:
```bash
chmod -R 755 /home/user/leo/minalia-perfume/public/uploads
chmod -R 755 /home/user/leo/minalia-perfume/logs
```

### Sayfalar yüklenmiyor:
- Apache mod_rewrite etkin mi kontrol edin
- `.htaccess` dosyası var mı kontrol edin
- PHP version >=7.4 olmalı

### Görseller gözükmüyor:
Normal - Demo görseller eklenmedi. Kendi ürün görsellerinizi:
```bash
/home/user/leo/minalia-perfume/public/images/products/
```
klasörüne ekleyebilirsiniz.

## 📱 Test Senaryoları

1. **Ana Sayfa:**
   - Hero slider otomatik geçişleri izleyin
   - Kategori kartlarına tıklayın
   - Ürün kartlarında hover efektlerini test edin

2. **Ürün İşlemleri:**
   - "Sepete Ekle" butonuna tıklayın
   - Sağ üstteki sepet ikonuna tıklayın
   - Favorilere ekleyin/çıkarın

3. **Kullanıcı İşlemleri:**
   - Yeni hesap oluşturun
   - Giriş yapın
   - Profil bilgilerini güncelleyin

4. **Arama:**
   - Arama kutusuna "chanel" yazın
   - Otomatik öneriler görün
   - Ürün arayın

## 🚀 Sonraki Adımlar

1. Gerçek ürün görselleri ekleyin
2. E-posta SMTP ayarlarını yapılandırın
3. Ödeme sistemi entegre edin
4. Admin panelini tamamlayın
5. Canlıya alın!

## 💬 Destek

Sorun yaşarsanız:
- GitHub Issues'da konu açın
- README.md dosyasını okuyun
- Kod içindeki yorumları inceleyin

---

**Kolay gelsin!** 🎉 MINALIA ile başarılı e-ticaret deneyimi dileriz.
