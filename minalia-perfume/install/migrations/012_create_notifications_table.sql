-- =====================================================
-- NOTIFICATIONS TABLE
-- Kullanıcı bildirimlerini saklar
-- =====================================================

CREATE TABLE IF NOT EXISTS notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,

    -- Notification Details
    type ENUM('order', 'return', 'payment', 'shipping', 'promotion', 'system', 'review', 'wishlist', 'loyalty') NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,

    -- Action URL
    action_url VARCHAR(255) NULL COMMENT 'Click target URL',

    -- Icon & Style
    icon VARCHAR(50) NULL COMMENT 'FontAwesome icon class',
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',

    -- Status
    is_read TINYINT(1) DEFAULT 0,
    read_at DATETIME NULL,

    -- Related Entity
    related_type VARCHAR(50) NULL COMMENT 'order, return, product, etc.',
    related_id INT UNSIGNED NULL COMMENT 'Related entity ID',

    -- Timestamps
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NULL COMMENT 'Auto-delete after this date',

    -- Foreign Keys
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,

    -- Indexes
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read),
    INDEX idx_type (type),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='User notifications';

-- =====================================================
-- NOTIFICATION PREFERENCES TABLE
-- Kullanıcı bildirim tercihleri
-- =====================================================

CREATE TABLE IF NOT EXISTS notification_preferences (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,

    -- Notification Channels
    email_notifications TINYINT(1) DEFAULT 1,
    sms_notifications TINYINT(1) DEFAULT 1,
    push_notifications TINYINT(1) DEFAULT 1,

    -- Notification Types
    order_updates TINYINT(1) DEFAULT 1,
    return_updates TINYINT(1) DEFAULT 1,
    shipping_updates TINYINT(1) DEFAULT 1,
    promotions TINYINT(1) DEFAULT 1,
    price_drops TINYINT(1) DEFAULT 1,
    stock_alerts TINYINT(1) DEFAULT 1,
    review_responses TINYINT(1) DEFAULT 1,
    loyalty_updates TINYINT(1) DEFAULT 1,

    -- Timestamps
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Foreign Keys
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,

    -- Unique constraint
    UNIQUE KEY unique_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='User notification preferences';
