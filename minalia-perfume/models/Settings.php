<?php

/**
 * Settings Model - Professional Configuration Management
 * Handles all system-wide settings with caching support
 */

class Settings {
    private $db;
    private static $cache = [];
    private static $cacheLoaded = false;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->loadCache();
    }

    /**
     * Load all settings into memory cache for performance
     */
    private function loadCache() {
        if (self::$cacheLoaded) {
            return;
        }

        try {
            $stmt = $this->db->query("SELECT setting_key, setting_value FROM settings");
            $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($settings as $setting) {
                self::$cache[$setting['setting_key']] = $setting['setting_value'];
            }

            self::$cacheLoaded = true;
        } catch (PDOException $e) {
            error_log("Settings cache load error: " . $e->getMessage());
        }
    }

    /**
     * Get a single setting value
     *
     * @param string $key Setting key
     * @param mixed $default Default value if not found
     * @return mixed Setting value
     */
    public function get($key, $default = null) {
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }

        try {
            $stmt = $this->db->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
            $stmt->execute([$key]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                self::$cache[$key] = $result['setting_value'];
                return $result['setting_value'];
            }
        } catch (PDOException $e) {
            error_log("Settings get error: " . $e->getMessage());
        }

        return $default;
    }

    /**
     * Get multiple settings by category
     *
     * @param string $category Settings category
     * @return array Array of settings
     */
    public function getByCategory($category) {
        try {
            $stmt = $this->db->prepare("SELECT setting_key, setting_value, setting_type FROM settings WHERE category = ? ORDER BY id");
            $stmt->execute([$category]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Settings getByCategory error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all settings grouped by category
     *
     * @return array Associative array of categories and their settings
     */
    public function getAllGrouped() {
        try {
            $stmt = $this->db->query("SELECT category, setting_key, setting_value, setting_type FROM settings ORDER BY category, id");
            $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $grouped = [];
            foreach ($settings as $setting) {
                $category = $setting['category'];
                if (!isset($grouped[$category])) {
                    $grouped[$category] = [];
                }
                $grouped[$category][$setting['setting_key']] = [
                    'value' => $setting['setting_value'],
                    'type' => $setting['setting_type']
                ];
            }

            return $grouped;
        } catch (PDOException $e) {
            error_log("Settings getAllGrouped error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Set/Update a setting value
     *
     * @param string $key Setting key
     * @param mixed $value Setting value
     * @return bool Success status
     */
    public function set($key, $value) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO settings (setting_key, setting_value)
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
            ");

            $result = $stmt->execute([$key, $value]);

            if ($result) {
                // Update cache
                self::$cache[$key] = $value;
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Settings set error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update multiple settings at once (bulk update)
     *
     * @param array $settings Associative array of key => value pairs
     * @return bool Success status
     */
    public function updateMultiple($settings) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO settings (setting_key, setting_value)
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
            ");

            foreach ($settings as $key => $value) {
                $stmt->execute([$key, $value]);
                // Update cache
                self::$cache[$key] = $value;
            }

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Settings updateMultiple error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a setting
     *
     * @param string $key Setting key
     * @return bool Success status
     */
    public function delete($key) {
        try {
            $stmt = $this->db->prepare("DELETE FROM settings WHERE setting_key = ?");
            $result = $stmt->execute([$key]);

            if ($result) {
                // Remove from cache
                unset(self::$cache[$key]);
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Settings delete error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if setting exists
     *
     * @param string $key Setting key
     * @return bool
     */
    public function exists($key) {
        if (isset(self::$cache[$key])) {
            return true;
        }

        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM settings WHERE setting_key = ?");
            $stmt->execute([$key]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Settings exists error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear settings cache
     */
    public function clearCache() {
        self::$cache = [];
        self::$cacheLoaded = false;
        $this->loadCache();
    }

    /**
     * Get boolean setting value
     *
     * @param string $key Setting key
     * @param bool $default Default value
     * @return bool
     */
    public function getBool($key, $default = false) {
        $value = $this->get($key, $default);
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Get integer setting value
     *
     * @param string $key Setting key
     * @param int $default Default value
     * @return int
     */
    public function getInt($key, $default = 0) {
        $value = $this->get($key, $default);
        return (int) $value;
    }

    /**
     * Get float setting value
     *
     * @param string $key Setting key
     * @param float $default Default value
     * @return float
     */
    public function getFloat($key, $default = 0.0) {
        $value = $this->get($key, $default);
        return (float) $value;
    }

    /**
     * Get JSON decoded setting value
     *
     * @param string $key Setting key
     * @param mixed $default Default value
     * @return mixed
     */
    public function getJson($key, $default = []) {
        $value = $this->get($key, null);
        if ($value === null) {
            return $default;
        }

        $decoded = json_decode($value, true);
        return $decoded !== null ? $decoded : $default;
    }

    /**
     * Set JSON encoded setting value
     *
     * @param string $key Setting key
     * @param mixed $value Value to encode
     * @return bool
     */
    public function setJson($key, $value) {
        $encoded = json_encode($value);
        return $this->set($key, $encoded);
    }
}
