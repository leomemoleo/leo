<?php
/**
 * Admin Newsletter Controller
 * MINALIA Parfüm E-Ticaret Platformu
 */

require_once __DIR__ . '/../../app/helpers/EmailService.php';

class AdminNewsletterController {
    private $db;

    public function __construct() {
        requireAdminAuth();
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Newsletter dashboard
     */
    public function index() {
        // Get statistics
        $totalSql = "SELECT COUNT(*) as total FROM newsletter_subscribers";
        $totalStmt = $this->db->query($totalSql);
        $totalResult = $totalStmt->fetch(PDO::FETCH_ASSOC);

        $activeSql = "SELECT COUNT(*) as total FROM newsletter_subscribers WHERE is_active = 1";
        $activeStmt = $this->db->query($activeSql);
        $activeResult = $activeStmt->fetch(PDO::FETCH_ASSOC);

        $thisMonthSql = "SELECT COUNT(*) as total FROM newsletter_subscribers
                         WHERE MONTH(subscribed_at) = MONTH(CURRENT_DATE())
                         AND YEAR(subscribed_at) = YEAR(CURRENT_DATE())";
        $thisMonthStmt = $this->db->query($thisMonthSql);
        $thisMonthResult = $thisMonthStmt->fetch(PDO::FETCH_ASSOC);

        $stats = [
            'total_subscribers' => $totalResult['total'],
            'active_subscribers' => $activeResult['total'],
            'this_month_subscribers' => $thisMonthResult['total']
        ];

        $subscribersSql = "SELECT * FROM newsletter_subscribers ORDER BY subscribed_at DESC LIMIT 50";
        $subscribersStmt = $this->db->query($subscribersSql);
        $subscribers = $subscribersStmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'title' => 'Newsletter',
            'active_menu' => 'newsletter',
            'stats' => $stats,
            'subscribers' => $subscribers
        ];

        $this->render('pages/newsletter/index', $data);
    }

    /**
     * Send newsletter
     */
    public function send() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processSend();
            return;
        }

        // Get subscriber count
        $sql = "SELECT COUNT(*) as total FROM newsletter_subscribers WHERE is_active = 1";
        $stmt = $this->db->query($sql);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        $data = [
            'title' => 'Newsletter Gönder',
            'active_menu' => 'newsletter',
            'active_subscribers' => $stats['total']
        ];

        $this->render('pages/newsletter/send', $data);
    }

    /**
     * Process send
     */
    private function processSend() {
        try {
            $subject = sanitize($_POST['subject'] ?? '');
            $message = $_POST['message'] ?? '';
            $isTest = isset($_POST['send_test']);

            if (empty($subject) || empty($message)) {
                throw new Exception('Konu ve mesaj alanları zorunludur.');
            }

            $emailService = new EmailService();
            $sent = 0;

            if ($isTest) {
                // Send test email to admin only
                $adminEmail = $_SESSION[SESSION_ADMIN_EMAIL] ?? 'admin@minalia.com.tr';
                $result = $emailService->send($adminEmail, '[TEST] ' . $subject, $message);

                if ($result) {
                    setFlashMessage("Test emaili gönderildi: $adminEmail", 'success');
                } else {
                    setFlashMessage('Test emaili gönderilemedi.', 'error');
                }
                redirect(ADMIN_URL . '/newsletter/send');
                return;
            }

            // Get active subscribers
            $sql = "SELECT email FROM newsletter_subscribers WHERE is_active = 1";
            $stmt = $this->db->query($sql);
            $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($subscribers as $subscriber) {
                $result = $emailService->send($subscriber['email'], $subject, $message);
                if ($result) {
                    $sent++;
                }

                // Small delay to avoid spam filters
                usleep(100000); // 0.1 second
            }

            $this->logActivity('newsletter_sent', "Sent newsletter to $sent subscribers: $subject");

            setFlashMessage("Newsletter başarıyla gönderildi! ($sent / " . count($subscribers) . " kişi)", 'success');
            redirect(ADMIN_URL . '/newsletter');

        } catch (Exception $e) {
            setFlashMessage('Hata: ' . $e->getMessage(), 'error');
            redirect(ADMIN_URL . '/newsletter/send');
        }
    }

    /**
     * Toggle subscriber status
     */
    public function toggleStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $id = (int)($_POST['id'] ?? 0);

        try {
            // Get current status
            $sql = "SELECT is_active FROM newsletter_subscribers WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $subscriber = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$subscriber) {
                throw new Exception('Abone bulunamadı');
            }

            $newStatus = $subscriber['is_active'] ? 0 : 1;

            // Update status
            $updateSql = "UPDATE newsletter_subscribers SET is_active = ? WHERE id = ?";
            $updateStmt = $this->db->prepare($updateSql);
            $updateStmt->execute([$newStatus, $id]);

            echo json_encode(['success' => true, 'message' => 'Durum güncellendi']);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Delete subscriber
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $id = (int)($_POST['id'] ?? 0);

        try {
            $sql = "DELETE FROM newsletter_subscribers WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);

            $this->logActivity('subscriber_deleted', "Deleted subscriber ID: $id");

            echo json_encode(['success' => true, 'message' => 'Abone silindi']);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Export subscribers to CSV
     */
    public function export() {
        try {
            $sql = "SELECT email, name, is_active, subscribed_at FROM newsletter_subscribers ORDER BY subscribed_at DESC";
            $stmt = $this->db->query($sql);
            $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Set headers for CSV download
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=newsletter_subscribers_' . date('Y-m-d') . '.csv');

            // Create output stream
            $output = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            // Add CSV headers
            fputcsv($output, ['Email', 'İsim', 'Durum', 'Kayıt Tarihi']);

            // Add data
            foreach ($subscribers as $subscriber) {
                fputcsv($output, [
                    $subscriber['email'],
                    $subscriber['name'] ?? '',
                    $subscriber['is_active'] ? 'Aktif' : 'Pasif',
                    date('d.m.Y H:i', strtotime($subscriber['subscribed_at']))
                ]);
            }

            fclose($output);
            exit;

        } catch (Exception $e) {
            setFlashMessage('Hata: ' . $e->getMessage(), 'error');
            redirect(ADMIN_URL . '/newsletter');
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
