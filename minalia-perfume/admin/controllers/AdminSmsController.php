<?php
/**
 * Admin SMS Controller
 * Enterprise SMS notification management
 */

require_once __DIR__ . '/../../app/helpers/SmsGateway.php';

class AdminSmsController extends AdminController {

    /**
     * SMS Dashboard - Logs and statistics
     */
    public function index() {
        // Get SMS statistics
        $stats = [
            'today' => $this->db->query("SELECT COUNT(*) as count FROM sms_logs WHERE DATE(created_at) = CURDATE()")->fetch()['count'],
            'this_month' => $this->db->query("SELECT COUNT(*) as count FROM sms_logs WHERE MONTH(created_at) = MONTH(CURDATE())")->fetch()['count'],
            'total' => $this->db->query("SELECT COUNT(*) as count FROM sms_logs")->fetch()['count'],
            'failed' => $this->db->query("SELECT COUNT(*) as count FROM sms_logs WHERE status = 'failed'")->fetch()['count']
        ];

        // Get recent SMS logs
        $stmt = $this->db->query("
            SELECT * FROM sms_logs
            ORDER BY created_at DESC
            LIMIT 50
        ");
        $logs = $stmt->fetchAll();

        // Get SMS balance
        $balance = SmsHelper::getBalance();

        $this->render('pages/sms/index', [
            'title' => 'SMS Yönetimi',
            'stats' => $stats,
            'logs' => $logs,
            'balance' => $balance
        ]);
    }

    /**
     * Send single SMS
     */
    public function send() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $phone = $_POST['phone'] ?? '';
            $message = $_POST['message'] ?? '';

            if (empty($phone) || empty($message)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Telefon ve mesaj gerekli.'
                ]);
                return;
            }

            $result = SmsHelper::send($phone, $message);

            echo json_encode($result);
            return;
        }

        $this->render('pages/sms/send', [
            'title' => 'SMS Gönder'
        ]);
    }

    /**
     * Send bulk SMS
     */
    public function sendBulk() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $phones = $_POST['phones'] ?? '';
            $message = $_POST['message'] ?? '';

            // Parse phone numbers (comma, newline, or space separated)
            $phoneList = preg_split('/[\s,\n]+/', $phones, -1, PREG_SPLIT_NO_EMPTY);

            if (empty($phoneList) || empty($message)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Telefon numaraları ve mesaj gerekli.'
                ]);
                return;
            }

            $result = SmsHelper::sendBulk($phoneList, $message);

            echo json_encode($result);
            return;
        }

        // Get customer list for bulk SMS
        $stmt = $this->db->query("
            SELECT id, first_name, last_name, phone, email
            FROM users
            WHERE phone IS NOT NULL AND phone != ''
            ORDER BY first_name
        ");
        $customers = $stmt->fetchAll();

        $this->render('pages/sms/bulk', [
            'title' => 'Toplu SMS Gönder',
            'customers' => $customers
        ]);
    }

    /**
     * SMS Templates management
     */
    public function templates() {
        $stmt = $this->db->query("SELECT * FROM sms_templates ORDER BY name");
        $templates = $stmt->fetchAll();

        $this->render('pages/sms/templates', [
            'title' => 'SMS Şablonları',
            'templates' => $templates
        ]);
    }

    /**
     * Edit SMS template
     */
    public function editTemplate() {
        $id = (int)($_GET['id'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $message = $_POST['message'] ?? '';
            $isActive = isset($_POST['is_active']) ? 1 : 0;

            try {
                $stmt = $this->db->prepare("
                    UPDATE sms_templates
                    SET name = ?, message = ?, is_active = ?
                    WHERE id = ?
                ");

                $stmt->execute([$name, $message, $isActive, $id]);

                echo json_encode([
                    'success' => true,
                    'message' => 'Şablon güncellendi.'
                ]);

            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            return;
        }

        // Get template
        $stmt = $this->db->prepare("SELECT * FROM sms_templates WHERE id = ?");
        $stmt->execute([$id]);
        $template = $stmt->fetch();

        if (!$template) {
            header('Location: /admin/sms/templates');
            exit;
        }

        $this->render('pages/sms/edit-template', [
            'title' => 'Şablon Düzenle',
            'template' => $template
        ]);
    }

    /**
     * SMS Settings
     */
    public function settings() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $provider = $_POST['provider'] ?? 'netgsm';
            $settings = [
                'SMS_PROVIDER' => $provider,
                'SMS_ENABLED' => isset($_POST['sms_enabled']) ? '1' : '0',
                'SMS_ORDER_NOTIFICATIONS' => isset($_POST['order_notifications']) ? '1' : '0',
                'SMS_SHIPPING_NOTIFICATIONS' => isset($_POST['shipping_notifications']) ? '1' : '0',
            ];

            // Provider specific settings
            if ($provider === 'netgsm') {
                $settings['NETGSM_USERNAME'] = $_POST['netgsm_username'] ?? '';
                $settings['NETGSM_PASSWORD'] = $_POST['netgsm_password'] ?? '';
                $settings['NETGSM_HEADER'] = $_POST['netgsm_header'] ?? 'MINALIA';
            } elseif ($provider === 'iletimerkezi') {
                $settings['ILETIMERKEZI_API_KEY'] = $_POST['iletimerkezi_key'] ?? '';
                $settings['ILETIMERKEZI_API_HASH'] = $_POST['iletimerkezi_hash'] ?? '';
                $settings['ILETIMERKEZI_SENDER'] = $_POST['iletimerkezi_sender'] ?? 'MINALIA';
            }

            try {
                // Save to database
                foreach ($settings as $key => $value) {
                    $stmt = $this->db->prepare("
                        INSERT INTO settings (`key`, `value`)
                        VALUES (?, ?)
                        ON DUPLICATE KEY UPDATE `value` = ?
                    ");
                    $stmt->execute([$key, $value, $value]);
                }

                echo json_encode([
                    'success' => true,
                    'message' => 'SMS ayarları kaydedildi.'
                ]);

            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            return;
        }

        // Get current settings
        $stmt = $this->db->query("SELECT `key`, `value` FROM settings WHERE `key` LIKE 'SMS_%' OR `key` LIKE 'NETGSM_%' OR `key` LIKE 'ILETIMERKEZI_%'");
        $settingsData = $stmt->fetchAll();

        $settings = [];
        foreach ($settingsData as $row) {
            $settings[$row['key']] = $row['value'];
        }

        $this->render('pages/sms/settings', [
            'title' => 'SMS Ayarları',
            'settings' => $settings
        ]);
    }

    /**
     * Get SMS logs (AJAX)
     */
    public function getLogs() {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 50;
        $offset = ($page - 1) * $perPage;

        $filter = $_GET['filter'] ?? 'all';

        $where = '';
        if ($filter === 'failed') {
            $where = "WHERE status = 'failed'";
        } elseif ($filter === 'today') {
            $where = "WHERE DATE(created_at) = CURDATE()";
        }

        $stmt = $this->db->query("
            SELECT * FROM sms_logs
            {$where}
            ORDER BY created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ");

        $logs = $stmt->fetchAll();

        echo json_encode([
            'success' => true,
            'logs' => $logs
        ]);
    }

    /**
     * Test SMS connection
     */
    public function testConnection() {
        try {
            $balance = SmsHelper::getBalance();

            echo json_encode([
                'success' => $balance['success'],
                'message' => $balance['success'] ? 'Bağlantı başarılı!' : 'Bağlantı hatası',
                'data' => $balance
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
