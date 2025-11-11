<?php
/**
 * Product Model
 * MINALIA Parfüm E-Ticaret Platformu
 */

require_once __DIR__ . '/BaseModel.php';

class Product extends BaseModel {
    protected $table = 'products';

    /**
     * Get products with filters
     */
    public function getProducts($filters = [], $page = 1, $perPage = PRODUCTS_PER_PAGE) {
        $where = ['is_active' => 1];
        $params = [];

        // Category filter
        if (!empty($filters['category_id'])) {
            $where[] = "category_id = ?";
            $params[] = $filters['category_id'];
        }

        // Brand filter
        if (!empty($filters['brand_id'])) {
            $where[] = "brand_id = ?";
            $params[] = $filters['brand_id'];
        }

        // Gender filter
        if (!empty($filters['gender'])) {
            $where[] = "gender = ?";
            $params[] = $filters['gender'];
        }

        // Price range filter
        if (!empty($filters['min_price'])) {
            $where[] = "price >= ?";
            $params[] = $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $where[] = "price <= ?";
            $params[] = $filters['max_price'];
        }

        // Search query
        if (!empty($filters['search'])) {
            $where[] = "(name LIKE ? OR description LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        // Build WHERE clause
        $whereClause = count($where) > 0 ? ' WHERE ' . implode(' AND ', $where) : '';

        // Order by
        $orderBy = $filters['order_by'] ?? 'created_at DESC';

        // Get total count
        $countSql = "SELECT COUNT(*) FROM {$this->table}" . $whereClause;
        $countStmt = $this->query($countSql, $params);
        $total = $countStmt->fetchColumn();

        // Get products
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT p.*, b.name as brand_name, c.name as category_name
                FROM {$this->table} p
                LEFT JOIN brands b ON p.brand_id = b.id
                LEFT JOIN categories c ON p.category_id = c.id
                {$whereClause}
                ORDER BY {$orderBy}
                LIMIT ? OFFSET ?";

        $params[] = $perPage;
        $params[] = $offset;

        $stmt = $this->query($sql, $params);
        $products = $stmt->fetchAll();

        return [
            'data' => $products,
            'pagination' => paginate($total, $perPage, $page)
        ];
    }

    /**
     * Get product by slug
     */
    public function getBySlug($slug) {
        $sql = "SELECT p.*, b.name as brand_name, b.slug as brand_slug,
                       c.name as category_name, c.slug as category_slug
                FROM {$this->table} p
                LEFT JOIN brands b ON p.brand_id = b.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.slug = ?";

        $stmt = $this->query($sql, [$slug]);
        return $stmt->fetch();
    }

    /**
     * Get featured products
     */
    public function getFeatured($limit = 8) {
        $sql = "SELECT p.*, b.name as brand_name
                FROM {$this->table} p
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.is_featured = 1
                ORDER BY p.created_at DESC
                LIMIT ?";

        $stmt = $this->query($sql, [$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Get new products
     */
    public function getNew($limit = 8) {
        $sql = "SELECT p.*, b.name as brand_name
                FROM {$this->table} p
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.is_new = 1
                ORDER BY p.created_at DESC
                LIMIT ?";

        $stmt = $this->query($sql, [$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Get bestsellers
     */
    public function getBestsellers($limit = 8) {
        $sql = "SELECT p.*, b.name as brand_name
                FROM {$this->table} p
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.is_bestseller = 1
                ORDER BY p.created_at DESC
                LIMIT ?";

        $stmt = $this->query($sql, [$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Get related products
     */
    public function getRelated($productId, $categoryId, $limit = 4) {
        $sql = "SELECT p.*, b.name as brand_name
                FROM {$this->table} p
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.category_id = ?
                AND p.id != ?
                ORDER BY RAND()
                LIMIT ?";

        $stmt = $this->query($sql, [$categoryId, $productId, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Get product images
     */
    public function getImages($productId) {
        $sql = "SELECT * FROM product_images
                WHERE product_id = ?
                ORDER BY is_primary DESC, sort_order ASC";

        $stmt = $this->query($sql, [$productId]);
        return $stmt->fetchAll();
    }

    /**
     * Get product variants
     */
    public function getVariants($productId) {
        $sql = "SELECT * FROM product_variants
                WHERE product_id = ?
                ORDER BY price ASC";

        $stmt = $this->query($sql, [$productId]);
        return $stmt->fetchAll();
    }

    /**
     * Update product view count
     */
    public function incrementViewCount($productId) {
        $sql = "UPDATE {$this->table}
                SET view_count = view_count + 1
                WHERE id = ?";

        $stmt = $this->query($sql, [$productId]);
        return $stmt->execute();
    }

    /**
     * Update product rating
     */
    public function updateRating($productId) {
        $sql = "UPDATE {$this->table} p
                SET p.rating = (
                    SELECT AVG(r.rating)
                    FROM reviews r
                    WHERE r.product_id = p.id AND r.is_approved = 1
                ),
                p.review_count = (
                    SELECT COUNT(*)
                    FROM reviews r
                    WHERE r.product_id = p.id AND r.is_approved = 1
                )
                WHERE p.id = ?";

        $stmt = $this->query($sql, [$productId]);
        return $stmt->execute();
    }

    /**
     * Search products
     */
    public function search($query, $limit = 10) {
        $searchTerm = '%' . $query . '%';

        $sql = "SELECT p.*, b.name as brand_name
                FROM {$this->table} p
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE (p.name LIKE ? OR p.description LIKE ? OR b.name LIKE ?)
                ORDER BY p.name ASC
                LIMIT ?";

        $stmt = $this->query($sql, [$searchTerm, $searchTerm, $searchTerm, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Check stock availability
     */
    public function checkStock($productId, $variantId = null, $quantity = 1) {
        if ($variantId) {
            $sql = "SELECT stock_quantity FROM product_variants WHERE id = ?";
            $stmt = $this->query($sql, [$variantId]);
        } else {
            $sql = "SELECT stock_quantity FROM {$this->table} WHERE id = ?";
            $stmt = $this->query($sql, [$productId]);
        }

        $stock = $stmt->fetchColumn();
        return $stock >= $quantity;
    }
}
