<?php
/**
 * Coupon Model
 * MINALIA Parfüm E-Ticaret Platformu
 */

require_once __DIR__ . '/BaseModel.php';

class Coupon extends BaseModel {
    protected $table = 'coupons';

    /**
     * Validate and apply coupon
     */
    public function validateCoupon($code, $cartTotal, $userId = null) {
        $coupon = $this->findBy('code', strtoupper($code));

        if (!$coupon) {
            return ['valid' => false, 'message' => 'Geçersiz kupon kodu'];
        }

        // Check if active
        if (!$coupon['is_active']) {
            return ['valid' => false, 'message' => 'Bu kupon artık geçerli değil'];
        }

        // Check dates
        $now = date('Y-m-d H:i:s');
        if (strtotime($now) < strtotime($coupon['valid_from'])) {
            return ['valid' => false, 'message' => 'Bu kupon henüz kullanıma açılmadı'];
        }

        if ($coupon['valid_until'] && strtotime($now) > strtotime($coupon['valid_until'])) {
            return ['valid' => false, 'message' => 'Bu kuponun süresi dolmuş'];
        }

        // Check usage limit
        if ($coupon['usage_limit'] && $coupon['usage_count'] >= $coupon['usage_limit']) {
            return ['valid' => false, 'message' => 'Bu kupon kullanım limitine ulaştı'];
        }

        // Check minimum order amount
        if ($cartTotal < $coupon['min_order_amount']) {
            return [
                'valid' => false,
                'message' => 'Minimum sepet tutarı ' . formatPrice($coupon['min_order_amount'])
            ];
        }

        // Calculate discount
        $discount = $this->calculateDiscount($coupon, $cartTotal);

        return [
            'valid' => true,
            'coupon_id' => $coupon['id'],
            'discount_amount' => $discount,
            'message' => 'Kupon başarıyla uygulandı!'
        ];
    }

    /**
     * Calculate discount amount
     */
    private function calculateDiscount($coupon, $cartTotal) {
        if ($coupon['discount_type'] === 'percentage') {
            $discount = ($cartTotal * $coupon['discount_value']) / 100;

            // Apply max discount limit
            if ($coupon['max_discount'] && $discount > $coupon['max_discount']) {
                $discount = $coupon['max_discount'];
            }
        } else {
            $discount = $coupon['discount_value'];
        }

        // Discount cannot exceed cart total
        return min($discount, $cartTotal);
    }

    /**
     * Apply coupon usage
     */
    public function applyCoupon($couponId) {
        $sql = "UPDATE {$this->table}
                SET usage_count = usage_count + 1
                WHERE id = ?";

        $stmt = $this->query($sql, [$couponId]);
        return $stmt->execute();
    }

    /**
     * Create coupon
     */
    public function createCoupon($data) {
        $data['code'] = strtoupper($data['code']);
        return $this->insert($data);
    }

    /**
     * Get active coupons
     */
    public function getActiveCoupons() {
        $sql = "SELECT * FROM {$this->table}
                WHERE is_active = 1
                AND valid_from <= NOW()
                AND (valid_until IS NULL OR valid_until >= NOW())
                AND (usage_limit IS NULL OR usage_count < usage_limit)
                ORDER BY created_at DESC";

        $stmt = $this->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Generate random coupon code
     */
    public static function generateCode($prefix = 'MIN', $length = 8) {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = $prefix;

        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $code;
    }

    /**
     * Get coupon statistics
     */
    public function getStats($couponId) {
        $sql = "SELECT
                    c.*,
                    COUNT(o.id) as order_count,
                    SUM(o.discount) as total_discount
                FROM {$this->table} c
                LEFT JOIN orders o ON o.coupon_code = c.code
                WHERE c.id = ?
                GROUP BY c.id";

        $stmt = $this->query($sql, [$couponId]);
        return $stmt->fetch();
    }
}
