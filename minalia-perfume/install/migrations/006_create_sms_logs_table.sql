-- SMS Logs Table
-- Stores all SMS notifications sent by the system
CREATE TABLE IF NOT EXISTS `sms_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `status` enum('sent','failed','pending','delivered') DEFAULT 'pending',
  `message_id` varchar(100) DEFAULT NULL,
  `provider` varchar(50) DEFAULT 'netgsm',
  `error_message` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT 'general' COMMENT 'general, order, verification, password_reset, shipping',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_phone` (`phone`),
  KEY `idx_status` (`status`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SMS Templates Table
CREATE TABLE IF NOT EXISTS `sms_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL UNIQUE,
  `message` text NOT NULL,
  `variables` text COMMENT 'JSON array of available variables',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default SMS templates
INSERT INTO `sms_templates` (`name`, `slug`, `message`, `variables`, `is_active`) VALUES
('Sipariş Alındı', 'order_received', 'MINALIA: Siparişiniz #{order_number} alındı. Toplam: {total} TL. Teşekkür ederiz!', '["order_number","total"]', 1),
('Sipariş Hazırlanıyor', 'order_processing', 'MINALIA: Siparişiniz #{order_number} hazırlanıyor. Yakında kargoya verilecektir.', '["order_number"]', 1),
('Kargoya Verildi', 'order_shipped', 'MINALIA: Sipariş #{order_number} kargoya verildi. Kargo: {courier}, Takip: {tracking_code}', '["order_number","courier","tracking_code"]', 1),
('Sipariş Teslim Edildi', 'order_delivered', 'MINALIA: Siparişiniz #{order_number} teslim edildi. Beğendiğinizi umuyoruz!', '["order_number"]', 1),
('Sipariş İptal', 'order_cancelled', 'MINALIA: Siparişiniz #{order_number} iptal edildi. Bilgi: {phone}', '["order_number","phone"]', 1),
('Doğrulama Kodu', 'verification_code', 'MINALIA: Doğrulama kodunuz: {code}. Bu kodu kimseyle paylaşmayın.', '["code"]', 1),
('Şifre Sıfırlama', 'password_reset', 'MINALIA: Şifre sıfırlama kodunuz: {code}. Geçerlilik: 15 dakika.', '["code"]', 1),
('Kampanya Bildirimi', 'campaign', 'MINALIA: {campaign_message}', '["campaign_message"]', 1);
