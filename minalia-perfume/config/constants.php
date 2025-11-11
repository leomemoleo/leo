<?php
/**
 * Application Constants
 * MINALIA Parfüm E-Ticaret Platformu
 */

// Load environment variables
require_once __DIR__ . '/database.php';
Database::getInstance();

// Base paths
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');
define('LOG_PATH', ROOT_PATH . '/logs');

// URLs
define('BASE_URL', rtrim($_ENV['SITE_URL'] ?? 'http://localhost', '/'));
define('ADMIN_URL', rtrim($_ENV['ADMIN_URL'] ?? BASE_URL . '/admin', '/'));
define('ASSETS_URL', BASE_URL . '/assets');
define('UPLOADS_URL', BASE_URL . '/uploads');

// Site Information
define('SITE_NAME', $_ENV['SITE_NAME'] ?? 'MINALIA Parfüm');
define('SITE_TAGLINE', $_ENV['SITE_TAGLINE'] ?? 'Lüks Parfüm Deneyimi');

// Database constants (already loaded via Database class)
define('DB_PREFIX', 'mp_'); // Table prefix if needed

// Security
define('SESSION_LIFETIME', $_ENV['SESSION_LIFETIME'] ?? 7200);
define('CSRF_TOKEN_EXPIRE', $_ENV['CSRF_TOKEN_EXPIRE'] ?? 3600);
define('SECURE_COOKIES', filter_var($_ENV['SECURE_COOKIES'] ?? false, FILTER_VALIDATE_BOOLEAN));
define('PASSWORD_MIN_LENGTH', 8);

// File Upload
define('MAX_FILE_SIZE', $_ENV['MAX_FILE_SIZE'] ?? 5242880); // 5MB
define('ALLOWED_IMAGE_TYPES', explode(',', $_ENV['ALLOWED_IMAGE_TYPES'] ?? 'jpg,jpeg,png,gif,webp'));
define('THUMBNAIL_WIDTH', 300);
define('THUMBNAIL_HEIGHT', 300);

// Pagination
define('PRODUCTS_PER_PAGE', 12);
define('REVIEWS_PER_PAGE', 10);
define('ORDERS_PER_PAGE', 20);

// Currency & Tax
define('CURRENCY', $_ENV['PAYMENT_CURRENCY'] ?? 'TRY');
define('CURRENCY_SYMBOL', '₺');
define('TAX_RATE', 20); // %20 KDV
define('FREE_SHIPPING_THRESHOLD', 500); // Free shipping over 500 TRY

// Email
define('SMTP_HOST', $_ENV['SMTP_HOST'] ?? 'localhost');
define('SMTP_PORT', $_ENV['SMTP_PORT'] ?? 587);
define('SMTP_USERNAME', $_ENV['SMTP_USERNAME'] ?? '');
define('SMTP_PASSWORD', $_ENV['SMTP_PASSWORD'] ?? '');
define('SMTP_FROM_EMAIL', $_ENV['SMTP_FROM_EMAIL'] ?? 'noreply@minalia.com');
define('SMTP_FROM_NAME', $_ENV['SMTP_FROM_NAME'] ?? SITE_NAME);

// Cache
define('CACHE_ENABLED', filter_var($_ENV['CACHE_ENABLED'] ?? true, FILTER_VALIDATE_BOOLEAN));
define('CACHE_LIFETIME', $_ENV['CACHE_LIFETIME'] ?? 3600);
define('CACHE_PATH', ROOT_PATH . '/cache');

// Debug & Logging
define('DEBUG_MODE', filter_var($_ENV['DEBUG_MODE'] ?? false, FILTER_VALIDATE_BOOLEAN));
define('ERROR_LOGGING', filter_var($_ENV['ERROR_LOGGING'] ?? true, FILTER_VALIDATE_BOOLEAN));

// Product Status
define('PRODUCT_STATUS_ACTIVE', 1);
define('PRODUCT_STATUS_INACTIVE', 0);
define('PRODUCT_STATUS_OUT_OF_STOCK', 2);

// Order Status
define('ORDER_STATUS_PENDING', 'pending');
define('ORDER_STATUS_PROCESSING', 'processing');
define('ORDER_STATUS_SHIPPED', 'shipped');
define('ORDER_STATUS_DELIVERED', 'delivered');
define('ORDER_STATUS_CANCELLED', 'cancelled');
define('ORDER_STATUS_REFUNDED', 'refunded');

// Payment Status
define('PAYMENT_STATUS_PENDING', 'pending');
define('PAYMENT_STATUS_PAID', 'paid');
define('PAYMENT_STATUS_FAILED', 'failed');
define('PAYMENT_STATUS_REFUNDED', 'refunded');

// User Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_MANAGER', 'manager');
define('ROLE_EDITOR', 'editor');
define('ROLE_USER', 'user');

// Session Keys
define('SESSION_USER_ID', 'user_id');
define('SESSION_USER_EMAIL', 'user_email');
define('SESSION_USER_NAME', 'user_name');
define('SESSION_ADMIN_ID', 'admin_id');
define('SESSION_ADMIN_ROLE', 'admin_role');
define('SESSION_CART', 'cart');
define('SESSION_WISHLIST', 'wishlist');

// Color Palette (from design requirements)
define('COLOR_PRIMARY', '#7A8B5C');     // Green
define('COLOR_DARK', '#1A1A1A');        // Dark background
define('COLOR_WHITE', '#FFFFFF');       // Pure white
define('COLOR_CREAM', '#F8F5F0');       // Cream background
define('COLOR_GOLD', '#D4AF37');        // Gold accent
define('COLOR_TEXT_PRIMARY', '#2C2C2C');
define('COLOR_TEXT_SECONDARY', '#666666');

// API Endpoints
define('API_BASE', BASE_URL . '/api');

// Error Messages
define('ERROR_REQUIRED_FIELD', 'Bu alan zorunludur.');
define('ERROR_INVALID_EMAIL', 'Geçerli bir e-posta adresi giriniz.');
define('ERROR_INVALID_PHONE', 'Geçerli bir telefon numarası giriniz.');
define('ERROR_WEAK_PASSWORD', 'Şifre en az ' . PASSWORD_MIN_LENGTH . ' karakter olmalıdır.');
define('ERROR_PASSWORD_MISMATCH', 'Şifreler eşleşmiyor.');
define('ERROR_INVALID_CREDENTIALS', 'E-posta veya şifre hatalı.');
define('ERROR_USER_EXISTS', 'Bu e-posta adresi zaten kayıtlı.');
define('ERROR_DATABASE', 'Veritabanı hatası oluştu.');
define('ERROR_FILE_UPLOAD', 'Dosya yükleme hatası.');
define('ERROR_INVALID_FILE_TYPE', 'Geçersiz dosya türü.');
define('ERROR_FILE_TOO_LARGE', 'Dosya boyutu çok büyük.');
define('ERROR_PERMISSION_DENIED', 'Bu işlem için yetkiniz yok.');
define('ERROR_NOT_FOUND', 'İstenen kayıt bulunamadı.');
define('ERROR_OUT_OF_STOCK', 'Ürün stokta yok.');

// Success Messages
define('SUCCESS_REGISTERED', 'Kayıt başarılı. Giriş yapabilirsiniz.');
define('SUCCESS_LOGIN', 'Giriş başarılı.');
define('SUCCESS_LOGOUT', 'Çıkış yapıldı.');
define('SUCCESS_PROFILE_UPDATED', 'Profil güncellendi.');
define('SUCCESS_PASSWORD_CHANGED', 'Şifre değiştirildi.');
define('SUCCESS_ADDED_TO_CART', 'Ürün sepete eklendi.');
define('SUCCESS_ADDED_TO_WISHLIST', 'Ürün favorilere eklendi.');
define('SUCCESS_ORDER_PLACED', 'Siparişiniz alındı.');
define('SUCCESS_REVIEW_SUBMITTED', 'Yorumunuz gönderildi.');

// Validation Patterns
define('PATTERN_EMAIL', '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');
define('PATTERN_PHONE', '/^[\d\s\-\+\(\)]{10,}$/');
define('PATTERN_POSTAL_CODE', '/^\d{5}$/');

// Set error reporting based on debug mode
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
    ini_set('display_errors', 0);
}

// Set timezone
date_default_timezone_set('Europe/Istanbul');

// Create required directories if they don't exist
$requiredDirs = [UPLOAD_PATH, LOG_PATH, CACHE_PATH];
foreach ($requiredDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}
