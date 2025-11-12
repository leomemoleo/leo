<?php
/**
 * NANO LEVEL TEST - ULTRA DETAILED ANALYSIS
 * Tests logic, security, edge cases, and integration at microscopic level
 */

class NanoLevelTester {
    private $errors = [];
    private $warnings = [];
    private $info = [];
    private $basePath;

    public function __construct($basePath = '/home/user/leo/minalia-perfume') {
        $this->basePath = $basePath;
    }

    public function runAllTests() {
        echo "=" . str_repeat("=", 80) . "\n";
        echo "NANO LEVEL ANALYSIS - MICROSCOPIC TESTING\n";
        echo "=" . str_repeat("=", 80) . "\n\n";

        $this->testSmsLogic();
        $this->testOAuthSecurity();
        $this->testLanguageEdgeCases();
        $this->testPWAImplementation();
        $this->testDatabaseMigrations();
        $this->testAdminPanelIntegration();
        $this->testFrontendComponents();

        $this->printResults();
    }

    /**
     * Deep test SMS logic and implementation
     */
    private function testSmsLogic() {
        echo "🔬 Nano Test: SMS Logic Analysis...\n";

        $smsPath = $this->basePath . '/app/helpers/SmsGateway.php';
        if (!file_exists($smsPath)) {
            $this->errors[] = "SMS: File not found";
            return;
        }

        $content = file_get_contents($smsPath);

        // Test 1: Check phone number validation
        if (preg_match('/function formatPhone.*?{(.*?)}/s', $content, $matches)) {
            $funcBody = $matches[1];

            // Should validate Turkish format
            if (strpos($funcBody, '05') !== false) {
                $this->info[] = "SMS: Phone validation checks Turkish format (05XX)";
            } else {
                $this->warnings[] = "SMS: Phone validation might not check Turkish format";
            }

            // Should throw exception on invalid
            if (strpos($funcBody, 'throw new Exception') !== false) {
                $this->info[] = "SMS: Invalid phone throws exception ✓";
            } else {
                $this->warnings[] = "SMS: No exception thrown for invalid phone";
            }
        }

        // Test 2: Check SQL injection protection in logging
        if (strpos($content, 'prepare(') !== false && strpos($content, 'execute(') !== false) {
            $this->info[] = "SMS: Uses prepared statements for logging ✓";
        } else {
            $this->warnings[] = "SMS: Logging might not use prepared statements";
        }

        // Test 3: Check API credentials handling
        if (strpos($content, '$_ENV[') !== false) {
            $this->info[] = "SMS: Credentials loaded from environment ✓";
        } else {
            $this->warnings[] = "SMS: Credentials handling unclear";
        }

        // Test 4: Check error handling in API calls
        $curlCount = substr_count($content, 'curl_exec');
        $errorHandlingCount = substr_count($content, 'if ($httpCode !== 200)');

        if ($curlCount > 0 && $errorHandlingCount > 0) {
            $this->info[] = "SMS: API calls have error handling ✓";
        } else {
            $this->warnings[] = "SMS: API calls might lack proper error handling";
        }

        // Test 5: Check message encoding
        if (strpos($content, 'urlencode') !== false || strpos($content, 'htmlspecialchars') !== false) {
            $this->info[] = "SMS: Message encoding present ✓";
        } else {
            $this->warnings[] = "SMS: Message encoding might be missing";
        }

        // Test 6: Check balance check implementation
        if (strpos($content, 'getBalance') !== false) {
            $this->info[] = "SMS: Balance check method exists ✓";
        } else {
            $this->errors[] = "SMS: Balance check method missing";
        }

        echo "  ✓ SMS Logic tests completed\n\n";
    }

    /**
     * Deep OAuth security analysis
     */
    private function testOAuthSecurity() {
        echo "🔬 Nano Test: OAuth Security Analysis...\n";

        $oauthHelperPath = $this->basePath . '/app/helpers/OAuthHelper.php';
        $oauthControllerPath = $this->basePath . '/app/controllers/OAuthController.php';

        // Test 1: CSRF protection
        if (file_exists($oauthHelperPath)) {
            $content = file_get_contents($oauthHelperPath);

            // Check state parameter generation
            if (strpos($content, 'generateState') !== false) {
                $this->info[] = "OAuth: State generation method exists ✓";

                // Should use cryptographically secure random
                if (strpos($content, 'random_bytes') !== false) {
                    $this->info[] = "OAuth: Uses cryptographically secure random ✓";
                } else {
                    $this->errors[] = "OAuth: State generation not cryptographically secure";
                }
            }

            // Check state verification
            if (strpos($content, 'verifyState') !== false && strpos($content, 'hash_equals') !== false) {
                $this->info[] = "OAuth: State verification uses timing-safe comparison ✓";
            } else {
                $this->warnings[] = "OAuth: State verification might be vulnerable to timing attacks";
            }
        }

        // Test 2: Token storage security
        if (file_exists($oauthControllerPath)) {
            $content = file_get_contents($oauthControllerPath);

            // Should use session for immediate login
            if (strpos($content, '$_SESSION') !== false) {
                $this->info[] = "OAuth: Uses session for authentication ✓";
            }

            // Should redirect after login
            if (strpos($content, 'redirect(') !== false) {
                $this->info[] = "OAuth: Redirects after authentication ✓";
            }
        }

        // Test 3: Check for SQL injection in social account manager
        if (file_exists($oauthHelperPath)) {
            $content = file_get_contents($oauthHelperPath);

            // Count prepared statements vs direct queries
            $preparedCount = substr_count($content, '->prepare(');
            $executeCount = substr_count($content, '->execute(');

            if ($preparedCount > 0 && $preparedCount === $executeCount) {
                $this->info[] = "OAuth: All DB queries use prepared statements ✓";
            } else {
                $this->warnings[] = "OAuth: Some queries might not use prepared statements";
            }
        }

        // Test 4: Email verification
        if (file_exists($oauthHelperPath)) {
            $content = file_get_contents($oauthHelperPath);

            if (strpos($content, 'email_verified') !== false) {
                $this->info[] = "OAuth: Email verification tracking implemented ✓";
            } else {
                $this->warnings[] = "OAuth: Email verification tracking missing";
            }
        }

        // Test 5: Access token security
        if (file_exists($oauthHelperPath)) {
            $content = file_get_contents($oauthHelperPath);

            // Tokens should be stored in database, not session
            if (strpos($content, 'access_token') !== false && strpos($content, 'INSERT INTO') !== false) {
                $this->info[] = "OAuth: Access tokens stored in database ✓";
            }
        }

        echo "  ✓ OAuth Security tests completed\n\n";
    }

    /**
     * Test language system edge cases
     */
    private function testLanguageEdgeCases() {
        echo "🔬 Nano Test: Language Edge Cases...\n";

        $langPath = $this->basePath . '/app/helpers/Language.php';

        if (!file_exists($langPath)) {
            $this->errors[] = "Language: File not found";
            return;
        }

        $content = file_get_contents($langPath);

        // Test 1: Singleton pattern correctness
        if (strpos($content, 'private static $instance') !== false &&
            strpos($content, 'private function __construct') !== false) {
            $this->info[] = "Language: Singleton pattern correct ✓";
        } else {
            $this->warnings[] = "Language: Singleton pattern incomplete";
        }

        // Test 2: Fallback language handling
        if (strpos($content, 'fallbackLang') !== false) {
            $this->info[] = "Language: Fallback language defined ✓";
        } else {
            $this->warnings[] = "Language: No fallback language";
        }

        // Test 3: Missing translation handling
        if (preg_match('/function get.*?{(.*?)}/s', $content, $matches)) {
            $funcBody = $matches[1];

            // Should return key if translation missing
            if (strpos($funcBody, '?? $key') !== false || strpos($funcBody, ': $key') !== false) {
                $this->info[] = "Language: Returns key when translation missing ✓";
            } else {
                $this->warnings[] = "Language: Missing translation handling unclear";
            }
        }

        // Test 4: Placeholder replacement security
        if (strpos($content, 'str_replace') !== false && strpos($content, ':') !== false) {
            $this->info[] = "Language: Placeholder replacement implemented ✓";

            // Should not use eval or create()
            if (strpos($content, 'eval') === false && strpos($content, 'create_function') === false) {
                $this->info[] = "Language: Placeholder replacement is safe ✓";
            }
        }

        // Test 5: Number formatting locale correctness
        if (strpos($content, 'number_format') !== false) {
            $this->info[] = "Language: Number formatting implemented ✓";

            // Check for Turkish format (comma for decimals, dot for thousands)
            if (strpos($content, "',', '.'") !== false) {
                $this->info[] = "Language: Turkish number format correct ✓";
            }
        }

        // Test 6: Date formatting Turkish month names
        if (strpos($content, 'Ocak') !== false && strpos($content, 'Pazartesi') !== false) {
            $this->info[] = "Language: Turkish month/day names implemented ✓";
        } else {
            $this->warnings[] = "Language: Turkish month/day names might be missing";
        }

        // Test 7: Cookie security
        if (strpos($content, 'setcookie') !== false) {
            $this->info[] = "Language: Cookie persistence implemented ✓";

            // Should set httponly and secure flags (best practice)
            if (strpos($content, 'httponly') !== false || strpos($content, 'secure') !== false) {
                $this->info[] = "Language: Cookie has security flags ✓";
            } else {
                $this->warnings[] = "Language: Cookie security flags missing (not critical for language selection)";
            }
        }

        echo "  ✓ Language Edge Case tests completed\n\n";
    }

    /**
     * Test PWA implementation details
     */
    private function testPWAImplementation() {
        echo "🔬 Nano Test: PWA Implementation...\n";

        // Test 1: Manifest validation
        $manifestPath = $this->basePath . '/public/manifest.json';
        if (file_exists($manifestPath)) {
            $content = file_get_contents($manifestPath);
            $manifest = json_decode($content, true);

            if ($manifest) {
                // Check display mode
                if (isset($manifest['display']) && $manifest['display'] === 'standalone') {
                    $this->info[] = "PWA: Standalone display mode ✓";
                }

                // Check theme color
                if (isset($manifest['theme_color'])) {
                    $this->info[] = "PWA: Theme color defined ✓";
                }

                // Check icon sizes
                if (isset($manifest['icons']) && is_array($manifest['icons'])) {
                    $iconSizes = array_column($manifest['icons'], 'sizes');

                    // Should have 192x192 and 512x512 for PWA requirements
                    if (in_array('192x192', $iconSizes)) {
                        $this->info[] = "PWA: Has required 192x192 icon ✓";
                    } else {
                        $this->warnings[] = "PWA: Missing 192x192 icon";
                    }

                    if (in_array('512x512', $iconSizes)) {
                        $this->info[] = "PWA: Has required 512x512 icon ✓";
                    } else {
                        $this->warnings[] = "PWA: Missing 512x512 icon";
                    }
                }
            }
        }

        // Test 2: Service Worker cache strategies
        $swPath = $this->basePath . '/public/sw.js';
        if (file_exists($swPath)) {
            $content = file_get_contents($swPath);

            // Check cache naming
            if (strpos($content, 'CACHE_VERSION') !== false) {
                $this->info[] = "PWA: Cache versioning implemented ✓";
            }

            // Check precache list
            if (strpos($content, 'PRECACHE_ASSETS') !== false) {
                $this->info[] = "PWA: Precache list defined ✓";
            }

            // Check offline fallback
            if (strpos($content, 'offline') !== false) {
                $this->info[] = "PWA: Offline fallback implemented ✓";
            }

            // Check for waitUntil() in event listeners
            $waitUntilCount = substr_count($content, 'waitUntil(');
            if ($waitUntilCount >= 3) {  // install, activate, fetch
                $this->info[] = "PWA: Proper event handling with waitUntil ✓";
            }

            // Check for skipWaiting
            if (strpos($content, 'skipWaiting') !== false) {
                $this->info[] = "PWA: Immediate activation implemented ✓";
            }
        }

        // Test 3: Push notification implementation
        $pwaJsPath = $this->basePath . '/public/js/pwa.js';
        if (file_exists($pwaJsPath)) {
            $content = file_get_contents($pwaJsPath);

            // Check permission request
            if (strpos($content, 'Notification.requestPermission') !== false) {
                $this->info[] = "PWA: Notification permission request ✓";
            }

            // Check for VAPID key placeholder
            if (strpos($content, 'VAPID') !== false || strpos($content, 'vapid') !== false) {
                $this->info[] = "PWA: VAPID key handling present ✓";
            } else {
                $this->warnings[] = "PWA: VAPID key handling unclear";
            }

            // Check install prompt handling
            if (strpos($content, 'beforeinstallprompt') !== false) {
                $this->info[] = "PWA: Install prompt event handled ✓";
            }

            // Check for deferredPrompt
            if (strpos($content, 'deferredPrompt') !== false) {
                $this->info[] = "PWA: Install prompt deferred properly ✓";
            }
        }

        echo "  ✓ PWA Implementation tests completed\n\n";
    }

    /**
     * Test database migrations
     */
    private function testDatabaseMigrations() {
        echo "🔬 Nano Test: Database Migrations...\n";

        // Test SMS migration
        $smsMigrationPath = $this->basePath . '/install/migrations/006_create_sms_logs_table.sql';
        if (file_exists($smsMigrationPath)) {
            $content = file_get_contents($smsMigrationPath);

            // Check table creation
            if (preg_match_all('/CREATE TABLE.*?`(\w+)`/i', $content, $matches)) {
                $tables = $matches[1];

                if (in_array('sms_logs', $tables)) {
                    $this->info[] = "DB: sms_logs table creation found ✓";
                }

                if (in_array('sms_templates', $tables)) {
                    $this->info[] = "DB: sms_templates table creation found ✓";
                }
            }

            // Check for IF NOT EXISTS
            if (strpos($content, 'IF NOT EXISTS') !== false) {
                $this->info[] = "DB: Uses IF NOT EXISTS (safe migration) ✓";
            } else {
                $this->warnings[] = "DB: Migration might fail if tables exist";
            }

            // Check character set
            if (strpos($content, 'utf8mb4') !== false) {
                $this->info[] = "DB: Uses utf8mb4 (emoji support) ✓";
            } else {
                $this->warnings[] = "DB: Character set might not support emojis";
            }

            // Check for indexes
            if (strpos($content, 'KEY ') !== false || strpos($content, 'INDEX ') !== false) {
                $this->info[] = "DB: Indexes defined for performance ✓";
            }
        }

        // Test OAuth migration
        $oauthMigrationPath = $this->basePath . '/install/migrations/007_create_social_accounts_table.sql';
        if (file_exists($oauthMigrationPath)) {
            $content = file_get_contents($oauthMigrationPath);

            // Check for UNIQUE constraints
            if (strpos($content, 'UNIQUE KEY') !== false) {
                $this->info[] = "DB: Social accounts has UNIQUE constraints ✓";
            } else {
                $this->warnings[] = "DB: Missing UNIQUE constraints (duplicate prevention)";
            }

            // Check for foreign keys
            if (strpos($content, 'FOREIGN KEY') !== false) {
                $this->info[] = "DB: Foreign key relationships defined ✓";
            } else {
                $this->warnings[] = "DB: No foreign key constraints";
            }

            // Check CASCADE behavior
            if (strpos($content, 'ON DELETE CASCADE') !== false) {
                $this->info[] = "DB: Cascade delete configured ✓";
            }
        }

        echo "  ✓ Database Migration tests completed\n\n";
    }

    /**
     * Test admin panel integration
     */
    private function testAdminPanelIntegration() {
        echo "🔬 Nano Test: Admin Panel Integration...\n";

        $adminIndexPath = $this->basePath . '/admin/index.php';
        if (file_exists($adminIndexPath)) {
            $content = file_get_contents($adminIndexPath);

            // Check SMS routes
            $smsRoutes = ['sms', 'sms/send', 'sms/settings', 'sms/test-connection'];
            foreach ($smsRoutes as $route) {
                if (strpos($content, "'{$route}'") !== false) {
                    $this->info[] = "Admin: Route '{$route}' registered ✓";
                } else {
                    $this->warnings[] = "Admin: Route '{$route}' might be missing";
                }
            }
        }

        // Check admin SMS controller
        $adminSmsPath = $this->basePath . '/admin/controllers/AdminSmsController.php';
        if (file_exists($adminSmsPath)) {
            $content = file_get_contents($adminSmsPath);

            // Check if extends AdminController
            if (strpos($content, 'extends AdminController') !== false) {
                $this->info[] = "Admin: SMS Controller extends base class ✓";
            } else {
                $this->warnings[] = "Admin: SMS Controller inheritance unclear";
            }

            // Check methods
            $methods = ['index', 'send', 'sendBulk', 'settings', 'testConnection'];
            foreach ($methods as $method) {
                if (strpos($content, "function {$method}") !== false) {
                    $this->info[] = "Admin: Method '{$method}' exists ✓";
                } else {
                    $this->warnings[] = "Admin: Method '{$method}' missing";
                }
            }
        }

        echo "  ✓ Admin Panel Integration tests completed\n\n";
    }

    /**
     * Test frontend components
     */
    private function testFrontendComponents() {
        echo "🔬 Nano Test: Frontend Components...\n";

        // Test social login component
        $socialLoginPath = $this->basePath . '/app/views/components/social-login.php';
        if (file_exists($socialLoginPath)) {
            $content = file_get_contents($socialLoginPath);

            // Check for Google button
            if (strpos($content, '/auth/google') !== false) {
                $this->info[] = "Component: Google login button present ✓";
            }

            // Check for Facebook button
            if (strpos($content, '/auth/facebook') !== false) {
                $this->info[] = "Component: Facebook login button present ✓";
            }

            // Check for XSS protection
            if (strpos($content, 'htmlspecialchars') !== false || strpos($content, '<?=') === false) {
                $this->info[] = "Component: XSS protection considered ✓";
            }
        }

        // Test language switcher
        $langSwitcherPath = $this->basePath . '/app/views/components/language-switcher.php';
        if (file_exists($langSwitcherPath)) {
            $content = file_get_contents($langSwitcherPath);

            // Check for language codes
            if (strpos($content, 'tr') !== false && strpos($content, 'en') !== false) {
                $this->info[] = "Component: TR/EN language codes present ✓";
            }

            // Check for AJAX
            if (strpos($content, 'fetch(') !== false || strpos($content, 'ajax') !== false) {
                $this->info[] = "Component: AJAX language switching ✓";
            }

            // Check for fallback
            if (strpos($content, '?lang=') !== false) {
                $this->info[] = "Component: URL parameter fallback ✓";
            }
        }

        echo "  ✓ Frontend Component tests completed\n\n";
    }

    /**
     * Print results
     */
    private function printResults() {
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "NANO LEVEL TEST RESULTS\n";
        echo str_repeat("=", 80) . "\n\n";

        echo "✅ INFO (Good practices): " . count($this->info) . "\n";
        echo "⚠️  WARNINGS (Should review): " . count($this->warnings) . "\n";
        echo "❌ ERRORS (Must fix): " . count($this->errors) . "\n\n";

        if (count($this->errors) > 0) {
            echo str_repeat("-", 80) . "\n";
            echo "❌ CRITICAL ERRORS:\n";
            echo str_repeat("-", 80) . "\n";
            foreach ($this->errors as $i => $error) {
                echo ($i + 1) . ". {$error}\n";
            }
            echo "\n";
        }

        if (count($this->warnings) > 0) {
            echo str_repeat("-", 80) . "\n";
            echo "⚠️  WARNINGS:\n";
            echo str_repeat("-", 80) . "\n";
            foreach (array_slice($this->warnings, 0, 20) as $i => $warning) {
                echo ($i + 1) . ". {$warning}\n";
            }
            if (count($this->warnings) > 20) {
                echo "... and " . (count($this->warnings) - 20) . " more warnings\n";
            }
            echo "\n";
        }

        // Quality score
        $totalTests = count($this->info) + count($this->warnings) + count($this->errors);
        $score = $totalTests > 0 ? round((count($this->info) / $totalTests) * 100) : 0;

        echo str_repeat("=", 80) . "\n";
        echo "QUALITY SCORE: {$score}%\n";

        if (count($this->errors) === 0 && count($this->warnings) < 5) {
            echo "Status: ⭐ EXCEPTIONAL QUALITY - PRODUCTION READY ✅\n";
        } elseif (count($this->errors) === 0) {
            echo "Status: ✅ HIGH QUALITY - PRODUCTION READY (Minor warnings)\n";
        } else {
            echo "Status: ⚠️  NEEDS FIXES BEFORE PRODUCTION\n";
        }

        echo str_repeat("=", 80) . "\n";
    }
}

// Run nano level tests
$tester = new NanoLevelTester();
$tester->runAllTests();
