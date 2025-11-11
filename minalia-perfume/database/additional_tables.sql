-- Additional Tables for New Features
-- MINALIA Parfüm E-Ticaret Platformu

USE minalia_perfume;

-- Loyalty Points Table
CREATE TABLE IF NOT EXISTS loyalty_points (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    points INT NOT NULL,
    type ENUM('purchase', 'signup', 'birthday', 'review', 'referral', 'redemption') NOT NULL,
    description TEXT,
    reference_id INT,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_type (type),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Abandoned Cart Emails Log
CREATE TABLE IF NOT EXISTS abandoned_cart_emails (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    opened_at TIMESTAMP NULL,
    clicked_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_sent_at (sent_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- AI Recommendations Log
CREATE TABLE IF NOT EXISTS ai_recommendations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    product_id INT NOT NULL,
    reason TEXT,
    clicked BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Stock Alert Requests
CREATE TABLE IF NOT EXISTS stock_alerts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    product_id INT NOT NULL,
    email VARCHAR(255) NOT NULL,
    notified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product_id (product_id),
    INDEX idx_notified (notified)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User Preferences
CREATE TABLE IF NOT EXISTS user_preferences (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    preferred_notes JSON,
    preferred_brands JSON,
    budget_range VARCHAR(50),
    occasion_type VARCHAR(50),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Coupon Usage Log
CREATE TABLE IF NOT EXISTS coupon_usage (
    id INT PRIMARY KEY AUTO_INCREMENT,
    coupon_id INT NOT NULL,
    user_id INT,
    order_id INT,
    discount_amount DECIMAL(10, 2),
    used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
    INDEX idx_coupon_id (coupon_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add coupon fields to orders table
ALTER TABLE orders
ADD COLUMN IF NOT EXISTS coupon_code VARCHAR(50),
ADD COLUMN IF NOT EXISTS coupon_discount DECIMAL(10, 2) DEFAULT 0,
ADD COLUMN IF NOT EXISTS points_used INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS points_discount DECIMAL(10, 2) DEFAULT 0;

-- Sample Loyalty Points for test user
INSERT INTO loyalty_points (user_id, points, type, description) VALUES
(1, 100, 'signup', 'Hoş geldin bonusu'),
(1, 50, 'review', 'Ürün yorumu için puan'),
(1, 150, 'purchase', 'Sipariş için puan');

-- Sample Coupons
INSERT INTO coupons (code, description, discount_type, discount_value, min_order_amount, max_discount, usage_limit, valid_from, valid_until, is_active) VALUES
('HOSGELDIN15', 'Yeni üyelere özel %15 indirim', 'percentage', 15, 0, 500, 1000, NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR), 1),
('SEPET10', 'Terk edilmiş sepet için %10 indirim', 'percentage', 10, 300, 200, NULL, NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH), 1),
('YAZ2025', 'Yaz kampanyası', 'percentage', 20, 500, 1000, 500, NOW(), DATE_ADD(NOW(), INTERVAL 3 MONTH), 1),
('MINALIA100', '100 TL indirim kuponu', 'fixed', 100, 1000, NULL, 200, NOW(), DATE_ADD(NOW(), INTERVAL 6 MONTH), 1),
('DOGUMGUNU2025', 'Doğum günü indirimi', 'percentage', 20, 0, 300, NULL, NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR), 1);
