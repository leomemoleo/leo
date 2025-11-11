<?php
/**
 * Admin Page Controller
 * Manage static pages (About, Contact, Terms, Privacy)
 * MINALIA Parfüm E-Ticaret Platformu
 */

class AdminPageController {
    private $db;

    public function __construct() {
        requireAdminAuth();
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Page listing
     */
    public function index() {
        $sql = "SELECT * FROM pages ORDER BY title";
        $stmt = $this->db->query($sql);
        $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'title' => 'Sayfa Yönetimi',
            'active_menu' => 'pages',
            'pages' => $pages
        ];

        $this->render('pages/page-management/index', $data);
    }

    /**
     * Create page
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processCreate();
            return;
        }

        $data = [
            'title' => 'Yeni Sayfa Ekle',
            'active_menu' => 'pages'
        ];

        $this->render('pages/page-management/create', $data);
    }

    /**
     * Process create
     */
    private function processCreate() {
        try {
            $title = sanitize($_POST['title'] ?? '');
            $slug = $this->generateSlug($_POST['slug'] ?? $title);
            $content = $_POST['content'] ?? '';
            $meta_title = sanitize($_POST['meta_title'] ?? $title);
            $meta_description = sanitize($_POST['meta_description'] ?? '');
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            $sql = "INSERT INTO pages (title, slug, content, meta_title, meta_description, is_active, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$title, $slug, $content, $meta_title, $meta_description, $is_active]);

            $this->logActivity('page_created', "Created page: $title");

            setFlashMessage('Sayfa başarıyla oluşturuldu!', 'success');
            redirect(ADMIN_URL . '/pages');

        } catch (Exception $e) {
            setFlashMessage('Hata: ' . $e->getMessage(), 'error');
            redirect(ADMIN_URL . '/pages/create');
        }
    }

    /**
     * Edit page
     */
    public function edit() {
        $id = (int)($_GET['id'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processEdit($id);
            return;
        }

        $sql = "SELECT * FROM pages WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $page = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$page) {
            setFlashMessage('Sayfa bulunamadı.', 'error');
            redirect(ADMIN_URL . '/pages');
            return;
        }

        $data = [
            'title' => 'Sayfa Düzenle',
            'active_menu' => 'pages',
            'page' => $page
        ];

        $this->render('pages/page-management/edit', $data);
    }

    /**
     * Process edit
     */
    private function processEdit($id) {
        try {
            $title = sanitize($_POST['title'] ?? '');
            $slug = $this->generateSlug($_POST['slug'] ?? $title);
            $content = $_POST['content'] ?? '';
            $meta_title = sanitize($_POST['meta_title'] ?? $title);
            $meta_description = sanitize($_POST['meta_description'] ?? '');
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            $sql = "UPDATE pages SET
                        title = ?, slug = ?, content = ?,
                        meta_title = ?, meta_description = ?,
                        is_active = ?, updated_at = NOW()
                    WHERE id = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$title, $slug, $content, $meta_title, $meta_description, $is_active, $id]);

            $this->logActivity('page_updated', "Updated page: $title");

            setFlashMessage('Sayfa başarıyla güncellendi!', 'success');
            redirect(ADMIN_URL . '/pages');

        } catch (Exception $e) {
            setFlashMessage('Hata: ' . $e->getMessage(), 'error');
            redirect(ADMIN_URL . '/pages/edit?id=' . $id);
        }
    }

    /**
     * Delete page
     */
    public function delete() {
        $id = (int)($_POST['id'] ?? 0);

        try {
            $sql = "SELECT title FROM pages WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $page = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$page) {
                echo json_encode(['success' => false, 'message' => 'Sayfa bulunamadı.']);
                return;
            }

            $deleteSql = "DELETE FROM pages WHERE id = ?";
            $deleteStmt = $this->db->prepare($deleteSql);
            $deleteStmt->execute([$id]);

            $this->logActivity('page_deleted', "Deleted page: {$page['title']}");

            echo json_encode(['success' => true, 'message' => 'Sayfa silindi.']);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate slug
     */
    private function generateSlug($text) {
        // Turkish character mapping
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
