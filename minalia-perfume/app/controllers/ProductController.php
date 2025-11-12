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
     * Product listing page with faceted search
     */
    public function index() {
        $page = max(1, (int)$this->get('page', 1));

        // Get comprehensive filters from request
        $filters = [
            'category_id' => $this->get('category'),
            'brand_id' => $this->get('brand'),
            'gender' => $this->get('gender'),
            'min_price' => $this->get('min_price'),
            'max_price' => $this->get('max_price'),
            'min_rating' => $this->get('min_rating'),
            'launch_year' => $this->get('year'),
            'is_featured' => $this->get('featured'),
            'is_bestseller' => $this->get('bestseller'),
            'is_new' => $this->get('new'),
            'in_stock' => $this->get('in_stock', true), // Default: show only in-stock
            'notes' => $this->get('notes', []), // Fragrance notes filter
            'order_by' => $this->getSortOption($this->get('sort', 'newest'))
        ];

        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return $value !== null && $value !== '' && $value !== [];
        });

        // Get products with faceted data
        $result = $this->productModel->getProductsWithFacets($filters, $page);

        // Get filter options for sidebar
        $filterOptions = $this->getFilterOptions($filters);

        // Track filter analytics
        $this->trackFilterUsage($filters, count($result['data']));

        $this->view('products/index', [
            'title' => 'Tüm Ürünler',
            'products' => $result['data'],
            'pagination' => $result['pagination'],
            'filters' => $filters,
            'filter_options' => $filterOptions,
            'facets' => $result['facets'] ?? [],
            'result_count' => $result['total'] ?? 0
        ]);
    }

    /**
     * Get sort option from user-friendly key
     */
    private function getSortOption($sort) {
        $sortMap = [
            'newest' => 'created_at DESC',
            'oldest' => 'created_at ASC',
            'price_low' => 'price ASC',
            'price_high' => 'price DESC',
            'popular' => 'view_count DESC',
            'rating' => 'rating DESC, review_count DESC',
            'name_az' => 'name ASC',
            'name_za' => 'name DESC'
        ];

        return $sortMap[$sort] ?? 'created_at DESC';
    }

    /**
     * Get available filter options (brands, categories, etc.)
     */
    private function getFilterOptions($currentFilters = []) {
        $db = $this->productModel->db;

        // Get active brands with product count
        $brandsSql = "SELECT b.id, b.name, b.slug, COUNT(p.id) as product_count
                      FROM brands b
                      INNER JOIN products p ON b.id = p.brand_id
                      WHERE p.is_active = 1
                      GROUP BY b.id, b.name, b.slug
                      HAVING product_count > 0
                      ORDER BY b.name ASC";
        $brandsStmt = $db->prepare($brandsSql);
        $brandsStmt->execute();
        $brands = $brandsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Get active categories with product count
        $categoriesSql = "SELECT c.id, c.name, c.slug, c.parent_id, COUNT(p.id) as product_count
                          FROM categories c
                          INNER JOIN products p ON c.id = p.category_id
                          WHERE c.is_active = 1 AND p.is_active = 1
                          GROUP BY c.id, c.name, c.slug, c.parent_id
                          HAVING product_count > 0
                          ORDER BY c.sort_order ASC, c.name ASC";
        $categoriesStmt = $db->prepare($categoriesSql);
        $categoriesStmt->execute();
        $categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

        // Get price range
        $priceSql = "SELECT MIN(price) as min_price, MAX(price) as max_price
                     FROM products WHERE is_active = 1";
        $priceStmt = $db->prepare($priceSql);
        $priceStmt->execute();
        $priceRange = $priceStmt->fetch(PDO::FETCH_ASSOC);

        // Get available years
        $yearsSql = "SELECT DISTINCT launch_year
                     FROM products
                     WHERE is_active = 1 AND launch_year IS NOT NULL
                     ORDER BY launch_year DESC";
        $yearsStmt = $db->prepare($yearsSql);
        $yearsStmt->execute();
        $years = $yearsStmt->fetchAll(PDO::FETCH_COLUMN);

        // Common fragrance notes (top 20)
        $notes = ['Bergamot', 'Lavanta', 'Gül', 'Vanilya', 'Amber', 'Misk', 'Sandal Ağacı',
                  'Yasemin', 'Portakal Çiçeği', 'Paçuli', 'Oud', 'Vetiver', 'Tonka', 'İris',
                  'Karanfil', 'Tarçın', 'Kakao', 'Kahve', 'Deri', 'Tütün'];

        return [
            'brands' => $brands,
            'categories' => $categories,
            'price_range' => $priceRange,
            'years' => $years,
            'notes' => $notes,
            'genders' => [
                ['value' => 'men', 'label' => 'Erkek'],
                ['value' => 'women', 'label' => 'Kadın'],
                ['value' => 'unisex', 'label' => 'Unisex']
            ]
        ];
    }

    /**
     * Track filter usage for analytics
     */
    private function trackFilterUsage($filters, $resultCount) {
        if (empty($filters)) return;

        try {
            $db = $this->productModel->db;

            // Remove sensitive and non-filter data
            $trackFilters = $filters;
            unset($trackFilters['order_by'], $trackFilters['page'], $trackFilters['csrf_token']);

            // Sanitize filter values before JSON encoding
            $trackFilters = array_map(function($value) {
                if (is_string($value)) {
                    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                }
                return $value;
            }, $trackFilters);

            $filterJson = json_encode($trackFilters);

            // Prevent JSON too large
            if (strlen($filterJson) > 1000) {
                error_log("Filter combination too large, skipping analytics");
                return;
            }

            $sql = "INSERT INTO filter_analytics (filter_combination, usage_count, avg_result_count, last_used_at)
                    VALUES (?, 1, ?, NOW())
                    ON DUPLICATE KEY UPDATE
                        usage_count = usage_count + 1,
                        avg_result_count = ((avg_result_count * usage_count) + ?) / (usage_count + 1),
                        last_used_at = NOW()";

            $stmt = $db->prepare($sql);
            $stmt->execute([$filterJson, $resultCount, $resultCount]);

        } catch (Exception $e) {
            error_log("Filter analytics tracking error: " . $e->getMessage());
            // Silent fail - analytics should never break user experience
        }
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
     * Search products with enhanced features
     */
    public function search() {
        $query = trim($this->get('q', ''));
        $page = max(1, (int)$this->get('page', 1));

        if (empty($query)) {
            $this->redirect(BASE_URL . '/products');
            return;
        }

        // Get all filters including search
        $filters = [
            'search' => $query,
            'category_id' => $this->get('category'),
            'brand_id' => $this->get('brand'),
            'gender' => $this->get('gender'),
            'min_price' => $this->get('min_price'),
            'max_price' => $this->get('max_price'),
            'min_rating' => $this->get('min_rating'),
            'in_stock' => $this->get('in_stock', true),
            'order_by' => $this->getSortOption($this->get('sort', 'relevance'))
        ];

        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return $value !== null && $value !== '';
        });

        // Get search results with facets
        $result = $this->productModel->searchProducts($query, $filters, $page);

        // Get filter options
        $filterOptions = $this->getFilterOptions($filters);

        // Track search
        $this->trackSearch($query, count($result['data']), $filters);

        // Update popular searches
        $this->updatePopularSearch($query);

        $this->view('products/index', [
            'title' => 'Arama: ' . $query,
            'products' => $result['data'],
            'pagination' => $result['pagination'],
            'filters' => $filters,
            'filter_options' => $filterOptions,
            'search_query' => $query,
            'result_count' => $result['total'] ?? 0,
            'suggestions' => $result['suggestions'] ?? []
        ]);
    }

    /**
     * Track search for analytics
     */
    private function trackSearch($query, $resultCount, $filters) {
        if (empty($query)) return;

        // Sanitize query for storage
        $query = trim($query);
        if (strlen($query) > 255) {
            $query = substr($query, 0, 255);
        }

        try {
            $db = $this->productModel->db;
            $userId = getCurrentUserId();
            $sessionId = session_id();

            // Remove sensitive data from filters before storage
            $safeFilters = $filters;
            unset($safeFilters['csrf_token']);

            $filterJson = json_encode($safeFilters);

            $sql = "INSERT INTO search_history (user_id, session_id, search_term, result_count, filters_used, created_at)
                    VALUES (?, ?, ?, ?, ?, NOW())";

            $stmt = $db->prepare($sql);
            $stmt->execute([$userId, $sessionId, $query, $resultCount, $filterJson]);

        } catch (Exception $e) {
            error_log("Search tracking error: " . $e->getMessage());
            // Silent fail - don't break user experience
        }
    }

    /**
     * Update popular searches
     */
    private function updatePopularSearch($query) {
        if (empty($query) || strlen($query) < 3) return;

        try {
            $db = $this->productModel->db;

            $sql = "INSERT INTO popular_searches (search_term, search_count, last_searched_at)
                    VALUES (?, 1, NOW())
                    ON DUPLICATE KEY UPDATE
                        search_count = search_count + 1,
                        last_searched_at = NOW()";

            $stmt = $db->prepare($sql);
            $stmt->execute([$query]);

            // Mark as trending if search_count > 100
            $trendingSql = "UPDATE popular_searches
                            SET is_trending = 1
                            WHERE search_count > 100 AND search_term = ?";
            $trendingStmt = $db->prepare($trendingSql);
            $trendingStmt->execute([$query]);

        } catch (Exception $e) {
            error_log("Popular search update error: " . $e->getMessage());
        }
    }
}
