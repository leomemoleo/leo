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
        $loyaltyStats = $this->loyaltyModel->getUserSummary($userId);

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
            $this->updateProfile($userId);
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
    private function updateProfile($userId) {
        $name = sanitize($_POST['name'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $birth_date = $_POST['birth_date'] ?? null;

        try {
            $sql = "UPDATE users SET name = ?, phone = ?, birth_date = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$name, $phone, $birth_date, $userId]);

            setFlashMessage('Profil bilgileriniz güncellendi.', 'success');
        } catch (Exception $e) {
            setFlashMessage('Hata: ' . $e->getMessage(), 'error');
        }

        redirect('/account/profile');
    }

    /**
     * Loyalty points
     */
    public function loyalty() {
        $userId = getCurrentUserId();

        $summary = $this->loyaltyModel->getUserSummary($userId);
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
}
