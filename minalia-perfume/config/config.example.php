<?php
/**
 * MINALIA Configuration File - EXAMPLE
 * Copy this to config.php and update with your settings
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'minalia_perfume');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site Configuration
define('BASE_URL', 'http://localhost/minalia-perfume');
define('SITE_NAME', 'MINALIA Parfüm');

// Security
define('SESSION_PREFIX', 'minalia_');
define('SESSION_ADMIN_ID', SESSION_PREFIX . 'admin_id');
define('SESSION_ADMIN_USERNAME', SESSION_PREFIX . 'admin_username');
define('SESSION_ADMIN_EMAIL', SESSION_PREFIX . 'admin_email');
define('SESSION_USER_ID', SESSION_PREFIX . 'user_id');
define('SESSION_USER_EMAIL', SESSION_PREFIX . 'user_email');

// File Upload
define('UPLOAD_PATH', __DIR__ . '/../public/uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

// Validation Patterns
define('PATTERN_EMAIL', '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');
define('PATTERN_PHONE', '/^[\d\s\-\+\(\)]+$/');

// Environment
define('ENVIRONMENT', 'production'); // production, development
define('DEBUG_MODE', false);

// Timezone
date_default_timezone_set('Europe/Istanbul');
