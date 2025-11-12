-- User Preferences table
-- Stores user's perfume preferences and KVKK (GDPR) consent settings
CREATE TABLE IF NOT EXISTS user_preferences (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,

    -- Perfume Preferences
    preferred_notes JSON NULL COMMENT 'Preferred fragrance notes (e.g., ["Çiçek", "Meyve", "Odunsu"])',
    preferred_brands JSON NULL COMMENT 'Preferred brand IDs',
    budget_range VARCHAR(50) NULL COMMENT 'Budget range (e.g., "500-1000", "2000+")',
    occasion_type VARCHAR(50) NULL COMMENT 'Usage occasion (daily, office, evening, sport)',

    -- KVKK / GDPR Compliance
    marketing_consent TINYINT(1) DEFAULT 0 COMMENT 'Consent for marketing communications',
    personalized_ads_consent TINYINT(1) DEFAULT 0 COMMENT 'Consent for personalized advertising',
    data_sharing_consent TINYINT(1) DEFAULT 0 COMMENT 'Consent for sharing data with 3rd parties',
    profiling_consent TINYINT(1) DEFAULT 0 COMMENT 'Consent for profiling and analytics',

    -- KVKK Rights Exercised
    right_to_access_exercised_at DATETIME NULL COMMENT 'When user requested their data',
    right_to_deletion_requested_at DATETIME NULL COMMENT 'When user requested account deletion',
    right_to_portability_exercised_at DATETIME NULL COMMENT 'When user requested data export',

    -- Metadata
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user (user_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- KVKK Data Processing Log
-- Logs all data processing activities for compliance
CREATE TABLE IF NOT EXISTS kvkk_data_processing_log (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,

    activity_type ENUM('access', 'update', 'deletion_request', 'data_export', 'consent_change', 'profile_view') NOT NULL,
    description TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,

    -- Additional data
    data_categories JSON NULL COMMENT 'Which data categories were affected (e.g., ["profile", "orders", "addresses"])',
    processing_purpose VARCHAR(255) NULL COMMENT 'Purpose of processing (e.g., "user request", "system maintenance")',

    performed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_activity (user_id, activity_type),
    INDEX idx_performed_at (performed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Account Deletion Requests
-- Tracks KVKK right to deletion requests
CREATE TABLE IF NOT EXISTS account_deletion_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,

    status ENUM('pending', 'approved', 'completed', 'cancelled') DEFAULT 'pending',

    reason TEXT NULL COMMENT 'User-provided reason for deletion',
    admin_notes TEXT NULL COMMENT 'Internal admin notes',

    -- KVKK mandates 30-day retention for certain data
    deletion_scheduled_at DATETIME NOT NULL COMMENT 'When account will be permanently deleted',
    deleted_at DATETIME NULL COMMENT 'When account was actually deleted',

    requested_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    processed_by INT UNSIGNED NULL COMMENT 'Admin user who processed this',

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_scheduled (deletion_scheduled_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
