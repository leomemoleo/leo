<?php
/**
 * Admin Report Controller
 * MINALIA Parfüm E-Ticaret Platformu
 */

class AdminReportController extends AdminController {

    /**
     * Reports dashboard
     */
    public function index() {
        // Sales report
        $salesSql = "SELECT
                        DATE(created_at) as date,
                        COUNT(*) as orders,
                        SUM(total_amount) as revenue
                    FROM orders
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    GROUP BY DATE(created_at)
                    ORDER BY date DESC";
        $stmt = $this->db->query($salesSql);
        $salesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Top products
        $productsSql = "SELECT
                            p.name,
                            COUNT(oi.id) as total_sold,
                            SUM(oi.quantity) as total_quantity,
                            SUM(oi.price * oi.quantity) as total_revenue
                        FROM order_items oi
                        JOIN products p ON oi.product_id = p.id
                        JOIN orders o ON oi.order_id = o.id
                        WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                        GROUP BY p.id
                        ORDER BY total_sold DESC
                        LIMIT 10";
        $stmt = $this->db->query($productsSql);
        $topProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Customer stats
        $customerSql = "SELECT
                            COUNT(DISTINCT user_id) as total_customers,
                            AVG(total_amount) as avg_order_value
                        FROM orders
                        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        $stmt = $this->db->query($customerSql);
        $customerStats = $stmt->fetch(PDO::FETCH_ASSOC);

        $data = [
            'title' => 'Raporlar',
            'active_menu' => 'reports',
            'sales_data' => $salesData,
            'top_products' => $topProducts,
            'customer_stats' => $customerStats
        ];

        $this->render('pages/reports/index', $data);
    }

    /**
     * AI-powered reports (placeholder)
     */
    public function aiReports() {
        $data = [
            'title' => 'AI Raporları',
            'active_menu' => 'ai-reports'
        ];

        $this->render('pages/reports/ai', $data);
    }
}
