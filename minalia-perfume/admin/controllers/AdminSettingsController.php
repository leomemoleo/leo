<?php

require_once __DIR__ . '/../../models/Settings.php';

/**
 * Admin Settings Controller - Professional Configuration Management
 * Handles all system settings with categorized views
 */
class AdminSettingsController extends AdminController {
    private $settingsModel;

    public function __construct() {
        parent::__construct();
        $this->settingsModel = new Settings();
    }

    /**
     * Settings Dashboard - Main view with tabs
     */
    public function index() {
        $activeTab = $_GET['tab'] ?? 'general';

        // Get all settings grouped by category
        $allSettings = $this->settingsModel->getAllGrouped();

        // Prepare data for view
        $data = [
            'title' => 'Site Ayarları',
            'active_menu' => 'settings',
            'active_tab' => $activeTab,
            'settings' => $allSettings
        ];

        $this->renderView('settings/index', $data);
    }

    /**
     * Update General Settings
     */
    public function updateGeneral() {
        try {
            $settings = [
                'site_name' => sanitize($_POST['site_name'] ?? ''),
                'site_slogan' => sanitize($_POST['site_slogan'] ?? ''),
                'site_description' => sanitize($_POST['site_description'] ?? ''),
                'site_keywords' => sanitize($_POST['site_keywords'] ?? ''),
                'contact_email' => sanitize($_POST['contact_email'] ?? ''),
                'contact_phone' => sanitize($_POST['contact_phone'] ?? ''),
                'contact_address' => sanitize($_POST['contact_address'] ?? ''),
                'business_hours' => sanitize($_POST['business_hours'] ?? '')
            ];

            // Handle logo upload
            if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
                $logoPath = $this->handleImageUpload($_FILES['site_logo'], 'logo');
                if ($logoPath) {
                    $settings['site_logo'] = $logoPath;
                }
            }

            // Handle favicon upload
            if (isset($_FILES['site_favicon']) && $_FILES['site_favicon']['error'] === UPLOAD_ERR_OK) {
                $faviconPath = $this->handleImageUpload($_FILES['site_favicon'], 'favicon');
                if ($faviconPath) {
                    $settings['site_favicon'] = $faviconPath;
                }
            }

            $result = $this->settingsModel->updateMultiple($settings);

            if ($result) {
                $this->logActivity('settings_updated', 'General settings updated');
                setFlashMessage('Genel ayarlar başarıyla güncellendi.', 'success');
            } else {
                throw new Exception('Ayarlar güncellenemedi.');
            }

        } catch (Exception $e) {
            setFlashMessage('Hata: ' . $e->getMessage(), 'error');
        }

        redirect(ADMIN_URL . '/settings?tab=general');
    }

    /**
     * Update Email Settings
     */
    public function updateEmail() {
        try {
            $settings = [
                'smtp_enabled' => isset($_POST['smtp_enabled']) ? '1' : '0',
                'smtp_host' => sanitize($_POST['smtp_host'] ?? ''),
                'smtp_port' => sanitize($_POST['smtp_port'] ?? '587'),
                'smtp_username' => sanitize($_POST['smtp_username'] ?? ''),
                'smtp_encryption' => sanitize($_POST['smtp_encryption'] ?? 'tls'),
                'email_from_name' => sanitize($_POST['email_from_name'] ?? ''),
                'email_from_address' => sanitize($_POST['email_from_address'] ?? '')
            ];

            // Only update password if provided
            if (!empty($_POST['smtp_password'])) {
                $settings['smtp_password'] = $_POST['smtp_password'];
            }

            $result = $this->settingsModel->updateMultiple($settings);

            if ($result) {
                $this->logActivity('settings_updated', 'Email settings updated');
                setFlashMessage('Email ayarları başarıyla güncellendi.', 'success');
            } else {
                throw new Exception('Ayarlar güncellenemedi.');
            }

        } catch (Exception $e) {
            setFlashMessage('Hata: ' . $e->getMessage(), 'error');
        }

        redirect(ADMIN_URL . '/settings?tab=email');
    }

    /**
     * Update Payment Settings
     */
    public function updatePayment() {
        try {
            $settings = [
                'payment_enabled' => isset($_POST['payment_enabled']) ? '1' : '0',
                'payment_test_mode' => isset($_POST['payment_test_mode']) ? '1' : '0',
                'cash_on_delivery_enabled' => isset($_POST['cash_on_delivery_enabled']) ? '1' : '0',

                // iyzico
                'iyzico_enabled' => isset($_POST['iyzico_enabled']) ? '1' : '0',
                'iyzico_api_key' => sanitize($_POST['iyzico_api_key'] ?? ''),
                'iyzico_secret_key' => sanitize($_POST['iyzico_secret_key'] ?? ''),

                // Stripe
                'stripe_enabled' => isset($_POST['stripe_enabled']) ? '1' : '0',
                'stripe_publishable_key' => sanitize($_POST['stripe_publishable_key'] ?? ''),
                'stripe_secret_key' => sanitize($_POST['stripe_secret_key'] ?? ''),

                // PayPal
                'paypal_enabled' => isset($_POST['paypal_enabled']) ? '1' : '0',
                'paypal_client_id' => sanitize($_POST['paypal_client_id'] ?? ''),
                'paypal_secret' => sanitize($_POST['paypal_secret'] ?? '')
            ];

            $result = $this->settingsModel->updateMultiple($settings);

            if ($result) {
                $this->logActivity('settings_updated', 'Payment settings updated');
                setFlashMessage('Ödeme ayarları başarıyla güncellendi.', 'success');
            } else {
                throw new Exception('Ayarlar güncellenemedi.');
            }

        } catch (Exception $e) {
            setFlashMessage('Hata: ' . $e->getMessage(), 'error');
        }

        redirect(ADMIN_URL . '/settings?tab=payment');
    }

    /**
     * Update Shipping Settings
     */
    public function updateShipping() {
        try {
            $settings = [
                'shipping_enabled' => isset($_POST['shipping_enabled']) ? '1' : '0',
                'free_shipping_threshold' => sanitize($_POST['free_shipping_threshold'] ?? '500'),
                'shipping_cost' => sanitize($_POST['shipping_cost'] ?? '29.90'),
                'express_shipping_cost' => sanitize($_POST['express_shipping_cost'] ?? '49.90'),
                'shipping_companies' => sanitize($_POST['shipping_companies'] ?? ''),
                'estimated_delivery_days' => sanitize($_POST['estimated_delivery_days'] ?? '2-3')
            ];

            $result = $this->settingsModel->updateMultiple($settings);

            if ($result) {
                $this->logActivity('settings_updated', 'Shipping settings updated');
                setFlashMessage('Kargo ayarları başarıyla güncellendi.', 'success');
            } else {
                throw new Exception('Ayarlar güncellenemedi.');
            }

        } catch (Exception $e) {
            setFlashMessage('Hata: ' . $e->getMessage(), 'error');
        }

        redirect(ADMIN_URL . '/settings?tab=shipping');
    }

    /**
     * Update SEO Settings
     */
    public function updateSeo() {
        try {
            $settings = [
                'seo_enabled' => isset($_POST['seo_enabled']) ? '1' : '0',
                'meta_title' => sanitize($_POST['meta_title'] ?? ''),
                'meta_description' => sanitize($_POST['meta_description'] ?? ''),
                'meta_keywords' => sanitize($_POST['meta_keywords'] ?? ''),
                'og_title' => sanitize($_POST['og_title'] ?? ''),
                'og_description' => sanitize($_POST['og_description'] ?? ''),
                'google_site_verification' => sanitize($_POST['google_site_verification'] ?? '')
            ];

            // Handle OG image upload
            if (isset($_FILES['og_image']) && $_FILES['og_image']['error'] === UPLOAD_ERR_OK) {
                $ogImagePath = $this->handleImageUpload($_FILES['og_image'], 'og');
                if ($ogImagePath) {
                    $settings['og_image'] = $ogImagePath;
                }
            }

            $result = $this->settingsModel->updateMultiple($settings);

            if ($result) {
                $this->logActivity('settings_updated', 'SEO settings updated');
                setFlashMessage('SEO ayarları başarıyla güncellendi.', 'success');
            } else {
                throw new Exception('Ayarlar güncellenemedi.');
            }

        } catch (Exception $e) {
            setFlashMessage('Hata: ' . $e->getMessage(), 'error');
        }

        redirect(ADMIN_URL . '/settings?tab=seo');
    }

    /**
     * Update Social Media Settings
     */
    public function updateSocial() {
        try {
            $settings = [
                'social_facebook' => sanitize($_POST['social_facebook'] ?? ''),
                'social_instagram' => sanitize($_POST['social_instagram'] ?? ''),
                'social_twitter' => sanitize($_POST['social_twitter'] ?? ''),
                'social_youtube' => sanitize($_POST['social_youtube'] ?? ''),
                'social_pinterest' => sanitize($_POST['social_pinterest'] ?? ''),
                'social_tiktok' => sanitize($_POST['social_tiktok'] ?? ''),
                'social_linkedin' => sanitize($_POST['social_linkedin'] ?? '')
            ];

            $result = $this->settingsModel->updateMultiple($settings);

            if ($result) {
                $this->logActivity('settings_updated', 'Social media settings updated');
                setFlashMessage('Sosyal medya ayarları başarıyla güncellendi.', 'success');
            } else {
                throw new Exception('Ayarlar güncellenemedi.');
            }

        } catch (Exception $e) {
            setFlashMessage('Hata: ' . $e->getMessage(), 'error');
        }

        redirect(ADMIN_URL . '/settings?tab=social');
    }

    /**
     * Update Analytics Settings
     */
    public function updateAnalytics() {
        try {
            $settings = [
                'google_analytics_id' => sanitize($_POST['google_analytics_id'] ?? ''),
                'facebook_pixel_id' => sanitize($_POST['facebook_pixel_id'] ?? ''),
                'whatsapp_enabled' => isset($_POST['whatsapp_enabled']) ? '1' : '0',
                'whatsapp_number' => sanitize($_POST['whatsapp_number'] ?? '')
            ];

            $result = $this->settingsModel->updateMultiple($settings);

            if ($result) {
                $this->logActivity('settings_updated', 'Analytics settings updated');
                setFlashMessage('Analytics ayarları başarıyla güncellendi.', 'success');
            } else {
                throw new Exception('Ayarlar güncellenemedi.');
            }

        } catch (Exception $e) {
            setFlashMessage('Hata: ' . $e->getMessage(), 'error');
        }

        redirect(ADMIN_URL . '/settings?tab=analytics');
    }

    /**
     * Update Advanced Settings
     */
    public function updateAdvanced() {
        try {
            $settings = [
                'maintenance_mode' => isset($_POST['maintenance_mode']) ? '1' : '0',
                'maintenance_message' => sanitize($_POST['maintenance_message'] ?? ''),
                'currency' => sanitize($_POST['currency'] ?? 'TRY'),
                'currency_symbol' => sanitize($_POST['currency_symbol'] ?? '₺'),
                'tax_enabled' => isset($_POST['tax_enabled']) ? '1' : '0',
                'tax_rate' => sanitize($_POST['tax_rate'] ?? '20'),
                'stock_alert_enabled' => isset($_POST['stock_alert_enabled']) ? '1' : '0',
                'stock_alert_threshold' => sanitize($_POST['stock_alert_threshold'] ?? '10'),
                'enable_reviews' => isset($_POST['enable_reviews']) ? '1' : '0',
                'enable_wishlist' => isset($_POST['enable_wishlist']) ? '1' : '0',
                'enable_loyalty' => isset($_POST['enable_loyalty']) ? '1' : '0',
                'loyalty_points_rate' => sanitize($_POST['loyalty_points_rate'] ?? '10'),
                'min_order_amount' => sanitize($_POST['min_order_amount'] ?? '50'),
                'max_cart_items' => sanitize($_POST['max_cart_items'] ?? '50'),
                'session_timeout' => sanitize($_POST['session_timeout'] ?? '3600'),
                'password_min_length' => sanitize($_POST['password_min_length'] ?? '6')
            ];

            $result = $this->settingsModel->updateMultiple($settings);

            if ($result) {
                $this->logActivity('settings_updated', 'Advanced settings updated');
                setFlashMessage('Gelişmiş ayarlar başarıyla güncellendi.', 'success');
            } else {
                throw new Exception('Ayarlar güncellenemedi.');
            }

        } catch (Exception $e) {
            setFlashMessage('Hata: ' . $e->getMessage(), 'error');
        }

        redirect(ADMIN_URL . '/settings?tab=advanced');
    }

    /**
     * Test Email Configuration
     */
    public function testEmail() {
        try {
            $testEmail = sanitize($_POST['test_email'] ?? '');

            if (empty($testEmail)) {
                throw new Exception('Test email adresi gerekli.');
            }

            require_once __DIR__ . '/../../services/EmailService.php';
            $emailService = new EmailService();

            $subject = 'MINALIA - Email Test';
            $message = '<h2>Email Testi Başarılı!</h2><p>SMTP ayarlarınız doğru yapılandırılmış.</p>';

            $result = $emailService->send($testEmail, $subject, $message);

            if ($result) {
                setFlashMessage("Test emaili başarıyla gönderildi: $testEmail", 'success');
            } else {
                throw new Exception('Test emaili gönderilemedi. SMTP ayarlarınızı kontrol edin.');
            }

        } catch (Exception $e) {
            setFlashMessage('Hata: ' . $e->getMessage(), 'error');
        }

        redirect(ADMIN_URL . '/settings?tab=email');
    }

    /**
     * Clear System Cache
     */
    public function clearCache() {
        try {
            // Clear settings cache
            $this->settingsModel->clearCache();

            // Clear other caches if needed
            if (function_exists('opcache_reset')) {
                opcache_reset();
            }

            $this->logActivity('cache_cleared', 'System cache cleared');
            setFlashMessage('Sistem önbelleği temizlendi.', 'success');

        } catch (Exception $e) {
            setFlashMessage('Hata: ' . $e->getMessage(), 'error');
        }

        redirect(ADMIN_URL . '/settings?tab=advanced');
    }

    /**
     * Handle image upload for settings
     *
     * @param array $file $_FILES array element
     * @param string $type Image type (logo, favicon, og)
     * @return string|false Upload path or false on failure
     */
    private function handleImageUpload($file, $type) {
        try {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/x-icon', 'image/vnd.microsoft.icon'];

            if (!in_array($file['type'], $allowedTypes)) {
                throw new Exception('Geçersiz dosya türü.');
            }

            // Max file size: 5MB
            if ($file['size'] > 5 * 1024 * 1024) {
                throw new Exception('Dosya boyutu 5MB\'dan küçük olmalıdır.');
            }

            $uploadDir = __DIR__ . '/../../public/uploads/settings/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = $type . '_' . time() . '.' . $extension;
            $uploadPath = $uploadDir . $filename;

            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                return '/uploads/settings/' . $filename;
            }

            return false;

        } catch (Exception $e) {
            error_log("Image upload error: " . $e->getMessage());
            return false;
        }
    }
}
