<?php
/**
 * Helper Functions
 * MINALIA Parfüm E-Ticaret Platformu
 */

/**
 * Sanitize input data
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match(PATTERN_EMAIL, $email);
}

/**
 * Validate phone
 */
function isValidPhone($phone) {
    return preg_match(PATTERN_PHONE, $phone);
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token']) ||
        !isset($_SESSION['csrf_token_time']) ||
        (time() - $_SESSION['csrf_token_time']) > CSRF_TOKEN_EXPIRE) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
        return false;
    }

    if ((time() - $_SESSION['csrf_token_time']) > CSRF_TOKEN_EXPIRE) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Redirect to URL
 */
function redirect($url, $statusCode = 302) {
    header("Location: $url", true, $statusCode);
    exit();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION[SESSION_USER_ID]) && !empty($_SESSION[SESSION_USER_ID]);
}

/**
 * Check if admin is logged in
 */
function isAdminLoggedIn() {
    return isset($_SESSION[SESSION_ADMIN_ID]) && !empty($_SESSION[SESSION_ADMIN_ID]);
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return $_SESSION[SESSION_USER_ID] ?? null;
}

/**
 * Get current admin ID
 */
function getCurrentAdminId() {
    return $_SESSION[SESSION_ADMIN_ID] ?? null;
}

/**
 * Format price
 */
function formatPrice($price, $showCurrency = true) {
    $formatted = number_format($price, 2, ',', '.');
    return $showCurrency ? CURRENCY_SYMBOL . $formatted : $formatted;
}

/**
 * Calculate tax
 */
function calculateTax($amount, $rate = TAX_RATE) {
    return $amount * ($rate / 100);
}

/**
 * Format date
 */
function formatDate($date, $format = 'd.m.Y') {
    return date($format, strtotime($date));
}

/**
 * Format datetime
 */
function formatDateTime($datetime, $format = 'd.m.Y H:i') {
    return date($format, strtotime($datetime));
}

/**
 * Generate slug from text
 */
function generateSlug($text) {
    // Turkish characters to English
    $turkish = ['ş', 'Ş', 'ı', 'İ', 'ğ', 'Ğ', 'ü', 'Ü', 'ö', 'Ö', 'ç', 'Ç'];
    $english = ['s', 's', 'i', 'i', 'g', 'g', 'u', 'u', 'o', 'o', 'c', 'c'];
    $text = str_replace($turkish, $english, $text);

    // Convert to lowercase
    $text = strtolower($text);

    // Replace non-alphanumeric characters with hyphens
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);

    // Remove leading/trailing hyphens
    $text = trim($text, '-');

    return $text;
}

/**
 * Upload image
 */
function uploadImage($file, $subdir = '') {
    $errors = [];

    // Check if file was uploaded
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return ['success' => false, 'error' => ERROR_FILE_UPLOAD];
    }

    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'error' => ERROR_FILE_TOO_LARGE];
    }

    // Check file type
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExt, ALLOWED_IMAGE_TYPES)) {
        return ['success' => false, 'error' => ERROR_INVALID_FILE_TYPE];
    }

    // Generate unique filename
    $filename = uniqid() . '_' . time() . '.' . $fileExt;
    $uploadDir = UPLOAD_PATH . ($subdir ? '/' . trim($subdir, '/') : '');

    // Create directory if not exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $targetPath = $uploadDir . '/' . $filename;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Create thumbnail
        createThumbnail($targetPath, $uploadDir . '/thumb_' . $filename);

        return [
            'success' => true,
            'filename' => $filename,
            'path' => $targetPath,
            'url' => UPLOADS_URL . ($subdir ? '/' . trim($subdir, '/') : '') . '/' . $filename
        ];
    }

    return ['success' => false, 'error' => ERROR_FILE_UPLOAD];
}

/**
 * Create thumbnail
 */
function createThumbnail($source, $destination, $width = THUMBNAIL_WIDTH, $height = THUMBNAIL_HEIGHT) {
    list($origWidth, $origHeight, $type) = getimagesize($source);

    // Calculate dimensions
    $ratio = min($width / $origWidth, $height / $origHeight);
    $newWidth = $origWidth * $ratio;
    $newHeight = $origHeight * $ratio;

    // Create image resource
    switch ($type) {
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($source);
            break;
        case IMAGETYPE_GIF:
            $image = imagecreatefromgif($source);
            break;
        default:
            return false;
    }

    // Create thumbnail
    $thumbnail = imagecreatetruecolor($newWidth, $newHeight);

    // Preserve transparency for PNG and GIF
    if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
        imagealphablending($thumbnail, false);
        imagesavealpha($thumbnail, true);
    }

    imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

    // Save thumbnail
    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($thumbnail, $destination, 90);
            break;
        case IMAGETYPE_PNG:
            imagepng($thumbnail, $destination, 9);
            break;
        case IMAGETYPE_GIF:
            imagegif($thumbnail, $destination);
            break;
    }

    imagedestroy($image);
    imagedestroy($thumbnail);

    return true;
}

/**
 * Delete file
 */
function deleteFile($filepath) {
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    return false;
}

/**
 * Send JSON response
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Get session message and clear it
 */
function getFlashMessage($key = 'message') {
    $message = $_SESSION[$key] ?? null;
    unset($_SESSION[$key]);
    return $message;
}

/**
 * Set session message
 */
function setFlashMessage($message, $type = 'success', $key = 'message') {
    $_SESSION[$key] = [
        'text' => $message,
        'type' => $type
    ];
}

/**
 * Log error
 */
function logError($message, $file = 'error.log') {
    if (ERROR_LOGGING) {
        $logFile = LOG_PATH . '/' . $file;
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message" . PHP_EOL;
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
}

/**
 * Generate random string
 */
function generateRandomString($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Get client IP address
 */
function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Pagination helper
 */
function paginate($totalItems, $itemsPerPage, $currentPage = 1) {
    $totalPages = ceil($totalItems / $itemsPerPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $itemsPerPage;

    return [
        'total_items' => $totalItems,
        'items_per_page' => $itemsPerPage,
        'total_pages' => $totalPages,
        'current_page' => $currentPage,
        'offset' => $offset,
        'has_prev' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages
    ];
}

/**
 * Generate order number
 */
function generateOrderNumber() {
    return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
}

/**
 * Clean old files (cache, logs, etc.)
 */
function cleanOldFiles($directory, $days = 7) {
    $files = glob($directory . '/*');
    $now = time();

    foreach ($files as $file) {
        if (is_file($file)) {
            if ($now - filemtime($file) >= 60 * 60 * 24 * $days) {
                unlink($file);
            }
        }
    }
}

/**
 * Rate limiting
 */
function checkRateLimit($key, $maxAttempts = 5, $timeWindow = 300) {
    $cacheFile = CACHE_PATH . '/ratelimit_' . md5($key) . '.json';

    $data = [];
    if (file_exists($cacheFile)) {
        $data = json_decode(file_get_contents($cacheFile), true);
    }

    $now = time();
    $data = array_filter($data, function($timestamp) use ($now, $timeWindow) {
        return ($now - $timestamp) < $timeWindow;
    });

    if (count($data) >= $maxAttempts) {
        return false;
    }

    $data[] = $now;
    file_put_contents($cacheFile, json_encode($data));

    return true;
}

/**
 * SEO friendly title
 */
function seoTitle($title) {
    return $title . ' | ' . SITE_NAME;
}

/**
 * Truncate text
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

// ===================================
// SETTINGS HELPER FUNCTIONS
// ===================================

/**
 * Get a setting value
 *
 * @param string $key Setting key
 * @param mixed $default Default value if not found
 * @return mixed Setting value
 */
function getSetting($key, $default = null) {
    static $settingsModel = null;

    if ($settingsModel === null) {
        require_once __DIR__ . '/../../models/Settings.php';
        $settingsModel = new Settings();
    }

    return $settingsModel->get($key, $default);
}

/**
 * Get a boolean setting value
 *
 * @param string $key Setting key
 * @param bool $default Default value
 * @return bool
 */
function getSettingBool($key, $default = false) {
    static $settingsModel = null;

    if ($settingsModel === null) {
        require_once __DIR__ . '/../../models/Settings.php';
        $settingsModel = new Settings();
    }

    return $settingsModel->getBool($key, $default);
}

/**
 * Get an integer setting value
 *
 * @param string $key Setting key
 * @param int $default Default value
 * @return int
 */
function getSettingInt($key, $default = 0) {
    static $settingsModel = null;

    if ($settingsModel === null) {
        require_once __DIR__ . '/../../models/Settings.php';
        $settingsModel = new Settings();
    }

    return $settingsModel->getInt($key, $default);
}

/**
 * Update a setting value
 *
 * @param string $key Setting key
 * @param mixed $value Setting value
 * @return bool Success status
 */
function updateSetting($key, $value) {
    static $settingsModel = null;

    if ($settingsModel === null) {
        require_once __DIR__ . '/../../models/Settings.php';
        $settingsModel = new Settings();
    }

    return $settingsModel->set($key, $value);
}

/**
 * Check if site is in maintenance mode
 *
 * @return bool
 */
function isMaintenanceMode() {
    return getSettingBool('maintenance_mode', false);
}

/**
 * Get site currency symbol
 *
 * @return string
 */
function getCurrencySymbol() {
    return getSetting('currency_symbol', '₺');
}

/**
 * Get site currency code
 *
 * @return string
 */
function getCurrency() {
    return getSetting('currency', 'TRY');
}

/**
 * Check if feature is enabled
 *
 * @param string $feature Feature name (reviews, wishlist, loyalty, etc.)
 * @return bool
 */
function isFeatureEnabled($feature) {
    return getSettingBool('enable_' . $feature, true);
}

// ===================================
// PLACEHOLDER IMAGE HELPERS
// ===================================

/**
 * Generate placeholder image URL
 *
 * @param int $width Image width
 * @param int $height Image height
 * @param string $text Placeholder text
 * @param string $bgColor Background color (hex without #)
 * @param string $textColor Text color (hex without #)
 * @return string Placeholder URL
 */
function getPlaceholder($width = 400, $height = 400, $text = null, $bgColor = '7A8B5C', $textColor = 'FFFFFF') {
    $text = $text ?? "{$width}x{$height}";
    $text = urlencode($text);
    return "https://via.placeholder.com/{$width}x{$height}/{$bgColor}/{$textColor}?text={$text}";
}

/**
 * Get product placeholder image
 *
 * @param string $productName Product name for text
 * @param int $width Image width
 * @param int $height Image height
 * @return string Placeholder URL
 */
function getProductPlaceholder($productName = null, $width = 400, $height = 400) {
    $text = $productName ? truncate($productName, 20, '...') : 'MINALIA';
    return getPlaceholder($width, $height, $text, '7A8B5C', 'FFFFFF');
}

/**
 * Get category placeholder image
 *
 * @param string $categoryName Category name
 * @param int $width Image width
 * @param int $height Image height
 * @return string Placeholder URL
 */
function getCategoryPlaceholder($categoryName = null, $width = 600, $height = 400) {
    $text = $categoryName ?? 'Kategori';
    return getPlaceholder($width, $height, $text, 'D4AF37', 'FFFFFF');
}

/**
 * Get brand logo placeholder
 *
 * @param string $brandName Brand name
 * @param int $width Image width
 * @param int $height Image height
 * @return string Placeholder URL
 */
function getBrandPlaceholder($brandName = null, $width = 200, $height = 100) {
    $text = $brandName ?? 'Brand';
    return getPlaceholder($width, $height, $text, 'F8F5F0', '2C2C2C');
}

/**
 * Get user avatar placeholder
 *
 * @param string $name User name
 * @param int $size Avatar size
 * @return string Placeholder URL
 */
function getAvatarPlaceholder($name = null, $size = 100) {
    $initials = 'U';
    if ($name) {
        $parts = explode(' ', $name);
        $initials = strtoupper(substr($parts[0], 0, 1));
        if (isset($parts[1])) {
            $initials .= strtoupper(substr($parts[1], 0, 1));
        }
    }
    return getPlaceholder($size, $size, $initials, '7A8B5C', 'FFFFFF');
}

/**
 * Get image with fallback to placeholder
 *
 * @param string|null $imagePath Image path
 * @param string $placeholderText Placeholder text
 * @param int $width Width
 * @param int $height Height
 * @return string Image URL or placeholder
 */
function getImageOrPlaceholder($imagePath, $placeholderText = null, $width = 400, $height = 400) {
    if ($imagePath && file_exists(PUBLIC_PATH . $imagePath)) {
        return BASE_URL . $imagePath;
    }
    return getProductPlaceholder($placeholderText, $width, $height);
}

/**
 * Generate local SVG placeholder
 * Returns data URI with inline SVG
 *
 * @param int $width Width
 * @param int $height Height
 * @param string $text Text to display
 * @param string $bgColor Background color
 * @param string $textColor Text color
 * @return string Data URI
 */
function getSVGPlaceholder($width = 400, $height = 400, $text = 'MINALIA', $bgColor = '#7A8B5C', $textColor = '#FFFFFF') {
    $fontSize = min($width, $height) / 8;
    $svg = <<<SVG
<svg width="{$width}" height="{$height}" xmlns="http://www.w3.org/2000/svg">
  <rect width="{$width}" height="{$height}" fill="{$bgColor}"/>
  <text x="50%" y="50%" font-family="Arial, sans-serif" font-size="{$fontSize}" fill="{$textColor}" text-anchor="middle" dominant-baseline="middle">{$text}</text>
  <text x="50%" y="60%" font-family="Arial, sans-serif" font-size="14" fill="{$textColor}" opacity="0.7" text-anchor="middle" dominant-baseline="middle">{$width}×{$height}</text>
</svg>
SVG;
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}
