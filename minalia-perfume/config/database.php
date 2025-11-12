<?php
/**
 * Database Configuration and Connection Handler  
 * MINALIA Parfüm E-Ticaret Platformu
 */

class Database {
    private static $instance = null;
    private $connection;

    private $host;
    private $dbname;
    private $username;
    private $password;
    private $charset;

    /**
     * Constructor - Loads configuration
     */
    private function __construct() {
        // Load config.php if exists (installed via wizard)
        $configFile = dirname(__DIR__) . '/config/config.php';
        if (file_exists($configFile)) {
            require_once $configFile;
        }

        // Use constants from config.php if available, otherwise use defaults
        $this->host = defined('DB_HOST') ? DB_HOST : 'localhost';
        $this->dbname = defined('DB_NAME') ? DB_NAME : 'minalia_perfume';
        $this->username = defined('DB_USER') ? DB_USER : 'root';
        $this->password = defined('DB_PASS') ? DB_PASS : '';
        $this->charset = 'utf8mb4';
    }

    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get PDO connection
     */
    public function getConnection() {
        if ($this->connection === null) {
            try {
                $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";

                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}"
                ];

                $this->connection = new PDO($dsn, $this->username, $this->password, $options);

            } catch (PDOException $e) {
                $this->handleError($e);
            }
        }

        return $this->connection;
    }

    /**
     * Handle database errors
     */
    private function handleError($e) {
        $debugMode = defined('DEBUG_MODE') ? DEBUG_MODE : false;

        if ($debugMode) {
            die("Database Error: " . $e->getMessage());
        } else {
            error_log("Database Error: " . $e->getMessage());
            die("Bir hata oluştu. Lütfen daha sonra tekrar deneyin.");
        }
    }

    /**
     * Close connection
     */
    public function closeConnection() {
        $this->connection = null;
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}

    /**
     * Prevent unserialization
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

/**
 * Helper function to get database connection
 */
function getDB() {
    return Database::getInstance()->getConnection();
}
