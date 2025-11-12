# MINALIA E-Ticaret - Yükseltme Rehberi

Bu rehber, mevcut MINALIA kurulumlarını yeni sürüme yükseltmek için gerekli adımları açıklar.

## 📋 Gereksinimler

- Mevcut bir MINALIA kurulumu
- SSH/Terminal erişimi
- MySQL/MariaDB kullanıcı erişimi
- PHP CLI erişimi

## ⚠️ Önemli Notlar

1. **Yedekleme Alın!** Yükseltme öncesi mutlaka veritabanı ve dosya yedeği alın
2. **Test Ortamı:** Mümkünse önce test ortamında deneyin
3. **Bakım Modu:** Yükseltme sırasında siteyi bakım moduna alın

---

## 🚀 FAZ 5: PWA & Faceted Search Yükseltmesi (2025-11-12)

Bu yükseltme aşağıdaki özellikleri ekler:

### Yeni Özellikler:
✅ **Progressive Web App (PWA)** - Çevrimdışı destek, push bildirimleri
✅ **Faceted Search** - 11 farklı filtre tipi, autocomplete, arama analitiği
✅ **Arama Analitiği** - Trend aramalar, popüler terimler, kullanıcı davranış analizi

### Veritabanı Değişiklikleri:
- **3 yeni tablo:** search_history, popular_searches, filter_analytics
- **12 yeni index:** products tablosuna performans optimizasyonu
- **2 tablo indexi:** categories ve brands tablolarına

---

## 📝 Yükseltme Adımları

### Adım 1: Yedek Alma

```bash
# Veritabanı yedeği
mysqldump -u kullaniciadi -p veritabani_adi > backup_$(date +%Y%m%d).sql

# Dosya yedeği
tar -czf minalia_backup_$(date +%Y%m%d).tar.gz /path/to/minalia-perfume/
```

### Adım 2: Kodu Güncelleme

```bash
cd /path/to/minalia-perfume

# Git ile güncelleme
git pull origin main

# Veya dosyaları manuel olarak güncelleyin
```

### Adım 3: Veritabanı Migrasyonlarını Çalıştırma

```bash
# Migration runner ile otomatik güncelleme
php install/migrate.php
```

**Çıktı örneği:**
```
✅ Connected to database: minalia_db
✅ Migrations tracking table ready
📊 Already executed: 14 migrations

📁 Found 15 migration files

🔄 RUNNING: 015_add_search_indexes.sql
   ✅ SUCCESS: 18 statements executed

─────────────────────────────────────────
📊 MIGRATION SUMMARY:
   ✅ Executed: 1
   ⏭️  Skipped:  14
   ❌ Failed:   0
─────────────────────────────────────────

🎉 Database migrations completed successfully!
```

### Adım 4: Manuel Migrasyon (Alternatif)

Eğer `migrate.php` çalışmazsa, manuel olarak çalıştırabilirsiniz:

```bash
mysql -u kullaniciadi -p veritabani_adi < install/migrations/015_add_search_indexes.sql
```

### Adım 5: Önbellek Temizleme

```bash
# Tarayıcı önbellekleri için (service worker güncellemesi)
# Kullanıcılar sayfayı yenilediğinde otomatik güncellenecek

# Eğer Opcache kullanıyorsanız:
php -r "opcache_reset();"
# veya Apache/Nginx yeniden başlatın
```

### Adım 6: İzin Kontrolü

```bash
# Dosya izinlerini kontrol edin
chmod -R 755 /path/to/minalia-perfume/public
chmod -R 755 /path/to/minalia-perfume/public/js
chmod 644 /path/to/minalia-perfume/public/sw.js
chmod 644 /path/to/minalia-perfume/public/manifest.json
```

---

## 🧪 Test Etme

Yükseltme sonrası aşağıdakileri test edin:

### PWA Testleri:
1. **Manifest Test:** `https://siteniz.com/manifest.json` açılıyor mu?
2. **Service Worker:** Geliştirici konsolunda (F12) "Application" sekmesinde service worker kayıtlı mı?
3. **Çevrimdışı Mod:** İnternet bağlantısını kapatıp önbellekteki sayfalar açılıyor mu?
4. **Install Prompt:** Mobil cihazda "Ana ekrana ekle" bildirimi geliyor mu?

### Faceted Search Testleri:
1. **Autocomplete:** Arama kutusuna yazmaya başladığınızda öneriler geliyor mu?
2. **Filtreler:** Ürün sayfasında filtreler çalışıyor mu?
3. **Performans:** Çok sayıda filtre ile sayfa hızlı yükleniyor mu?
4. **Arama Analitiği:** `SELECT * FROM popular_searches` ile aramalar kaydediliyor mu?

### Veritabanı Testleri:
```sql
-- Yeni tabloların varlığını kontrol edin
SHOW TABLES LIKE 'search_history';
SHOW TABLES LIKE 'popular_searches';
SHOW TABLES LIKE 'filter_analytics';

-- Index'lerin varlığını kontrol edin
SHOW INDEX FROM products WHERE Key_name LIKE 'idx_%';
SHOW INDEX FROM categories WHERE Key_name = 'idx_parent_active';
SHOW INDEX FROM brands WHERE Key_name = 'idx_featured_sort';
```

---

## 🐛 Sorun Giderme

### Migrasyon Hataları

**Hata:** "Table already exists"
**Çözüm:** Normal, migration sistemi zaten çalıştırılmış. `CREATE TABLE IF NOT EXISTS` güvenli.

**Hata:** "Duplicate key name 'idx_...'"
**Çözüm:** Index zaten var, bu normal. Migration güvenli yazılmış.

**Hata:** "Cannot add foreign key constraint"
**Çözüm:**
- Referans edilen tablolar mevcut mu kontrol edin
- InnoDB engine kullanıldığından emin olun
- `SET FOREIGN_KEY_CHECKS=0;` ile geçici olarak kapatabilirsiniz

### PWA Sorunları

**Sorun:** Service worker kayıt olmuyor
**Çözüm:**
- HTTPS kullanıyor musunuz? (localhost hariç PWA için HTTPS gerekli)
- `sw.js` dosyası erişilebilir mi? `https://siteniz.com/sw.js`
- Tarayıcı konsolunda hata var mı?

**Sorun:** Offline page gösterilmiyor
**Çözüm:**
- `offline.html` dosyası var mı?
- Service worker cache stratejisi doğru mu?
- Network tab'ında dosya cache'leniyor mu?

### Arama Sorunları

**Sorun:** Autocomplete çalışmıyor
**Çözüm:**
- API endpoint erişilebilir mi? `/api/search/autocomplete?q=test`
- Tarayıcı konsolunda JavaScript hatası var mı?
- `faceted-search.js` yükleniyor mu?

**Sorun:** Filtreler sonuç döndürmüyor
**Çözüm:**
- Index'ler oluşturuldu mu? `SHOW INDEX FROM products;`
- ProductController güncellenmiş mi?
- Error log'larını kontrol edin

---

## 🔄 Geri Alma (Rollback)

Bir sorun çıkarsa geri alabilirsiniz:

### Adım 1: Veritabanı Geri Yükleme
```bash
mysql -u kullaniciadi -p veritabani_adi < backup_20251112.sql
```

### Adım 2: Kod Geri Alma
```bash
git checkout previous_commit_hash
# veya yedek dosyalardan geri yükleyin
```

### Adım 3: Önbellek Temizleme
```bash
# Service worker'ı kaldırın (tarayıcı geliştirici araçlarında)
# Application > Service Workers > Unregister

# Veya tarayıcı önbelleğini tamamen temizleyin
```

---

## 📊 Performans İyileştirmeleri

Yükseltme sonrası ek optimizasyonlar:

### Index Optimizasyonu
```sql
-- Index kullanımını analiz edin
EXPLAIN SELECT * FROM products WHERE price BETWEEN 100 AND 500;

-- Kullanılmayan index'leri kaldırın (opsiyonel)
-- ALTER TABLE products DROP INDEX unused_index;
```

### Query Cache (MySQL 5.7 ve öncesi)
```sql
SET GLOBAL query_cache_size = 67108864; -- 64MB
SET GLOBAL query_cache_type = 1;
```

### Opcache (PHP)
```ini
; php.ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
```

---

## 📞 Destek

Sorun yaşarsanız:

1. **Log Dosyaları:** `error_log`, PHP error log, MySQL error log
2. **GitHub Issues:** https://github.com/your-repo/issues
3. **Dokümantasyon:** `README.md`, `INSTALL.md`
4. **E-posta:** info@minalia.com.tr

---

## 📝 Değişiklik Geçmişi

### FAZ 5 (2025-11-12)
- ✅ PWA desteği eklendi
- ✅ Faceted Search implementasyonu
- ✅ XSS güvenlik düzeltmeleri
- ✅ Arama analitiği sistemi
- ✅ 3 yeni tablo, 12 yeni index

### FAZ 4 (önceki)
- KVKK uyumluluğu
- Veri gizliliği implementasyonu

---

**Son Güncelleme:** 12 Kasım 2025
**Sürüm:** FAZ 5 - PWA & Faceted Search
**Tahmini Yükseltme Süresi:** 15-30 dakika
