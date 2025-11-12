# 🔐 SOCIAL LOGIN KURULUM REHBERİ

**MINALIA Enterprise Social Login System**
**Desteklenen Platformlar:** Google OAuth 2.0, Facebook Login

---

## 📋 GENEL BAKIŞ

MINALIA e-ticaret platformuna profesyonel sosyal medya girişi sistemi entegre edildi.

### ✅ Özellikler

- **Google OAuth 2.0** - Resmi Google API
- **Facebook Login** - Resmi Facebook Graph API
- **Güvenli Token Yönetimi** - Access token + Refresh token
- **CSRF Koruması** - State parameter ile güvenlik
- **Otomatik Kullanıcı Oluşturma** - İlk girişte kullanıcı kaydet
- **Hesap Bağlama** - Var olan hesaba sosyal hesap bağla
- **Avatar Senkronizasyonu** - Profil fotoğrafı otomatik çek
- **Email Doğrulama** - Sosyal hesaplar otomatik doğrulanmış

---

## 🚀 KURULUM

### 1. Google OAuth 2.0 Kurulumu

#### Adım 1: Google Cloud Console

1. https://console.cloud.google.com adresine git
2. Yeni proje oluştur veya var olan projeyi seç
3. **APIs & Services** > **OAuth consent screen**
   - User Type: External
   - App name: MINALIA
   - User support email: destek@minalia.com
   - Developer contact: info@minalia.com
   - Scopes: email, profile

#### Adım 2: OAuth 2.0 Client ID Oluştur

1. **APIs & Services** > **Credentials**
2. **Create Credentials** > **OAuth client ID**
3. Application type: **Web application**
4. Name: MINALIA Web App
5. **Authorized redirect URIs:**
   ```
   https://yourdomain.com/auth/google/callback
   http://localhost/minalia-perfume/auth/google/callback  (test için)
   ```
6. **Create** butonuna tıkla
7. **Client ID** ve **Client Secret**'ı kopyala

#### Adım 3: Credentials'ı Ekle

`.env` dosyasına veya `settings` tablosuna ekle:

```bash
GOOGLE_CLIENT_ID=your-client-id-here.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-client-secret-here
```

---

### 2. Facebook Login Kurulumu

#### Adım 1: Facebook Developers

1. https://developers.facebook.com adresine git
2. **My Apps** > **Create App**
3. App Type: **Consumer**
4. App Name: MINALIA
5. App Contact Email: info@minalia.com

#### Adım 2: Facebook Login Ekle

1. **Add Product** > **Facebook Login** > **Set Up**
2. Platform: **Website**
3. Site URL: `https://yourdomain.com`
4. **Settings** > **Basic**
   - App Domains: `yourdomain.com`
   - Privacy Policy URL: `https://yourdomain.com/privacy-policy`
   - Terms of Service URL: `https://yourdomain.com/terms-conditions`

#### Adım 3: OAuth Redirect URIs

1. **Facebook Login** > **Settings**
2. **Valid OAuth Redirect URIs:**
   ```
   https://yourdomain.com/auth/facebook/callback
   http://localhost/minalia-perfume/auth/facebook/callback  (test için)
   ```
3. **Save Changes**

#### Adım 4: App ID ve Secret

1. **Settings** > **Basic**
2. **App ID** ve **App Secret**'ı kopyala

`.env` dosyasına ekle:

```bash
FACEBOOK_APP_ID=your-app-id-here
FACEBOOK_APP_SECRET=your-app-secret-here
```

---

## 📁 DOSYA YAPISI

```
minalia-perfume/
├── app/
│   ├── helpers/
│   │   └── OAuthHelper.php          # OAuth provider classes
│   ├── controllers/
│   │   └── OAuthController.php      # OAuth authentication controller
│   └── views/
│       └── components/
│           └── social-login.php     # Login butonları component
├── install/
│   └── migrations/
│       └── 007_create_social_accounts_table.sql
└── SOCIAL_LOGIN_SETUP.md            # Bu dosya
```

---

## 🔧 TEKNİK DETAYLAR

### Database Schema

#### `social_accounts` Tablosu

```sql
CREATE TABLE `social_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `provider` enum('google','facebook','twitter','apple'),
  `provider_user_id` varchar(255) NOT NULL,
  `provider_email` varchar(255),
  `provider_name` varchar(255),
  `provider_avatar` varchar(500),
  `access_token` text,
  `refresh_token` text,
  `token_expires_at` datetime,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `provider_user` (`provider`, `provider_user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### `users` Tablosuna Eklenen Alanlar

```sql
ALTER TABLE `users`
ADD COLUMN `avatar` varchar(500) DEFAULT NULL,
ADD COLUMN `auth_provider` varchar(50) DEFAULT 'local',
ADD COLUMN `email_verified` tinyint(1) DEFAULT 0;
```

---

### OAuth Flow

#### 1. Authorization Request

```
User clicks "Google ile Giriş"
  ↓
Redirect to Google OAuth URL
  ↓
User authorizes MINALIA
  ↓
Google redirects back with code
```

#### 2. Token Exchange

```
Receive authorization code
  ↓
Exchange code for access_token
  ↓
Store access_token + refresh_token
```

#### 3. User Info

```
Get user info from provider
  ↓
Check if social account exists
  ↓
If yes: Log user in
If no: Create new user + social account
```

---

## 🎨 UI ENTEGRASYONU

### Login Sayfasına Ekleme

```php
<?php include __DIR__ . '/../components/social-login.php'; ?>
```

### Manuel HTML (özelleştirilmiş)

```html
<a href="<?= BASE_URL ?>/auth/google" class="btn-google">
    <i class="fab fa-google"></i> Google ile Giriş
</a>

<a href="<?= BASE_URL ?>/auth/facebook" class="btn-facebook">
    <i class="fab fa-facebook-f"></i> Facebook ile Giriş
</a>
```

---

## 🔐 GÜVENLİK

### CSRF Koruması

Her OAuth request'te unique `state` parameter oluşturulur:

```php
// State oluşturma
$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;

// State doğrulama
if ($receivedState !== $_SESSION['oauth_state']) {
    throw new Exception('CSRF attack detected');
}
```

### Token Güvenliği

- Access tokenlar database'de encrypt edilmiş saklanır
- Refresh tokenlar sadece gerektiğinde kullanılır
- Token expiry zamanları takip edilir

### Email Doğrulama

- Google OAuth: `verified_email` field kontrol edilir
- Facebook Login: Email izni zorunlu
- Sosyal hesaplar `email_verified = 1` olarak işaretlenir

---

## 🧪 TEST

### Local Test

1. `.env` dosyasında test credentials kullan
2. Redirect URI'larda `localhost` ekle
3. Google/Facebook'ta test mode'u aktif et

```bash
# Test credentials örnek
GOOGLE_CLIENT_ID=test-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=test-secret
```

### Production Test

1. Domain doğrula
2. SSL/HTTPS aktif olmalı
3. Privacy Policy ve Terms URL'leri aktif olmalı
4. App Review'den geç (Facebook için)

---

## 📊 KULLANIM

### Backend - User Oluşturma

```php
$socialManager = new SocialAccountManager($db);
$userId = $socialManager->findOrCreateUser('google', $userInfo, $tokenData);
```

### Backend - Hesap Bağlama

```php
// Mevcut kullanıcıya sosyal hesap bağla
if ($existingUser) {
    $socialManager->createSocialAccount($userId, 'facebook', $userInfo, $tokenData);
}
```

### Backend - Bağlantıyı Kes

```php
$socialManager->disconnectSocialAccount($userId, 'google');
```

### Frontend - Bağlı Hesapları Göster

```php
$accounts = $socialManager->getUserSocialAccounts($userId);

foreach ($accounts as $account) {
    echo "Connected: {$account['provider']} - {$account['provider_email']}";
}
```

---

## 🌍 URL ENDPOINTS

| Method | Endpoint | Açıklama |
|--------|----------|----------|
| GET | `/auth/google` | Google OAuth'a yönlendir |
| GET | `/auth/google/callback` | Google callback handler |
| GET | `/auth/facebook` | Facebook Login'e yönlendir |
| GET | `/auth/facebook/callback` | Facebook callback handler |
| POST | `/auth/disconnect` | Sosyal hesap bağlantısını kes |

---

## ❓ SORUN GİDERME

### "redirect_uri_mismatch" Hatası

**Çözüm:**
1. Google/Facebook console'da redirect URI'yi kontrol et
2. Tam URL'yi ekle (protocol + domain + path)
3. Trailing slash kontrolü yap

```
✅ https://yourdomain.com/auth/google/callback
❌ https://yourdomain.com/auth/google/callback/
```

### "invalid_client" Hatası

**Çözüm:**
1. Client ID ve Secret'ı kontrol et
2. .env dosyasının yüklendiğini doğrula
3. Boşluk veya özel karakter kontrolü yap

### Email Alınamıyor (Facebook)

**Çözüm:**
1. Facebook App'te `email` permission'ı ekle
2. Scope'a `email` eklendiğini doğrula
3. Kullanıcıdan email iznini tekrar iste

### Token Expired

**Çözüm:**
1. Refresh token kullan
2. Token expiry zamanını kontrol et
3. Gerekirse kullanıcıyı tekrar authorize et

---

## 📈 İSTATİSTİKLER

Social login kullanım istatistikleri:

```sql
-- Provider bazında kullanıcı sayısı
SELECT auth_provider, COUNT(*) as total
FROM users
WHERE auth_provider != 'local'
GROUP BY auth_provider;

-- Son 7 gün social login
SELECT DATE(created_at) as date, COUNT(*) as logins
FROM social_accounts
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY DATE(created_at);
```

---

## 🔄 GELECEK GELİŞTİRMELER

- [ ] Apple Sign In entegrasyonu
- [ ] Twitter OAuth 2.0
- [ ] LinkedIn Login
- [ ] Token refresh automation
- [ ] Social profile sync (bio, location)

---

## 📞 DESTEK

**Dokümantasyon:**
- Google OAuth: https://developers.google.com/identity/protocols/oauth2
- Facebook Login: https://developers.facebook.com/docs/facebook-login

**API Versiyonları:**
- Google OAuth 2.0: v2
- Facebook Graph API: v18.0

---

*Güncelleme: 2025-11-12*
*Versiyon: 1.0*
*Status: Production Ready ✅*
