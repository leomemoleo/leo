-- =====================================================
-- SAVED PAYMENT METHODS TABLE
-- Müşterilerin kayıtlı kredi kartlarını saklar
-- =====================================================

CREATE TABLE IF NOT EXISTS saved_payment_methods (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,

    -- Card Information (Tokenized)
    card_token VARCHAR(255) NOT NULL COMMENT 'Payment gateway token (Iyzico, etc.)',
    card_alias VARCHAR(100) NOT NULL COMMENT 'User-friendly name (Visa **1234, İş Bankası)',
    card_type ENUM('credit_card', 'debit_card', 'prepaid') DEFAULT 'credit_card',
    card_brand VARCHAR(50) NOT NULL COMMENT 'visa, mastercard, amex, troy',
    last_four_digits CHAR(4) NOT NULL COMMENT 'Last 4 digits for display',
    expiry_month TINYINT(2) NOT NULL COMMENT 'Expiry month (1-12)',
    expiry_year SMALLINT(4) NOT NULL COMMENT 'Expiry year (2024)',

    -- Card Holder
    cardholder_name VARCHAR(255) NOT NULL,

    -- Default Card
    is_default TINYINT(1) DEFAULT 0 COMMENT 'Default payment method',

    -- Metadata
    gateway VARCHAR(50) DEFAULT 'iyzico' COMMENT 'Payment gateway name',
    gateway_customer_id VARCHAR(255) NULL COMMENT 'Customer ID in payment gateway',

    -- Status
    is_active TINYINT(1) DEFAULT 1,
    last_used_at DATETIME NULL,

    -- Timestamps
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Foreign Keys
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,

    -- Indexes
    INDEX idx_user_id (user_id),
    INDEX idx_is_default (is_default),
    INDEX idx_card_token (card_token),
    UNIQUE KEY unique_user_token (user_id, card_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Saved payment methods with tokenization';

-- =====================================================
-- NOTES:
-- - NEVER store real card numbers (PCI DSS compliance)
-- - Store only tokenized references from payment gateway
-- - Use SSL/TLS for data transmission
-- - Implement 3D Secure for transactions
-- =====================================================
