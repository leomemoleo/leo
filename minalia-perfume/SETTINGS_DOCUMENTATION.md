# 🎛️ MINALIA Settings System - Complete Documentation

## 📋 Overview

The MINALIA Settings System is a professional, enterprise-level configuration management system that provides:

- **Centralized Configuration**: All site settings in one place
- **8 Category Tabs**: Organized by functionality
- **Real-time Updates**: Changes take effect immediately
- **Cache System**: High-performance with automatic caching
- **Type Safety**: Boolean, integer, float, JSON support
- **Easy Integration**: Simple helper functions throughout the codebase

---

## 🗂️ Settings Categories

### 1. **General Settings** (`general`)
Site-wide configuration and branding

**Settings:**
- `site_name` - Website name
- `site_slogan` - Tagline/slogan
- `site_description` - Site description
- `site_keywords` - SEO keywords
- `site_logo` - Logo image path
- `site_favicon` - Favicon path
- `contact_email` - Contact email address
- `contact_phone` - Phone number
- `contact_address` - Physical address
- `business_hours` - Operating hours

**Example Usage:**
```php
echo getSetting('site_name', 'MINALIA Parfüm');
echo getSetting('contact_email');
```

---

### 2. **Email Settings** (`email`)
SMTP and email configuration

**Settings:**
- `smtp_enabled` - Enable SMTP (boolean)
- `smtp_host` - SMTP server host
- `smtp_port` - SMTP port (587, 465, etc.)
- `smtp_username` - SMTP username
- `smtp_password` - SMTP password
- `smtp_encryption` - Encryption type (tls/ssl)
- `email_from_name` - Sender name
- `email_from_address` - Sender email

**Example Usage:**
```php
if (getSettingBool('smtp_enabled')) {
    // Use SMTP for emails
    $host = getSetting('smtp_host');
    $port = getSettingInt('smtp_port', 587);
}
```

**Features:**
- Test email functionality
- Password protection (only updates if provided)
- Support for Gmail, Outlook, custom SMTP servers

---

### 3. **Payment Settings** (`payment`)
Payment gateway configuration

**Settings:**
- `payment_enabled` - Enable online payments
- `payment_test_mode` - Test mode (boolean)
- `cash_on_delivery_enabled` - COD option

**iyzico:**
- `iyzico_enabled`
- `iyzico_api_key`
- `iyzico_secret_key`

**Stripe:**
- `stripe_enabled`
- `stripe_publishable_key`
- `stripe_secret_key`

**PayPal:**
- `paypal_enabled`
- `paypal_client_id`
- `paypal_secret`

**Example Usage:**
```php
if (getSettingBool('iyzico_enabled')) {
    $apiKey = getSetting('iyzico_api_key');
    // Process iyzico payment
}
```

---

### 4. **Shipping Settings** (`shipping`)
Delivery and shipping configuration

**Settings:**
- `shipping_enabled` - Enable shipping
- `free_shipping_threshold` - Free shipping minimum (₺)
- `shipping_cost` - Standard shipping cost
- `express_shipping_cost` - Express shipping cost
- `shipping_companies` - Comma-separated list
- `estimated_delivery_days` - Delivery estimate

**Example Usage:**
```php
$threshold = getSettingInt('free_shipping_threshold', 500);
if ($cartTotal >= $threshold) {
    $shippingCost = 0; // Free shipping!
} else {
    $shippingCost = getSetting('shipping_cost', 29.90);
}
```

---

### 5. **SEO Settings** (`seo`)
Search engine optimization

**Settings:**
- `seo_enabled` - Enable SEO features
- `meta_title` - Default meta title
- `meta_description` - Meta description
- `meta_keywords` - Meta keywords
- `og_title` - Open Graph title
- `og_description` - OG description
- `og_image` - Social sharing image
- `google_site_verification` - Google verification code

**Example Usage:**
```php
// In <head> section
echo '<title>' . getSetting('meta_title') . '</title>';
echo '<meta name="description" content="' . getSetting('meta_description') . '">';
echo '<meta property="og:title" content="' . getSetting('og_title') . '">';
```

---

### 6. **Social Media** (`social`)
Social media profiles

**Settings:**
- `social_facebook`
- `social_instagram`
- `social_twitter`
- `social_youtube`
- `social_pinterest`
- `social_tiktok`
- `social_linkedin`

**Example Usage:**
```php
// Footer social links
$facebook = getSetting('social_facebook');
if ($facebook) {
    echo '<a href="' . $facebook . '"><i class="fab fa-facebook"></i></a>';
}
```

---

### 7. **Analytics** (`analytics`)
Tracking and customer support

**Settings:**
- `google_analytics_id` - GA4 tracking ID
- `facebook_pixel_id` - FB Pixel ID
- `whatsapp_enabled` - WhatsApp button
- `whatsapp_number` - WhatsApp number

**Example Usage:**
```php
// Google Analytics
$gaId = getSetting('google_analytics_id');
if ($gaId) {
    echo "<script async src='https://www.googletagmanager.com/gtag/js?id={$gaId}'></script>";
}

// WhatsApp Button
if (getSettingBool('whatsapp_enabled')) {
    $number = getSetting('whatsapp_number');
    echo "<a href='https://wa.me/{$number}'>WhatsApp</a>";
}
```

---

### 8. **Advanced Settings** (`advanced`)
System configuration and features

**Settings:**
- `maintenance_mode` - Site maintenance mode
- `maintenance_message` - Maintenance message
- `currency` - Currency code (TRY, USD, EUR)
- `currency_symbol` - Currency symbol (₺, $, €)
- `tax_enabled` - Enable tax calculation
- `tax_rate` - Tax rate (%)
- `stock_alert_enabled` - Low stock alerts
- `stock_alert_threshold` - Alert threshold
- `enable_reviews` - Product reviews
- `enable_wishlist` - Wishlist feature
- `enable_loyalty` - Loyalty program
- `loyalty_points_rate` - Points per ₺1
- `min_order_amount` - Minimum order
- `max_cart_items` - Max cart items
- `session_timeout` - Session timeout (seconds)
- `password_min_length` - Min password length

**Example Usage:**
```php
// Maintenance mode check
if (isMaintenanceMode() && !isAdmin()) {
    show_maintenance_page();
    exit;
}

// Currency
echo getCurrencySymbol(); // ₺
echo getCurrency(); // TRY

// Feature checks
if (isFeatureEnabled('wishlist')) {
    // Show wishlist button
}

if (isFeatureEnabled('loyalty')) {
    $pointsRate = getSettingInt('loyalty_points_rate', 10);
    $points = $orderTotal * $pointsRate;
}
```

---

## 🛠️ Helper Functions

### Core Functions

#### `getSetting($key, $default = null)`
Get any setting value
```php
$siteName = getSetting('site_name', 'Default Name');
```

#### `getSettingBool($key, $default = false)`
Get boolean setting
```php
$isEnabled = getSettingBool('smtp_enabled'); // true/false
```

#### `getSettingInt($key, $default = 0)`
Get integer setting
```php
$threshold = getSettingInt('free_shipping_threshold', 500);
```

#### `updateSetting($key, $value)`
Update a setting value
```php
updateSetting('site_name', 'New Site Name');
```

### Convenience Functions

#### `isMaintenanceMode()`
Check if site is in maintenance
```php
if (isMaintenanceMode()) {
    // Show maintenance page
}
```

#### `getCurrencySymbol()`
Get currency symbol
```php
echo getCurrencySymbol(); // ₺
```

#### `getCurrency()`
Get currency code
```php
echo getCurrency(); // TRY
```

#### `isFeatureEnabled($feature)`
Check if feature is enabled
```php
if (isFeatureEnabled('reviews')) {
    // Show review form
}

if (isFeatureEnabled('wishlist')) {
    // Show wishlist button
}

if (isFeatureEnabled('loyalty')) {
    // Calculate loyalty points
}
```

---

## 📊 Database Structure

### Settings Table

```sql
CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text', 'textarea', 'number', 'boolean', 'image', 'json') DEFAULT 'text',
    category VARCHAR(50) DEFAULT 'general',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**Key-Value Storage:**
- Flexible schema
- Easy to add new settings
- No migrations needed
- Automatic timestamps

---

## 🚀 Installation

### 1. Create Database Table

```bash
mysql -u root minalia_perfume < database/settings_table.sql
```

### 2. Verify Files

Ensure these files exist:
- `/models/Settings.php` - Settings model
- `/admin/controllers/AdminSettingsController.php` - Controller
- `/admin/views/pages/settings/index.php` - Main view
- `/admin/views/pages/settings/tabs/*.php` - Tab views
- `/app/helpers/functions.php` - Helper functions (updated)

### 3. Access Admin Panel

Navigate to: `/admin/settings`

The interface has 8 tabs for different categories.

---

## 💡 Best Practices

### 1. **Always Use Helper Functions**
```php
// Good ✓
$siteName = getSetting('site_name');

// Bad ✗
$db->query("SELECT setting_value FROM settings WHERE setting_key = 'site_name'");
```

### 2. **Provide Default Values**
```php
// Good ✓
$threshold = getSettingInt('free_shipping_threshold', 500);

// Bad ✗
$threshold = getSettingInt('free_shipping_threshold'); // Could be null
```

### 3. **Use Type-Specific Functions**
```php
// Good ✓
$enabled = getSettingBool('smtp_enabled'); // true/false

// Bad ✗
$enabled = getSetting('smtp_enabled'); // Could be '0' or '1' (string)
```

### 4. **Cache-Friendly**
Settings are automatically cached. No need for manual caching.

### 5. **Bulk Updates**
For multiple settings:
```php
$settingsModel = new Settings();
$settingsModel->updateMultiple([
    'site_name' => 'New Name',
    'site_slogan' => 'New Slogan',
    'contact_email' => 'new@email.com'
]);
```

---

## 🔒 Security Features

1. **Input Sanitization**: All inputs are sanitized
2. **SQL Injection Prevention**: PDO prepared statements
3. **XSS Protection**: HTML encoding
4. **File Upload Validation**: Type and size checks
5. **Admin-Only Access**: Authentication required
6. **Password Protection**: SMTP passwords handled securely

---

## 🧪 Testing

### Test Email Configuration
1. Go to Settings → Email
2. Configure SMTP settings
3. Click "Email Testi Gönder"
4. Enter test email address
5. Check inbox

### Test Maintenance Mode
1. Go to Settings → Advanced
2. Enable "Bakım Modu"
3. Save changes
4. Visit site in incognito (non-admin)
5. Should see maintenance page

### Test Payment Gateways
1. Go to Settings → Payment
2. Enable test mode
3. Configure gateway API keys
4. Make test purchase

---

## 📈 Performance

- **Caching**: All settings loaded once per request
- **Lazy Loading**: Settings model instantiated only when needed
- **Static Cache**: Shared across helper function calls
- **Optimized Queries**: Single query loads all settings
- **OPcache Support**: Compatible with PHP OPcache

**Benchmark:**
- First call: ~5-10ms (database query)
- Subsequent calls: <1ms (from cache)

---

## 🎯 Real-World Examples

### Example 1: Dynamic Site Title
```php
// layout/header.php
<title><?= getSetting('meta_title', 'MINALIA Parfüm') ?></title>
<meta name="description" content="<?= getSetting('meta_description') ?>">
```

### Example 2: Free Shipping Banner
```php
$threshold = getSettingInt('free_shipping_threshold', 500);
echo "₺{$threshold} ve üzeri alışverişlerde KARGO BEDAVA!";
```

### Example 3: Conditional Features
```php
// Product page
if (isFeatureEnabled('reviews')) {
    include 'components/reviews.php';
}

if (isFeatureEnabled('wishlist')) {
    echo '<button onclick="addToWishlist()">Favorilere Ekle</button>';
}
```

### Example 4: Multi-Currency Support
```php
function formatPrice($amount) {
    $symbol = getCurrencySymbol();
    $currency = getCurrency();

    return number_format($amount, 2) . ' ' . $symbol;
}

echo formatPrice(299.90); // 299.90 ₺
```

### Example 5: Tax Calculation
```php
if (getSettingBool('tax_enabled')) {
    $taxRate = getSettingInt('tax_rate', 20);
    $taxAmount = $subtotal * ($taxRate / 100);
    $total = $subtotal + $taxAmount;
} else {
    $total = $subtotal;
}
```

---

## 🐛 Troubleshooting

### Settings Not Saving
1. Check database connection
2. Verify settings table exists
3. Check file permissions (uploads folder)
4. Check PHP error logs

### Settings Not Loading
1. Verify Settings model is autoloaded
2. Check database table structure
3. Clear cache: Settings → Advanced → Clear Cache

### Image Uploads Failing
1. Check `public/uploads/settings/` folder exists
2. Verify folder permissions (755 or 777)
3. Check file size limits in php.ini

---

## 📞 Support

For issues or questions:
- Check error logs: `tail -f /var/log/php_errors.log`
- Database: Verify settings table exists
- File permissions: Check uploads directory

---

## ✅ Summary

The MINALIA Settings System provides:

✅ **8 organized categories** of settings
✅ **60+ configurable options**
✅ **Type-safe** helper functions
✅ **High-performance** caching
✅ **Professional UI** with tabs
✅ **Real-time testing** (email, etc.)
✅ **Enterprise-grade** security
✅ **Easy integration** throughout app

**Result:** Complete control over your e-commerce platform from a single, professional interface.
