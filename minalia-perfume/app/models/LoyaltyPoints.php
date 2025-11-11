<?php
/**
 * Loyalty Points System
 * MINALIA Parfüm E-Ticaret Platformu
 */

require_once __DIR__ . '/BaseModel.php';

class LoyaltyPoints extends BaseModel {
    protected $table = 'loyalty_points';

    // Point earning rates
    const POINTS_PER_TRY = 1; // 1 TL = 1 Puan
    const SIGNUP_BONUS = 100;
    const BIRTHDAY_BONUS = 200;
    const REVIEW_BONUS = 50;
    const REFERRAL_BONUS = 500;

    // VIP Levels
    const LEVEL_BRONZE = ['min' => 0, 'name' => 'Bronz', 'multiplier' => 1];
    const LEVEL_SILVER = ['min' => 1000, 'name' => 'Gümüş', 'multiplier' => 1.5];
    const LEVEL_GOLD = ['min' => 5000, 'name' => 'Altın', 'multiplier' => 2];
    const LEVEL_PLATINUM = ['min' => 15000, 'name' => 'Platin', 'multiplier' => 3];

    /**
     * Get user total points
     */
    public function getUserPoints($userId) {
        $sql = "SELECT SUM(points) as total_points
                FROM {$this->table}
                WHERE user_id = ?
                AND (expires_at IS NULL OR expires_at > NOW())";

        $stmt = $this->query($sql, [$userId]);
        $result = $stmt->fetch();

        return $result['total_points'] ?? 0;
    }

    /**
     * Get user level
     */
    public function getUserLevel($userId) {
        $totalPoints = $this->getUserPoints($userId);

        if ($totalPoints >= self::LEVEL_PLATINUM['min']) {
            return self::LEVEL_PLATINUM;
        } elseif ($totalPoints >= self::LEVEL_GOLD['min']) {
            return self::LEVEL_GOLD;
        } elseif ($totalPoints >= self::LEVEL_SILVER['min']) {
            return self::LEVEL_SILVER;
        }

        return self::LEVEL_BRONZE;
    }

    /**
     * Award points for purchase
     */
    public function awardPurchasePoints($userId, $orderAmount, $orderId) {
        $level = $this->getUserLevel($userId);
        $basePoints = floor($orderAmount * self::POINTS_PER_TRY);
        $points = floor($basePoints * $level['multiplier']);

        return $this->addPoints($userId, $points, 'purchase', "Sipariş #$orderId için puan", $orderId);
    }

    /**
     * Award signup bonus
     */
    public function awardSignupBonus($userId) {
        return $this->addPoints($userId, self::SIGNUP_BONUS, 'signup', 'Hoş geldin bonusu');
    }

    /**
     * Award birthday bonus
     */
    public function awardBirthdayBonus($userId) {
        // Check if already given this year
        $year = date('Y');
        $sql = "SELECT COUNT(*) FROM {$this->table}
                WHERE user_id = ? AND type = 'birthday'
                AND YEAR(created_at) = ?";

        $stmt = $this->query($sql, [$userId, $year]);
        $count = $stmt->fetchColumn();

        if ($count == 0) {
            return $this->addPoints($userId, self::BIRTHDAY_BONUS, 'birthday', 'Doğum günü bonusu');
        }

        return false;
    }

    /**
     * Award review bonus
     */
    public function awardReviewBonus($userId, $productId) {
        return $this->addPoints($userId, self::REVIEW_BONUS, 'review', "Ürün yorumu için puan", $productId);
    }

    /**
     * Award referral bonus
     */
    public function awardReferralBonus($referrerId, $newUserId) {
        return $this->addPoints($referrerId, self::REFERRAL_BONUS, 'referral', "Arkadaşını davet et bonusu");
    }

    /**
     * Redeem points
     */
    public function redeemPoints($userId, $points, $orderId = null) {
        $availablePoints = $this->getUserPoints($userId);

        if ($availablePoints < $points) {
            return [
                'success' => false,
                'message' => 'Yetersiz puan bakiyesi'
            ];
        }

        $result = $this->addPoints($userId, -$points, 'redemption', "Puan kullanımı", $orderId);

        if ($result) {
            return [
                'success' => true,
                'message' => "$points puan başarıyla kullanıldı",
                'remaining_points' => $availablePoints - $points
            ];
        }

        return [
            'success' => false,
            'message' => 'Puan kullanımı başarısız'
        ];
    }

    /**
     * Add points to user
     */
    private function addPoints($userId, $points, $type, $description = '', $referenceId = null) {
        $expiresAt = null;

        // Set expiration (1 year)
        if ($points > 0) {
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 year'));
        }

        $data = [
            'user_id' => $userId,
            'points' => $points,
            'type' => $type,
            'description' => $description,
            'reference_id' => $referenceId,
            'expires_at' => $expiresAt
        ];

        return $this->insert($data);
    }

    /**
     * Get points history
     */
    public function getPointsHistory($userId, $limit = 50) {
        $sql = "SELECT * FROM {$this->table}
                WHERE user_id = ?
                ORDER BY created_at DESC
                LIMIT ?";

        $stmt = $this->query($sql, [$userId, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Calculate discount from points
     */
    public function calculatePointsDiscount($points) {
        // 100 points = 10 TL discount
        return ($points / 100) * 10;
    }

    /**
     * Get leaderboard
     */
    public function getLeaderboard($limit = 10) {
        $sql = "SELECT
                    u.id,
                    u.first_name,
                    u.last_name,
                    SUM(lp.points) as total_points
                FROM users u
                INNER JOIN {$this->table} lp ON u.id = lp.user_id
                WHERE lp.expires_at IS NULL OR lp.expires_at > NOW()
                GROUP BY u.id
                ORDER BY total_points DESC
                LIMIT ?";

        $stmt = $this->query($sql, [$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Expire old points
     */
    public function expireOldPoints() {
        $sql = "UPDATE {$this->table}
                SET points = 0
                WHERE expires_at < NOW()
                AND points > 0";

        $stmt = $this->query($sql);
        return $stmt->execute();
    }

    /**
     * Get points summary
     */
    public function getPointsSummary($userId) {
        $totalPoints = $this->getUserPoints($userId);
        $level = $this->getUserLevel($userId);

        // Points to next level
        $nextLevel = $this->getNextLevel($level);
        $pointsToNextLevel = $nextLevel ? $nextLevel['min'] - $totalPoints : 0;

        // Calculate discount value
        $discountValue = $this->calculatePointsDiscount($totalPoints);

        return [
            'total_points' => $totalPoints,
            'current_level' => $level,
            'next_level' => $nextLevel,
            'points_to_next_level' => $pointsToNextLevel,
            'discount_value' => $discountValue,
            'multiplier' => $level['multiplier']
        ];
    }

    /**
     * Get next level
     */
    private function getNextLevel($currentLevel) {
        $levels = [self::LEVEL_BRONZE, self::LEVEL_SILVER, self::LEVEL_GOLD, self::LEVEL_PLATINUM];

        foreach ($levels as $level) {
            if ($level['min'] > $currentLevel['min']) {
                return $level;
            }
        }

        return null;
    }
}
