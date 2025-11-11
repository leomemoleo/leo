<?php
/**
 * Admin Brand Controller
 * MINALIA Parfüm E-Ticaret Platformu
 */

class AdminBrandController {
    private $db;

    public function __construct() {
        requireAdminAuth();
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Brand listing
     */
    public function index() {
        $sql = "SELECT b.*,
                (SELECT COUNT(*) FROM products WHERE brand_id = b.id) as product_count
                FROM brands b
                ORDER BY b.sort_order, b.name";
        $stmt = $this->db->query($sql);
        $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'title' => 'Markalar',
            'active_menu' => 'brands',
            'brands' => $brands
        ];

        $this->render('pages/brands/index', $data);
    }

    /**
     * Create brand
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processCreate();
            return;
        }

        $data = [
            'title' => 'Yeni Marka',
            'active_menu' => 'brands'
        ];

        $this->render('pages/brands/create', $data);
    }

    /**
     * Process create
     */
    private function processCreate() {
        try {
            $name = sanitize($_POST['name'] ?? '');
            $slug = $this->generateSlug($_POST['slug'] ?: $name);
            $description = sanitize($_POST['description'] ?? '');
            $country = sanitize($_POST['country'] ?? '');
            $sort_order = (int)($_POST['sort_order'] ?? 0);
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;

            $sql = "INSERT INTO brands (name, slug, description, country, sort_order, is_featured, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$name, $slug, $description, $country, $sort_order, $is_featured]);

            $this->logActivity('brand_created', "Created brand: $name");

            setFlashMessage('Marka başarıyla eklendi!', 'success');
            redirect(ADMIN_URL . '/brands');

        } catch (Exception $e) {
            setFlashMessage('Hata: ' . $e->getMessage(), 'error');
            redirect(ADMIN_URL . '/brands/create');
        }
    }

    /**
     * Edit brand
     */
    public function edit() {
        $id = (int)($_GET['id'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processEdit($id);
            return;
        }

        $sql = "SELECT * FROM brands WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $brand = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$brand) {
            setFlashMessage('Marka bulunamadı.', 'error');
            redirect(ADMIN_URL . '/brands');
            return;
        }

        $data = [
            'title' => 'Marka Düzenle',
            'active_menu' => 'brands',
            'brand' => $brand
        ];

        $this->render('pages/brands/edit', $data);
    }

    /**
     * Process edit
     */
    private function processEdit($id) {
        try {
            $name = sanitize($_POST['name'] ?? '');
            $slug = $this->generateSlug($_POST['slug'] ?: $name);
            $description = sanitize($_POST['description'] ?? '');
            $country = sanitize($_POST['country'] ?? '');
            $sort_order = (int)($_POST['sort_order'] ?? 0);
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;

            $sql = "UPDATE brands SET
                        name = ?, slug = ?, description = ?,
                        country = ?, sort_order = ?, is_featured = ?, updated_at = NOW()
                    WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$name, $slug, $description, $country, $sort_order, $is_featured, $id]);

            $this->logActivity('brand_updated', "Updated brand: $name");

            setFlashMessage('Marka başarıyla güncellendi!', 'success');
            redirect(ADMIN_URL . '/brands');

        } catch (Exception $e) {
            setFlashMessage('Hata: ' . $e->getMessage(), 'error');
            redirect(ADMIN_URL . '/brands/edit?id=' . $id);
        }
    }

    /**
     * Delete brand
     */
    public function delete() {
        $id = (int)($_POST['id'] ?? 0);

        try {
            // Check if brand has products
            $checkSql = "SELECT COUNT(*) as count FROM products WHERE brand_id = ?";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute([$id]);
            $result = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($result['count'] > 0) {
                echo json_encode(['success' => false, 'message' => 'Bu markada ürünler var. Önce ürünleri silin veya başka markaya taşıyın.']);
                return;
            }

            $sql = "DELETE FROM brands WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);

            $this->logActivity('brand_deleted', "Deleted brand ID: $id");

            echo json_encode(['success' => true, 'message' => 'Marka silindi.']);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate slug
     */
    private function generateSlug($text) {
        $turkishChars = ['ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç'];
        $englishChars = ['i', 'g', 'u', 's', 'o', 'c', 'i', 'g', 'u', 's', 'o', 'c'];
        $text = str_replace($turkishChars, $englishChars, $text);
        $slug = strtolower(trim($text));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
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
