# 🚀 MINALIA Parfüm - Yeni Özellikler Dokümantasyonu

## 📋 İçindekiler
1. [Gelişmiş Filtreleme Sistemi](#1-gelişmiş-filtreleme-sistemi)
2. [Akıllı Arama ve Autocomplete](#2-akıllı-arama-ve-autocomplete)
3. [Ödeme Entegrasyonları](#3-ödeme-entegrasyonları)
4. [Kupon Sistemi](#4-kupon-sistemi)
5. [Loyalty/Puan Sistemi](#5-loyalty-puan-sistemi)
6. [Email Marketing](#6-email-marketing)
7. [AI Parfüm Önerisi](#7-ai-parfüm-önerisi)

---

## 1. Gelişmiş Filtreleme Sistemi

### ✨ Özellikler
- **Fiyat Aralığı:** Dual-slider ile min-max fiyat seçimi
- **Marka Filtreleme:** Arama özellikli checkbox listesi
- **Cinsiyet Filtreleme:** Erkek, Kadın, Unisex
- **Koku Notaları:** Odunsu, Çiçeksi, Meyvemsi, vs.
- **Değerlendirme:** Yıldız bazlı filtreleme
- **Stok Durumu:** Stokta var, İndirimde

### 📁 Dosyalar
- `app/views/components/filter-sidebar.php` - Filtre UI komponenti
- `public/js/filters.js` - AJAX filtreleme mantığı

### 🔧 Kullanım
```html
<!-- Ürün listeleme sayfasında -->
<?php include __DIR__ . '/../../components/filter-sidebar.php'; ?>
```

```javascript
// JavaScript ile filtreleme
FilterManager.init();
```

### API Endpoint
```
GET /api/products/filter
Parameters:
  - min_price: 0
  - max_price: 20000
  - brands: 1,2,3
  - genders: men,women
  - rating: 4
  - sort: price_asc
```

---

## 2. Akıllı Arama ve Autocomplete

### ✨ Özellikler
- **Anlık Öneriler:** 2+ karakter sonrası otomatik öneriler
- **Ürün Arama:** İsim, açıklama, marka bazlı arama
- **Debounce:** 300ms gecikme ile performans optimizasyonu
- **Görsel Önizleme:** Ürün resimleri ile öneriler

### 📁 Dosyalar
- `public/js/filters.js` - SearchManager sınıfı

### API Endpoint
```
GET /api/products/search-suggestions?q=chanel
Response: {
  "success": true,
  "results": {
    "products": [...],
    "brands": [...]
  }
}
```

---

## 3. Ödeme Entegrasyonları

### ✨ Desteklenen Sistemler
- **iyzico** - 3D Secure, taksit desteği
- **PayTR** - Türkiye'nin ödeme sistemi

### 🏗️ Adapter Pattern
```php
// Factory ile provider seçimi
$payment = PaymentFactory::create('iyzico');

// Ödeme oluştur
$result = $payment->createPayment($orderData);

// İade işlemi
$refund = $payment->refund($paymentId, $amount);
```

### 📁 Dosyalar
- `app/helpers/PaymentGateway.php` - Tüm payment adapter'ları

### ⚙️ Yapılandırma (.env)
```env
# iyzico
PAYMENT_PROVIDER=iyzico
IYZICO_API_KEY=sandbox-xxx
IYZICO_SECRET_KEY=sandbox-yyy
IYZICO_BASE_URL=https://sandbox-api.iyzipay.com

# PayTR
PAYTR_MERCHANT_ID=123456
PAYTR_MERCHANT_KEY=abc123
PAYTR_MERCHANT_SALT=xyz789
```

### 💳 Test Kartları
**iyzico Test Kartı:**
```
Kart No: 5528790000000008
SKT: 12/30
CVC: 123
3D Şifre: 123456
```

---

## 4. Kupon Sistemi

### ✨ Özellikler
- **Kupon Tipleri:** Percentage (%), Fixed (TL)
- **Validasyon:** Minimum tutar, kullanım limiti, tarih kontrolü
- **Otomatik Uygulama:** Sepet koşullarına göre kupon önerisi

### 📁 Dosyalar
- `app/models/Coupon.php` - Kupon model ve validasyon
- `database/additional_tables.sql` - Coupons tablosu

### 🎁 Örnek Kuponlar
```
HOSGELDIN15  - Yeni üyelere %15 indirim
SEPET10      - Terk edilmiş sepet için %10
YAZ2025      - Mevsimsel kampanya %20
MINALIA100   - 100 TL sabit indirim
```

### 🔧 Kullanım
```php
$couponModel = new Coupon();

// Kupon validasyonu
$result = $couponModel->validateCoupon('HOSGELDIN15', 500, $userId);

if ($result['valid']) {
    echo "İndirim: " . formatPrice($result['discount_amount']);
}

// Kuponu uygula
$couponModel->applyCoupon($result['coupon_id']);
```

### API Endpoint
```
POST /api/coupon/validate
Body: {
  "code": "HOSGELDIN15",
  "cart_total": 500
}
```

---

## 5. Loyalty/Puan Sistemi

### ✨ Özellikler
- **Puan Kazanma:** Alışveriş, kayıt, yorum, referans
- **VIP Seviyeleri:** Bronz, Gümüş, Altın, Platin
- **Puan Harcama:** 100 puan = 10 TL indirim
- **Puan Çarpanları:** Seviyeye göre 1x - 3x

### 📊 VIP Seviyeleri
| Seviye | Minimum Puan | Çarpan |
|--------|-------------|--------|
| Bronz | 0 | 1x |
| Gümüş | 1,000 | 1.5x |
| Altın | 5,000 | 2x |
| Platin | 15,000 | 3x |

### 🎯 Puan Kazanma
- **Kayıt Bonusu:** 100 puan
- **Alışveriş:** 1 TL = 1 puan (seviye çarpanı uygulanır)
- **Ürün Yorumu:** 50 puan
- **Doğum Günü:** 200 puan (yılda 1 kez)
- **Referans:** 500 puan

### 📁 Dosyalar
- `app/models/LoyaltyPoints.php` - Loyalty model

### 🔧 Kullanım
```php
$loyaltyModel = new LoyaltyPoints();

// Kullanıcı puanları
$points = $loyaltyModel->getUserPoints($userId);

// Seviye bilgisi
$level = $loyaltyModel->getUserLevel($userId);

// Alışveriş puanı ver
$loyaltyModel->awardPurchasePoints($userId, 500, $orderId);

// Puan kullan
$result = $loyaltyModel->redeemPoints($userId, 500);
```

---

## 6. Email Marketing

### ✨ Özellikler
- **Terk Edilmiş Sepet:** Otomatik email gönderimi
- **Sipariş Onayı:** Order confirmation emails
- **Hoş Geldin Email'i:** Welcome bonus ile
- **Doğum Günü İndirimi:** Otomatik birthday discount
- **Stok Bildirimi:** Ürün stoğa girince bildirim
- **Newsletter:** Toplu email gönderimi

### 📁 Dosyalar
- `app/helpers/EmailService.php` - Email service class

### 🔧 Kullanım
```php
$emailService = new EmailService();

// Terk edilmiş sepet emaili
$emailService->sendAbandonedCartEmail(
    'user@email.com',
    'Ahmet',
    $cartItems,
    $cartTotal
);

// Hoş geldin emaili
$emailService->sendWelcomeEmail('user@email.com', 'Ahmet');

// Sipariş onayı
$emailService->sendOrderConfirmation('user@email.com', $orderData);
```

### 🤖 Otomatik Terk Edilmiş Sepet Tracker
```php
$tracker = new AbandonedCartTracker();

// Cron job ile çalıştırın (her saat)
$sentCount = $tracker->trackAbandonedCarts();
echo "Sent $sentCount abandoned cart emails";
```

**Cron Job Kurulumu:**
```bash
# crontab -e
0 * * * * /usr/bin/php /path/to/abandoned-cart-cron.php
```

---

## 7. AI Parfüm Önerisi

### ✨ Özellikler
- **Kişiselleştirilmiş Öneriler:** Geçmiş alışverişlere göre
- **Quiz Bazlı Öneri:** Sorularla parfüm bulma
- **Akıllı Açıklama:** AI ile ürün açıklaması oluşturma
- **Dual Provider:** OpenAI + DeepSeek desteği

### 🤖 Desteklenen AI Sağlayıcılar

#### OpenAI (GPT-4)
- **Model:** gpt-4, gpt-3.5-turbo
- **Avantajlar:** Yüksek kalite, geniş dil desteği
- **Maliyet:** Orta-yüksek

#### DeepSeek
- **Model:** deepseek-chat
- **Avantajlar:** Uygun fiyat, hızlı yanıt
- **Maliyet:** Düşük

### 📁 Dosyalar
- `app/helpers/AIRecommendation.php` - AI recommendation system

### ⚙️ Yapılandırma (.env)
```env
# Hangi AI provider kullanılacak
AI_PROVIDER=openai

# OpenAI
OPENAI_API_KEY=sk-xxx
OPENAI_MODEL=gpt-4

# DeepSeek
DEEPSEEK_API_KEY=sk-yyy
DEEPSEEK_MODEL=deepseek-chat
```

### 🔧 Kullanım

**Kişiselleştirilmiş Öneriler:**
```php
$aiManager = new AIRecommendationManager();

// Kullanıcıya özel
$recommendations = $aiManager->getPersonalizedRecommendations($userId);

foreach ($recommendations as $rec) {
    echo $rec['product']['name'];
    echo $rec['reason']; // AI'ın önerme nedeni
}
```

**Quiz Bazlı Öneriler:**
```php
$quizAnswers = [
    'gender' => 'women',
    'preferred_notes' => ['çiçeksi', 'meyvemsi'],
    'budget' => 'medium',
    'occasion' => 'daily'
];

$recommendations = $aiManager->getQuizRecommendations($quizAnswers);
```

**AI ile Ürün Açıklaması:**
```php
$description = $aiManager->generateDescription($productId);
// "Tom Ford Oud Wood, Doğu'nun gizemli kokularını modern bir yorumla..."
```

### API Endpoints

**Kişisel Öneriler:**
```
GET /api/ai/recommendations
Headers: Authorization: Bearer {token}
Response: {
  "success": true,
  "recommendations": [
    {
      "product": {...},
      "reason": "Geçmiş alışverişlerinize göre..."
    }
  ]
}
```

**Quiz Önerileri:**
```
POST /api/ai/quiz-recommendations
Body: {
  "answers": {
    "gender": "women",
    "preferred_notes": ["çiçeksi"],
    "budget": "medium"
  }
}
```

### 💡 Kullanım Senaryoları

**Senaryo 1: Ana Sayfa Önerileri**
```javascript
// Ana sayfada "Size Özel Parfümler" bölümü
fetch('/api/ai/recommendations')
    .then(res => res.json())
    .then(data => {
        displayRecommendations(data.recommendations);
    });
```

**Senaryo 2: Parfüm Quiz'i**
```html
<!-- Quiz formu -->
<form id="perfume-quiz">
    <input type="radio" name="gender" value="women"> Kadın
    <input type="checkbox" name="notes[]" value="çiçeksi"> Çiçeksi
    <button type="submit">Parfümümü Bul</button>
</form>
```

---

## 🗄️ Veritabanı Güncellemeleri

Yeni tabloları eklemek için:

```bash
mysql -u root -p minalia_perfume < database/additional_tables.sql
```

Eklenen Tablolar:
- `loyalty_points` - Kullanıcı puanları
- `abandoned_cart_emails` - Email log
- `ai_recommendations` - AI öneri logları
- `stock_alerts` - Stok bildirimleri
- `user_preferences` - Kullanıcı tercihleri
- `coupon_usage` - Kupon kullanım logları

---

## 🚀 Kurulum ve Kullanıma Alma

### 1. Veritabanı Güncellemesi
```bash
mysql -u root -p minalia_perfume < database/additional_tables.sql
```

### 2. Environment Yapılandırması
`.env` dosyasını düzenleyin:
```env
# Ödeme sistemi
PAYMENT_PROVIDER=iyzico
IYZICO_API_KEY=your-key

# AI sistemi
AI_PROVIDER=openai
OPENAI_API_KEY=your-key
```

### 3. Cron Jobs Kurulumu
```bash
# Terk edilmiş sepet emaili (her saat)
0 * * * * php /path/to/abandoned-cart-cron.php

# Puan süre dolumu (her gün gece)
0 0 * * * php /path/to/expire-points-cron.php

# Doğum günü emaili (her gün sabah)
0 8 * * * php /path/to/birthday-cron.php
```

### 4. JavaScript Dosyalarını Dahil Edin
```html
<script src="/js/filters.js"></script>
<script src="/js/main.js"></script>
```

---

## 📊 Performans ve Optimizasyon

### Cache Stratejisi
- AI önerileri 1 saat cache'lenir
- Filtre sonuçları 15 dakika cache
- Kupon validasyonu cache'lenmez (real-time)

### Rate Limiting
- AI API: 10 istek/dakika/kullanıcı
- Email gönderimi: 100 email/saat
- Stok bildirimi: 5 kayıt/saat/kullanıcı

### Database Indexing
Tüm yeni tablolarda uygun indexler tanımlanmıştır:
- `user_id` kolonları indexed
- `created_at` kolonları indexed
- Foreign key ilişkileri optimize edilmiş

---

## 🧪 Test Senaryoları

### Filtreleme Testi
1. Ürün listeleme sayfasına git
2. Fiyat slider'ını ayarla
3. Marka seç
4. "Filtreleri Uygula" tıkla
5. Sonuçların güncellendiğini kontrol et

### Kupon Testi
1. Sepete ürün ekle
2. Checkout sayfasına git
3. "HOSGELDIN15" kuponunu gir
4. İndirimin uygulandığını kontrol et

### AI Öneri Testi
1. Giriş yap
2. `/api/ai/recommendations` endpoint'ini çağır
3. Önerilerin kullanıcı geçmişine uygun olduğunu kontrol et

### Email Testi
1. Sepete ürün ekle
2. Sayfayı kapat (terk et)
3. 1 saat bekle
4. Terk edilmiş sepet emailini kontrol et

---

## 🎓 Best Practices

### Güvenlik
- Tüm API endpoint'leri CSRF korumalı
- Rate limiting uygulanmış
- SQL injection koruması (PDO Prepared Statements)
- XSS koruması (input sanitization)

### Performans
- AJAX ile sayfa yenilenmeden veri yükleme
- Debounce ile gereksiz API çağrılarını önleme
- Database indexing ile hızlı sorgu
- Cache kullanımı

### Kullanıcı Deneyimi
- Loading spinners (yükleme göstergeleri)
- Toast notifications (başarı/hata mesajları)
- Smooth animasyonlar
- Responsive tasarım

---

## 📞 Destek ve Yardım

Sorunlarla karşılaşırsanız:

1. **Debug Mode:** `.env` dosyasında `DEBUG_MODE=true` yapın
2. **Logları Kontrol Edin:** `logs/` klasöründeki log dosyalarını inceleyin
3. **API Test:** Postman ile API endpoint'lerini test edin

---

## 📝 Changelog

### v2.0.0 - 2025-11-11
- ✅ Gelişmiş filtreleme sistemi
- ✅ AJAX arama ve autocomplete
- ✅ iyzico + PayTR ödeme entegrasyonu
- ✅ Kupon sistemi
- ✅ Loyalty/puan sistemi
- ✅ Email marketing (terk edilmiş sepet)
- ✅ AI parfüm önerisi (OpenAI + DeepSeek)

---

**🎉 Tebrikler!** MINALIA artık profesyonel bir e-ticaret platformuna dönüştü.
