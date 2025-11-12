-- Social Accounts Table
-- Stores social media login credentials
CREATE TABLE IF NOT EXISTS `social_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `provider` enum('google','facebook','twitter','apple') NOT NULL,
  `provider_user_id` varchar(255) NOT NULL,
  `provider_email` varchar(255) DEFAULT NULL,
  `provider_name` varchar(255) DEFAULT NULL,
  `provider_avatar` varchar(500) DEFAULT NULL,
  `access_token` text,
  `refresh_token` text,
  `token_expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `provider_user` (`provider`, `provider_user_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_provider` (`provider`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add social login fields to users table
ALTER TABLE `users`
ADD COLUMN `avatar` varchar(500) DEFAULT NULL AFTER `email`,
ADD COLUMN `auth_provider` varchar(50) DEFAULT 'local' AFTER `avatar`,
ADD COLUMN `email_verified` tinyint(1) DEFAULT 0 AFTER `auth_provider`;

-- Update existing users to have email verified
UPDATE `users` SET `email_verified` = 1 WHERE `id` > 0;
