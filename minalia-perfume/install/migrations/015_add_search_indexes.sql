-- Migration: Enhanced Search Indexes for Faceted Filtering
-- Created: 2025-11-12
-- Purpose: Optimize product search and filtering performance

-- Add indexes for common filter fields
ALTER TABLE products
ADD INDEX idx_price_range (price),
ADD INDEX idx_gender (gender),
ADD INDEX idx_active (is_active),
ADD INDEX idx_featured_active (is_featured, is_active),
ADD INDEX idx_bestseller_active (is_bestseller, is_active),
ADD INDEX idx_new_active (is_new, is_active),
ADD INDEX idx_stock (stock_quantity),
ADD INDEX idx_rating_reviews (rating, review_count);

-- Add composite index for category + price filtering
ALTER TABLE products
ADD INDEX idx_category_price (category_id, price, is_active);

-- Add composite index for brand + price filtering
ALTER TABLE products
ADD INDEX idx_brand_price (brand_id, price, is_active);

-- Add index for launch year (vintage filtering)
ALTER TABLE products
ADD INDEX idx_launch_year (launch_year);

-- Optimize category table for hierarchical queries
ALTER TABLE categories
ADD INDEX idx_parent_active (parent_id, is_active, sort_order);

-- Optimize brands for filtering
ALTER TABLE brands
ADD INDEX idx_featured_sort (is_featured, sort_order);

-- Add search history table for autocomplete improvement
CREATE TABLE IF NOT EXISTS search_history (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    session_id VARCHAR(100) NULL,
    search_term VARCHAR(255) NOT NULL,
    result_count INT DEFAULT 0,
    filters_used JSON NULL,
    clicked_product_id INT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (clicked_product_id) REFERENCES products(id) ON DELETE SET NULL,

    INDEX idx_search_term (search_term),
    INDEX idx_user_id (user_id),
    INDEX idx_session_id (session_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add popular searches tracking
CREATE TABLE IF NOT EXISTS popular_searches (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    search_term VARCHAR(255) UNIQUE NOT NULL,
    search_count INT DEFAULT 1,
    last_searched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_trending BOOLEAN DEFAULT FALSE,

    INDEX idx_search_count (search_count DESC),
    INDEX idx_trending (is_trending, search_count DESC),
    INDEX idx_last_searched (last_searched_at DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add filter combinations tracking for analytics
CREATE TABLE IF NOT EXISTS filter_analytics (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    filter_combination JSON NOT NULL,
    usage_count INT DEFAULT 1,
    avg_result_count DECIMAL(10,2) DEFAULT 0,
    conversion_count INT DEFAULT 0,
    last_used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_usage_count (usage_count DESC),
    INDEX idx_last_used (last_used_at DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optimize existing fulltext index
-- Note: FULLTEXT indexes are already defined in complete_schema.sql
-- This is a reminder to use them in queries:
-- MATCH(name, description) AGAINST ('search term' IN NATURAL LANGUAGE MODE)
