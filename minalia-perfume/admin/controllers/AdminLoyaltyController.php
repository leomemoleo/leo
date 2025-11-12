<?php
/**
 * Admin Loyalty Controller
 * MINALIA Parfüm E-Ticaret Platformu
 */

class AdminLoyaltyController extends AdminController {

    /**
     * Loyalty program overview
     */
    public function index() {
        // Get loyalty statistics
        $sql = "SELECT
                    COUNT(DISTINCT user_id) as total_members,
                    SUM(points_balance) as total_points
                FROM loyalty_points
                WHERE points_balance > 0";
        $stmt = $this->db->query($sql);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        // Get recent transactions
        $sql = "SELECT lp.*, u.first_name, u.last_name, u.email
                FROM loyalty_points lp
                LEFT JOIN users u ON lp.user_id = u.id
                ORDER BY lp.updated_at DESC
                LIMIT 50";
        $stmt = $this->db->query($sql);
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'title' => 'Sadakat Programı',
            'active_menu' => 'loyalty',
            'stats' => $stats,
            'members' => $members
        ];

        $this->render('pages/loyalty/index', $data);
    }

    /**
     * Adjust customer points
     */
    public function adjustPoints() {
        try {
            $userId = $_POST['user_id'] ?? 0;
            $points = (int)($_POST['points'] ?? 0);
            $reason = sanitize($_POST['reason'] ?? '');

            // Get current points
            $stmt = $this->db->prepare("SELECT points_balance FROM loyalty_points WHERE user_id = ?");
            $stmt->execute([$userId]);
            $current = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($current) {
                // Update existing
                $newBalance = $current['points_balance'] + $points;
                $stmt = $this->db->prepare("
                    UPDATE loyalty_points
                    SET points_balance = ?, updated_at = NOW()
                    WHERE user_id = ?
                ");
                $stmt->execute([$newBalance, $userId]);
            } else {
                // Create new
                $stmt = $this->db->prepare("
                    INSERT INTO loyalty_points (user_id, points_balance, total_earned, created_at, updated_at)
                    VALUES (?, ?, ?, NOW(), NOW())
                ");
                $stmt->execute([$userId, max(0, $points), max(0, $points)]);
            }

            // Log transaction
            $stmt = $this->db->prepare("
                INSERT INTO loyalty_transactions (user_id, points, type, description, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $type = $points > 0 ? 'earned' : 'redeemed';
            $stmt->execute([$userId, abs($points), $type, $reason]);

            $this->logActivity('loyalty_adjusted', "User ID: $userId, Points: $points");

            setFlashMessage('Puan güncellendi.', 'success');
        } catch (Exception $e) {
            setFlashMessage('Hata: ' . $e->getMessage(), 'error');
        }

        redirect(ADMIN_URL . '/loyalty');
    }
}
