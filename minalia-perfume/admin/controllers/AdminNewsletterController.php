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
        $sql = "SELECT COUNT(*) as total FROM newsletter_subscribers WHERE is_active = 1";
        $stmt = $this->db->query($sql);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

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
            'subscriber_count' => $stats['total']
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

            if (empty($subject) || empty($message)) {
                throw new Exception('Konu ve mesaj alanları zorunludur.');
            }

            // Get active subscribers
            $sql = "SELECT email FROM newsletter_subscribers WHERE is_active = 1";
            $stmt = $this->db->query($sql);
            $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $emailService = new EmailService();
            $sent = 0;

            foreach ($subscribers as $subscriber) {
                $result = $emailService->sendNewsletter($subscriber['email'], $subject, $message);
                if ($result) {
                    $sent++;
                }
            }

            $this->logActivity('newsletter_sent', "Sent newsletter to $sent subscribers: $subject");

            setFlashMessage("Newsletter başarıyla gönderildi! ($sent kişi)", 'success');
            redirect(ADMIN_URL . '/newsletter');

        } catch (Exception $e) {
            setFlashMessage('Hata: ' . $e->getMessage(), 'error');
            redirect(ADMIN_URL . '/newsletter/send');
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
