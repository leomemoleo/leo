<?php
/**
 * Cart Controller - Database Integration
 * MINALIA Parfüm E-Ticaret Platformu
 */

class CartController extends BaseController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Add to cart (AJAX)
     */
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $product_id = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);

        try {
            $userId = getCurrentUserId();

            if ($userId) {
                // Logged in: Save to database
                $checkSql = "SELECT * FROM cart_items WHERE user_id = ? AND product_id = ?";
                $checkStmt = $this->db->prepare($checkSql);
                $checkStmt->execute([$userId, $product_id]);
                $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

                if ($existing) {
                    // Update quantity
                    $sql = "UPDATE cart_items SET quantity = quantity + ?, updated_at = NOW()
                            WHERE user_id = ? AND product_id = ?";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([$quantity, $userId, $product_id]);
                } else {
                    // Insert new
                    $sql = "INSERT INTO cart_items (user_id, product_id, quantity, created_at)
                            VALUES (?, ?, ?, NOW())";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([$userId, $product_id, $quantity]);
                }
            }

            echo json_encode(['success' => true, 'message' => 'Ürün sepete eklendi!']);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove from cart (AJAX)
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
                $sql = "DELETE FROM cart_items WHERE user_id = ? AND product_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$userId, $product_id]);
            }

            echo json_encode(['success' => true, 'message' => 'Ürün sepetten çıkarıldı']);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
        }
    }

    /**
     * Get cart items (AJAX)
     */
    public function items() {
        try {
            $userId = getCurrentUserId();

            if (!$userId) {
                echo json_encode(['success' => false, 'items' => []]);
                return;
            }

            $sql = "SELECT ci.*, p.name, p.price, p.discount_price, p.main_image, p.stock_quantity
                    FROM cart_items ci
                    INNER JOIN products p ON ci.product_id = p.id
                    WHERE ci.user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'items' => $items]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
        }
    }

    /**
     * Update quantity (AJAX)
     */
    public function updateQuantity() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $product_id = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);

        try {
            $userId = getCurrentUserId();

            if ($userId) {
                $sql = "UPDATE cart_items SET quantity = ?, updated_at = NOW()
                        WHERE user_id = ? AND product_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$quantity, $userId, $product_id]);
            }

            echo json_encode(['success' => true, 'message' => 'Miktar güncellendi']);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
        }
    }
}
