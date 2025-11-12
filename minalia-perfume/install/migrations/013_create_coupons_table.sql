-- Coupons table
-- Stores promotional coupons and discount codes
CREATE TABLE IF NOT EXISTS coupons (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL COMMENT 'Coupon code (e.g., SUMMER2024, NEWUSER10)',
    type ENUM('percentage', 'fixed_amount', 'free_shipping') NOT NULL DEFAULT 'percentage',
    discount_value DECIMAL(10,2) NOT NULL COMMENT 'Percentage (0-100) or fixed amount in TL',

    -- Usage limits
    minimum_order_amount DECIMAL(10,2) DEFAULT 0 COMMENT 'Minimum order total to use coupon',
    maximum_discount_amount DECIMAL(10,2) NULL COMMENT 'Max discount for percentage coupons',
    usage_limit_per_user INT DEFAULT 1 COMMENT 'How many times one user can use',
    usage_limit_total INT NULL COMMENT 'Total usage limit across all users (NULL = unlimited)',
    current_usage_count INT DEFAULT 0 COMMENT 'How many times coupon has been used',

    -- Validity
    valid_from DATETIME NOT NULL,
    valid_until DATETIME NOT NULL,
    is_active TINYINT(1) DEFAULT 1,

    -- Restrictions
    applicable_categories JSON NULL COMMENT 'Category IDs this coupon applies to',
    applicable_products JSON NULL COMMENT 'Product IDs this coupon applies to',
    excluded_categories JSON NULL COMMENT 'Category IDs excluded from coupon',
    excluded_products JSON NULL COMMENT 'Product IDs excluded from coupon',

    -- User restrictions
    user_type ENUM('all', 'new', 'existing', 'premium') DEFAULT 'all',
    minimum_loyalty_points INT DEFAULT 0 COMMENT 'Required loyalty points to use',

    -- Description
    title VARCHAR(255) NOT NULL COMMENT 'Coupon title shown to users',
    description TEXT NULL COMMENT 'Coupon description and terms',

    -- Metadata
    created_by INT UNSIGNED NULL COMMENT 'Admin user who created this',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_code (code),
    INDEX idx_active_dates (is_active, valid_from, valid_until),
    INDEX idx_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User Coupons table
-- Tracks which users have which coupons assigned to them
CREATE TABLE IF NOT EXISTS user_coupons (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    coupon_id INT UNSIGNED NOT NULL,

    -- Usage tracking
    times_used INT DEFAULT 0,
    first_used_at DATETIME NULL,
    last_used_at DATETIME NULL,

    -- Status
    status ENUM('available', 'used', 'expired') DEFAULT 'available',

    -- Assignment info
    assigned_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    assigned_reason VARCHAR(255) NULL COMMENT 'Why this coupon was given (e.g., signup bonus, loyalty reward)',

    -- Expiry (can override coupon expiry)
    custom_expiry_date DATETIME NULL COMMENT 'User-specific expiry date',

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE CASCADE,

    UNIQUE KEY unique_user_coupon (user_id, coupon_id),
    INDEX idx_user_status (user_id, status),
    INDEX idx_coupon (coupon_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Coupon Usage Log
-- Records each time a coupon is used in an order
CREATE TABLE IF NOT EXISTS coupon_usage_log (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    coupon_id INT UNSIGNED NOT NULL,
    order_id INT UNSIGNED NOT NULL,

    discount_amount DECIMAL(10,2) NOT NULL COMMENT 'Actual discount amount applied',
    order_total DECIMAL(10,2) NOT NULL COMMENT 'Order total before discount',

    used_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,

    INDEX idx_user (user_id),
    INDEX idx_coupon (coupon_id),
    INDEX idx_order (order_id),
    INDEX idx_used_at (used_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample coupons for testing
INSERT INTO coupons (code, type, discount_value, minimum_order_amount, maximum_discount_amount,
                     usage_limit_per_user, usage_limit_total, valid_from, valid_until,
                     title, description, user_type) VALUES
('WELCOME10', 'percentage', 10.00, 200.00, 50.00, 1, 1000, NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR),
 'Hoş Geldin İndirimi', 'İlk siparişinizde %10 indirim! Minimum 200 TL alışverişte geçerlidir.', 'new'),

('SUMMER25', 'percentage', 25.00, 500.00, 150.00, 3, NULL, NOW(), DATE_ADD(NOW(), INTERVAL 3 MONTH),
 'Yaz Kampanyası', 'Yaz sezonunda %25 indirim! Minimum 500 TL alışverişte geçerlidir.', 'all'),

('FREESHIP', 'free_shipping', 0.00, 300.00, NULL, 5, NULL, NOW(), DATE_ADD(NOW(), INTERVAL 6 MONTH),
 'Ücretsiz Kargo', '300 TL ve üzeri alışverişlerde ücretsiz kargo.', 'all'),

('VIP50', 'fixed_amount', 50.00, 400.00, NULL, 1, 500, NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH),
 'VIP İndirim', '400 TL ve üzeri alışverişlerde 50 TL indirim!', 'premium');
