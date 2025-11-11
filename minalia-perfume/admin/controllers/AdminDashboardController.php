<?php
/**
 * Admin Dashboard Controller
 * MINALIA Parfüm E-Ticaret Platformu
 */

class AdminDashboardController {
    private $db;

    public function __construct() {
        requireAdminAuth();
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Dashboard index
     */
    public function index() {
        $data = [
            'title' => 'Dashboard',
            'active_menu' => 'dashboard',
            'stats' => $this->getStats(),
            'recent_orders' => $this->getRecentOrders(),
            'low_stock_products' => $this->getLowStockProducts(),
            'recent_reviews' => $this->getRecentReviews()
        ];

        $this->render('pages/dashboard', $data);
    }

    /**
     * Get dashboard statistics
     */
    private function getStats() {
        // Total sales (this month)
        $sql = "SELECT
                    COUNT(*) as total_orders,
                    COALESCE(SUM(total_amount), 0) as total_revenue
                FROM orders
                WHERE MONTH(created_at) = MONTH(CURRENT_DATE())
                AND YEAR(created_at) = YEAR(CURRENT_DATE())
                AND status != 'cancelled'";
        $stmt = $this->db->query($sql);
        $salesData = $stmt->fetch(PDO::FETCH_ASSOC);

        // Total customers
        $customerSql = "SELECT COUNT(*) as total FROM users WHERE role = 'customer'";
        $customerStmt = $this->db->query($customerSql);
        $customerData = $customerStmt->fetch(PDO::FETCH_ASSOC);

        // Total products
        $productSql = "SELECT COUNT(*) as total FROM products";
        $productStmt = $this->db->query($productSql);
        $productData = $productStmt->fetch(PDO::FETCH_ASSOC);

        // Pending orders
        $pendingSql = "SELECT COUNT(*) as total FROM orders WHERE status = 'pending'";
        $pendingStmt = $this->db->query($pendingSql);
        $pendingData = $pendingStmt->fetch(PDO::FETCH_ASSOC);

        // Sales comparison (vs last month)
        $lastMonthSql = "SELECT COALESCE(SUM(total_amount), 0) as total
                         FROM orders
                         WHERE MONTH(created_at) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH)
                         AND YEAR(created_at) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)
                         AND status != 'cancelled'";
        $lastMonthStmt = $this->db->query($lastMonthSql);
        $lastMonthData = $lastMonthStmt->fetch(PDO::FETCH_ASSOC);

        $salesChange = 0;
        if ($lastMonthData['total'] > 0) {
            $salesChange = (($salesData['total_revenue'] - $lastMonthData['total']) / $lastMonthData['total']) * 100;
        }

        return [
            'total_revenue' => $salesData['total_revenue'],
            'total_orders' => $salesData['total_orders'],
            'total_customers' => $customerData['total'],
            'total_products' => $productData['total'],
            'pending_orders' => $pendingData['total'],
            'sales_change' => $salesChange
        ];
    }

    /**
     * Get recent orders
     */
    private function getRecentOrders() {
        $sql = "SELECT o.*, u.name as customer_name, u.email as customer_email
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC
                LIMIT 10";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get low stock products
     */
    private function getLowStockProducts() {
        $sql = "SELECT p.*, b.name as brand_name
                FROM products p
                INNER JOIN brands b ON p.brand_id = b.id
                WHERE p.stock_quantity <= p.low_stock_threshold
                AND p.stock_quantity > 0
                ORDER BY p.stock_quantity ASC
                LIMIT 10";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get recent reviews
     */
    private function getRecentReviews() {
        $sql = "SELECT r.*, p.name as product_name, u.name as customer_name
                FROM reviews r
                INNER JOIN products p ON r.product_id = p.id
                INNER JOIN users u ON r.user_id = u.id
                ORDER BY r.created_at DESC
                LIMIT 5";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
