<?php
/**
 * Admin Order Controller
 * MINALIA Parfüm E-Ticaret Platformu
 */

class AdminOrderController {
    private $db;

    public function __construct() {
        requireAdminAuth();
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Order listing
     */
    public function index() {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $status = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';

        $where = [];
        $params = [];

        if ($status) {
            $where[] = "o.status = ?";
            $params[] = $status;
        }

        if ($search) {
            $where[] = "(o.order_number LIKE ? OR u.name LIKE ? OR u.email LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM orders o
                     LEFT JOIN users u ON o.user_id = u.id
                     $whereClause";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Get orders
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT o.*, u.name as customer_name, u.email as customer_email
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                $whereClause
                ORDER BY o.created_at DESC
                LIMIT $perPage OFFSET $offset";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'title' => 'Siparişler',
            'active_menu' => 'orders',
            'orders' => $orders,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total / $perPage),
                'total' => $total
            ],
            'status' => $status,
            'search' => $search
        ];

        $this->render('pages/orders/index', $data);
    }

    /**
     * View order details
     */
    public function view() {
        $id = (int)($_GET['id'] ?? 0);

        // Get order
        $sql = "SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            setFlashMessage('Sipariş bulunamadı.', 'error');
            redirect(ADMIN_URL . '/orders');
            return;
        }

        // Get order items
        $itemsSql = "SELECT oi.*, p.name as product_name, p.main_image, b.name as brand_name
                     FROM order_items oi
                     INNER JOIN products p ON oi.product_id = p.id
                     INNER JOIN brands b ON p.brand_id = b.id
                     WHERE oi.order_id = ?";
        $itemsStmt = $this->db->prepare($itemsSql);
        $itemsStmt->execute([$id]);
        $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'title' => 'Sipariş Detayı #' . $order['order_number'],
            'active_menu' => 'orders',
            'order' => $order,
            'items' => $items
        ];

        $this->render('pages/orders/view', $data);
    }

    /**
     * Update order status
     */
    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';

        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        if (!in_array($status, $validStatuses)) {
            echo json_encode(['success' => false, 'message' => 'Geçersiz durum']);
            return;
        }

        try {
            $sql = "UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$status, $id]);

            $this->logActivity('order_status_updated', "Order #$id status changed to: $status");

            // Send email notification (optional)
            // EmailService::sendOrderStatusEmail($id, $status);

            echo json_encode(['success' => true, 'message' => 'Sipariş durumu güncellendi']);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
        }
    }

    /**
     * Log activity
     */
    private function logActivity($action, $description) {
        try {
            $sql = "INSERT INTO activity_logs (admin_id, action, description, ip_address, created_at)
                    VALUES (?, ?, ?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $_SESSION[SESSION_ADMIN_ID],
                $action,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
        } catch (Exception $e) {
            error_log("Failed to log activity: " . $e->getMessage());
        }
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
