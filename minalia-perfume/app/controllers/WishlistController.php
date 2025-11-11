<?php
/**
 * Wishlist Controller - Database Integration
 * MINALIA Parfüm E-Ticaret Platformu
 */

class WishlistController extends BaseController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Add to wishlist (AJAX)
     */
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $product_id = (int)($_POST['product_id'] ?? 0);

        try {
            $userId = getCurrentUserId();

            if (!$userId) {
                echo json_encode(['success' => false, 'message' => 'Lütfen giriş yapın']);
                return;
            }

            // Check if already exists
            $checkSql = "SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute([$userId, $product_id]);
            $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                echo json_encode(['success' => false, 'message' => 'Ürün zaten favorilerde']);
                return;
            }

            $sql = "INSERT INTO wishlist (user_id, product_id, created_at) VALUES (?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $product_id]);

            echo json_encode(['success' => true, 'message' => 'Favorilere eklendi!']);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove from wishlist (AJAX)
     */
    public function remove() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $product_id = (int)($_POST['product_id'] ?? 0);

        try {
            $userId = getCurrentUserId();

            if ($userId) {
                $sql = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$userId, $product_id]);
            }

            echo json_encode(['success' => true, 'message' => 'Favorilerden çıkarıldı']);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
        }
    }

    /**
     * Get wishlist items (AJAX)
     */
    public function items() {
        try {
            $userId = getCurrentUserId();

            if (!$userId) {
                echo json_encode(['success' => false, 'items' => []]);
                return;
            }

            $sql = "SELECT w.*, p.name, p.price, p.discount_price, p.main_image, p.stock_quantity
                    FROM wishlist w
                    INNER JOIN products p ON w.product_id = p.id
                    WHERE w.user_id = ?
                    ORDER BY w.created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'items' => $items]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
        }
    }

    /**
     * Wishlist page
     */
    public function index() {
        $this->requireAuth();

        $userId = getCurrentUserId();

        $sql = "SELECT w.*, p.name, p.slug, p.price, p.discount_price, p.main_image, p.stock_quantity, b.name as brand_name
                FROM wishlist w
                INNER JOIN products p ON w.product_id = p.id
                INNER JOIN brands b ON p.brand_id = b.id
                WHERE w.user_id = ?
                ORDER BY w.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('account/wishlist', [
            'title' => 'Favorilerim',
            'active_menu' => 'wishlist',
            'items' => $items
        ]);
    }
}
