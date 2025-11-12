<?php
/**
 * MINALIA Installation Wizard
 * WordPress-style Easy Installation
 */

session_start();

class InstallController {
    private $step;
    private $errors = [];
    private $success = [];

    public function __construct() {
        // Check if already installed
        if ($this->isInstalled()) {
            die('MINALIA is already installed. Please delete install.lock file to reinstall.');
        }

        $this->step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
    }

    /**
     * Main installation router
     */
    public function run() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost();
        }

        $this->renderStep();
    }

    /**
     * Handle POST requests for each step
     */
    private function handlePost() {
        switch ($this->step) {
            case 2:
                $this->handleDatabaseConfig();
                break;
            case 3:
                $this->handleSiteConfig();
                break;
            case 4:
                $this->handleInstallation();
                break;
        }
    }

    /**
     * Step 1: Welcome & System Requirements
     */
    private function renderStep() {
        include __DIR__ . '/views/layout-header.php';

        switch ($this->step) {
            case 1:
                $this->checkRequirements();
                include __DIR__ . '/views/step1-welcome.php';
                break;
            case 2:
                include __DIR__ . '/views/step2-database.php';
                break;
            case 3:
                include __DIR__ . '/views/step3-siteconfig.php';
                break;
            case 4:
                include __DIR__ . '/views/step4-install.php';
                break;
            case 5:
                include __DIR__ . '/views/step5-complete.php';
                break;
        }

        include __DIR__ . '/views/layout-footer.php';
    }

    /**
     * Check system requirements
     */
    private function checkRequirements() {
        $requirements = [
            'PHP Version >= 7.4' => version_compare(PHP_VERSION, '7.4.0', '>='),
            'PDO Extension' => extension_loaded('pdo'),
            'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
            'GD Extension (Images)' => extension_loaded('gd'),
            'cURL Extension' => extension_loaded('curl'),
            'JSON Extension' => extension_loaded('json'),
            'Writable: config/' => is_writable(__DIR__ . '/../config'),
            'Writable: public/uploads/' => is_writable(__DIR__ . '/../public/uploads'),
        ];

        $_SESSION['requirements'] = $requirements;
        $_SESSION['all_passed'] = !in_array(false, $requirements);
    }

    /**
     * Handle database configuration
     */
    private function handleDatabaseConfig() {
        $dbHost = $_POST['db_host'] ?? 'localhost';
        $dbName = $_POST['db_name'] ?? '';
        $dbUser = $_POST['db_user'] ?? 'root';
        $dbPass = $_POST['db_pass'] ?? '';

        // Validate database name (security: prevent SQL injection)
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $dbName)) {
            $this->errors[] = 'Invalid database name. Only letters, numbers and underscores allowed.';
            return;
        }

        // Test database connection
        try {
            $dsn = "mysql:host={$dbHost};charset=utf8mb4";
            $pdo = new PDO($dsn, $dbUser, $dbPass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Create database if not exists (safe: validated above)
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // Test connection to the database (safe: validated above)
            $pdo->exec("USE `{$dbName}`");

            // Save to session
            $_SESSION['db_config'] = [
                'host' => $dbHost,
                'name' => $dbName,
                'user' => $dbUser,
                'pass' => $dbPass
            ];

            $this->success[] = 'Database connection successful!';
            $this->step = 3;
            $_GET['step'] = 3;

        } catch (PDOException $e) {
            $this->errors[] = 'Database Error: ' . $e->getMessage();
        }
    }

    /**
     * Handle site configuration
     */
    private function handleSiteConfig() {
        $_SESSION['site_config'] = [
            'site_name' => $_POST['site_name'] ?? 'MINALIA Parfüm',
            'site_url' => $_POST['site_url'] ?? '',
            'admin_email' => $_POST['admin_email'] ?? '',
            'admin_username' => $_POST['admin_username'] ?? 'admin',
            'admin_password' => $_POST['admin_password'] ?? '',
            'install_demo' => isset($_POST['install_demo'])
        ];

        $this->step = 4;
        $_GET['step'] = 4;
    }

    /**
     * Handle final installation
     */
    private function handleInstallation() {
        try {
            $dbConfig = $_SESSION['db_config'];
            $siteConfig = $_SESSION['site_config'];

            // Connect to database
            $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['name']};charset=utf8mb4";
            $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Install database schema
            $this->installSchema($pdo);

            // Install settings
            $this->installSettings($pdo, $siteConfig);

            // Create admin user
            $this->createAdmin($pdo, $siteConfig);

            // Install demo data if selected
            if ($siteConfig['install_demo']) {
                $this->installDemoData($pdo);
            }

            // Create config file
            $this->createConfigFile($dbConfig, $siteConfig);

            // Create lock file
            $this->createLockFile();

            // Clear session
            session_destroy();

            $this->step = 5;
            $_GET['step'] = 5;

        } catch (Exception $e) {
            $this->errors[] = 'Installation Error: ' . $e->getMessage();
        }
    }

    /**
     * Install database schema
     */
    private function installSchema($pdo) {
        // Use complete schema that includes all tables
        $schemaFile = __DIR__ . '/../database/complete_schema.sql';
        if (!file_exists($schemaFile)) {
            // Fallback to basic schema
            $schemaFile = __DIR__ . '/../database/schema.sql';
        }

        if (!file_exists($schemaFile)) {
            throw new Exception('Schema file not found');
        }

        $sql = file_get_contents($schemaFile);

        // Remove USE database and CREATE DATABASE statements as we're already connected
        $sql = preg_replace('/CREATE DATABASE.*?;/is', '', $sql);
        $sql = preg_replace('/USE\s+`?[\w_]+`?;/i', '', $sql);

        // Execute schema (split by statements)
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
    }

    /**
     * Install default settings
     */
    private function installSettings($pdo, $siteConfig) {
        $settingsFile = __DIR__ . '/../database/settings_table.sql';
        if (file_exists($settingsFile)) {
            $sql = file_get_contents($settingsFile);
            $pdo->exec($sql);

            // Update site settings
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->execute([$siteConfig['site_name'], 'site_name']);
            $stmt->execute([$siteConfig['admin_email'], 'contact_email']);
        }
    }

    /**
     * Create admin user
     */
    private function createAdmin($pdo, $siteConfig) {
        $passwordHash = password_hash($siteConfig['admin_password'], PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO admins (username, email, password, full_name, is_active, created_at)
            VALUES (?, ?, ?, ?, 1, NOW())
        ");

        $stmt->execute([
            $siteConfig['admin_username'],
            $siteConfig['admin_email'],
            $passwordHash,
            'Site Administrator'
        ]);
    }

    /**
     * Install demo data
     */
    private function installDemoData($pdo) {
        $demoFile = __DIR__ . '/../database/demo_data.sql';
        if (file_exists($demoFile)) {
            $sql = file_get_contents($demoFile);
            $pdo->exec($sql);
        }
    }

    /**
     * Create config file
     */
    private function createConfigFile($dbConfig, $siteConfig) {
        $baseUrl = rtrim($siteConfig['site_url'], '/');

        $config = "<?php
/**
 * MINALIA Configuration File
 * Auto-generated by Installation Wizard
 */

// Database Configuration
define('DB_HOST', '{$dbConfig['host']}');
define('DB_NAME', '{$dbConfig['name']}');
define('DB_USER', '{$dbConfig['user']}');
define('DB_PASS', '{$dbConfig['pass']}');

// Site Configuration
define('BASE_URL', '{$baseUrl}');
define('SITE_NAME', '{$siteConfig['site_name']}');

// Security
define('SESSION_PREFIX', 'minalia_');
define('SESSION_ADMIN_ID', SESSION_PREFIX . 'admin_id');
define('SESSION_ADMIN_USERNAME', SESSION_PREFIX . 'admin_username');
define('SESSION_ADMIN_EMAIL', SESSION_PREFIX . 'admin_email');
define('SESSION_USER_ID', SESSION_PREFIX . 'user_id');
define('SESSION_USER_EMAIL', SESSION_PREFIX . 'user_email');

// File Upload
define('UPLOAD_PATH', __DIR__ . '/../public/uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

// Validation Patterns
define('PATTERN_EMAIL', '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');
define('PATTERN_PHONE', '/^[\d\s\-\+\(\)]+$/');

// Environment
define('ENVIRONMENT', 'production'); // production, development
define('DEBUG_MODE', false);

// Timezone
date_default_timezone_set('Europe/Istanbul');
";

        file_put_contents(__DIR__ . '/../config/config.php', $config);
    }

    /**
     * Create lock file to prevent reinstallation
     */
    private function createLockFile() {
        $lockContent = "Installation completed on: " . date('Y-m-d H:i:s') . "\n";
        $lockContent .= "DO NOT DELETE THIS FILE. It prevents reinstallation.\n";
        file_put_contents(__DIR__ . '/install.lock', $lockContent);
    }

    /**
     * Check if already installed
     */
    private function isInstalled() {
        return file_exists(__DIR__ . '/install.lock');
    }

    /**
     * Get errors
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Get success messages
     */
    public function getSuccess() {
        return $this->success;
    }
}

// Run installer
$installer = new InstallController();
$installer->run();
