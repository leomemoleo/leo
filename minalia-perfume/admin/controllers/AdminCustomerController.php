<?php
/**
 * Admin Customer Controller
 * MINALIA Parfüm E-Ticaret Platformu
 */

require_once __DIR__ . '/../../app/models/User.php';

class AdminCustomerController {
    private $db;
    private $userModel;

    public function __construct() {
        requireAdminAuth();
        $this->db = Database::getInstance()->getConnection();
        $this->userModel = new User();
    }

    /**
     * Customer listing
     */
    public function index() {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $search = $_GET['search'] ?? '';

        $where = "role = 'customer'";
        $params = [];

        if ($search) {
            $where .= " AND (name LIKE ? OR email LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM users WHERE $where";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Get customers
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT u.*,
                (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count,
                (SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE user_id = u.id AND status = 'completed') as total_spent
                FROM users u
                WHERE $where
                ORDER BY u.created_at DESC
                LIMIT $perPage OFFSET $offset";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'title' => 'Müşteriler',
            'active_menu' => 'customers',
            'customers' => $customers,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total / $perPage),
                'total' => $total
            ],
            'search' => $search
        ];

        $this->render('pages/customers/index', $data);
    }

    /**
     * View customer details
     */
    public function view() {
        $id = (int)($_GET['id'] ?? 0);

        $customer = $this->userModel->find($id);

        if (!$customer || $customer['role'] !== 'customer') {
            setFlashMessage('Müşteri bulunamadı.', 'error');
            redirect(ADMIN_URL . '/customers');
            return;
        }

        // Get orders
        $ordersSql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 10";
        $ordersStmt = $this->db->prepare($ordersSql);
        $ordersStmt->execute([$id]);
        $orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);

        // Get loyalty points
        $pointsSql = "SELECT * FROM loyalty_points WHERE user_id = ? ORDER BY created_at DESC LIMIT 10";
        $pointsStmt = $this->db->prepare($pointsSql);
        $pointsStmt->execute([$id]);
        $points = $pointsStmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'title' => 'Müşteri Detayı',
            'active_menu' => 'customers',
            'customer' => $customer,
            'orders' => $orders,
            'points' => $points
        ];

        $this->render('pages/customers/view', $data);
    }

    /**
     * Render view
     */
    private function render($view, $data = []) {
        extract($data);

        ob_start();
        require ADMIN_VIEWS_PATH . '/' . $view . '.php';
        $content = ob_get_clean();

        require ADMIN_VIEWS_PATH . '/layouts/main.php';
    }
}
