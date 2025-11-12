<?php
/**
 * Application Constants
 * MINALIA Parfüm E-Ticaret Platformu
 */

// Load config.php if exists (installed via wizard)
$configFile = __DIR__ . '/config.php';
if (file_exists($configFile)) {
    require_once $configFile;
}

// Load database
require_once __DIR__ . '/database.php';
Database::getInstance();

// Base paths
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('CONFIG_PATH', ROOT_PATH . '/config');
if (!defined('UPLOAD_PATH')) {
    define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');
}
define('LOG_PATH', ROOT_PATH . '/logs');

// URLs - Use from config.php if available
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/minalia-perfume');
}
if (!defined('ADMIN_URL')) {
    define('ADMIN_URL', BASE_URL . '/admin');
}
define('ASSETS_URL', BASE_URL . '/assets');
define('UPLOADS_URL', BASE_URL . '/uploads');

// Site Information - Use from config.php if available
if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'MINALIA Parfüm');
}
define('SITE_TAGLINE', 'Lüks Parfüm Deneyimi');

// Database constants
define('DB_PREFIX', 'mp_');

// Security
define('SESSION_LIFETIME', 7200);
define('CSRF_TOKEN_EXPIRE', 3600);
define('SECURE_COOKIES', false);
define('PASSWORD_MIN_LENGTH', 6);

// Session constants - Use from config.php if available
if (!defined('SESSION_PREFIX')) {
    define('SESSION_PREFIX', 'minalia_');
}
if (!defined('SESSION_ADMIN_ID')) {
    define('SESSION_ADMIN_ID', SESSION_PREFIX . 'admin_id');
}
if (!defined('SESSION_ADMIN_USERNAME')) {
    define('SESSION_ADMIN_USERNAME', SESSION_PREFIX . 'admin_username');
}
if (!defined('SESSION_ADMIN_EMAIL')) {
    define('SESSION_ADMIN_EMAIL', SESSION_PREFIX . 'admin_email');
}
if (!defined('SESSION_USER_ID')) {
    define('SESSION_USER_ID', SESSION_PREFIX . 'user_id');
}
if (!defined('SESSION_USER_EMAIL')) {
    define('SESSION_USER_EMAIL', SESSION_PREFIX . 'user_email');
}

// File Upload - Use from config.php if available
if (!defined('MAX_UPLOAD_SIZE')) {
    define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
}
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('THUMBNAIL_WIDTH', 300);
define('THUMBNAIL_HEIGHT', 300);

// Pagination
define('PRODUCTS_PER_PAGE', 12);
define('REVIEWS_PER_PAGE', 10);
define('ORDERS_PER_PAGE', 20);

// Currency & Tax
define('CURRENCY', 'TRY');
define('CURRENCY_SYMBOL', '₺');
define('TAX_RATE', 20);

// Email Settings
define('MAIL_FROM_NAME', SITE_NAME);
define('MAIL_FROM_ADDRESS', 'noreply@minalia.com.tr');

// Validation Patterns - Use from config.php if available
if (!defined('PATTERN_EMAIL')) {
    define('PATTERN_EMAIL', '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');
}
if (!defined('PATTERN_PHONE')) {
    define('PATTERN_PHONE', '/^[\d\s\-\+\(\)]+$/');
}

// Environment - Use from config.php if available
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'production');
}
if (!defined('DEBUG_MODE')) {
    define('DEBUG_MODE', false);
}

// Timezone
if (!ini_get('date.timezone')) {
    date_default_timezone_set('Europe/Istanbul');
}

// Error reporting based on environment
if (defined('DEBUG_MODE') && DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}
