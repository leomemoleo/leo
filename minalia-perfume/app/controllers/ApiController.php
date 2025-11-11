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
     * Search suggestions API
     */
    public function searchSuggestions() {
        $query = $this->get('q', '');

        if (strlen($query) < 2) {
            $this->json(['success' => false, 'message' => 'Query too short']);
            return;
        }

        $products = $this->productModel->search($query, 5);

        // Get brands
        $brandModel = new BaseModel();
        $brandModel->table = 'brands';
        $brands = [];

        $this->json([
            'success' => true,
            'results' => [
                'products' => $products,
                'brands' => $brands
            ]
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
