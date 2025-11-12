<?php
/**
 * Account Controller - User Dashboard
 * MINALIA Parfüm E-Ticaret Platformu
 */

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/LoyaltyPoints.php';

class AccountController extends BaseController {
    private $db;
    private $userModel;
    private $loyaltyModel;

    public function __construct() {
        $this->requireAuth();
        $this->db = Database::getInstance()->getConnection();
        $this->userModel = new User();
        $this->loyaltyModel = new LoyaltyPoints();
    }

    /**
     * Dashboard
     */
    public function index() {
        $userId = getCurrentUserId();

        // Get order statistics
        $orderSql = "SELECT COUNT(*) as total_orders,
                     COALESCE(SUM(total_amount), 0) as total_spent
                     FROM orders WHERE user_id = ? AND status != 'cancelled'";
        $orderStmt = $this->db->prepare($orderSql);
        $orderStmt->execute([$userId]);
        $orderStats = $orderStmt->fetch(PDO::FETCH_ASSOC);

        // Get loyalty points
        $loyaltyStats = $this->loyaltyModel->getPointsSummary($userId);

        // Get recent orders
        $recentSql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";
        $recentStmt = $this->db->prepare($recentSql);
        $recentStmt->execute([$userId]);
        $recentOrders = $recentStmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('account/dashboard', [
            'title' => 'Hesabım',
            'order_stats' => $orderStats,
            'loyalty_stats' => $loyaltyStats,
            'recent_orders' => $recentOrders
        ]);
    }

    /**
     * Orders list
     */
    public function orders() {
        $userId = getCurrentUserId();
        $page = max(1, (int)$this->get('page', 1));

        $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 20 OFFSET " . (($page - 1) * 20);
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('account/orders', [
            'title' => 'Siparişlerim',
            'orders' => $orders
        ]);
    }

    /**
     * Order detail
     */
    public function orderDetail($id) {
        $userId = getCurrentUserId();

        $sql = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id, $userId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            setFlashMessage('Sipariş bulunamadı.', 'error');
            redirect('/account/orders');
            return;
        }

        // Get order items
        $itemsSql = "SELECT oi.*, p.name, p.main_image, b.name as brand_name
                     FROM order_items oi
                     INNER JOIN products p ON oi.product_id = p.id
                     INNER JOIN brands b ON p.brand_id = b.id
                     WHERE oi.order_id = ?";
        $itemsStmt = $this->db->prepare($itemsSql);
        $itemsStmt->execute([$id]);
        $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('account/order-detail', [
            'title' => 'Sipariş #' . $order['order_number'],
            'order' => $order,
            'items' => $items
        ]);
    }

    /**
     * Profile
     */
    public function profile() {
        $userId = getCurrentUserId();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->updateProfile();
            return;
        }

        $user = $this->userModel->find($userId);

        $this->view('account/profile', [
            'title' => 'Profil Bilgilerim',
            'user' => $user
        ]);
    }

    /**
     * Update profile
     */
    public function updateProfile() {
        $userId = getCurrentUserId();

        // CSRF validation
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('Güvenlik hatası. Lütfen tekrar deneyin.', 'error');
            redirect('/account/profile');
            return;
        }

        // Validate and sanitize inputs
        $firstName = sanitize($_POST['first_name'] ?? '');
        $lastName = sanitize($_POST['last_name'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $birthDate = $_POST['birth_date'] ?? null;
        $gender = sanitize($_POST['gender'] ?? '');
        $newsletterSubscribed = isset($_POST['newsletter_subscribed']) ? 1 : 0;
        $smsSubscribed = isset($_POST['sms_subscribed']) ? 1 : 0;

        // Validation
        $errors = [];

        if (empty($firstName)) {
            $errors[] = 'Ad alanı zorunludur.';
        }

        if (empty($lastName)) {
            $errors[] = 'Soyad alanı zorunludur.';
        }

        if (!empty($phone) && !preg_match('/^[0-9\s\-\+\(\)]{10,20}$/', $phone)) {
            $errors[] = 'Geçerli bir telefon numarası giriniz.';
        }

        if (!empty($birthDate)) {
            $date = DateTime::createFromFormat('Y-m-d', $birthDate);
            if (!$date || $date->format('Y-m-d') !== $birthDate) {
                $errors[] = 'Geçerli bir doğum tarihi giriniz.';
            }
        }

        if (!empty($gender) && !in_array($gender, ['male', 'female', 'other'])) {
            $errors[] = 'Geçerli bir cinsiyet seçiniz.';
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                setFlashMessage($error, 'error');
            }
            redirect('/account/profile');
            return;
        }

        try {
            $data = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone,
                'birth_date' => $birthDate,
                'gender' => $gender,
                'newsletter_subscribed' => $newsletterSubscribed,
                'sms_subscribed' => $smsSubscribed
            ];

            $this->userModel->updateProfile($userId, $data);

            setFlashMessage('Profil bilgileriniz başarıyla güncellendi.', 'success');
        } catch (Exception $e) {
            error_log("Profile update error: " . $e->getMessage());
            setFlashMessage('Profil güncellenirken bir hata oluştu. Lütfen tekrar deneyin.', 'error');
        }

        redirect('/account/profile');
    }

    /**
     * Loyalty points
     */
    public function loyalty() {
        $userId = getCurrentUserId();

        $summary = $this->loyaltyModel->getPointsSummary($userId);
        $history = $this->loyaltyModel->getPointsHistory($userId, 20);

        $this->view('account/loyalty', [
            'title' => 'Sadakat Puanlarım',
            'summary' => $summary,
            'history' => $history
        ]);
    }

    /**
     * Addresses
     */
    public function addresses() {
        $userId = getCurrentUserId();

        $sql = "SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC, created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('account/addresses', [
            'title' => 'Adreslerim',
            'addresses' => $addresses
        ]);
    }

    /**
     * Change password
     */
    public function changePassword() {
        $userId = getCurrentUserId();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/account/profile');
            return;
        }

        // CSRF validation
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('Güvenlik hatası. Lütfen tekrar deneyin.', 'error');
            redirect('/account/profile');
            return;
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validation
        $errors = [];

        if (empty($currentPassword)) {
            $errors[] = 'Mevcut şifrenizi giriniz.';
        }

        if (empty($newPassword)) {
            $errors[] = 'Yeni şifrenizi giriniz.';
        } elseif (strlen($newPassword) < 6) {
            $errors[] = 'Yeni şifre en az 6 karakter olmalıdır.';
        }

        if ($newPassword !== $confirmPassword) {
            $errors[] = 'Yeni şifreler eşleşmiyor.';
        }

        if ($currentPassword === $newPassword) {
            $errors[] = 'Yeni şifre mevcut şifre ile aynı olamaz.';
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                setFlashMessage($error, 'error');
            }
            redirect('/account/profile');
            return;
        }

        try {
            $result = $this->userModel->changePassword($userId, $currentPassword, $newPassword);

            if ($result) {
                setFlashMessage('Şifreniz başarıyla değiştirildi.', 'success');
            } else {
                setFlashMessage('Mevcut şifreniz hatalı.', 'error');
            }
        } catch (Exception $e) {
            error_log("Password change error: " . $e->getMessage());
            setFlashMessage('Şifre değiştirme sırasında bir hata oluştu. Lütfen tekrar deneyin.', 'error');
        }

        redirect('/account/profile');
    }

    /**
     * Add new address
     */
    public function addAddress() {
        $userId = getCurrentUserId();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/account/addresses');
            return;
        }

        // CSRF validation
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('Güvenlik hatası. Lütfen tekrar deneyin.', 'error');
            redirect('/account/addresses');
            return;
        }

        // Validate and sanitize inputs
        $title = sanitize($_POST['title'] ?? '');
        $fullName = sanitize($_POST['full_name'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $addressLine1 = sanitize($_POST['address_line1'] ?? '');
        $addressLine2 = sanitize($_POST['address_line2'] ?? '');
        $district = sanitize($_POST['district'] ?? '');
        $city = sanitize($_POST['city'] ?? '');
        $postalCode = sanitize($_POST['postal_code'] ?? '');
        $addressType = sanitize($_POST['address_type'] ?? 'both');
        $isDefault = isset($_POST['is_default']) ? 1 : 0;

        // Validation
        $errors = [];

        if (empty($title)) {
            $errors[] = 'Adres başlığı zorunludur.';
        }

        if (empty($fullName)) {
            $errors[] = 'Ad Soyad zorunludur.';
        }

        if (empty($phone)) {
            $errors[] = 'Telefon numarası zorunludur.';
        } else {
            // Clean phone number for validation
            $cleanPhone = preg_replace('/[\s\-\(\)]/', '', $phone);

            // Turkish phone format: 0xxx xxx xx xx (11 digits starting with 0)
            // Or international: +90xxx xxx xx xx (13 digits starting with +90)
            if (!preg_match('/^(0[0-9]{10}|\\+90[0-9]{10})$/', $cleanPhone)) {
                $errors[] = 'Geçerli bir telefon numarası giriniz (örn: 0532 123 45 67 veya +90 532 123 45 67).';
            }
        }

        if (empty($addressLine1)) {
            $errors[] = 'Adres satırı zorunludur.';
        } elseif (strlen($addressLine1) < 10) {
            $errors[] = 'Adres en az 10 karakter olmalıdır.';
        }

        if (empty($district)) {
            $errors[] = 'İlçe zorunludur.';
        } elseif (strlen($district) < 2) {
            $errors[] = 'Geçerli bir ilçe adı giriniz.';
        }

        if (empty($city)) {
            $errors[] = 'İl zorunludur.';
        } elseif (strlen($city) < 2) {
            $errors[] = 'Geçerli bir il adı giriniz.';
        }

        if (empty($postalCode)) {
            $errors[] = 'Posta kodu zorunludur.';
        } elseif (!preg_match('/^[0-9]{5}$/', $postalCode)) {
            $errors[] = 'Posta kodu 5 haneli sayı olmalıdır (örn: 34000).';
        } elseif (intval($postalCode) < 1000 || intval($postalCode) > 81999) {
            $errors[] = 'Geçerli bir Türkiye posta kodu giriniz (01000-81999).';
        }

        if (!in_array($addressType, ['billing', 'shipping', 'both'])) {
            $errors[] = 'Geçersiz adres tipi.';
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                setFlashMessage($error, 'error');
            }
            redirect('/account/addresses');
            return;
        }

        try {
            $this->db->beginTransaction();

            // If this is set as default, remove default from other addresses
            if ($isDefault) {
                $sql = "UPDATE addresses SET is_default = 0 WHERE user_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$userId]);
            }

            // Insert new address
            $sql = "INSERT INTO addresses (user_id, title, full_name, phone, address_line1, address_line2,
                    district, city, postal_code, country, address_type, is_default, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Türkiye', ?, ?, NOW(), NOW())";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $userId, $title, $fullName, $phone, $addressLine1, $addressLine2,
                $district, $city, $postalCode, $addressType, $isDefault
            ]);

            $this->db->commit();

            setFlashMessage('Adres başarıyla eklendi.', 'success');
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Add address error: " . $e->getMessage());
            setFlashMessage('Adres eklenirken bir hata oluştu. Lütfen tekrar deneyin.', 'error');
        }

        redirect('/account/addresses');
    }

    /**
     * Update address
     */
    public function updateAddress($id) {
        $userId = getCurrentUserId();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/account/addresses');
            return;
        }

        // CSRF validation
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('Güvenlik hatası. Lütfen tekrar deneyin.', 'error');
            redirect('/account/addresses');
            return;
        }

        // Check if address belongs to user
        $checkSql = "SELECT id FROM addresses WHERE id = ? AND user_id = ?";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->execute([$id, $userId]);

        if (!$checkStmt->fetch()) {
            setFlashMessage('Adres bulunamadı.', 'error');
            redirect('/account/addresses');
            return;
        }

        // Validate and sanitize inputs
        $title = sanitize($_POST['title'] ?? '');
        $fullName = sanitize($_POST['full_name'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $addressLine1 = sanitize($_POST['address_line1'] ?? '');
        $addressLine2 = sanitize($_POST['address_line2'] ?? '');
        $district = sanitize($_POST['district'] ?? '');
        $city = sanitize($_POST['city'] ?? '');
        $postalCode = sanitize($_POST['postal_code'] ?? '');
        $addressType = sanitize($_POST['address_type'] ?? 'both');
        $isDefault = isset($_POST['is_default']) ? 1 : 0;

        // Validation
        $errors = [];

        if (empty($title)) {
            $errors[] = 'Adres başlığı zorunludur.';
        }

        if (empty($fullName)) {
            $errors[] = 'Ad Soyad zorunludur.';
        }

        if (empty($phone)) {
            $errors[] = 'Telefon numarası zorunludur.';
        } else {
            // Clean phone number for validation
            $cleanPhone = preg_replace('/[\s\-\(\)]/', '', $phone);

            // Turkish phone format: 0xxx xxx xx xx (11 digits starting with 0)
            // Or international: +90xxx xxx xx xx (13 digits starting with +90)
            if (!preg_match('/^(0[0-9]{10}|\\+90[0-9]{10})$/', $cleanPhone)) {
                $errors[] = 'Geçerli bir telefon numarası giriniz (örn: 0532 123 45 67 veya +90 532 123 45 67).';
            }
        }

        if (empty($addressLine1)) {
            $errors[] = 'Adres satırı zorunludur.';
        } elseif (strlen($addressLine1) < 10) {
            $errors[] = 'Adres en az 10 karakter olmalıdır.';
        }

        if (empty($district)) {
            $errors[] = 'İlçe zorunludur.';
        } elseif (strlen($district) < 2) {
            $errors[] = 'Geçerli bir ilçe adı giriniz.';
        }

        if (empty($city)) {
            $errors[] = 'İl zorunludur.';
        } elseif (strlen($city) < 2) {
            $errors[] = 'Geçerli bir il adı giriniz.';
        }

        if (empty($postalCode)) {
            $errors[] = 'Posta kodu zorunludur.';
        } elseif (!preg_match('/^[0-9]{5}$/', $postalCode)) {
            $errors[] = 'Posta kodu 5 haneli sayı olmalıdır (örn: 34000).';
        } elseif (intval($postalCode) < 1000 || intval($postalCode) > 81999) {
            $errors[] = 'Geçerli bir Türkiye posta kodu giriniz (01000-81999).';
        }

        if (!in_array($addressType, ['billing', 'shipping', 'both'])) {
            $errors[] = 'Geçersiz adres tipi.';
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                setFlashMessage($error, 'error');
            }
            redirect('/account/addresses');
            return;
        }

        try {
            $this->db->beginTransaction();

            // If this is set as default, remove default from other addresses
            if ($isDefault) {
                $sql = "UPDATE addresses SET is_default = 0 WHERE user_id = ? AND id != ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$userId, $id]);
            }

            // Update address
            $sql = "UPDATE addresses SET title = ?, full_name = ?, phone = ?, address_line1 = ?,
                    address_line2 = ?, district = ?, city = ?, postal_code = ?, address_type = ?,
                    is_default = ?, updated_at = NOW()
                    WHERE id = ? AND user_id = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $title, $fullName, $phone, $addressLine1, $addressLine2,
                $district, $city, $postalCode, $addressType, $isDefault, $id, $userId
            ]);

            $this->db->commit();

            setFlashMessage('Adres başarıyla güncellendi.', 'success');
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Update address error: " . $e->getMessage());
            setFlashMessage('Adres güncellenirken bir hata oluştu. Lütfen tekrar deneyin.', 'error');
        }

        redirect('/account/addresses');
    }

    /**
     * Delete address
     */
    public function deleteAddress($id) {
        $userId = getCurrentUserId();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/account/addresses');
            return;
        }

        // CSRF validation
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('Güvenlik hatası. Lütfen tekrar deneyin.', 'error');
            redirect('/account/addresses');
            return;
        }

        try {
            // Check if address belongs to user
            $checkSql = "SELECT id FROM addresses WHERE id = ? AND user_id = ?";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute([$id, $userId]);

            if (!$checkStmt->fetch()) {
                setFlashMessage('Adres bulunamadı.', 'error');
                redirect('/account/addresses');
                return;
            }

            // Delete address
            $sql = "DELETE FROM addresses WHERE id = ? AND user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id, $userId]);

            setFlashMessage('Adres başarıyla silindi.', 'success');
        } catch (Exception $e) {
            error_log("Delete address error: " . $e->getMessage());
            setFlashMessage('Adres silinirken bir hata oluştu. Lütfen tekrar deneyin.', 'error');
        }

        redirect('/account/addresses');
    }

    /**
     * Set default address
     */
    public function setDefaultAddress($id) {
        $userId = getCurrentUserId();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/account/addresses');
            return;
        }

        // CSRF validation
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('Güvenlik hatası. Lütfen tekrar deneyin.', 'error');
            redirect('/account/addresses');
            return;
        }

        try {
            // Check if address belongs to user
            $checkSql = "SELECT id FROM addresses WHERE id = ? AND user_id = ?";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute([$id, $userId]);

            if (!$checkStmt->fetch()) {
                setFlashMessage('Adres bulunamadı.', 'error');
                redirect('/account/addresses');
                return;
            }

            $this->db->beginTransaction();

            // Remove default from all addresses
            $sql = "UPDATE addresses SET is_default = 0 WHERE user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);

            // Set this address as default
            $sql = "UPDATE addresses SET is_default = 1 WHERE id = ? AND user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id, $userId]);

            $this->db->commit();

            setFlashMessage('Varsayılan adres ayarlandı.', 'success');
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Set default address error: " . $e->getMessage());
            setFlashMessage('Varsayılan adres ayarlanırken bir hata oluştu. Lütfen tekrar deneyin.', 'error');
        }

        redirect('/account/addresses');
    }

    /**
     * Payment methods list
     */
    public function paymentMethods() {
        $userId = getCurrentUserId();

        $sql = "SELECT * FROM saved_payment_methods WHERE user_id = ? AND is_active = 1 ORDER BY is_default DESC, created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $paymentMethods = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('account/payment-methods', [
            'title' => 'Ödeme Yöntemlerim',
            'payment_methods' => $paymentMethods
        ]);
    }

    /**
     * Add payment method
     */
    public function addPaymentMethod() {
        $userId = getCurrentUserId();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/account/payment-methods');
            return;
        }

        // CSRF validation
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('Güvenlik hatası. Lütfen tekrar deneyin.', 'error');
            redirect('/account/payment-methods');
            return;
        }

        // Get card information
        $cardAlias = sanitize($_POST['card_alias'] ?? '');
        $cardToken = sanitize($_POST['card_token'] ?? ''); // From payment gateway
        $cardBrand = sanitize($_POST['card_brand'] ?? '');
        $lastFourDigits = sanitize($_POST['last_four_digits'] ?? '');
        $expiryMonth = (int)($_POST['expiry_month'] ?? 0);
        $expiryYear = (int)($_POST['expiry_year'] ?? 0);
        $cardholderName = sanitize($_POST['cardholder_name'] ?? '');
        $isDefault = isset($_POST['is_default']) ? 1 : 0;

        // Validation
        $errors = [];

        if (empty($cardAlias)) {
            $errors[] = 'Kart ismi zorunludur.';
        }

        if (empty($cardToken)) {
            $errors[] = 'Geçersiz kart bilgisi.';
        }

        if (empty($cardholderName)) {
            $errors[] = 'Kart sahibi adı zorunludur.';
        }

        if (!in_array($cardBrand, ['visa', 'mastercard', 'amex', 'troy'])) {
            $errors[] = 'Geçersiz kart markası.';
        }

        if ($expiryMonth < 1 || $expiryMonth > 12) {
            $errors[] = 'Geçersiz ay.';
        }

        if ($expiryYear < date('Y')) {
            $errors[] = 'Kart süresi dolmuş.';
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                setFlashMessage($error, 'error');
            }
            redirect('/account/payment-methods');
            return;
        }

        try {
            $this->db->beginTransaction();

            // If this is set as default, remove default from other cards
            if ($isDefault) {
                $sql = "UPDATE saved_payment_methods SET is_default = 0 WHERE user_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$userId]);
            }

            // Insert new payment method
            $sql = "INSERT INTO saved_payment_methods (user_id, card_token, card_alias, card_brand,
                    last_four_digits, expiry_month, expiry_year, cardholder_name, is_default, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $userId, $cardToken, $cardAlias, $cardBrand,
                $lastFourDigits, $expiryMonth, $expiryYear, $cardholderName, $isDefault
            ]);

            $this->db->commit();

            setFlashMessage('Ödeme yöntemi başarıyla eklendi.', 'success');
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Add payment method error: " . $e->getMessage());
            setFlashMessage('Ödeme yöntemi eklenirken bir hata oluştu. Lütfen tekrar deneyin.', 'error');
        }

        redirect('/account/payment-methods');
    }

    /**
     * Delete payment method
     */
    public function deletePaymentMethod($id) {
        $userId = getCurrentUserId();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/account/payment-methods');
            return;
        }

        // CSRF validation
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('Güvenlik hatası. Lütfen tekrar deneyin.', 'error');
            redirect('/account/payment-methods');
            return;
        }

        try {
            // Check if payment method belongs to user
            $checkSql = "SELECT id FROM saved_payment_methods WHERE id = ? AND user_id = ?";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute([$id, $userId]);

            if (!$checkStmt->fetch()) {
                setFlashMessage('Ödeme yöntemi bulunamadı.', 'error');
                redirect('/account/payment-methods');
                return;
            }

            // Soft delete - set is_active to 0
            $sql = "UPDATE saved_payment_methods SET is_active = 0 WHERE id = ? AND user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id, $userId]);

            setFlashMessage('Ödeme yöntemi başarıyla silindi.', 'success');
        } catch (Exception $e) {
            error_log("Delete payment method error: " . $e->getMessage());
            setFlashMessage('Ödeme yöntemi silinirken bir hata oluştu. Lütfen tekrar deneyin.', 'error');
        }

        redirect('/account/payment-methods');
    }

    /**
     * Set default payment method
     */
    public function setDefaultPaymentMethod($id) {
        $userId = getCurrentUserId();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/account/payment-methods');
            return;
        }

        // CSRF validation
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('Güvenlik hatası. Lütfen tekrar deneyin.', 'error');
            redirect('/account/payment-methods');
            return;
        }

        try {
            // Check if payment method belongs to user
            $checkSql = "SELECT id FROM saved_payment_methods WHERE id = ? AND user_id = ?";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute([$id, $userId]);

            if (!$checkStmt->fetch()) {
                setFlashMessage('Ödeme yöntemi bulunamadı.', 'error');
                redirect('/account/payment-methods');
                return;
            }

            $this->db->beginTransaction();

            // Remove default from all payment methods
            $sql = "UPDATE saved_payment_methods SET is_default = 0 WHERE user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);

            // Set this payment method as default
            $sql = "UPDATE saved_payment_methods SET is_default = 1 WHERE id = ? AND user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id, $userId]);

            $this->db->commit();

            setFlashMessage('Varsayılan ödeme yöntemi ayarlandı.', 'success');
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Set default payment method error: " . $e->getMessage());
            setFlashMessage('Varsayılan ödeme yöntemi ayarlanırken bir hata oluştu. Lütfen tekrar deneyin.', 'error');
        }

        redirect('/account/payment-methods');
    }

    /**
     * Returns/Cancellations list
     */
    public function returns() {
        $userId = getCurrentUserId();

        $sql = "SELECT r.*, o.order_number, o.total_amount as order_total
                FROM returns r
                INNER JOIN orders o ON r.order_id = o.id
                WHERE r.user_id = ?
                ORDER BY r.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $returns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('account/returns', [
            'title' => 'İadelerim',
            'returns' => $returns
        ]);
    }

    /**
     * Return detail
     */
    public function returnDetail($id) {
        $userId = getCurrentUserId();

        $sql = "SELECT r.*, o.order_number, o.total_amount as order_total, o.created_at as order_date
                FROM returns r
                INNER JOIN orders o ON r.order_id = o.id
                WHERE r.id = ? AND r.user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id, $userId]);
        $return = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$return) {
            setFlashMessage('İade talebi bulunamadı.', 'error');
            redirect('/account/returns');
            return;
        }

        // Decode JSON fields
        $return['return_items'] = json_decode($return['return_items'], true);
        $return['proof_images'] = $return['proof_images'] ? json_decode($return['proof_images'], true) : [];

        $this->view('account/return-detail', [
            'title' => 'İade Detayı #' . $return['return_number'],
            'return' => $return
        ]);
    }

    /**
     * Create return request
     */
    public function createReturn($orderId) {
        $userId = getCurrentUserId();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Show create return form
            $sql = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$orderId, $userId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                setFlashMessage('Sipariş bulunamadı.', 'error');
                redirect('/account/orders');
                return;
            }

            // Get order items
            $itemsSql = "SELECT oi.*, p.name, p.main_image
                        FROM order_items oi
                        INNER JOIN products p ON oi.product_id = p.id
                        WHERE oi.order_id = ?";
            $itemsStmt = $this->db->prepare($itemsSql);
            $itemsStmt->execute([$orderId]);
            $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

            $this->view('account/create-return', [
                'title' => 'İade Talebi Oluştur',
                'order' => $order,
                'items' => $items
            ]);
            return;
        }

        // Process return request
        // CSRF validation
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('Güvenlik hatası. Lütfen tekrar deneyin.', 'error');
            redirect('/account/orders');
            return;
        }

        // Validate order belongs to user
        $sql = "SELECT id, order_number FROM orders WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId, $userId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            setFlashMessage('Sipariş bulunamadı.', 'error');
            redirect('/account/orders');
            return;
        }

        $returnType = sanitize($_POST['return_type'] ?? 'return');
        $reason = sanitize($_POST['reason'] ?? '');
        $reasonDetails = sanitize($_POST['reason_details'] ?? '');
        $refundMethod = sanitize($_POST['refund_method'] ?? 'original_payment');
        $returnItems = $_POST['return_items'] ?? [];

        // Validation
        $errors = [];

        if (!in_array($returnType, ['return', 'cancel'])) {
            $errors[] = 'Geçersiz işlem tipi.';
        }

        if (empty($reason)) {
            $errors[] = 'İade sebebi zorunludur.';
        }

        if (empty($returnItems)) {
            $errors[] = 'En az bir ürün seçmelisiniz.';
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                setFlashMessage($error, 'error');
            }
            redirect('/account/orders/' . $order['order_number']);
            return;
        }

        try {
            // Generate return number
            $returnNumber = 'RET-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            // Prepare return items JSON
            $returnItemsFormatted = [];
            foreach ($returnItems as $productId => $quantity) {
                if ($quantity > 0) {
                    $returnItemsFormatted[] = [
                        'product_id' => (int)$productId,
                        'quantity' => (int)$quantity
                    ];
                }
            }

            // Insert return request
            $sql = "INSERT INTO returns (return_number, order_id, user_id, return_type, reason,
                    reason_details, return_items, refund_method, status, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW(), NOW())";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $returnNumber,
                $orderId,
                $userId,
                $returnType,
                $reason,
                $reasonDetails,
                json_encode($returnItemsFormatted),
                $refundMethod
            ]);

            setFlashMessage('İade talebiniz başarıyla oluşturuldu. Talebiniz en kısa sürede değerlendirilecektir.', 'success');
            redirect('/account/returns');
        } catch (Exception $e) {
            error_log("Create return error: " . $e->getMessage());
            setFlashMessage('İade talebi oluşturulurken bir hata oluştu. Lütfen tekrar deneyin.', 'error');
            redirect('/account/orders');
        }
    }

    /**
     * Cancel return request
     */
    public function cancelReturn($id) {
        $userId = getCurrentUserId();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/account/returns');
            return;
        }

        // CSRF validation
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('Güvenlik hatası. Lütfen tekrar deneyin.', 'error');
            redirect('/account/returns');
            return;
        }

        try {
            // Check if return belongs to user and is pending
            $sql = "SELECT id, status FROM returns WHERE id = ? AND user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id, $userId]);
            $return = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$return) {
                setFlashMessage('İade talebi bulunamadı.', 'error');
                redirect('/account/returns');
                return;
            }

            if ($return['status'] !== 'pending') {
                setFlashMessage('Bu talep artık iptal edilemez.', 'error');
                redirect('/account/returns');
                return;
            }

            // Update status to rejected (user cancelled)
            $sql = "UPDATE returns SET status = 'rejected', rejection_reason = 'Müşteri tarafından iptal edildi', updated_at = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);

            setFlashMessage('İade talebiniz iptal edildi.', 'success');
        } catch (Exception $e) {
            error_log("Cancel return error: " . $e->getMessage());
            setFlashMessage('İade talebi iptal edilirken bir hata oluştu.', 'error');
        }

        redirect('/account/returns');
    }

    /**
     * Notifications list
     */
    public function notifications() {
        $userId = getCurrentUserId();

        // Mark as read if requested
        if (isset($_GET['mark_all_read'])) {
            $sql = "UPDATE notifications SET is_read = 1, read_at = NOW() WHERE user_id = ? AND is_read = 0";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            redirect('/account/notifications');
            return;
        }

        // Get notifications
        $sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get unread count
        $countSql = "SELECT COUNT(*) as unread_count FROM notifications WHERE user_id = ? AND is_read = 0";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute([$userId]);
        $unreadCount = $countStmt->fetch(PDO::FETCH_ASSOC)['unread_count'];

        $this->view('account/notifications', [
            'title' => 'Bildirimlerim',
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead($id) {
        $userId = getCurrentUserId();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/account/notifications');
            return;
        }

        try {
            $sql = "UPDATE notifications SET is_read = 1, read_at = NOW() WHERE id = ? AND user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id, $userId]);

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Delete notification
     */
    public function deleteNotification($id) {
        $userId = getCurrentUserId();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/account/notifications');
            return;
        }

        // CSRF validation
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('Güvenlik hatası.', 'error');
            redirect('/account/notifications');
            return;
        }

        try {
            $sql = "DELETE FROM notifications WHERE id = ? AND user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id, $userId]);

            setFlashMessage('Bildirim silindi.', 'success');
        } catch (Exception $e) {
            error_log("Delete notification error: " . $e->getMessage());
            setFlashMessage('Bildirim silinirken bir hata oluştu.', 'error');
        }

        redirect('/account/notifications');
    }

    /**
     * Notification preferences
     */
    public function notificationPreferences() {
        $userId = getCurrentUserId();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF validation
            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                setFlashMessage('Güvenlik hatası.', 'error');
                redirect('/account/notifications/preferences');
                return;
            }

            $preferences = [
                'email_notifications' => isset($_POST['email_notifications']) ? 1 : 0,
                'sms_notifications' => isset($_POST['sms_notifications']) ? 1 : 0,
                'push_notifications' => isset($_POST['push_notifications']) ? 1 : 0,
                'order_updates' => isset($_POST['order_updates']) ? 1 : 0,
                'return_updates' => isset($_POST['return_updates']) ? 1 : 0,
                'shipping_updates' => isset($_POST['shipping_updates']) ? 1 : 0,
                'promotions' => isset($_POST['promotions']) ? 1 : 0,
                'price_drops' => isset($_POST['price_drops']) ? 1 : 0,
                'stock_alerts' => isset($_POST['stock_alerts']) ? 1 : 0,
                'review_responses' => isset($_POST['review_responses']) ? 1 : 0,
                'loyalty_updates' => isset($_POST['loyalty_updates']) ? 1 : 0
            ];

            try {
                // Check if preferences exist
                $checkSql = "SELECT id FROM notification_preferences WHERE user_id = ?";
                $checkStmt = $this->db->prepare($checkSql);
                $checkStmt->execute([$userId]);
                $exists = $checkStmt->fetch();

                if ($exists) {
                    // Update
                    $sql = "UPDATE notification_preferences SET
                            email_notifications = ?, sms_notifications = ?, push_notifications = ?,
                            order_updates = ?, return_updates = ?, shipping_updates = ?,
                            promotions = ?, price_drops = ?, stock_alerts = ?,
                            review_responses = ?, loyalty_updates = ?, updated_at = NOW()
                            WHERE user_id = ?";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([
                        $preferences['email_notifications'], $preferences['sms_notifications'], $preferences['push_notifications'],
                        $preferences['order_updates'], $preferences['return_updates'], $preferences['shipping_updates'],
                        $preferences['promotions'], $preferences['price_drops'], $preferences['stock_alerts'],
                        $preferences['review_responses'], $preferences['loyalty_updates'], $userId
                    ]);
                } else {
                    // Insert
                    $sql = "INSERT INTO notification_preferences (user_id, email_notifications, sms_notifications, push_notifications,
                            order_updates, return_updates, shipping_updates, promotions, price_drops, stock_alerts,
                            review_responses, loyalty_updates, updated_at)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([
                        $userId, $preferences['email_notifications'], $preferences['sms_notifications'], $preferences['push_notifications'],
                        $preferences['order_updates'], $preferences['return_updates'], $preferences['shipping_updates'],
                        $preferences['promotions'], $preferences['price_drops'], $preferences['stock_alerts'],
                        $preferences['review_responses'], $preferences['loyalty_updates']
                    ]);
                }

                setFlashMessage('Bildirim tercihleri güncellendi.', 'success');
            } catch (Exception $e) {
                error_log("Update notification preferences error: " . $e->getMessage());
                setFlashMessage('Tercihler güncellenirken bir hata oluştu.', 'error');
            }

            redirect('/account/notifications/preferences');
            return;
        }

        // Get current preferences
        $sql = "SELECT * FROM notification_preferences WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $preferences = $stmt->fetch(PDO::FETCH_ASSOC);

        // Default preferences if not set
        if (!$preferences) {
            $preferences = [
                'email_notifications' => 1,
                'sms_notifications' => 1,
                'push_notifications' => 1,
                'order_updates' => 1,
                'return_updates' => 1,
                'shipping_updates' => 1,
                'promotions' => 1,
                'price_drops' => 1,
                'stock_alerts' => 1,
                'review_responses' => 1,
                'loyalty_updates' => 1
            ];
        }

        $this->view('account/notification-preferences', [
            'title' => 'Bildirim Tercihleri',
            'preferences' => $preferences
        ]);
    }

    /**
     * My reviews
     */
    public function reviews() {
        $userId = getCurrentUserId();

        $sql = "SELECT r.*, p.name as product_name, p.slug as product_slug, p.main_image as product_image,
                b.name as brand_name
                FROM reviews r
                INNER JOIN products p ON r.product_id = p.id
                INNER JOIN brands b ON p.brand_id = b.id
                WHERE r.user_id = ?
                ORDER BY r.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('account/reviews', [
            'title' => 'Ürün Yorumlarım',
            'reviews' => $reviews
        ]);
    }

    /**
     * Delete review
     */
    public function deleteReview($id) {
        $userId = getCurrentUserId();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/account/reviews');
            return;
        }

        // CSRF validation
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('Güvenlik hatası.', 'error');
            redirect('/account/reviews');
            return;
        }

        try {
            // Check if review belongs to user
            $checkSql = "SELECT id FROM reviews WHERE id = ? AND user_id = ?";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute([$id, $userId]);

            if (!$checkStmt->fetch()) {
                setFlashMessage('Yorum bulunamadı.', 'error');
                redirect('/account/reviews');
                return;
            }

            // Delete review
            $sql = "DELETE FROM reviews WHERE id = ? AND user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id, $userId]);

            setFlashMessage('Yorumunuz silindi.', 'success');
        } catch (Exception $e) {
            error_log("Delete review error: " . $e->getMessage());
            setFlashMessage('Yorum silinirken bir hata oluştu.', 'error');
        }

        redirect('/account/reviews');
    }

    /**
     * User preferences
     */
    public function preferences() {
        $userId = getCurrentUserId();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF validation
            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                setFlashMessage('Güvenlik hatası.', 'error');
                redirect('/account/preferences');
                return;
            }

            $preferredNotes = isset($_POST['preferred_notes']) ? array_filter($_POST['preferred_notes']) : [];
            $preferredBrands = isset($_POST['preferred_brands']) ? array_filter($_POST['preferred_brands']) : [];
            $budgetRange = sanitize($_POST['budget_range'] ?? '');
            $occasionType = sanitize($_POST['occasion_type'] ?? '');

            try {
                // Check if preferences exist
                $checkSql = "SELECT id FROM user_preferences WHERE user_id = ?";
                $checkStmt = $this->db->prepare($checkSql);
                $checkStmt->execute([$userId]);
                $exists = $checkStmt->fetch();

                if ($exists) {
                    $sql = "UPDATE user_preferences SET preferred_notes = ?, preferred_brands = ?,
                            budget_range = ?, occasion_type = ?, updated_at = NOW() WHERE user_id = ?";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([
                        json_encode($preferredNotes),
                        json_encode($preferredBrands),
                        $budgetRange,
                        $occasionType,
                        $userId
                    ]);
                } else {
                    $sql = "INSERT INTO user_preferences (user_id, preferred_notes, preferred_brands,
                            budget_range, occasion_type, updated_at) VALUES (?, ?, ?, ?, ?, NOW())";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([
                        $userId,
                        json_encode($preferredNotes),
                        json_encode($preferredBrands),
                        $budgetRange,
                        $occasionType
                    ]);
                }

                setFlashMessage('Tercihleriniz başarıyla güncellendi.', 'success');
            } catch (Exception $e) {
                error_log("Update preferences error: " . $e->getMessage());
                setFlashMessage('Tercihler güncellenirken bir hata oluştu.', 'error');
            }

            redirect('/account/preferences');
            return;
        }

        // Get preferences
        $sql = "SELECT * FROM user_preferences WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $preferences = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($preferences) {
            $preferences['preferred_notes'] = json_decode($preferences['preferred_notes'], true) ?: [];
            $preferences['preferred_brands'] = json_decode($preferences['preferred_brands'], true) ?: [];
        }

        // Get available brands
        $brandsSql = "SELECT id, name FROM brands ORDER BY name";
        $brandsStmt = $this->db->prepare($brandsSql);
        $brandsStmt->execute();
        $brands = $brandsStmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('account/preferences', [
            'title' => 'Tercihlerim',
            'preferences' => $preferences,
            'brands' => $brands
        ]);
    }

    /**
     * Account security page
     */
    public function security() {
        $userId = getCurrentUserId();

        // Get user info
        $user = $this->userModel->find($userId);

        // Get social accounts
        $socialSql = "SELECT * FROM social_accounts WHERE user_id = ?";
        $socialStmt = $this->db->prepare($socialSql);
        $socialStmt->execute([$userId]);
        $socialAccounts = $socialStmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('account/security', [
            'title' => 'Hesap Güvenliği',
            'user' => $user,
            'social_accounts' => $socialAccounts
        ]);
    }

    /**
     * Reorder - Add all items from a previous order to cart
     */
    public function reorder($orderId) {
        if (!isLoggedIn()) {
            redirect('/login');
            return;
        }

        // CSRF validation
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('Güvenlik hatası. Lütfen tekrar deneyin.', 'error');
            redirect('/account/orders');
            return;
        }

        $userId = getCurrentUserId();

        try {
            // Verify order belongs to user
            $orderSql = "SELECT id FROM orders WHERE id = ? AND user_id = ?";
            $orderStmt = $this->db->prepare($orderSql);
            $orderStmt->execute([$orderId, $userId]);
            $order = $orderStmt->fetch();

            if (!$order) {
                setFlashMessage('Sipariş bulunamadı.', 'error');
                redirect('/account/orders');
                return;
            }

            // Get order items with product availability check
            $itemsSql = "SELECT oi.product_id, oi.quantity, p.name, p.stock_quantity, p.is_active, p.price
                         FROM order_items oi
                         INNER JOIN products p ON oi.product_id = p.id
                         WHERE oi.order_id = ?";
            $itemsStmt = $this->db->prepare($itemsSql);
            $itemsStmt->execute([$orderId]);
            $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($items)) {
                setFlashMessage('Bu siparişte ürün bulunamadı.', 'error');
                redirect('/account/orders/' . $orderId);
                return;
            }

            // Initialize cart if not exists
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            $addedCount = 0;
            $skippedProducts = [];

            foreach ($items as $item) {
                // Check if product is available
                if ($item['is_active'] == 0) {
                    $skippedProducts[] = $item['name'] . ' (Ürün aktif değil)';
                    continue;
                }

                // Check stock
                if ($item['stock_quantity'] < $item['quantity']) {
                    if ($item['stock_quantity'] > 0) {
                        // Add available stock
                        $_SESSION['cart'][$item['product_id']] = [
                            'product_id' => $item['product_id'],
                            'quantity' => $item['stock_quantity'],
                            'price' => $item['price']
                        ];
                        $skippedProducts[] = $item['name'] . ' (Sadece ' . $item['stock_quantity'] . ' adet mevcut)';
                        $addedCount++;
                    } else {
                        $skippedProducts[] = $item['name'] . ' (Stokta yok)';
                    }
                    continue;
                }

                // Add to cart (or update quantity if already in cart)
                if (isset($_SESSION['cart'][$item['product_id']])) {
                    $_SESSION['cart'][$item['product_id']]['quantity'] += $item['quantity'];
                } else {
                    $_SESSION['cart'][$item['product_id']] = [
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price']
                    ];
                }

                $addedCount++;
            }

            // Build success message
            $message = '';
            if ($addedCount > 0) {
                $message = $addedCount . ' ürün sepete eklendi.';
            }

            if (!empty($skippedProducts)) {
                $message .= ' Bazı ürünler eklenemedi: ' . implode(', ', $skippedProducts);
            }

            if ($addedCount > 0) {
                setFlashMessage($message, 'success');
                redirect('/cart');
            } else {
                setFlashMessage('Hiçbir ürün sepete eklenemedi. ' . implode(', ', $skippedProducts), 'error');
                redirect('/account/orders/' . $orderId);
            }

        } catch (Exception $e) {
            error_log('Reorder error: ' . $e->getMessage());
            setFlashMessage('Tekrar sipariş verme sırasında bir hata oluştu.', 'error');
            redirect('/account/orders/' . $orderId);
        }
    }

    /**
     * User coupons page
     */
    public function coupons() {
        $userId = getCurrentUserId();

        // Get user's coupons with coupon details
        $sql = "SELECT uc.*, c.code, c.type, c.discount_value, c.minimum_order_amount,
                c.maximum_discount_amount, c.valid_from, c.valid_until, c.title, c.description,
                c.usage_limit_per_user, c.is_active
                FROM user_coupons uc
                INNER JOIN coupons c ON uc.coupon_id = c.id
                WHERE uc.user_id = ?
                ORDER BY
                    CASE uc.status
                        WHEN 'available' THEN 1
                        WHEN 'expired' THEN 2
                        WHEN 'used' THEN 3
                    END,
                    c.valid_until ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $userCoupons = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Update expired coupons
        foreach ($userCoupons as &$coupon) {
            // Check if coupon has expired
            $expiryDate = $coupon['custom_expiry_date'] ?? $coupon['valid_until'];

            if ($coupon['status'] === 'available' && (strtotime($expiryDate) < time() || $coupon['is_active'] == 0)) {
                // Mark as expired
                $updateSql = "UPDATE user_coupons SET status = 'expired' WHERE id = ?";
                $updateStmt = $this->db->prepare($updateSql);
                $updateStmt->execute([$coupon['id']]);
                $coupon['status'] = 'expired';
            }

            // Check if usage limit reached
            if ($coupon['status'] === 'available' && $coupon['times_used'] >= $coupon['usage_limit_per_user']) {
                $updateSql = "UPDATE user_coupons SET status = 'used' WHERE id = ?";
                $updateStmt = $this->db->prepare($updateSql);
                $updateStmt->execute([$coupon['id']]);
                $coupon['status'] = 'used';
            }
        }

        // Get usage history
        $historySql = "SELECT cul.*, c.code, c.title, o.order_number,
                       cul.used_at as coupon_used_at, o.created_at as order_date
                       FROM coupon_usage_log cul
                       INNER JOIN coupons c ON cul.coupon_id = c.id
                       INNER JOIN orders o ON cul.order_id = o.id
                       WHERE cul.user_id = ?
                       ORDER BY cul.used_at DESC
                       LIMIT 10";

        $historyStmt = $this->db->prepare($historySql);
        $historyStmt->execute([$userId]);
        $usageHistory = $historyStmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('account/coupons', [
            'title' => 'Kuponlarım',
            'coupons' => $userCoupons,
            'usage_history' => $usageHistory
        ]);
    }

    /**
     * Update KVKK consent settings
     */
    public function updateKVKKConsent() {
        if (!isLoggedIn()) {
            redirect('/login');
            return;
        }

        $userId = getCurrentUserId();

        // CSRF validation
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('Güvenlik hatası. Lütfen tekrar deneyin.', 'error');
            redirect('/account/security');
            return;
        }

        $marketingConsent = isset($_POST['marketing_consent']) ? 1 : 0;
        $personalizedAdsConsent = isset($_POST['personalized_ads_consent']) ? 1 : 0;
        $dataSharingConsent = isset($_POST['data_sharing_consent']) ? 1 : 0;
        $profilingConsent = isset($_POST['profiling_consent']) ? 1 : 0;

        try {
            // Check if preferences exist
            $checkSql = "SELECT id FROM user_preferences WHERE user_id = ?";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute([$userId]);
            $exists = $checkStmt->fetch();

            if ($exists) {
                $sql = "UPDATE user_preferences SET
                        marketing_consent = ?,
                        personalized_ads_consent = ?,
                        data_sharing_consent = ?,
                        profiling_consent = ?,
                        updated_at = NOW()
                        WHERE user_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    $marketingConsent,
                    $personalizedAdsConsent,
                    $dataSharingConsent,
                    $profilingConsent,
                    $userId
                ]);
            } else {
                $sql = "INSERT INTO user_preferences (user_id, marketing_consent,
                        personalized_ads_consent, data_sharing_consent, profiling_consent)
                        VALUES (?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    $userId,
                    $marketingConsent,
                    $personalizedAdsConsent,
                    $dataSharingConsent,
                    $profilingConsent
                ]);
            }

            // Log KVKK activity
            $logSql = "INSERT INTO kvkk_data_processing_log (user_id, activity_type, description,
                       ip_address, user_agent, data_categories, processing_purpose)
                       VALUES (?, 'consent_change', ?, ?, ?, ?, ?)";
            $logStmt = $this->db->prepare($logSql);
            $logStmt->execute([
                $userId,
                'KVKK izinleri güncellendi',
                getClientIP(),
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                json_encode(['preferences', 'consent']),
                'user request'
            ]);

            setFlashMessage('KVKK izinleriniz başarıyla güncellendi.', 'success');
        } catch (Exception $e) {
            error_log("KVKK consent update error: " . $e->getMessage());
            setFlashMessage('İzinler güncellenirken bir hata oluştu.', 'error');
        }

        redirect('/account/security#kvkk');
    }

    /**
     * Request account deletion (KVKK right to deletion)
     */
    public function requestAccountDeletion() {
        if (!isLoggedIn()) {
            redirect('/login');
            return;
        }

        $userId = getCurrentUserId();

        // CSRF validation
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('Güvenlik hatası. Lütfen tekrar deneyin.', 'error');
            redirect('/account/security');
            return;
        }

        $reason = sanitize($_POST['deletion_reason'] ?? '');

        try {
            // Check if there's already a pending request
            $checkSql = "SELECT id FROM account_deletion_requests
                         WHERE user_id = ? AND status = 'pending'";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute([$userId]);

            if ($checkStmt->fetch()) {
                setFlashMessage('Zaten bekleyen bir hesap silme talebiniz var.', 'info');
                redirect('/account/security');
                return;
            }

            // KVKK mandates 30-day retention period
            $deletionDate = date('Y-m-d H:i:s', strtotime('+30 days'));

            $sql = "INSERT INTO account_deletion_requests (user_id, reason, deletion_scheduled_at)
                    VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $reason, $deletionDate]);

            // Update user preferences (INSERT if not exists)
            $prefSql = "INSERT INTO user_preferences (user_id, right_to_deletion_requested_at)
                        VALUES (?, NOW())
                        ON DUPLICATE KEY UPDATE
                        right_to_deletion_requested_at = NOW()";
            $prefStmt = $this->db->prepare($prefSql);
            $prefStmt->execute([$userId]);

            // Log KVKK activity
            $logSql = "INSERT INTO kvkk_data_processing_log (user_id, activity_type, description,
                       ip_address, user_agent, data_categories, processing_purpose)
                       VALUES (?, 'deletion_request', ?, ?, ?, ?, ?)";
            $logStmt = $this->db->prepare($logSql);
            $logStmt->execute([
                $userId,
                'Hesap silme talebi oluşturuldu. Silme tarihi: ' . $deletionDate,
                getClientIP(),
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                json_encode(['all_data']),
                'KVKK right to deletion'
            ]);

            setFlashMessage('Hesap silme talebiniz alınmıştır. KVKK gereği hesabınız 30 gün sonra silinecektir. Bu süre içinde iptal edebilirsiniz.', 'success');

            // Send email notification (would be implemented)
            // TODO: Send deletion confirmation email

        } catch (Exception $e) {
            error_log("Account deletion request error: " . $e->getMessage());
            setFlashMessage('Hesap silme talebi oluşturulurken bir hata oluştu.', 'error');
        }

        redirect('/account/security');
    }

    /**
     * Download user data (KVKK right to portability)
     */
    public function downloadMyData() {
        if (!isLoggedIn()) {
            redirect('/login');
            return;
        }

        $userId = getCurrentUserId();

        try {
            // Collect all user data
            $userInfo = $this->userModel->find($userId);

            // SECURITY: Remove sensitive fields before export
            unset($userInfo['password']);
            unset($userInfo['reset_token']);
            unset($userInfo['reset_token_expire']);
            unset($userInfo['verification_token']);

            $userData = [
                'export_date' => date('Y-m-d H:i:s'),
                'export_info' => 'KVKK - Kişisel Verilerin Korunması Kanunu uyarınca veri taşınabilirliği hakkınız',
                'user_info' => $userInfo,
                'addresses' => [],
                'orders' => [],
                'reviews' => [],
                'preferences' => []
            ];

            // Get addresses
            $addrSql = "SELECT * FROM addresses WHERE user_id = ?";
            $addrStmt = $this->db->prepare($addrSql);
            $addrStmt->execute([$userId]);
            $userData['addresses'] = $addrStmt->fetchAll(PDO::FETCH_ASSOC);

            // Get orders (last 100)
            $orderSql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 100";
            $orderStmt = $this->db->prepare($orderSql);
            $orderStmt->execute([$userId]);
            $userData['orders'] = $orderStmt->fetchAll(PDO::FETCH_ASSOC);

            // Get reviews
            $reviewSql = "SELECT * FROM reviews WHERE user_id = ?";
            $reviewStmt = $this->db->prepare($reviewSql);
            $reviewStmt->execute([$userId]);
            $userData['reviews'] = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);

            // Get preferences
            $prefSql = "SELECT * FROM user_preferences WHERE user_id = ?";
            $prefStmt = $this->db->prepare($prefSql);
            $prefStmt->execute([$userId]);
            $userData['preferences'] = $prefStmt->fetch(PDO::FETCH_ASSOC);

            // Log KVKK activity
            $logSql = "INSERT INTO kvkk_data_processing_log (user_id, activity_type, description,
                       ip_address, user_agent, data_categories, processing_purpose)
                       VALUES (?, 'data_export', ?, ?, ?, ?, ?)";
            $logStmt = $this->db->prepare($logSql);
            $logStmt->execute([
                $userId,
                'Kullanıcı verilerini indirdi',
                getClientIP(),
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                json_encode(['profile', 'orders', 'addresses', 'reviews', 'preferences']),
                'KVKK right to portability'
            ]);

            // Update preference (INSERT if not exists)
            $updateSql = "INSERT INTO user_preferences (user_id, right_to_portability_exercised_at)
                          VALUES (?, NOW())
                          ON DUPLICATE KEY UPDATE
                          right_to_portability_exercised_at = NOW()";
            $updateStmt = $this->db->prepare($updateSql);
            $updateStmt->execute([$userId]);

            // Return JSON file
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="minalia_kullanici_verileri_' . $userId . '_' . date('Ymd') . '.json"');
            echo json_encode($userData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;

        } catch (Exception $e) {
            error_log("Data export error: " . $e->getMessage());
            setFlashMessage('Veri indirme sırasında bir hata oluştu.', 'error');
            redirect('/account/security');
        }
    }
}
