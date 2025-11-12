<?php
/**
 * API Controller
 * MINALIA Parfüm E-Ticaret Platformu
 */

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Coupon.php';
require_once __DIR__ . '/../helpers/AIRecommendation.php';

class ApiController extends BaseController {
    private $productModel;
    private $couponModel;

    public function __construct() {
        $this->productModel = new Product();
        $this->couponModel = new Coupon();
    }

    /**
     * Filter products API
     */
    public function filterProducts() {
        $filters = [
            'min_price' => $this->get('min_price'),
            'max_price' => $this->get('max_price'),
            'brands' => $this->get('brands') ? explode(',', $this->get('brands')) : [],
            'genders' => $this->get('genders') ? explode(',', $this->get('genders')) : [],
            'rating' => $this->get('rating'),
            'stock' => $this->get('stock') ? explode(',', $this->get('stock')) : []
        ];

        $page = max(1, (int)$this->get('page', 1));
        $sort = $this->get('sort', 'newest');

        // Convert sort parameter
        $orderBy = $this->getSortOrder($sort);

        $filters['order_by'] = $orderBy;

        $result = $this->productModel->getProducts($filters, $page);

        $this->json([
            'success' => true,
            'products' => $result['data'],
            'total' => $result['pagination']['total_items'],
            'page' => $result['pagination']['current_page'],
            'pages' => $result['pagination']['total_pages']
        ]);
    }

    /**
     * Enhanced autocomplete/search suggestions API
     */
    public function searchSuggestions() {
        $query = trim($this->get('q', ''));

        // Input validation and sanitization
        if (strlen($query) < 2) {
            $this->json(['success' => false, 'message' => 'Query too short']);
            return;
        }

        if (strlen($query) > 100) {
            $this->json(['success' => false, 'message' => 'Query too long']);
            return;
        }

        // Remove potentially dangerous characters
        $query = preg_replace('/[<>"\']/', '', $query);

        // Additional XSS protection
        $query = htmlspecialchars($query, ENT_QUOTES, 'UTF-8');

        $db = $this->productModel->db;

        // Get matching products (top 5)
        $productsSql = "SELECT id, name, slug, price, main_image, brand_name, rating, review_count
                        FROM products
                        WHERE is_active = 1
                        AND (
                            name LIKE ? OR
                            description LIKE ? OR
                            brand_name LIKE ? OR
                            MATCH(name, description) AGAINST(? IN NATURAL LANGUAGE MODE)
                        )
                        ORDER BY
                            CASE
                                WHEN name LIKE ? THEN 1
                                WHEN name LIKE ? THEN 2
                                ELSE 3
                            END,
                            view_count DESC,
                            rating DESC
                        LIMIT 5";

        $searchTerm = '%' . $query . '%';
        $exactMatch = $query . '%';

        $productsStmt = $db->prepare($productsSql);
        $productsStmt->execute([$searchTerm, $searchTerm, $searchTerm, $query, $exactMatch, $searchTerm]);
        $products = $productsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Get matching brands (top 3)
        $brandsSql = "SELECT b.id, b.name, b.slug, COUNT(p.id) as product_count
                      FROM brands b
                      INNER JOIN products p ON b.id = p.brand_id
                      WHERE b.name LIKE ? AND p.is_active = 1
                      GROUP BY b.id, b.name, b.slug
                      ORDER BY product_count DESC
                      LIMIT 3";

        $brandsStmt = $db->prepare($brandsSql);
        $brandsStmt->execute([$searchTerm]);
        $brands = $brandsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Get matching categories (top 3)
        $categoriesSql = "SELECT c.id, c.name, c.slug, COUNT(p.id) as product_count
                          FROM categories c
                          INNER JOIN products p ON c.id = p.category_id
                          WHERE c.name LIKE ? AND c.is_active = 1 AND p.is_active = 1
                          GROUP BY c.id, c.name, c.slug
                          ORDER BY product_count DESC
                          LIMIT 3";

        $categoriesStmt = $db->prepare($categoriesSql);
        $categoriesStmt->execute([$searchTerm]);
        $categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

        // Get popular searches (top 5)
        $popularSql = "SELECT search_term, search_count, is_trending
                       FROM popular_searches
                       WHERE search_term LIKE ?
                       ORDER BY search_count DESC, is_trending DESC
                       LIMIT 5";

        $popularStmt = $db->prepare($popularSql);
        $popularStmt->execute([$searchTerm]);
        $popularSearches = $popularStmt->fetchAll(PDO::FETCH_ASSOC);

        // Get trending searches (top 3)
        $trendingSql = "SELECT search_term, search_count
                        FROM popular_searches
                        WHERE is_trending = 1
                        ORDER BY search_count DESC, last_searched_at DESC
                        LIMIT 3";

        $trendingStmt = $db->prepare($trendingSql);
        $trendingStmt->execute();
        $trendingSearches = $trendingStmt->fetchAll(PDO::FETCH_ASSOC);

        $this->json([
            'success' => true,
            'query' => $query,
            'results' => [
                'products' => $products,
                'brands' => $brands,
                'categories' => $categories,
                'popular' => $popularSearches,
                'trending' => $trendingSearches
            ],
            'total_results' => count($products) + count($brands) + count($categories)
        ]);
    }

    /**
     * Validate coupon API
     */
    public function validateCoupon() {
        $code = $this->post('code');
        $cartTotal = (float)$this->post('cart_total');
        $userId = getCurrentUserId();

        $result = $this->couponModel->validateCoupon($code, $cartTotal, $userId);

        $this->json([
            'success' => $result['valid'],
            'message' => $result['message'],
            'discount' => $result['discount_amount'] ?? 0
        ]);
    }

    /**
     * AI Recommendations API
     */
    public function aiRecommendations() {
        $userId = getCurrentUserId();

        if (!$userId) {
            $this->json(['success' => false, 'message' => 'User not logged in']);
            return;
        }

        try {
            $aiManager = new AIRecommendationManager();
            $recommendations = $aiManager->getPersonalizedRecommendations($userId);

            $this->json([
                'success' => true,
                'recommendations' => $recommendations
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'AI service unavailable',
                'error' => DEBUG_MODE ? $e->getMessage() : null
            ]);
        }
    }

    /**
     * Quiz recommendations API
     */
    public function quizRecommendations() {
        $answers = $this->post('answers');

        if (empty($answers)) {
            $this->json(['success' => false, 'message' => 'No quiz answers provided']);
            return;
        }

        try {
            $aiManager = new AIRecommendationManager();
            $recommendations = $aiManager->getQuizRecommendations($answers);

            $this->json([
                'success' => true,
                'recommendations' => $recommendations
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Recommendation failed',
                'error' => DEBUG_MODE ? $e->getMessage() : null
            ]);
        }
    }

    /**
     * Get user loyalty points
     */
    public function loyaltyPoints() {
        $userId = getCurrentUserId();

        if (!$userId) {
            $this->json(['success' => false, 'message' => 'User not logged in']);
            return;
        }

        require_once __DIR__ . '/../models/LoyaltyPoints.php';
        $loyaltyModel = new LoyaltyPoints();

        $summary = $loyaltyModel->getPointsSummary($userId);

        $this->json([
            'success' => true,
            'points' => $summary
        ]);
    }

    /**
     * Request stock alert
     */
    public function stockAlert() {
        $productId = (int)$this->post('product_id');
        $email = sanitize($this->post('email'));
        $userId = getCurrentUserId();

        if (!isValidEmail($email)) {
            $this->json(['success' => false, 'message' => ERROR_INVALID_EMAIL]);
            return;
        }

        $db = Database::getInstance()->getConnection();
        $sql = "INSERT INTO stock_alerts (user_id, product_id, email) VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);

        if ($stmt->execute([$userId, $productId, $email])) {
            $this->json([
                'success' => true,
                'message' => 'Ürün stoğa girdiğinde size haber vereceğiz!'
            ]);
        } else {
            $this->json(['success' => false, 'message' => ERROR_DATABASE]);
        }
    }

    /**
     * Get sort order from parameter
     */
    private function getSortOrder($sort) {
        switch ($sort) {
            case 'price_asc':
                return 'price ASC';
            case 'price_desc':
                return 'price DESC';
            case 'name_asc':
                return 'name ASC';
            case 'name_desc':
                return 'name DESC';
            case 'rating':
                return 'rating DESC';
            case 'bestseller':
                return 'is_bestseller DESC, created_at DESC';
            case 'newest':
            default:
                return 'created_at DESC';
        }
    }
}
