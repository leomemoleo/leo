<?php
/**
 * Admin Base Controller
 * MINALIA Parfüm E-Ticaret Platformu
 *
 * All admin controllers extend this base class
 */

class AdminController {
    protected $db;

    public function __construct() {
        // Require admin authentication
        requireAdminAuth();

        // Get database connection
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Render view with layout
     *
     * @param string $view View file path (relative to admin/views/)
     * @param array $data Data to pass to view
     */
    protected function render($view, $data = []) {
        extract($data);

        ob_start();
        require ADMIN_VIEWS_PATH . '/' . $view . '.php';
        $content = ob_get_clean();

        require ADMIN_VIEWS_PATH . '/layouts/main.php';
    }

    /**
     * Render JSON response
     *
     * @param mixed $data Data to return
     * @param int $statusCode HTTP status code
     */
    protected function renderJson($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Log admin activity
     *
     * @param string $action Action type
     * @param string $description Action description
     */
    protected function logActivity($action, $description = '') {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO activity_logs (admin_id, action, description, ip_address, user_agent, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");

            $stmt->execute([
                $_SESSION[SESSION_ADMIN_ID] ?? 0,
                $action,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
        } catch (PDOException $e) {
            // Log error but don't break the flow
            error_log("Activity log error: " . $e->getMessage());
        }
    }

    /**
     * Upload image helper
     *
     * @param array $file $_FILES array element
     * @param string $directory Subdirectory in uploads/
     * @param array $allowedTypes Allowed MIME types
     * @param int $maxSize Max file size in bytes
     * @return string|false Upload path or false on failure
     */
    protected function uploadImage($file, $directory = 'products', $allowedTypes = null, $maxSize = 5242880) {
        if ($allowedTypes === null) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        }

        try {
            // Check for upload errors
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Upload error: ' . $file['error']);
            }

            // Check file type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedTypes)) {
                throw new Exception('Invalid file type');
            }

            // Check file size
            if ($file['size'] > $maxSize) {
                throw new Exception('File too large');
            }

            // Create upload directory
            $uploadDir = __DIR__ . '/../../public/uploads/' . $directory . '/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $uploadPath = $uploadDir . $filename;

            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                return '/uploads/' . $directory . '/' . $filename;
            }

            return false;

        } catch (Exception $e) {
            error_log("Image upload error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete image helper
     *
     * @param string $imagePath Image path (relative to public/)
     * @return bool Success status
     */
    protected function deleteImage($imagePath) {
        if (empty($imagePath)) {
            return false;
        }

        $fullPath = __DIR__ . '/../../public' . $imagePath;
        if (file_exists($fullPath) && is_file($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }

    /**
     * Validate CSRF token
     *
     * @return bool
     */
    protected function validateCsrfToken() {
        $token = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';

        return hash_equals($sessionToken, $token);
    }

    /**
     * Generate CSRF token
     *
     * @return string
     */
    protected function generateCsrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }
}
