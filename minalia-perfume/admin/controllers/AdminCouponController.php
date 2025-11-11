<?php
/**
 * Admin Coupon Controller
 * MINALIA Parfüm E-Ticaret Platformu
 */

require_once __DIR__ . '/../../app/models/Coupon.php';

class AdminCouponController {
    private $db;
    private $couponModel;

    public function __construct() {
        requireAdminAuth();
        $this->db = Database::getInstance()->getConnection();
        $this->couponModel = new Coupon();
    }

    /**
     * Coupon listing
     */
    public function index() {
        $sql = "SELECT * FROM coupons ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        $coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'title' => 'Kuponlar',
            'active_menu' => 'coupons',
            'coupons' => $coupons
        ];

        $this->render('pages/coupons/index', $data);
    }

    /**
     * Create coupon
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processCreate();
            return;
        }

        $data = [
            'title' => 'Yeni Kupon Ekle',
            'active_menu' => 'coupons'
        ];

        $this->render('pages/coupons/create', $data);
    }

    /**
     * Process create
     */
    private function processCreate() {
        try {
            $code = strtoupper(sanitize($_POST['code'] ?? ''));
            $type = $_POST['type'] ?? 'percentage';
            $value = (float)($_POST['value'] ?? 0);
            $min_purchase = !empty($_POST['min_purchase']) ? (float)$_POST['min_purchase'] : null;
            $max_uses = !empty($_POST['max_uses']) ? (int)$_POST['max_uses'] : null;
            $start_date = $_POST['start_date'] ?? null;
            $end_date = $_POST['end_date'] ?? null;
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            $sql = "INSERT INTO coupons (code, type, value, min_purchase, max_uses, start_date, end_date, is_active, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$code, $type, $value, $min_purchase, $max_uses, $start_date, $end_date, $is_active]);

            setFlashMessage('Kupon başarıyla oluşturuldu!', 'success');
            redirect(ADMIN_URL . '/coupons');

        } catch (Exception $e) {
            setFlashMessage('Hata: ' . $e->getMessage(), 'error');
            redirect(ADMIN_URL . '/coupons/create');
        }
    }

    /**
     * Edit coupon
     */
    public function edit() {
        $id = (int)($_GET['id'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processEdit($id);
            return;
        }

        $coupon = $this->couponModel->find($id);

        if (!$coupon) {
            setFlashMessage('Kupon bulunamadı.', 'error');
            redirect(ADMIN_URL . '/coupons');
            return;
        }

        $data = [
            'title' => 'Kupon Düzenle',
            'active_menu' => 'coupons',
            'coupon' => $coupon
        ];

        $this->render('pages/coupons/edit', $data);
    }

    /**
     * Process edit
     */
    private function processEdit($id) {
        try {
            $code = strtoupper(sanitize($_POST['code'] ?? ''));
            $type = $_POST['type'] ?? 'percentage';
            $value = (float)($_POST['value'] ?? 0);
            $min_purchase = !empty($_POST['min_purchase']) ? (float)$_POST['min_purchase'] : null;
            $max_uses = !empty($_POST['max_uses']) ? (int)$_POST['max_uses'] : null;
            $start_date = $_POST['start_date'] ?? null;
            $end_date = $_POST['end_date'] ?? null;
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            $sql = "UPDATE coupons SET
                        code = ?, type = ?, value = ?, min_purchase = ?,
                        max_uses = ?, start_date = ?, end_date = ?, is_active = ?
                    WHERE id = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$code, $type, $value, $min_purchase, $max_uses, $start_date, $end_date, $is_active, $id]);

            setFlashMessage('Kupon başarıyla güncellendi!', 'success');
            redirect(ADMIN_URL . '/coupons');

        } catch (Exception $e) {
            setFlashMessage('Hata: ' . $e->getMessage(), 'error');
            redirect(ADMIN_URL . '/coupons/edit?id=' . $id);
        }
    }

    /**
     * Delete coupon
     */
    public function delete() {
        $id = (int)($_POST['id'] ?? 0);

        try {
            $this->couponModel->delete($id);
            echo json_encode(['success' => true, 'message' => 'Kupon silindi.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
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
