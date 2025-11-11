<?php
/**
 * User Model
 * MINALIA Parfüm E-Ticaret Platformu
 */

require_once __DIR__ . '/BaseModel.php';

class User extends BaseModel {
    protected $table = 'users';

    /**
     * Create new user
     */
    public function create($data) {
        // Hash password
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        // Generate verification token
        $data['verification_token'] = generateRandomString(64);

        return $this->insert($data);
    }

    /**
     * Find user by email
     */
    public function findByEmail($email) {
        return $this->findBy('email', $email);
    }

    /**
     * Verify user credentials
     */
    public function verifyCredentials($email, $password) {
        $user = $this->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    /**
     * Update last login
     */
    public function updateLastLogin($userId) {
        return $this->update($userId, [
            'last_login' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Generate password reset token
     */
    public function generateResetToken($email) {
        $user = $this->findByEmail($email);

        if (!$user) {
            return false;
        }

        $token = generateRandomString(64);
        $expire = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $this->update($user['id'], [
            'reset_token' => $token,
            'reset_token_expire' => $expire
        ]);

        return $token;
    }

    /**
     * Verify reset token
     */
    public function verifyResetToken($token) {
        $sql = "SELECT * FROM {$this->table}
                WHERE reset_token = ?
                AND reset_token_expire > NOW()
                AND is_active = 1";

        $stmt = $this->query($sql, [$token]);
        return $stmt->fetch();
    }

    /**
     * Reset password
     */
    public function resetPassword($token, $newPassword) {
        $user = $this->verifyResetToken($token);

        if (!$user) {
            return false;
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        return $this->update($user['id'], [
            'password' => $hashedPassword,
            'reset_token' => null,
            'reset_token_expire' => null
        ]);
    }

    /**
     * Change password
     */
    public function changePassword($userId, $currentPassword, $newPassword) {
        $user = $this->find($userId);

        if (!$user || !password_verify($currentPassword, $user['password'])) {
            return false;
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        return $this->update($userId, [
            'password' => $hashedPassword
        ]);
    }

    /**
     * Update profile
     */
    public function updateProfile($userId, $data) {
        // Remove sensitive fields
        unset($data['password'], $data['email'], $data['verification_token'], $data['reset_token']);

        return $this->update($userId, $data);
    }

    /**
     * Check if email exists
     */
    public function emailExists($email, $excludeUserId = null) {
        return $this->exists('email', $email, $excludeUserId);
    }

    /**
     * Activate user account
     */
    public function activate($token) {
        $user = $this->findBy('verification_token', $token);

        if (!$user) {
            return false;
        }

        return $this->update($user['id'], [
            'is_active' => true,
            'verification_token' => null
        ]);
    }

    /**
     * Get user statistics
     */
    public function getUserStats($userId) {
        $stats = [];

        // Total orders
        $sql = "SELECT COUNT(*) as total FROM orders WHERE user_id = ?";
        $stmt = $this->query($sql, [$userId]);
        $stats['total_orders'] = $stmt->fetchColumn();

        // Total spent
        $sql = "SELECT SUM(total) as total_spent FROM orders WHERE user_id = ? AND status != 'cancelled'";
        $stmt = $this->query($sql, [$userId]);
        $stats['total_spent'] = $stmt->fetchColumn() ?? 0;

        // Wishlist count
        $sql = "SELECT COUNT(*) as total FROM wishlist WHERE user_id = ?";
        $stmt = $this->query($sql, [$userId]);
        $stats['wishlist_count'] = $stmt->fetchColumn();

        // Reviews count
        $sql = "SELECT COUNT(*) as total FROM reviews WHERE user_id = ?";
        $stmt = $this->query($sql, [$userId]);
        $stats['reviews_count'] = $stmt->fetchColumn();

        return $stats;
    }
}
