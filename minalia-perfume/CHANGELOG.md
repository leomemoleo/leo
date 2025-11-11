# Changelog

Tüm önemli değişiklikler bu dosyada belgelenir.

## [2.0.0] - 2025-11-11

### ⭐ Yeni Özellikler

#### Admin Paneli (Tam Fonksiyonel)
- ✅ Profesyonel admin paneli tasarımı
- ✅ Admin kimlik doğrulama sistemi (login/logout)
- ✅ Dashboard ile kapsamlı istatistikler
  - Aylık satış karşılaştırması
  - Sipariş, müşteri, ürün metrikleri
  - Düşük stok uyarıları
  - Son siparişler ve yorumlar listesi
- ✅ Ürün yönetimi (CRUD)
  - Resim yükleme sistemi
  - Parfüm notaları yönetimi (üst, orta, alt)
  - Marka ve kategori seçimi
  - Stok ve fiyat kontrolü
  - Öne çıkan/Çok satan işaretleme
- ✅ **Sayfa yönetimi sistemi** ⭐
  - Dinamik statik sayfa oluşturma
  - HTML editör desteği
  - SEO ayarları (meta title, description)
  - Yayın durumu kontrolü (aktif/taslak)
  - URL slug yönetimi

#### Frontend Sayfalar
- ✅ Hakkımızda sayfası (admin'den yönetilebilir)
- ✅ İletişim sayfası (formlu)
  - İletişim formu
  - Sosyal medya linkleri
  - Telefon, WhatsApp, Email, Adres bilgileri
- ✅ Gizlilik Politikası
- ✅ Kullanım Koşulları
- ✅ Dinamik sayfa görüntüleme sistemi

#### Veritabanı
- ✅ `pages` tablosu (statik içerik yönetimi)
- ✅ `contact_messages` tablosu (iletişim formu kayıtları)
- ✅ 4 varsayılan sayfa eklendi

#### Admin UI/UX
- ✅ Responsive admin tasarımı
- ✅ Mobil menü desteği
- ✅ Flash mesaj sistemi
- ✅ Bildirim sistemi
- ✅ Aktivite loglama
- ✅ JavaScript interaktivite (tooltip, alert, form validation)

### 🔧 İyileştirmeler
- README.md tamamen güncellendi
- Proje yapısı dokümantasyonu eklendi
- Admin panel kurulum talimatları eklendi
- Test senaryoları dokümante edildi

### 📁 Yeni Dosyalar
```
admin/
├── index.php
├── controllers/
│   ├── AdminAuthController.php
│   ├── AdminDashboardController.php
│   ├── AdminProductController.php
│   └── AdminPageController.php
├── views/
│   ├── layouts/main.php
│   ├── pages/dashboard.php
│   ├── pages/products/
│   └── pages/page-management/
└── assets/
    ├── css/admin.css
    └── js/admin.js

app/controllers/PageController.php
app/views/pages/
├── page-detail.php
└── contact.php

database/
├── pages_table.sql
└── contact_messages_table.sql
```

---

## [1.0.0] - 2025-11-11

### ⭐ İlk Sürüm

#### Gelişmiş E-Ticaret Özellikleri
- ✅ Gelişmiş filtreleme sistemi
  - Dual-handle fiyat slider
  - Marka, cinsiyet, parfüm notaları
  - Stok durumu ve rating filtreleri
  - AJAX dinamik yükleme
- ✅ **AI Parfüm Önerileri**
  - OpenAI GPT-4 entegrasyonu
  - DeepSeek AI desteği
  - Kişiselleştirilmiş öneriler
  - Quiz tabanlı öneriler
- ✅ **Kupon ve İndirim Sistemi**
  - Yüzde/Sabit tutar indirimleri
  - Minimum sipariş tutarı
  - Kullanım limitleri
  - Geçerlilik tarihleri
- ✅ **Sadakat Puanı Sistemi**
  - VIP seviyeleri (Bronze, Silver, Gold, Platinum)
  - Puan kazanma (alışveriş, yorum, doğum günü)
  - Puan kullanımı (100 puan = 10 TL)
- ✅ **Email Pazarlama**
  - Terk edilmiş sepet e-postaları
  - Doğum günü kampanyaları
  - Newsletter sistemi
- ✅ **Ödeme Entegrasyonları**
  - iyzico (3D Secure, taksit)
  - PayTR desteği
  - Adapter pattern ile esnek yapı
- ✅ **Stok Uyarı Sistemi**

#### Core Özellikler
- ✅ MVC mimarisi
- ✅ Veritabanı şeması (17+ tablo)
- ✅ Authentication sistemi
- ✅ Ana sayfa ve ürün listeleme
- ✅ Hero slider
- ✅ Responsive tasarım
- ✅ Sepet ve wishlist (localStorage)
- ✅ JavaScript interaktif özellikler

#### Güvenlik
- ✅ Password hashing (bcrypt)
- ✅ SQL Injection koruması
- ✅ XSS koruması
- ✅ CSRF token
- ✅ Rate limiting
- ✅ Secure sessions

#### API Endpoints
- ✅ `/api/products/filter` - Ürün filtreleme
- ✅ `/api/search/suggestions` - Arama önerileri
- ✅ `/api/coupon/validate` - Kupon doğrulama
- ✅ `/api/ai/recommendations` - AI önerileri
- ✅ `/api/ai/quiz` - Quiz önerileri
- ✅ `/api/loyalty/points` - Puan sorgulama
- ✅ `/api/stock/alert` - Stok uyarı kaydı

---

## Format

Bu changelog [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) formatını takip eder ve [Semantic Versioning](https://semver.org/spec/v2.0.0.html) kullanır.

### Kategoriler
- **Yeni Özellikler** - Yeni eklemeler
- **İyileştirmeler** - Mevcut özelliklerdeki iyileştirmeler
- **Hata Düzeltmeleri** - Bug fix'ler
- **Değişiklikler** - Breaking changes
- **Kaldırılanlar** - Deprecated özellikler
- **Güvenlik** - Güvenlik iyileştirmeleri
