-- =====================================================
-- RETURNS TABLE
-- Sipariş iade ve iptal taleplerini yönetir
-- =====================================================

CREATE TABLE IF NOT EXISTS returns (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    return_number VARCHAR(50) UNIQUE NOT NULL COMMENT 'Unique return tracking number',

    -- Order Information
    order_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,

    -- Return Type
    return_type ENUM('return', 'cancel') NOT NULL COMMENT 'return: iade, cancel: iptal',

    -- Status
    status ENUM('pending', 'approved', 'rejected', 'processing', 'completed', 'refunded') DEFAULT 'pending',

    -- Return Details
    reason ENUM(
        'defective_product',
        'wrong_product',
        'not_as_described',
        'changed_mind',
        'better_price',
        'late_delivery',
        'damaged_package',
        'other'
    ) NOT NULL,
    reason_details TEXT NULL COMMENT 'Additional explanation',

    -- Items (JSON array of product IDs and quantities)
    return_items JSON NOT NULL COMMENT '[{"product_id": 1, "quantity": 2, "reason": "..."}]',

    -- Refund Information
    refund_amount DECIMAL(10,2) DEFAULT 0,
    refund_method ENUM('original_payment', 'bank_transfer', 'store_credit') DEFAULT 'original_payment',
    refund_status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
    refunded_at DATETIME NULL,

    -- Images (JSON array of image URLs)
    proof_images JSON NULL COMMENT '["image1.jpg", "image2.jpg"]',

    -- Admin Notes
    admin_notes TEXT NULL,
    rejection_reason TEXT NULL,

    -- Bank Account (for refunds)
    bank_name VARCHAR(100) NULL,
    iban VARCHAR(34) NULL,
    account_holder VARCHAR(255) NULL,

    -- Tracking
    cargo_company VARCHAR(100) NULL,
    cargo_tracking_number VARCHAR(100) NULL,
    shipped_back_at DATETIME NULL,
    received_at DATETIME NULL,

    -- Timestamps
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    approved_at DATETIME NULL,
    processed_at DATETIME NULL,
    completed_at DATETIME NULL,

    -- Foreign Keys
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,

    -- Indexes
    INDEX idx_user_id (user_id),
    INDEX idx_order_id (order_id),
    INDEX idx_status (status),
    INDEX idx_return_type (return_type),
    INDEX idx_return_number (return_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Order returns and cancellations';

-- =====================================================
-- RETURN STATUS NOTES:
-- - pending: Müşteri talebi oluşturdu, admin onayı bekliyor
-- - approved: Admin onayladı, müşteri ürünü gönderecek
-- - rejected: Admin reddetti
-- - processing: Ürün depoya ulaştı, kontrol ediliyor
-- - completed: İade tamamlandı
-- - refunded: Para iadesi yapıldı
-- =====================================================
