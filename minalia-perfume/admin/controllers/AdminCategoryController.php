<?php
/**
 * Admin Category Controller
 * MINALIA Parfüm E-Ticaret Platformu
 */

class AdminCategoryController {
    private $db;

    public function __construct() {
        requireAdminAuth();
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Category listing
     */
    public function index() {
        $sql = "SELECT c.*,
                (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count
                FROM categories c
                ORDER BY c.sort_order, c.name";
        $stmt = $this->db->query($sql);
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'title' => 'Kategoriler',
            'active_menu' => 'categories',
            'categories' => $categories
        ];

        $this->render('pages/categories/index', $data);
    }

    /**
     * Create category
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processCreate();
            return;
        }

        $data = [
            'title' => 'Yeni Kategori',
            'active_menu' => 'categories'
        ];

        $this->render('pages/categories/create', $data);
    }

    /**
     * Process create
     */
    private function processCreate() {
        try {
            $name = sanitize($_POST['name'] ?? '');
            $slug = $this->generateSlug($_POST['slug'] ?: $name);
            $description = sanitize($_POST['description'] ?? '');
            $sort_order = (int)($_POST['sort_order'] ?? 0);
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            $sql = "INSERT INTO categories (name, slug, description, sort_order, is_active, created_at)
                    VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$name, $slug, $description, $sort_order, $is_active]);

            $this->logActivity('category_created', "Created category: $name");

            setFlashMessage('Kategori başarıyla eklendi!', 'success');
            redirect(ADMIN_URL . '/categories');

        } catch (Exception $e) {
            setFlashMessage('Hata: ' . $e->getMessage(), 'error');
            redirect(ADMIN_URL . '/categories/create');
        }
    }

    /**
     * Edit category
     */
    public function edit() {
        $id = (int)($_GET['id'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processEdit($id);
            return;
        }

        $sql = "SELECT * FROM categories WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$category) {
            setFlashMessage('Kategori bulunamadı.', 'error');
            redirect(ADMIN_URL . '/categories');
            return;
        }

        $data = [
            'title' => 'Kategori Düzenle',
            'active_menu' => 'categories',
            'category' => $category
        ];

        $this->render('pages/categories/edit', $data);
    }

    /**
     * Process edit
     */
    private function processEdit($id) {
        try {
            $name = sanitize($_POST['name'] ?? '');
            $slug = $this->generateSlug($_POST['slug'] ?: $name);
            $description = sanitize($_POST['description'] ?? '');
            $sort_order = (int)($_POST['sort_order'] ?? 0);
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            $sql = "UPDATE categories SET
                        name = ?, slug = ?, description = ?,
                        sort_order = ?, is_active = ?, updated_at = NOW()
                    WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$name, $slug, $description, $sort_order, $is_active, $id]);

            $this->logActivity('category_updated', "Updated category: $name");

            setFlashMessage('Kategori başarıyla güncellendi!', 'success');
            redirect(ADMIN_URL . '/categories');

        } catch (Exception $e) {
            setFlashMessage('Hata: ' . $e->getMessage(), 'error');
            redirect(ADMIN_URL . '/categories/edit?id=' . $id);
        }
    }

    /**
     * Delete category
     */
    public function delete() {
        $id = (int)($_POST['id'] ?? 0);

        try {
            // Check if category has products
            $checkSql = "SELECT COUNT(*) as count FROM products WHERE category_id = ?";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute([$id]);
            $result = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($result['count'] > 0) {
                echo json_encode(['success' => false, 'message' => 'Bu kategoride ürünler var. Önce ürünleri silin veya başka kategoriye taşıyın.']);
                return;
            }

            $sql = "DELETE FROM categories WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);

            $this->logActivity('category_deleted', "Deleted category ID: $id");

            echo json_encode(['success' => true, 'message' => 'Kategori silindi.']);

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
