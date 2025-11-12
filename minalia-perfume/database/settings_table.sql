-- ===================================
-- Settings Table - Professional Configuration System
-- ===================================

CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `setting_key` VARCHAR(100) UNIQUE NOT NULL,
    `setting_value` TEXT,
    `setting_type` ENUM('text', 'textarea', 'number', 'boolean', 'image', 'json') DEFAULT 'text',
    `category` VARCHAR(50) DEFAULT 'general',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Default Settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `category`) VALUES

-- GENERAL SETTINGS
('site_name', 'MINALIA Parfüm', 'text', 'general'),
('site_slogan', 'Zarafet ve Lüksün Adresi', 'text', 'general'),
('site_description', 'Minalia Parfüm olarak, en kaliteli ve özgün parfümleri sizlerle buluşturuyoruz.', 'textarea', 'general'),
('site_keywords', 'parfüm, minalia, lüks parfüm, kadın parfümü, erkek parfümü', 'text', 'general'),
('site_logo', '/images/logo.png', 'image', 'general'),
('site_favicon', '/images/favicon.ico', 'image', 'general'),
('contact_email', 'info@minalia.com.tr', 'text', 'general'),
('contact_phone', '+90 (212) 555 00 00', 'text', 'general'),
('contact_address', 'İstanbul, Türkiye', 'textarea', 'general'),
('business_hours', 'Pazartesi - Cumartesi: 09:00 - 18:00', 'text', 'general'),

-- EMAIL SETTINGS
('smtp_enabled', '0', 'boolean', 'email'),
('smtp_host', 'smtp.gmail.com', 'text', 'email'),
('smtp_port', '587', 'number', 'email'),
('smtp_username', '', 'text', 'email'),
('smtp_password', '', 'text', 'email'),
('smtp_encryption', 'tls', 'text', 'email'),
('email_from_name', 'MINALIA Parfüm', 'text', 'email'),
('email_from_address', 'noreply@minalia.com.tr', 'text', 'email'),

-- PAYMENT SETTINGS
('payment_enabled', '1', 'boolean', 'payment'),
('payment_test_mode', '1', 'boolean', 'payment'),
('iyzico_enabled', '0', 'boolean', 'payment'),
('iyzico_api_key', '', 'text', 'payment'),
('iyzico_secret_key', '', 'text', 'payment'),
('stripe_enabled', '0', 'boolean', 'payment'),
('stripe_publishable_key', '', 'text', 'payment'),
('stripe_secret_key', '', 'text', 'payment'),
('paypal_enabled', '0', 'boolean', 'payment'),
('paypal_client_id', '', 'text', 'payment'),
('paypal_secret', '', 'text', 'payment'),
('cash_on_delivery_enabled', '1', 'boolean', 'payment'),

-- SHIPPING SETTINGS
('shipping_enabled', '1', 'boolean', 'shipping'),
('free_shipping_threshold', '500', 'number', 'shipping'),
('shipping_cost', '29.90', 'number', 'shipping'),
('express_shipping_cost', '49.90', 'number', 'shipping'),
('shipping_companies', 'Aras Kargo,MNG Kargo,Yurtiçi Kargo,PTT Kargo', 'text', 'shipping'),
('estimated_delivery_days', '2-3', 'text', 'shipping'),

-- SEO SETTINGS
('seo_enabled', '1', 'boolean', 'seo'),
('meta_title', 'MINALIA Parfüm - Zarafet ve Lüksün Adresi', 'text', 'seo'),
('meta_description', 'Türkiye\'nin en kaliteli parfüm markası. Kadın, erkek ve unisex parfüm çeşitleri.', 'textarea', 'seo'),
('meta_keywords', 'parfüm, lüks parfüm, kadın parfümü, erkek parfümü, minalia', 'text', 'seo'),
('og_title', 'MINALIA Parfüm', 'text', 'seo'),
('og_description', 'Zarafet ve lüksün adresi', 'textarea', 'seo'),
('og_image', '/images/og-image.jpg', 'image', 'seo'),
('google_site_verification', '', 'text', 'seo'),

-- SOCIAL MEDIA
('social_facebook', '', 'text', 'social'),
('social_instagram', '', 'text', 'social'),
('social_twitter', '', 'text', 'social'),
('social_youtube', '', 'text', 'social'),
('social_pinterest', '', 'text', 'social'),
('social_tiktok', '', 'text', 'social'),
('social_linkedin', '', 'text', 'social'),

-- ANALYTICS
('google_analytics_id', '', 'text', 'analytics'),
('facebook_pixel_id', '', 'text', 'analytics'),
('whatsapp_number', '', 'text', 'analytics'),
('whatsapp_enabled', '0', 'boolean', 'analytics'),

-- ADVANCED SETTINGS
('maintenance_mode', '0', 'boolean', 'advanced'),
('maintenance_message', 'Sitemiz bakımdadır. Kısa süre içinde tekrar hizmete açılacaktır.', 'textarea', 'advanced'),
('currency', 'TRY', 'text', 'advanced'),
('currency_symbol', '₺', 'text', 'advanced'),
('tax_rate', '20', 'number', 'advanced'),
('tax_enabled', '1', 'boolean', 'advanced'),
('stock_alert_enabled', '1', 'boolean', 'advanced'),
('stock_alert_threshold', '10', 'number', 'advanced'),
('enable_reviews', '1', 'boolean', 'advanced'),
('enable_wishlist', '1', 'boolean', 'advanced'),
('enable_loyalty', '1', 'boolean', 'advanced'),
('loyalty_points_rate', '10', 'number', 'advanced'),
('min_order_amount', '50', 'number', 'advanced'),
('max_cart_items', '50', 'number', 'advanced'),
('session_timeout', '3600', 'number', 'advanced'),
('password_min_length', '6', 'number', 'advanced'),

-- EMAIL TEMPLATES
('email_welcome_subject', 'MINALIA\'ya Hoş Geldiniz!', 'text', 'email_templates'),
('email_welcome_body', 'Merhaba {name},\n\nMINALIA ailesine katıldığınız için teşekkür ederiz.', 'textarea', 'email_templates'),
('email_order_subject', 'Siparişiniz Alındı - #{order_number}', 'text', 'email_templates'),
('email_order_body', 'Sayın {name},\n\nSiparişiniz başarıyla oluşturuldu.', 'textarea', 'email_templates'),
('email_shipping_subject', 'Siparişiniz Kargoya Verildi', 'text', 'email_templates'),
('email_shipping_body', 'Siparişiniz {tracking_number} kargo takip numarası ile gönderildi.', 'textarea', 'email_templates');
