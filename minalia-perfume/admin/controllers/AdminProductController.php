<?php
/**
 * Admin Product Controller
 * MINALIA Parfüm E-Ticaret Platformu
 */

require_once __DIR__ . '/../../app/models/Product.php';

class AdminProductController extends AdminController {
    private $productModel;

    public function __construct() {
        parent::__construct();
        $this->productModel = new Product();
    }

    /**
     * Product listing
     */
    public function index() {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $search = $_GET['search'] ?? '';

        $filters = [];
        if ($search) {
            $filters['search'] = $search;
        }

        $result = $this->productModel->getProducts($filters, $page, 20);

        // Get brands and categories for filters
        $brands = $this->db->query("SELECT * FROM brands ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        $categories = $this->db->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'title' => 'Ürünler',
            'active_menu' => 'products',
            'products' => $result['data'],
            'pagination' => $result['pagination'],
            'brands' => $brands,
            'categories' => $categories,
            'search' => $search
        ];

        $this->render('pages/products/index', $data);
    }

    /**
     * Create product
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processCreate();
            return;
        }

        // Get brands and categories
        $brands = $this->db->query("SELECT * FROM brands ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        $categories = $this->db->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'title' => 'Yeni Ürün Ekle',
            'active_menu' => 'products',
            'brands' => $brands,
            'categories' => $categories
        ];

        $this->render('pages/products/create', $data);
    }

    /**
     * Process create
     */
    private function processCreate() {
        try {
            $name = sanitize($_POST['name'] ?? '');
            $slug = $this->generateSlug($name);
            $description = $_POST['description'] ?? '';
            $brand_id = (int)($_POST['brand_id'] ?? 0);
            $category_id = (int)($_POST['category_id'] ?? 0);
            $gender = $_POST['gender'] ?? 'unisex';
            $price = (float)($_POST['price'] ?? 0);
            $discount_price = !empty($_POST['discount_price']) ? (float)$_POST['discount_price'] : null;
            $stock_quantity = (int)($_POST['stock_quantity'] ?? 0);
            $low_stock_threshold = (int)($_POST['low_stock_threshold'] ?? 10);
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
            $is_bestseller = isset($_POST['is_bestseller']) ? 1 : 0;

            // Fragrance notes
            $fragranceNotes = [
                'ust' => array_filter($_POST['notes_top'] ?? []),
                'orta' => array_filter($_POST['notes_middle'] ?? []),
                'alt' => array_filter($_POST['notes_base'] ?? [])
            ];

            // Insert product
            $sql = "INSERT INTO products (
                        name, slug, description, brand_id, category_id, gender,
                        price, discount_price, stock_quantity, low_stock_threshold,
                        fragrance_notes, is_featured, is_bestseller, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $name, $slug, $description, $brand_id, $category_id, $gender,
                $price, $discount_price, $stock_quantity, $low_stock_threshold,
                json_encode($fragranceNotes, JSON_UNESCAPED_UNICODE),
                $is_featured, $is_bestseller
            ]);

            $productId = $this->db->lastInsertId();

            // Handle image upload
            if (!empty($_FILES['main_image']['name'])) {
                $this->uploadProductImage($productId, $_FILES['main_image']);
            }

            $this->logActivity('product_created', "Created product: $name (ID: $productId)");

            setFlashMessage('Ürün başarıyla eklendi!', 'success');
            redirect(ADMIN_URL . '/products');

        } catch (Exception $e) {
            setFlashMessage('Hata: ' . $e->getMessage(), 'error');
            redirect(ADMIN_URL . '/products/create');
        }
    }

    /**
     * Edit product
     */
    public function edit() {
        $id = (int)($_GET['id'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processEdit($id);
            return;
        }

        $product = $this->productModel->find($id);

        if (!$product) {
            setFlashMessage('Ürün bulunamadı.', 'error');
            redirect(ADMIN_URL . '/products');
            return;
        }

        // Decode fragrance notes
        $product['fragrance_notes'] = json_decode($product['fragrance_notes'], true);

        // Get brands and categories
        $brands = $this->db->query("SELECT * FROM brands ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        $categories = $this->db->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'title' => 'Ürün Düzenle',
            'active_menu' => 'products',
            'product' => $product,
            'brands' => $brands,
            'categories' => $categories
        ];

        $this->render('pages/products/edit', $data);
    }

    /**
     * Process edit
     */
    private function processEdit($id) {
        try {
            $name = sanitize($_POST['name'] ?? '');
            $description = $_POST['description'] ?? '';
            $brand_id = (int)($_POST['brand_id'] ?? 0);
            $category_id = (int)($_POST['category_id'] ?? 0);
            $gender = $_POST['gender'] ?? 'unisex';
            $price = (float)($_POST['price'] ?? 0);
            $discount_price = !empty($_POST['discount_price']) ? (float)$_POST['discount_price'] : null;
            $stock_quantity = (int)($_POST['stock_quantity'] ?? 0);
            $low_stock_threshold = (int)($_POST['low_stock_threshold'] ?? 10);
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
            $is_bestseller = isset($_POST['is_bestseller']) ? 1 : 0;

            // Fragrance notes
            $fragranceNotes = [
                'ust' => array_filter($_POST['notes_top'] ?? []),
                'orta' => array_filter($_POST['notes_middle'] ?? []),
                'alt' => array_filter($_POST['notes_base'] ?? [])
            ];

            $sql = "UPDATE products SET
                        name = ?, description = ?, brand_id = ?, category_id = ?,
                        gender = ?, price = ?, discount_price = ?, stock_quantity = ?,
                        low_stock_threshold = ?, fragrance_notes = ?,
                        is_featured = ?, is_bestseller = ?, updated_at = NOW()
                    WHERE id = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $name, $description, $brand_id, $category_id, $gender,
                $price, $discount_price, $stock_quantity, $low_stock_threshold,
                json_encode($fragranceNotes, JSON_UNESCAPED_UNICODE),
                $is_featured, $is_bestseller, $id
            ]);

            // Handle new image upload
            if (!empty($_FILES['main_image']['name'])) {
                $this->uploadProductImage($id, $_FILES['main_image']);
            }

            $this->logActivity('product_updated', "Updated product: $name (ID: $id)");

            setFlashMessage('Ürün başarıyla güncellendi!', 'success');
            redirect(ADMIN_URL . '/products');

        } catch (Exception $e) {
            setFlashMessage('Hata: ' . $e->getMessage(), 'error');
            redirect(ADMIN_URL . '/products/edit?id=' . $id);
        }
    }

    /**
     * Delete product
     */
    public function delete() {
        $id = (int)($_POST['id'] ?? 0);

        try {
            $product = $this->productModel->find($id);

            if (!$product) {
                echo json_encode(['success' => false, 'message' => 'Ürün bulunamadı.']);
                return;
            }

            $this->productModel->delete($id);

            $this->logActivity('product_deleted', "Deleted product: {$product['name']} (ID: $id)");

            echo json_encode(['success' => true, 'message' => 'Ürün silindi.']);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
        }
    }

    /**
     * AJAX Upload product image
     */
    public function uploadImage() {
        try {
            if (!isset($_FILES['image'])) {
                echo json_encode(['success' => false, 'message' => 'Dosya bulunamadı.']);
                return;
            }

            $productId = (int)($_POST['product_id'] ?? 0);
            $imagePath = $this->uploadProductImage($productId, $_FILES['image']);

            echo json_encode([
                'success' => true,
                'message' => 'Resim yüklendi.',
                'path' => $imagePath
            ]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
        }
    }

    /**
     * Upload product image
     */
    private function uploadProductImage($productId, $file) {
        $uploadDir = __DIR__ . '/../../public/uploads/products/';

        // Create directory if not exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception('Geçersiz dosya formatı. Sadece JPG, PNG, WEBP desteklenir.');
        }

        $filename = 'product_' . $productId . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Update product main image
            $relativePath = '/uploads/products/' . $filename;
            $sql = "UPDATE products SET main_image = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$relativePath, $productId]);

            return $relativePath;
        }

        throw new Exception('Dosya yükleme başarısız.');
    }

    /**
     * Generate slug
     */
    private function generateSlug($text) {
        $slug = strtolower(trim($text));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }

}
