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
     * Constructor - Loads environment variables
     */
    private function __construct() {
        $this->loadEnv();

        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->dbname = $_ENV['DB_NAME'] ?? 'minalia_perfume';
        $this->username = $_ENV['DB_USER'] ?? 'root';
        $this->password = $_ENV['DB_PASS'] ?? '';
        $this->charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';
    }

    /**
     * Load environment variables from .env file
     */
    private function loadEnv() {
        $envFile = dirname(__DIR__) . '/.env';

        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }

                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);

                    // Remove quotes if present
                    if (preg_match('/^(["\'])(.*)\\1$/', $value, $matches)) {
                        $value = $matches[2];
                    }

                    $_ENV[$key] = $value;
                    putenv("$key=$value");
                }
            }
        }
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
        if ($_ENV['DEBUG_MODE'] ?? false) {
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
