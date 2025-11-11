<?php
/**
 * Product Controller
 * MINALIA Parfüm E-Ticaret Platformu
 */

require_once __DIR__ . '/../models/Product.php';

class ProductController extends BaseController {
    private $productModel;

    public function __construct() {
        $this->productModel = new Product();
    }

    /**
     * Product listing page
     */
    public function index() {
        $page = max(1, (int)$this->get('page', 1));

        // Get filters from request
        $filters = [
            'category_id' => $this->get('category'),
            'brand_id' => $this->get('brand'),
            'gender' => $this->get('gender'),
            'min_price' => $this->get('min_price'),
            'max_price' => $this->get('max_price'),
            'order_by' => $this->get('sort', 'created_at DESC')
        ];

        // Get products
        $result = $this->productModel->getProducts($filters, $page);

        $this->view('products/index', [
            'title' => 'Tüm Ürünler',
            'products' => $result['data'],
            'pagination' => $result['pagination'],
            'filters' => $filters
        ]);
    }

    /**
     * Product detail page
     */
    public function show($slug) {
        $product = $this->productModel->getBySlug($slug);

        if (!$product) {
            $this->error404('Ürün bulunamadı.');
        }

        // Increment view count
        $this->productModel->incrementViewCount($product['id']);

        // Get product images
        $images = $this->productModel->getImages($product['id']);

        // Get product variants
        $variants = $this->productModel->getVariants($product['id']);

        // Get related products
        $relatedProducts = $this->productModel->getRelated($product['id'], $product['category_id']);

        // Get reviews (placeholder - will be implemented later)
        $reviews = [];

        $this->view('products/show', [
            'title' => $product['name'],
            'product' => $product,
            'images' => $images,
            'variants' => $variants,
            'related_products' => $relatedProducts,
            'reviews' => $reviews,
            'meta_description' => $product['short_description'] ?? truncate($product['description'], 160),
            'meta_keywords' => $product['name'] . ', ' . $product['brand_name']
        ]);
    }

    /**
     * Products by category
     */
    public function category($slug) {
        // Get category (placeholder)
        $categoryName = ucfirst(str_replace('-', ' ', $slug));

        $page = max(1, (int)$this->get('page', 1));

        $filters = [
            'order_by' => $this->get('sort', 'created_at DESC')
        ];

        $result = $this->productModel->getProducts($filters, $page);

        $this->view('products/index', [
            'title' => $categoryName,
            'products' => $result['data'],
            'pagination' => $result['pagination'],
            'category_name' => $categoryName
        ]);
    }

    /**
     * Products by brand
     */
    public function brand($slug) {
        $brandName = ucfirst(str_replace('-', ' ', $slug));

        $page = max(1, (int)$this->get('page', 1));

        $filters = [
            'order_by' => $this->get('sort', 'created_at DESC')
        ];

        $result = $this->productModel->getProducts($filters, $page);

        $this->view('products/index', [
            'title' => $brandName,
            'products' => $result['data'],
            'pagination' => $result['pagination'],
            'brand_name' => $brandName
        ]);
    }

    /**
     * Search products
     */
    public function search() {
        $query = $this->get('q', '');
        $page = max(1, (int)$this->get('page', 1));

        if (empty($query)) {
            $this->redirect(BASE_URL . '/products');
        }

        $filters = [
            'search' => $query,
            'order_by' => $this->get('sort', 'created_at DESC')
        ];

        $result = $this->productModel->getProducts($filters, $page);

        $this->view('products/index', [
            'title' => 'Arama: ' . $query,
            'products' => $result['data'],
            'pagination' => $result['pagination'],
            'search_query' => $query
        ]);
    }
}
