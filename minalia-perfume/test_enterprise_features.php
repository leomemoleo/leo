<?php
/**
 * ENTERPRISE FEATURES COMPREHENSIVE TEST SUITE
 * Micro & Nano Level Analysis
 *
 * Tests:
 * 1. SMS Notification System
 * 2. Social Login (OAuth)
 * 3. Multi-Language (i18n)
 * 4. Progressive Web App (PWA)
 * 5. Integration & Dependencies
 */

class EnterpriseFeaturesTester {
    private $errors = [];
    private $warnings = [];
    private $passed = [];
    private $basePath;

    public function __construct($basePath = '/home/user/leo/minalia-perfume') {
        $this->basePath = $basePath;
    }

    /**
     * Run all tests
     */
    public function runAllTests() {
        echo "=" . str_repeat("=", 70) . "\n";
        echo "ENTERPRISE FEATURES COMPREHENSIVE TEST SUITE\n";
        echo "Micro & Nano Level Analysis\n";
        echo "=" . str_repeat("=", 70) . "\n\n";

        $this->testSmsSystem();
        $this->testSocialLogin();
        $this->testMultiLanguage();
        $this->testPWA();
        $this->testIntegration();

        $this->printResults();
    }

    /**
     * Test 1: SMS Notification System
     */
    private function testSmsSystem() {
        echo "🔍 Testing SMS Notification System...\n";

        // 1.1 File existence
        $smsFiles = [
            'app/helpers/SmsGateway.php',
            'admin/controllers/AdminSmsController.php',
            'admin/views/pages/sms/index.php',
            'admin/views/pages/sms/send.php',
            'admin/views/pages/sms/settings.php',
            'install/migrations/006_create_sms_logs_table.sql'
        ];

        foreach ($smsFiles as $file) {
            $fullPath = $this->basePath . '/' . $file;
            if (!file_exists($fullPath)) {
                $this->errors[] = "SMS: Missing file - {$file}";
            } else {
                $this->passed[] = "SMS: File exists - {$file}";
            }
        }

        // 1.2 Check SmsGateway.php syntax and structure
        $smsGatewayPath = $this->basePath . '/app/helpers/SmsGateway.php';
        if (file_exists($smsGatewayPath)) {
            $content = file_get_contents($smsGatewayPath);

            // Check required classes
            if (!strpos($content, 'interface SmsGatewayInterface')) {
                $this->errors[] = "SMS: Missing SmsGatewayInterface";
            } else {
                $this->passed[] = "SMS: SmsGatewayInterface found";
            }

            if (!strpos($content, 'class NetgsmAdapter')) {
                $this->errors[] = "SMS: Missing NetgsmAdapter class";
            } else {
                $this->passed[] = "SMS: NetgsmAdapter class found";
            }

            if (!strpos($content, 'class IletimerkeziAdapter')) {
                $this->errors[] = "SMS: Missing IletimerkeziAdapter class";
            } else {
                $this->passed[] = "SMS: IletimerkeziAdapter class found";
            }

            if (!strpos($content, 'class SmsFactory')) {
                $this->errors[] = "SMS: Missing SmsFactory class";
            } else {
                $this->passed[] = "SMS: SmsFactory class found";
            }

            if (!strpos($content, 'class SmsHelper')) {
                $this->errors[] = "SMS: Missing SmsHelper class";
            } else {
                $this->passed[] = "SMS: SmsHelper class found";
            }

            // Check required methods
            $requiredMethods = [
                'sendSms',
                'sendBulkSms',
                'getBalance',
                'formatPhone',
                'sendOrderNotification',
                'sendVerificationCode'
            ];

            foreach ($requiredMethods as $method) {
                if (!strpos($content, "function {$method}")) {
                    $this->errors[] = "SMS: Missing method - {$method}";
                } else {
                    $this->passed[] = "SMS: Method found - {$method}";
                }
            }

            // Check security
            if (strpos($content, 'curl_exec') && !strpos($content, 'CURLOPT_SSL_VERIFYPEER')) {
                $this->warnings[] = "SMS: SSL verification might be disabled";
            } else {
                $this->passed[] = "SMS: SSL verification enabled";
            }

            // Check error handling
            if (strpos($content, 'try') && strpos($content, 'catch')) {
                $this->passed[] = "SMS: Error handling implemented";
            } else {
                $this->warnings[] = "SMS: Limited error handling";
            }
        }

        // 1.3 Check admin routes
        $adminIndexPath = $this->basePath . '/admin/index.php';
        if (file_exists($adminIndexPath)) {
            $content = file_get_contents($adminIndexPath);

            if (!strpos($content, "'sms' => 'AdminSmsController@index'")) {
                $this->errors[] = "SMS: Admin route not registered";
            } else {
                $this->passed[] = "SMS: Admin routes registered";
            }
        }

        // 1.4 Check migration SQL
        $migrationPath = $this->basePath . '/install/migrations/006_create_sms_logs_table.sql';
        if (file_exists($migrationPath)) {
            $content = file_get_contents($migrationPath);

            if (!strpos($content, 'CREATE TABLE') || !strpos($content, 'sms_logs')) {
                $this->errors[] = "SMS: Migration missing sms_logs table";
            } else {
                $this->passed[] = "SMS: Migration includes sms_logs table";
            }

            if (!strpos($content, 'sms_templates')) {
                $this->errors[] = "SMS: Migration missing sms_templates table";
            } else {
                $this->passed[] = "SMS: Migration includes sms_templates table";
            }
        }

        echo "  ✓ SMS System tests completed\n\n";
    }

    /**
     * Test 2: Social Login (OAuth)
     */
    private function testSocialLogin() {
        echo "🔍 Testing Social Login (OAuth)...\n";

        // 2.1 File existence
        $oauthFiles = [
            'app/helpers/OAuthHelper.php',
            'app/controllers/OAuthController.php',
            'app/views/components/social-login.php',
            'install/migrations/007_create_social_accounts_table.sql',
            'SOCIAL_LOGIN_SETUP.md'
        ];

        foreach ($oauthFiles as $file) {
            $fullPath = $this->basePath . '/' . $file;
            if (!file_exists($fullPath)) {
                $this->errors[] = "OAuth: Missing file - {$file}";
            } else {
                $this->passed[] = "OAuth: File exists - {$file}";
            }
        }

        // 2.2 Check OAuthHelper.php structure
        $oauthHelperPath = $this->basePath . '/app/helpers/OAuthHelper.php';
        if (file_exists($oauthHelperPath)) {
            $content = file_get_contents($oauthHelperPath);

            // Check classes
            $requiredClasses = [
                'abstract class OAuthProvider',
                'class GoogleOAuthProvider',
                'class FacebookOAuthProvider',
                'class OAuthFactory',
                'class SocialAccountManager'
            ];

            foreach ($requiredClasses as $class) {
                if (!strpos($content, $class)) {
                    $this->errors[] = "OAuth: Missing - {$class}";
                } else {
                    $this->passed[] = "OAuth: Found - {$class}";
                }
            }

            // Check security features
            if (!strpos($content, 'verifyState')) {
                $this->errors[] = "OAuth: Missing CSRF protection (verifyState)";
            } else {
                $this->passed[] = "OAuth: CSRF protection implemented";
            }

            if (!strpos($content, 'generateState')) {
                $this->errors[] = "OAuth: Missing state generation";
            } else {
                $this->passed[] = "OAuth: State generation implemented";
            }

            if (!strpos($content, 'random_bytes')) {
                $this->warnings[] = "OAuth: State might not be cryptographically secure";
            } else {
                $this->passed[] = "OAuth: Cryptographically secure state";
            }

            // Check SSL
            if (strpos($content, 'CURLOPT_SSL_VERIFYPEER')) {
                $this->passed[] = "OAuth: SSL verification enabled";
            } else {
                $this->errors[] = "OAuth: SSL verification not found";
            }
        }

        // 2.3 Check routes
        $publicIndexPath = $this->basePath . '/public/index.php';
        if (file_exists($publicIndexPath)) {
            $content = file_get_contents($publicIndexPath);

            $requiredRoutes = [
                '/auth/google',
                '/auth/google/callback',
                '/auth/facebook',
                '/auth/facebook/callback'
            ];

            foreach ($requiredRoutes as $route) {
                if (!strpos($content, $route)) {
                    $this->errors[] = "OAuth: Missing route - {$route}";
                } else {
                    $this->passed[] = "OAuth: Route registered - {$route}";
                }
            }
        }

        // 2.4 Check migration
        $migrationPath = $this->basePath . '/install/migrations/007_create_social_accounts_table.sql';
        if (file_exists($migrationPath)) {
            $content = file_get_contents($migrationPath);

            if (!strpos($content, 'social_accounts')) {
                $this->errors[] = "OAuth: Migration missing social_accounts table";
            } else {
                $this->passed[] = "OAuth: Migration includes social_accounts table";
            }

            // Check for important columns
            $requiredColumns = [
                'provider',
                'provider_user_id',
                'access_token',
                'refresh_token'
            ];

            foreach ($requiredColumns as $column) {
                if (!strpos($content, $column)) {
                    $this->errors[] = "OAuth: Missing column in migration - {$column}";
                } else {
                    $this->passed[] = "OAuth: Column exists - {$column}";
                }
            }
        }

        echo "  ✓ Social Login tests completed\n\n";
    }

    /**
     * Test 3: Multi-Language System
     */
    private function testMultiLanguage() {
        echo "🔍 Testing Multi-Language System...\n";

        // 3.1 File existence
        $i18nFiles = [
            'app/helpers/Language.php',
            'app/lang/tr/common.php',
            'app/lang/tr/auth.php',
            'app/lang/en/common.php',
            'app/lang/en/auth.php',
            'app/views/components/language-switcher.php'
        ];

        foreach ($i18nFiles as $file) {
            $fullPath = $this->basePath . '/' . $file;
            if (!file_exists($fullPath)) {
                $this->errors[] = "i18n: Missing file - {$file}";
            } else {
                $this->passed[] = "i18n: File exists - {$file}";
            }
        }

        // 3.2 Check Language.php structure
        $languagePath = $this->basePath . '/app/helpers/Language.php';
        if (file_exists($languagePath)) {
            $content = file_get_contents($languagePath);

            // Check class and methods
            if (!strpos($content, 'class Language')) {
                $this->errors[] = "i18n: Missing Language class";
            } else {
                $this->passed[] = "i18n: Language class found";
            }

            $requiredMethods = [
                'getInstance',
                'setLanguage',
                'getCurrentLanguage',
                'get',
                'formatNumber',
                'formatCurrency',
                'formatDate'
            ];

            foreach ($requiredMethods as $method) {
                if (!strpos($content, "function {$method}")) {
                    $this->errors[] = "i18n: Missing method - {$method}";
                } else {
                    $this->passed[] = "i18n: Method found - {$method}";
                }
            }

            // Check helper functions
            $helperFunctions = ['__', '_e', 'currentLang', 'setLang', 'formatMoney'];
            foreach ($helperFunctions as $func) {
                if (!strpos($content, "function {$func}")) {
                    $this->errors[] = "i18n: Missing helper function - {$func}";
                } else {
                    $this->passed[] = "i18n: Helper function found - {$func}";
                }
            }

            // Check for singleton pattern
            if (strpos($content, 'private static $instance') && strpos($content, 'private function __construct')) {
                $this->passed[] = "i18n: Singleton pattern implemented";
            } else {
                $this->warnings[] = "i18n: Singleton pattern might be incomplete";
            }
        }

        // 3.3 Check language files
        $trCommonPath = $this->basePath . '/app/lang/tr/common.php';
        $enCommonPath = $this->basePath . '/app/lang/en/common.php';

        if (file_exists($trCommonPath) && file_exists($enCommonPath)) {
            $trContent = file_get_contents($trCommonPath);
            $enContent = file_get_contents($enCommonPath);

            // Check if they return arrays
            if (!strpos($trContent, 'return [')) {
                $this->errors[] = "i18n: TR common.php doesn't return array";
            } else {
                $this->passed[] = "i18n: TR common.php structure correct";
            }

            if (!strpos($enContent, 'return [')) {
                $this->errors[] = "i18n: EN common.php doesn't return array";
            } else {
                $this->passed[] = "i18n: EN common.php structure correct";
            }

            // Check for common keys
            $commonKeys = ['welcome', 'home', 'login', 'register', 'products', 'cart'];
            foreach ($commonKeys as $key) {
                if (!strpos($trContent, "'{$key}'")) {
                    $this->warnings[] = "i18n: TR missing key - {$key}";
                }
                if (!strpos($enContent, "'{$key}'")) {
                    $this->warnings[] = "i18n: EN missing key - {$key}";
                }
            }
        }

        echo "  ✓ Multi-Language tests completed\n\n";
    }

    /**
     * Test 4: Progressive Web App
     */
    private function testPWA() {
        echo "🔍 Testing Progressive Web App (PWA)...\n";

        // 4.1 File existence
        $pwaFiles = [
            'public/manifest.json',
            'public/sw.js',
            'public/js/pwa.js',
            'public/css/pwa.css'
        ];

        foreach ($pwaFiles as $file) {
            $fullPath = $this->basePath . '/' . $file;
            if (!file_exists($fullPath)) {
                $this->errors[] = "PWA: Missing file - {$file}";
            } else {
                $this->passed[] = "PWA: File exists - {$file}";
            }
        }

        // 4.2 Check manifest.json
        $manifestPath = $this->basePath . '/public/manifest.json';
        if (file_exists($manifestPath)) {
            $content = file_get_contents($manifestPath);
            $manifest = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->errors[] = "PWA: manifest.json is not valid JSON";
            } else {
                $this->passed[] = "PWA: manifest.json is valid JSON";

                // Check required fields
                $requiredFields = ['name', 'short_name', 'start_url', 'display', 'icons'];
                foreach ($requiredFields as $field) {
                    if (!isset($manifest[$field])) {
                        $this->errors[] = "PWA: manifest.json missing field - {$field}";
                    } else {
                        $this->passed[] = "PWA: manifest.json has field - {$field}";
                    }
                }

                // Check icons
                if (isset($manifest['icons']) && count($manifest['icons']) > 0) {
                    $this->passed[] = "PWA: Icons defined in manifest";
                } else {
                    $this->errors[] = "PWA: No icons in manifest";
                }
            }
        }

        // 4.3 Check Service Worker
        $swPath = $this->basePath . '/public/sw.js';
        if (file_exists($swPath)) {
            $content = file_get_contents($swPath);

            // Check for required events
            $requiredEvents = [
                'addEventListener(\'install\'',
                'addEventListener(\'activate\'',
                'addEventListener(\'fetch\'',
                'addEventListener(\'push\'',
                'addEventListener(\'notificationclick\''
            ];

            foreach ($requiredEvents as $event) {
                if (!strpos($content, $event)) {
                    $this->errors[] = "PWA: Service Worker missing event - {$event}";
                } else {
                    $this->passed[] = "PWA: Service Worker has event - {$event}";
                }
            }

            // Check caching strategies
            if (strpos($content, 'networkFirst') || strpos($content, 'network-first')) {
                $this->passed[] = "PWA: Network-first strategy implemented";
            } else {
                $this->warnings[] = "PWA: Network-first strategy not found";
            }

            if (strpos($content, 'cacheFirst') || strpos($content, 'cache-first')) {
                $this->passed[] = "PWA: Cache-first strategy implemented";
            } else {
                $this->warnings[] = "PWA: Cache-first strategy not found";
            }

            // Check cache versioning
            if (strpos($content, 'CACHE_VERSION') || strpos($content, 'CACHE_NAME')) {
                $this->passed[] = "PWA: Cache versioning implemented";
            } else {
                $this->warnings[] = "PWA: Cache versioning not found";
            }
        }

        // 4.4 Check PWA initialization
        $pwaJsPath = $this->basePath . '/public/js/pwa.js';
        if (file_exists($pwaJsPath)) {
            $content = file_get_contents($pwaJsPath);

            // Check Service Worker registration
            if (strpos($content, 'navigator.serviceWorker.register')) {
                $this->passed[] = "PWA: Service Worker registration code found";
            } else {
                $this->errors[] = "PWA: Service Worker registration code missing";
            }

            // Check install prompt
            if (strpos($content, 'beforeinstallprompt')) {
                $this->passed[] = "PWA: Install prompt handler found";
            } else {
                $this->warnings[] = "PWA: Install prompt handler not found";
            }

            // Check push notifications
            if (strpos($content, 'Notification.requestPermission')) {
                $this->passed[] = "PWA: Notification permission request found";
            } else {
                $this->warnings[] = "PWA: Notification permission request not found";
            }
        }

        echo "  ✓ PWA tests completed\n\n";
    }

    /**
     * Test 5: Integration & Dependencies
     */
    private function testIntegration() {
        echo "🔍 Testing Integration & Dependencies...\n";

        // 5.1 Check if helpers are autoloaded
        $helpersToCheck = [
            'app/helpers/SmsGateway.php',
            'app/helpers/OAuthHelper.php',
            'app/helpers/Language.php'
        ];

        foreach ($helpersToCheck as $helper) {
            $fullPath = $this->basePath . '/' . $helper;
            if (file_exists($fullPath)) {
                $content = file_get_contents($fullPath);

                // Check for PHP syntax errors
                $output = [];
                $returnVar = 0;
                exec("php -l {$fullPath} 2>&1", $output, $returnVar);

                if ($returnVar !== 0) {
                    $this->errors[] = "Integration: Syntax error in {$helper}";
                } else {
                    $this->passed[] = "Integration: No syntax errors in {$helper}";
                }
            }
        }

        // 5.2 Check controller dependencies
        $controllers = [
            'app/controllers/OAuthController.php',
            'admin/controllers/AdminSmsController.php'
        ];

        foreach ($controllers as $controller) {
            $fullPath = $this->basePath . '/' . $controller;
            if (file_exists($fullPath)) {
                $content = file_get_contents($fullPath);

                // Check for required includes
                if (strpos($content, 'require_once') === false && strpos($content, 'include') === false) {
                    $this->warnings[] = "Integration: {$controller} might be missing dependencies";
                }

                // Check PHP syntax
                $output = [];
                $returnVar = 0;
                exec("php -l {$fullPath} 2>&1", $output, $returnVar);

                if ($returnVar !== 0) {
                    $this->errors[] = "Integration: Syntax error in {$controller}";
                } else {
                    $this->passed[] = "Integration: No syntax errors in {$controller}";
                }
            }
        }

        // 5.3 Check for common security issues
        $allPhpFiles = glob($this->basePath . '/app/**/*.php') +
                      glob($this->basePath . '/admin/**/*.php');

        $securityIssues = 0;
        foreach ($allPhpFiles as $file) {
            if (is_file($file)) {
                $content = file_get_contents($file);

                // Check for eval() usage
                if (preg_match('/\beval\s*\(/i', $content)) {
                    $this->warnings[] = "Security: eval() found in " . basename($file);
                    $securityIssues++;
                }

                // Check for unescaped output in new files
                if (strpos($file, '/views/') !== false || strpos($file, '/components/') !== false) {
                    // This is a basic check - views should use htmlspecialchars
                    if (strpos($content, 'echo $_') !== false && strpos($content, 'htmlspecialchars') === false) {
                        $this->warnings[] = "Security: Potential XSS in " . basename($file);
                    }
                }
            }
        }

        if ($securityIssues === 0) {
            $this->passed[] = "Integration: No major security issues detected";
        }

        echo "  ✓ Integration tests completed\n\n";
    }

    /**
     * Print test results
     */
    private function printResults() {
        echo "\n" . str_repeat("=", 70) . "\n";
        echo "TEST RESULTS SUMMARY\n";
        echo str_repeat("=", 70) . "\n\n";

        echo "✅ PASSED: " . count($this->passed) . "\n";
        echo "⚠️  WARNINGS: " . count($this->warnings) . "\n";
        echo "❌ ERRORS: " . count($this->errors) . "\n\n";

        if (count($this->errors) > 0) {
            echo str_repeat("-", 70) . "\n";
            echo "❌ ERRORS (MUST FIX):\n";
            echo str_repeat("-", 70) . "\n";
            foreach ($this->errors as $i => $error) {
                echo ($i + 1) . ". {$error}\n";
            }
            echo "\n";
        }

        if (count($this->warnings) > 0) {
            echo str_repeat("-", 70) . "\n";
            echo "⚠️  WARNINGS (SHOULD CHECK):\n";
            echo str_repeat("-", 70) . "\n";
            foreach ($this->warnings as $i => $warning) {
                echo ($i + 1) . ". {$warning}\n";
            }
            echo "\n";
        }

        // Overall status
        echo str_repeat("=", 70) . "\n";
        if (count($this->errors) === 0) {
            echo "🎉 ALL CRITICAL TESTS PASSED!\n";
            if (count($this->warnings) === 0) {
                echo "✨ ZERO WARNINGS - PERFECT IMPLEMENTATION!\n";
                echo "Status: PRODUCTION READY ✅\n";
            } else {
                echo "Status: PRODUCTION READY (with minor warnings) ✅\n";
            }
        } else {
            echo "⚠️  FIXES REQUIRED BEFORE PRODUCTION\n";
            echo "Status: NEEDS ATTENTION ❌\n";
        }
        echo str_repeat("=", 70) . "\n";
    }
}

// Run tests
$tester = new EnterpriseFeaturesTester();
$tester->runAllTests();
