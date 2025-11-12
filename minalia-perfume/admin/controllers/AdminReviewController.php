<?php
/**
 * Admin Review Controller
 * MINALIA Parfüm E-Ticaret Platformu
 */

class AdminReviewController extends AdminController {

    /**
     * Reviews list
     */
    public function index() {
        $sql = "SELECT r.*, p.name as product_name, u.first_name, u.last_name
                FROM reviews r
                LEFT JOIN products p ON r.product_id = p.id
                LEFT JOIN users u ON r.user_id = u.id
                ORDER BY r.created_at DESC
                LIMIT 50";

        $stmt = $this->db->query($sql);
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'title' => 'Yorumlar',
            'active_menu' => 'reviews',
            'reviews' => $reviews
        ];

        $this->render('pages/reviews/index', $data);
    }

    /**
     * Approve review
     */
    public function approve() {
        try {
            $id = $_POST['id'] ?? 0;

            $stmt = $this->db->prepare("UPDATE reviews SET is_approved = 1 WHERE id = ?");
            $stmt->execute([$id]);

            $this->logActivity('review_approved', "Review ID: $id");

            setFlashMessage('Yorum onaylandı.', 'success');
        } catch (Exception $e) {
            setFlashMessage('Hata: ' . $e->getMessage(), 'error');
        }

        redirect(ADMIN_URL . '/reviews');
    }

    /**
     * Delete review
     */
    public function delete() {
        try {
            $id = $_POST['id'] ?? 0;

            $stmt = $this->db->prepare("DELETE FROM reviews WHERE id = ?");
            $stmt->execute([$id]);

            $this->logActivity('review_deleted', "Review ID: $id");

            setFlashMessage('Yorum silindi.', 'success');
        } catch (Exception $e) {
            setFlashMessage('Hata: ' . $e->getMessage(), 'error');
        }

        redirect(ADMIN_URL . '/reviews');
    }
}
