<?php
/**
 * Language Manager
 * Enterprise i18n System for Multi-language Support
 * Supports: TR (Turkish), EN (English)
 */

class Language {
    private static $instance = null;
    private $currentLang = 'tr';
    private $fallbackLang = 'tr';
    private $translations = [];
    private $loadedFiles = [];

    /**
     * Singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Private constructor
     */
    private function __construct() {
        $this->detectLanguage();
        $this->loadCoreTranslations();
    }

    /**
     * Detect user's preferred language
     */
    private function detectLanguage() {
        // 1. Check URL parameter
        if (isset($_GET['lang'])) {
            $lang = strtolower($_GET['lang']);
            if ($this->isValidLanguage($lang)) {
                $this->setLanguage($lang);
                return;
            }
        }

        // 2. Check session
        if (isset($_SESSION['language'])) {
            $this->currentLang = $_SESSION['language'];
            return;
        }

        // 3. Check cookie
        if (isset($_COOKIE['site_language'])) {
            $lang = $_COOKIE['site_language'];
            if ($this->isValidLanguage($lang)) {
                $this->currentLang = $lang;
                return;
            }
        }

        // 4. Check browser language
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            if ($this->isValidLanguage($browserLang)) {
                $this->currentLang = $browserLang;
                return;
            }
        }

        // 5. Use fallback
        $this->currentLang = $this->fallbackLang;
    }

    /**
     * Load core translations
     */
    private function loadCoreTranslations() {
        $this->loadFile('common');
    }

    /**
     * Check if language is valid
     */
    private function isValidLanguage($lang) {
        return in_array($lang, ['tr', 'en']);
    }

    /**
     * Get available languages
     */
    public static function getAvailableLanguages() {
        return [
            'tr' => [
                'code' => 'tr',
                'name' => 'Türkçe',
                'native_name' => 'Türkçe',
                'flag' => '🇹🇷',
                'locale' => 'tr_TR'
            ],
            'en' => [
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'English',
                'flag' => '🇬🇧',
                'locale' => 'en_US'
            ]
        ];
    }

    /**
     * Set current language
     */
    public function setLanguage($lang) {
        if (!$this->isValidLanguage($lang)) {
            return false;
        }

        $this->currentLang = $lang;
        $_SESSION['language'] = $lang;

        // Set cookie for 30 days
        setcookie('site_language', $lang, time() + (30 * 24 * 60 * 60), '/');

        // Reload translations
        $this->translations = [];
        $this->loadedFiles = [];
        $this->loadCoreTranslations();

        return true;
    }

    /**
     * Get current language
     */
    public function getCurrentLanguage() {
        return $this->currentLang;
    }

    /**
     * Load translation file
     */
    public function loadFile($file) {
        // Check if already loaded
        if (in_array($file, $this->loadedFiles)) {
            return;
        }

        $filePath = __DIR__ . '/../lang/' . $this->currentLang . '/' . $file . '.php';

        if (!file_exists($filePath)) {
            // Try fallback language
            $filePath = __DIR__ . '/../lang/' . $this->fallbackLang . '/' . $file . '.php';

            if (!file_exists($filePath)) {
                return;
            }
        }

        $translations = include $filePath;

        if (is_array($translations)) {
            $this->translations = array_merge($this->translations, $translations);
            $this->loadedFiles[] = $file;
        }
    }

    /**
     * Get translation
     */
    public function get($key, $replace = []) {
        // Load file if key contains dot notation (file.key)
        if (strpos($key, '.') !== false) {
            list($file, $subkey) = explode('.', $key, 2);
            $this->loadFile($file);
            $key = $subkey;
        }

        // Get translation
        $translation = $this->translations[$key] ?? $key;

        // Replace placeholders
        if (!empty($replace)) {
            foreach ($replace as $placeholder => $value) {
                $translation = str_replace(':' . $placeholder, $value, $translation);
            }
        }

        return $translation;
    }

    /**
     * Check if translation exists
     */
    public function has($key) {
        if (strpos($key, '.') !== false) {
            list($file, $subkey) = explode('.', $key, 2);
            $this->loadFile($file);
            $key = $subkey;
        }

        return isset($this->translations[$key]);
    }

    /**
     * Get all loaded translations
     */
    public function all() {
        return $this->translations;
    }

    /**
     * Format number according to locale
     */
    public static function formatNumber($number, $decimals = 0) {
        $lang = self::getInstance()->getCurrentLanguage();

        if ($lang === 'tr') {
            return number_format($number, $decimals, ',', '.');
        }

        return number_format($number, $decimals, '.', ',');
    }

    /**
     * Format currency
     */
    public static function formatCurrency($amount, $currency = 'TRY') {
        $lang = self::getInstance()->getCurrentLanguage();
        $formatted = self::formatNumber($amount, 2);

        $symbols = [
            'TRY' => '₺',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£'
        ];

        $symbol = $symbols[$currency] ?? $currency;

        if ($lang === 'tr') {
            return $formatted . ' ' . $symbol;
        }

        return $symbol . $formatted;
    }

    /**
     * Format date according to locale
     */
    public static function formatDate($date, $format = 'medium') {
        $lang = self::getInstance()->getCurrentLanguage();
        $timestamp = is_numeric($date) ? $date : strtotime($date);

        $formats = [
            'tr' => [
                'short' => 'd.m.Y',
                'medium' => 'd F Y',
                'long' => 'd F Y, l',
                'full' => 'd F Y, l H:i'
            ],
            'en' => [
                'short' => 'm/d/Y',
                'medium' => 'F d, Y',
                'long' => 'l, F d, Y',
                'full' => 'l, F d, Y H:i'
            ]
        ];

        $formatString = $formats[$lang][$format] ?? $formats[$lang]['medium'];

        // Set Turkish month names
        if ($lang === 'tr') {
            $months = [
                'January' => 'Ocak', 'February' => 'Şubat', 'March' => 'Mart',
                'April' => 'Nisan', 'May' => 'Mayıs', 'June' => 'Haziran',
                'July' => 'Temmuz', 'August' => 'Ağustos', 'September' => 'Eylül',
                'October' => 'Ekim', 'November' => 'Kasım', 'December' => 'Aralık'
            ];

            $days = [
                'Monday' => 'Pazartesi', 'Tuesday' => 'Salı', 'Wednesday' => 'Çarşamba',
                'Thursday' => 'Perşembe', 'Friday' => 'Cuma', 'Saturday' => 'Cumartesi',
                'Sunday' => 'Pazar'
            ];

            $formatted = date($formatString, $timestamp);

            foreach ($months as $en => $tr) {
                $formatted = str_replace($en, $tr, $formatted);
            }

            foreach ($days as $en => $tr) {
                $formatted = str_replace($en, $tr, $formatted);
            }

            return $formatted;
        }

        return date($formatString, $timestamp);
    }

    /**
     * Pluralize string (basic implementation)
     */
    public static function plural($key, $count, $replace = []) {
        $translation = self::getInstance()->get($key, $replace);

        // Simple pluralization
        if ($count > 1) {
            $translation .= self::getInstance()->get('s', []);
        }

        return $translation;
    }
}

/**
 * Global helper functions
 */

/**
 * Translate string
 */
function __($key, $replace = []) {
    return Language::getInstance()->get($key, $replace);
}

/**
 * Translate and echo
 */
function _e($key, $replace = []) {
    echo Language::getInstance()->get($key, $replace);
}

/**
 * Get current language
 */
function currentLang() {
    return Language::getInstance()->getCurrentLanguage();
}

/**
 * Set language
 */
function setLang($lang) {
    return Language::getInstance()->setLanguage($lang);
}

/**
 * Format number
 */
function formatNumber($number, $decimals = 0) {
    return Language::formatNumber($number, $decimals);
}

/**
 * Format currency
 */
function formatMoney($amount, $currency = 'TRY') {
    return Language::formatCurrency($amount, $currency);
}

/**
 * Format date
 */
function formatDate($date, $format = 'medium') {
    return Language::formatDate($date, $format);
}

/**
 * Get available languages
 */
function getLanguages() {
    return Language::getAvailableLanguages();
}
