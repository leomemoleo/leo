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
        } elseif (!preg_match('/^[0-9\s\-\+\(\)]{10,20}$/', $phone)) {
            $errors[] = 'Geçerli bir telefon numarası giriniz.';
        }

        if (empty($addressLine1)) {
            $errors[] = 'Adres satırı zorunludur.';
        }

        if (empty($district)) {
            $errors[] = 'İlçe zorunludur.';
        }

        if (empty($city)) {
            $errors[] = 'İl zorunludur.';
        }

        if (empty($postalCode)) {
            $errors[] = 'Posta kodu zorunludur.';
        } elseif (!preg_match('/^[0-9]{5}$/', $postalCode)) {
            $errors[] = 'Posta kodu 5 haneli olmalıdır.';
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
        }

        if (empty($addressLine1)) {
            $errors[] = 'Adres satırı zorunludur.';
        }

        if (empty($district)) {
            $errors[] = 'İlçe zorunludur.';
        }

        if (empty($city)) {
            $errors[] = 'İl zorunludur.';
        }

        if (empty($postalCode)) {
            $errors[] = 'Posta kodu zorunludur.';
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
}
