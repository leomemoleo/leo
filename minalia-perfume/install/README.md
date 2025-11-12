# 🚀 MINALIA Installation Wizard

WordPress-style easy installation for MINALIA Parfüm E-Commerce Platform

## ✨ Features

- **5-Step Wizard** - Easy and intuitive installation process
- **System Requirements Check** - Automatic verification before installation
- **Database Auto-Setup** - Creates database and tables automatically
- **Demo Data** - Optional sample products, categories, and brands
- **Config Generator** - Automatic configuration file creation
- **One-Click Install** - Complete setup in under 5 minutes
- **Lock Mechanism** - Prevents accidental reinstallation

## 📋 Requirements

- PHP >= 7.4
- MySQL/MariaDB 5.7+
- PDO Extension
- PDO MySQL Extension
- GD Extension (for images)
- cURL Extension
- JSON Extension
- Writable directories:
  - `/config`
  - `/public/uploads`

## 🎯 Installation Steps

### 1. Upload Files

Upload all MINALIA files to your web server.

### 2. Access Installer

Navigate to:
```
http://yourwebsite.com/install/
```

### 3. Follow the Wizard

**Step 1: Welcome & System Check**
- Review system requirements
- Ensure all checks pass ✓
- Click "Kuruluma Başla"

**Step 2: Database Configuration**
- Enter database host (usually `localhost`)
- Enter database name (will be created if not exists)
- Enter MySQL username
- Enter MySQL password
- Click "Bağlantıyı Test Et & Devam"

**Step 3: Site Configuration**
- Enter site name
- Enter site URL
- Create admin account:
  - Admin email
  - Admin username
  - Admin password (min 6 characters)
- Choose demo data option (recommended for testing)
- Click "Kuruluma Başla"

**Step 4: Installation**
- System installs automatically:
  - Database connection
  - Create tables
  - Install settings
  - Create admin user
  - Load demo data (if selected)
  - Generate config file
- Click "Kurulumu Başlat"

**Step 5: Complete!**
- Installation successful! 🎉
- Note your admin credentials
- Click "Admin Paneline Git" or "Siteyi Görüntüle"

### 4. Post-Installation

**Important Security Steps:**
1. Delete `/install` directory
2. Configure settings in Admin Panel → Settings
3. Set up SMTP email settings
4. Configure payment gateways (iyzico, Stripe, PayPal)
5. Update SEO and social media settings

## 🎨 Demo Data

If you selected "Install Demo Data", the following will be loaded:

**Products:** 10 luxury perfumes
- Chanel No. 5
- Bleu de Chanel
- Dior Sauvage
- Miss Dior Blooming Bouquet
- Tom Ford Oud Wood
- Tom Ford Black Orchid
- Creed Aventus
- Creed Silver Mountain Water
- Jo Malone Wood Sage & Sea Salt
- Jo Malone English Pear & Freesia

**Categories:** 4 categories
- Erkek Parfüm (Men's Perfume)
- Kadın Parfüm (Women's Perfume)
- Unisex Parfüm (Unisex Perfume)
- Niş Parfüm (Niche Perfume)

**Brands:** 5 luxury brands
- Chanel
- Dior
- Tom Ford
- Creed
- Jo Malone

**Plus:**
- Sample product reviews
- Test user account (test@minalia.com / test123)
- Newsletter subscribers
- Product variants (sizes)
- Product images (placeholders)

## 🔒 Security

**Lock File:**
After installation, a `install.lock` file is created to prevent reinstallation.

To reinstall:
1. Delete `install.lock` file
2. Delete or backup existing database
3. Delete `/config/config.php`
4. Access `/install/` again

## ⚙️ Manual Configuration

If automatic config creation fails, create `/config/config.php` manually:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'minalia_perfume');
define('DB_USER', 'root');
define('DB_PASS', '');

define('BASE_URL', 'http://yourwebsite.com');
define('SITE_NAME', 'MINALIA Parfüm');

// Session constants
define('SESSION_PREFIX', 'minalia_');
define('SESSION_ADMIN_ID', SESSION_PREFIX . 'admin_id');
define('SESSION_ADMIN_USERNAME', SESSION_PREFIX . 'admin_username');
define('SESSION_ADMIN_EMAIL', SESSION_PREFIX . 'admin_email');
define('SESSION_USER_ID', SESSION_PREFIX . 'user_id');
define('SESSION_USER_EMAIL', SESSION_PREFIX . 'user_email');

// File Upload
define('UPLOAD_PATH', __DIR__ . '/../public/uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);

// Validation
define('PATTERN_EMAIL', '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');
define('PATTERN_PHONE', '/^[\d\s\-\+\(\)]+$/');

// Environment
define('ENVIRONMENT', 'production');
define('DEBUG_MODE', false);

date_default_timezone_set('Europe/Istanbul');
```

## 🐛 Troubleshooting

### Installation Fails

**Database Connection Error:**
- Check database credentials
- Ensure MySQL is running
- Verify user has CREATE DATABASE privilege

**File Permission Error:**
- Make `/config` writable (755 or 777)
- Make `/public/uploads` writable (755 or 777)

**White Screen:**
- Check PHP error logs
- Verify PHP version >= 7.4
- Enable display_errors in php.ini for debugging

**Missing Extensions:**
- Install required PHP extensions
- Restart web server after installation

### Reset Installation

1. Delete `install.lock`
2. Drop database or delete tables
3. Delete `/config/config.php`
4. Access `/install/` again

## 🔄 Upgrading Existing Installation

If you already have MINALIA installed and want to upgrade to a new version:

### Automatic Migration (Recommended)

```bash
# Run the migration script
php install/migrate.php
```

The migration script will:
- ✅ Check current database version
- ✅ Run only pending migrations
- ✅ Track executed migrations
- ✅ Rollback on errors

### Manual Migration

Run specific migration files:
```bash
mysql -u username -p database_name < install/migrations/015_add_search_indexes.sql
```

**See:** `install/UPGRADE.md` for detailed upgrade instructions

## 📞 Support

For issues:
1. Check `/install/` directory permissions
2. Check `/database/` SQL files exist
3. Verify PHP error logs
4. Ensure MySQL credentials are correct
5. For upgrades, see `UPGRADE.md`

## ⚡ Quick Install

One-line installer access:
```
http://yourwebsite.com/install/
```

That's it! The wizard handles everything else automatically.

---

**Installation Time:** ~3-5 minutes
**Difficulty:** ⭐ (Very Easy)
**Required Knowledge:** Basic (just database credentials)

Built with ❤️ for MINALIA Parfüm Platform
