#!/usr/bin/env php
<?php
/**
 * MINALIA ULTRA DEEP TEST - İĞNE DELİĞİ SEVİYESİ
 * En küçük hatayı bile algılayan kapsamlı test sistemi
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

class UltraDeepTest {
    private $errors = [];
    private $warnings = [];
    private $info = [];
    private $totalTests = 0;

    private $baseDir;
    private $phpFiles = [];
    private $sqlFiles = [];

    public function __construct() {
        $this->baseDir = __DIR__;
        $this->scanFiles();
    }

    private function scanFiles() {
        // Scan all PHP files
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->baseDir)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $ext = strtolower($file->getExtension());
                $path = $file->getPathname();

                // Skip vendor, node_modules, cache
                if (strpos($path, '/vendor/') !== false ||
                    strpos($path, '/node_modules/') !== false ||
                    strpos($path, '/.git/') !== false ||
                    strpos($path, '/cache/') !== false) {
                    continue;
                }

                if ($ext === 'php') {
                    $this->phpFiles[] = $path;
                } elseif ($ext === 'sql') {
                    $this->sqlFiles[] = $path;
                }
            }
        }
    }

    public function runAllTests() {
        $this->printHeader();

        // 1. PHP Syntax & Security
        $this->testPhpSyntax();
        $this->testSecurityVulnerabilities();
        $this->testSqlInjectionRisks();
        $this->testXssVulnerabilities();
        $this->testCsrfProtection();

        // 2. Code Quality
        $this->testCodeSmells();
        $this->testFunctionComplexity();
        $this->testVariableUsage();
        $this->testErrorHandling();

        // 3. Database
        $this->testDatabaseQueries();
        $this->testNPlusOneQueries();
        $this->testMissingIndexes();

        // 4. File & Directory
        $this->testFilePermissions();
        $this->testConfigFiles();
        $this->testUploadDirectories();

        // 5. Dependencies
        $this->testMissingDependencies();
        $this->testCircularDependencies();

        // 6. Performance
        $this->testPerformanceIssues();
        $this->testMemoryLeaks();

        // 7. Best Practices
        $this->testHardcodedCredentials();
        $this->testDebugCode();
        $this->testDeprecatedFunctions();

        $this->printSummary();
    }

    private function testPhpSyntax() {
        echo "\n🔍 TEST 1: PHP Syntax Validation (Ultra-Strict)\n";
        echo str_repeat('─', 60) . "\n";

        foreach ($this->phpFiles as $file) {
            $this->totalTests++;

            // Check syntax
            exec("php -l " . escapeshellarg($file) . " 2>&1", $output, $return);

            if ($return !== 0) {
                $this->errors[] = "Syntax error in: " . $this->relativePath($file);
            }

            // Check for parse errors
            $content = file_get_contents($file);

            // Missing semicolons before closing braces
            if (preg_match('/[a-zA-Z0-9_\)\]]\s*\n\s*\}/', $content)) {
                $this->warnings[] = "Possible missing semicolon in: " . $this->relativePath($file);
            }

            // Unclosed brackets
            $openBrackets = substr_count($content, '{');
            $closeBrackets = substr_count($content, '}');
            if ($openBrackets !== $closeBrackets) {
                $this->errors[] = "Unmatched brackets in: " . $this->relativePath($file) .
                    " (open: $openBrackets, close: $closeBrackets)";
            }

            // Trailing whitespace
            if (preg_match('/[ \t]+$/m', $content)) {
                $this->info[] = "Trailing whitespace found in: " . $this->relativePath($file);
            }

            // Mixed line endings
            if (strpos($content, "\r\n") !== false && strpos($content, "\n") !== false) {
                $this->warnings[] = "Mixed line endings in: " . $this->relativePath($file);
            }
        }

        echo "   ✓ Tested " . count($this->phpFiles) . " PHP files\n";
    }

    private function testSecurityVulnerabilities() {
        echo "\n🔒 TEST 2: Security Vulnerabilities (Critical)\n";
        echo str_repeat('─', 60) . "\n";

        foreach ($this->phpFiles as $file) {
            $content = file_get_contents($file);
            $relPath = $this->relativePath($file);

            // eval() usage
            if (preg_match('/\beval\s*\(/i', $content)) {
                $this->errors[] = "CRITICAL: eval() found in: $relPath";
            }

            // exec/shell_exec without escaping
            if (preg_match('/\b(exec|shell_exec|system|passthru)\s*\([^\)]*\$/', $content)) {
                $lines = $this->findMatchingLines($content, '/\b(exec|shell_exec|system|passthru)\s*\([^\)]*\$/');
                foreach ($lines as $line => $match) {
                    if (strpos($match, 'escapeshellarg') === false &&
                        strpos($match, 'escapeshellcmd') === false) {
                        $this->errors[] = "CRITICAL: Unescaped shell command in $relPath:$line";
                    }
                }
            }

            // Direct $_GET/$_POST usage in queries
            if (preg_match('/query\s*\([^)]*\$_(GET|POST|REQUEST|COOKIE)/i', $content)) {
                $this->errors[] = "CRITICAL: Potential SQL injection in: $relPath (direct \$_GET/\$_POST in query)";
            }

            // include/require with variable without validation
            if (preg_match('/(include|require)(_once)?\s*\([^\)]*\$_(GET|POST|REQUEST|COOKIE)/i', $content)) {
                $this->errors[] = "CRITICAL: File inclusion vulnerability in: $relPath";
            }

            // Unserialize with user input
            if (preg_match('/unserialize\s*\([^\)]*\$_(GET|POST|REQUEST|COOKIE)/i', $content)) {
                $this->errors[] = "CRITICAL: Unsafe unserialize in: $relPath";
            }

            // MD5/SHA1 for passwords
            if (preg_match('/md5\s*\([^\)]*password/i', $content) ||
                preg_match('/sha1\s*\([^\)]*password/i', $content)) {
                $this->errors[] = "CRITICAL: Weak password hashing (MD5/SHA1) in: $relPath";
            }
        }

        echo "   ✓ Scanned for security vulnerabilities\n";
    }

    private function testSqlInjectionRisks() {
        echo "\n💉 TEST 3: SQL Injection Analysis\n";
        echo str_repeat('─', 60) . "\n";

        foreach ($this->phpFiles as $file) {
            $content = file_get_contents($file);
            $relPath = $this->relativePath($file);

            // Find all SQL queries
            preg_match_all('/(SELECT|INSERT|UPDATE|DELETE|CREATE|DROP|ALTER).*?(FROM|INTO|TABLE|SET|WHERE)/is',
                $content, $matches, PREG_OFFSET_CAPTURE);

            foreach ($matches[0] as $match) {
                $query = $match[0];
                $position = $match[1];

                // Check for string concatenation with variables
                if (preg_match('/["\']\s*\.\s*\$/', $query) ||
                    preg_match('/\$\w+\s*\./', $query)) {

                    // Get line number
                    $lineNum = substr_count(substr($content, 0, $position), "\n") + 1;

                    // Check if prepare/execute is used nearby
                    $contextStart = max(0, $position - 200);
                    $contextEnd = min(strlen($content), $position + 200);
                    $context = substr($content, $contextStart, $contextEnd - $contextStart);

                    if (strpos($context, '->prepare(') === false &&
                        strpos($context, '->execute(') === false) {
                        $this->errors[] = "SQL Injection risk in $relPath:$lineNum - Query uses concatenation without prepared statements";
                    }
                }
            }
        }

        echo "   ✓ Analyzed SQL injection risks\n";
    }

    private function testXssVulnerabilities() {
        echo "\n🎭 TEST 4: XSS Vulnerability Detection\n";
        echo str_repeat('─', 60) . "\n";

        $viewFiles = array_filter($this->phpFiles, function($file) {
            return strpos($file, '/views/') !== false || strpos($file, '/templates/') !== false;
        });

        foreach ($viewFiles as $file) {
            $content = file_get_contents($file);
            $relPath = $this->relativePath($file);

            // Direct echo of variables without escaping
            preg_match_all('/echo\s+\$([a-zA-Z_][a-zA-Z0-9_\[\]\'\"]*);/', $content, $matches, PREG_OFFSET_CAPTURE);

            foreach ($matches[0] as $idx => $match) {
                $varName = $matches[1][$idx][0];
                $position = $match[1];
                $lineNum = substr_count(substr($content, 0, $position), "\n") + 1;

                // Check if htmlspecialchars or similar is used
                $contextStart = max(0, $position - 50);
                $contextEnd = min(strlen($content), $position + 50);
                $context = substr($content, $contextStart, $contextEnd - $contextStart);

                if (strpos($context, 'htmlspecialchars') === false &&
                    strpos($context, 'htmlentities') === false &&
                    strpos($context, 'esc_html') === false) {
                    $this->warnings[] = "Potential XSS in $relPath:$lineNum - Unescaped variable: \$$varName";
                }
            }

            // <?= without escaping
            preg_match_all('/<\?=\s*\$([a-zA-Z_][a-zA-Z0-9_\[\]\'\"]*)\s*\?>/', $content, $matches, PREG_OFFSET_CAPTURE);

            foreach ($matches[0] as $idx => $match) {
                $varName = $matches[1][$idx][0];
                $position = $match[1];
                $lineNum = substr_count(substr($content, 0, $position), "\n") + 1;

                // Check context
                $contextStart = max(0, $position - 100);
                $contextEnd = min(strlen($content), $position + 20);
                $context = substr($content, $contextStart, $contextEnd - $contextStart);

                if (strpos($context, 'htmlspecialchars') === false &&
                    strpos($context, 'htmlentities') === false) {
                    $this->warnings[] = "Potential XSS in $relPath:$lineNum - Unescaped output: \$$varName";
                }
            }
        }

        echo "   ✓ Scanned " . count($viewFiles) . " view files for XSS\n";
    }

    private function testCsrfProtection() {
        echo "\n🛡️  TEST 5: CSRF Protection Validation\n";
        echo str_repeat('─', 60) . "\n";

        foreach ($this->phpFiles as $file) {
            $content = file_get_contents($file);
            $relPath = $this->relativePath($file);

            // Check POST handlers
            if (preg_match('/\$_SERVER\[.REQUEST_METHOD.\]\s*===?\s*["\']POST["\']/i', $content)) {
                // Look for CSRF token validation
                if (strpos($content, 'csrf') === false &&
                    strpos($content, 'token') === false &&
                    strpos($content, 'nonce') === false) {
                    $this->warnings[] = "Missing CSRF protection in POST handler: $relPath";
                }
            }
        }

        echo "   ✓ Checked CSRF protection\n";
    }

    private function testCodeSmells() {
        echo "\n👃 TEST 6: Code Smells & Anti-Patterns\n";
        echo str_repeat('─', 60) . "\n";

        foreach ($this->phpFiles as $file) {
            $content = file_get_contents($file);
            $relPath = $this->relativePath($file);

            // Long functions (> 100 lines)
            preg_match_all('/function\s+\w+\s*\([^)]*\)\s*\{/', $content, $matches, PREG_OFFSET_CAPTURE);
            foreach ($matches[0] as $match) {
                $start = $match[1];
                $bracketCount = 1;
                $i = $start + strlen($match[0]);
                $length = strlen($content);

                while ($bracketCount > 0 && $i < $length) {
                    if ($content[$i] === '{') $bracketCount++;
                    if ($content[$i] === '}') $bracketCount--;
                    $i++;
                }

                $functionContent = substr($content, $start, $i - $start);
                $lines = substr_count($functionContent, "\n");

                if ($lines > 100) {
                    $lineNum = substr_count(substr($content, 0, $start), "\n") + 1;
                    $this->warnings[] = "Long function ($lines lines) in $relPath:$lineNum";
                }
            }

            // Nested loops > 3 levels
            if (preg_match_all('/(for|foreach|while)\s*\(/', $content, $matches)) {
                // Simple heuristic for deep nesting
                $maxNesting = 0;
                $currentNesting = 0;

                for ($i = 0; $i < strlen($content); $i++) {
                    if (preg_match('/(for|foreach|while)\s*\(/', substr($content, $i, 15))) {
                        $currentNesting++;
                        $maxNesting = max($maxNesting, $currentNesting);
                    }
                    if ($content[$i] === '}') {
                        $currentNesting = max(0, $currentNesting - 1);
                    }
                }

                if ($maxNesting > 3) {
                    $this->warnings[] = "Deep loop nesting (level $maxNesting) in: $relPath";
                }
            }

            // Magic numbers
            if (preg_match_all('/\b(\d{4,})\b/', $content, $matches)) {
                foreach (array_unique($matches[1]) as $number) {
                    if ($number != '1000' && $number != '2000') { // Common page sizes
                        $this->info[] = "Magic number '$number' in: $relPath";
                    }
                }
            }

            // die() or exit() in middle of code
            if (preg_match('/\b(die|exit)\s*\(/i', $content)) {
                $lines = $this->findMatchingLines($content, '/\b(die|exit)\s*\(/i');
                if (count($lines) > 2) {
                    $this->warnings[] = "Multiple die/exit statements in: $relPath";
                }
            }
        }

        echo "   ✓ Analyzed code quality\n";
    }

    private function testFunctionComplexity() {
        echo "\n🧮 TEST 7: Cyclomatic Complexity\n";
        echo str_repeat('─', 60) . "\n";

        foreach ($this->phpFiles as $file) {
            $content = file_get_contents($file);
            $relPath = $this->relativePath($file);

            // Extract functions
            preg_match_all('/function\s+(\w+)\s*\([^)]*\)\s*\{/', $content, $matches, PREG_OFFSET_CAPTURE);

            foreach ($matches[0] as $idx => $match) {
                $funcName = $matches[1][$idx][0];
                $start = $match[1];

                // Find function end
                $bracketCount = 1;
                $i = $start + strlen($match[0]);
                $length = strlen($content);

                while ($bracketCount > 0 && $i < $length) {
                    if ($content[$i] === '{') $bracketCount++;
                    if ($content[$i] === '}') $bracketCount--;
                    $i++;
                }

                $functionContent = substr($content, $start, $i - $start);

                // Calculate complexity (simplified)
                $complexity = 1; // Base complexity
                $complexity += substr_count($functionContent, 'if ');
                $complexity += substr_count($functionContent, 'elseif');
                $complexity += substr_count($functionContent, 'for ');
                $complexity += substr_count($functionContent, 'foreach');
                $complexity += substr_count($functionContent, 'while ');
                $complexity += substr_count($functionContent, 'case ');
                $complexity += substr_count($functionContent, '&&');
                $complexity += substr_count($functionContent, '||');

                if ($complexity > 15) {
                    $lineNum = substr_count(substr($content, 0, $start), "\n") + 1;
                    $this->warnings[] = "High complexity (CC=$complexity) in function '$funcName' at $relPath:$lineNum";
                }
            }
        }

        echo "   ✓ Calculated function complexity\n";
    }

    private function testVariableUsage() {
        echo "\n📝 TEST 8: Variable Usage Analysis\n";
        echo str_repeat('─', 60) . "\n";

        foreach ($this->phpFiles as $file) {
            $content = file_get_contents($file);
            $relPath = $this->relativePath($file);

            // Undefined variable usage (basic check)
            if (preg_match('/\$\$/', $content)) {
                $this->warnings[] = "Variable variable (\$\$) used in: $relPath";
            }

            // Global variables
            if (preg_match_all('/global\s+\$/', $content, $matches)) {
                if (count($matches[0]) > 0) {
                    $this->info[] = count($matches[0]) . " global variable(s) in: $relPath";
                }
            }

            // Unused parameters (simple check)
            preg_match_all('/function\s+\w+\s*\(([^)]+)\)/', $content, $funcMatches);
            foreach ($funcMatches[1] as $params) {
                if (preg_match_all('/\$(\w+)/', $params, $paramMatches)) {
                    foreach ($paramMatches[1] as $param) {
                        if (substr_count($content, '$' . $param) === 1) {
                            $this->info[] = "Possibly unused parameter '\$$param' in: $relPath";
                        }
                    }
                }
            }
        }

        echo "   ✓ Analyzed variable usage\n";
    }

    private function testErrorHandling() {
        echo "\n⚠️  TEST 9: Error Handling Patterns\n";
        echo str_repeat('─', 60) . "\n";

        foreach ($this->phpFiles as $file) {
            $content = file_get_contents($file);
            $relPath = $this->relativePath($file);

            // Try-catch without catch
            if (preg_match('/try\s*\{[^}]*\}\s*$/m', $content)) {
                $this->warnings[] = "Try block without catch in: $relPath";
            }

            // Empty catch blocks
            if (preg_match('/catch\s*\([^)]+\)\s*\{\s*\}/i', $content)) {
                $this->warnings[] = "Empty catch block in: $relPath";
            }

            // Error suppression (@)
            if (preg_match('/@\$/', $content) || preg_match('/@\w+\(/', $content)) {
                $count = substr_count($content, '@');
                if ($count > 3) {
                    $this->warnings[] = "Excessive error suppression (@) in: $relPath ($count occurrences)";
                }
            }
        }

        echo "   ✓ Checked error handling\n";
    }

    private function testDatabaseQueries() {
        echo "\n🗄️  TEST 10: Database Query Optimization\n";
        echo str_repeat('─', 60) . "\n";

        foreach ($this->phpFiles as $file) {
            $content = file_get_contents($file);
            $relPath = $this->relativePath($file);

            // SELECT *
            if (preg_match_all('/SELECT\s+\*\s+FROM/i', $content, $matches)) {
                $this->info[] = count($matches[0]) . " SELECT * queries in: $relPath (consider selecting specific columns)";
            }

            // Missing LIMIT in queries
            preg_match_all('/SELECT.*?FROM[^;]*/is', $content, $matches);
            foreach ($matches[0] as $query) {
                if (stripos($query, 'LIMIT') === false && strlen($query) < 300) {
                    $this->info[] = "Query without LIMIT in: $relPath";
                    break;
                }
            }

            // Queries in loops
            if (preg_match('/(for|foreach|while)\s*\([^)]*\)\s*\{[^}]*->(query|prepare|execute)/is', $content)) {
                $this->warnings[] = "Database query inside loop in: $relPath (potential N+1 problem)";
            }
        }

        echo "   ✓ Analyzed database queries\n";
    }

    private function testNPlusOneQueries() {
        echo "\n🔁 TEST 11: N+1 Query Detection\n";
        echo str_repeat('─', 60) . "\n";

        foreach ($this->phpFiles as $file) {
            $content = file_get_contents($file);
            $relPath = $this->relativePath($file);

            // Pattern: foreach with query inside
            if (preg_match_all('/foreach\s*\([^)]+as[^)]+\)\s*\{([^}]*)\}/is', $content, $matches)) {
                foreach ($matches[1] as $loopContent) {
                    if (preg_match('/->(query|prepare|execute|fetchAll)/i', $loopContent)) {
                        $this->warnings[] = "Potential N+1 query in: $relPath";
                        break;
                    }
                }
            }
        }

        echo "   ✓ Detected N+1 query patterns\n";
    }

    private function testMissingIndexes() {
        echo "\n📇 TEST 12: Database Index Suggestions\n";
        echo str_repeat('─', 60) . "\n";

        foreach ($this->phpFiles as $file) {
            $content = file_get_contents($file);
            $relPath = $this->relativePath($file);

            // Look for WHERE clauses
            if (preg_match_all('/WHERE\s+([a-z_]+)\s*=/i', $content, $matches)) {
                $columns = array_unique($matches[1]);
                foreach ($columns as $col) {
                    if (!in_array($col, ['id', 'created_at', 'updated_at'])) {
                        $this->info[] = "Consider index on column '$col' in: $relPath";
                    }
                }
            }
        }

        echo "   ✓ Suggested database indexes\n";
    }

    private function testFilePermissions() {
        echo "\n🔐 TEST 13: File Permission Security\n";
        echo str_repeat('─', 60) . "\n";

        $sensitiveFiles = [
            $this->baseDir . '/config/config.php',
            $this->baseDir . '/config/database.php',
            $this->baseDir . '/.env',
        ];

        foreach ($sensitiveFiles as $file) {
            if (file_exists($file)) {
                $perms = fileperms($file);
                $octal = substr(sprintf('%o', $perms), -4);

                // Check if world-readable
                if ($perms & 0x0004) {
                    $this->warnings[] = "World-readable config file: " . $this->relativePath($file) . " (perms: $octal)";
                }
            }
        }

        // Check upload directories
        $uploadDirs = [
            $this->baseDir . '/public/uploads/',
            $this->baseDir . '/uploads/',
        ];

        foreach ($uploadDirs as $dir) {
            if (is_dir($dir)) {
                $perms = fileperms($dir);
                $octal = substr(sprintf('%o', $perms), -4);

                if ($perms & 0x0002) {
                    $this->errors[] = "World-writable upload directory: " . $this->relativePath($dir) . " (perms: $octal)";
                }
            }
        }

        echo "   ✓ Checked file permissions\n";
    }

    private function testConfigFiles() {
        echo "\n⚙️  TEST 14: Configuration Validation\n";
        echo str_repeat('─', 60) . "\n";

        $configFiles = [
            $this->baseDir . '/config/config.php',
            $this->baseDir . '/config/constants.php',
            $this->baseDir . '/config/database.php',
        ];

        foreach ($configFiles as $file) {
            if (file_exists($file)) {
                $content = file_get_contents($file);

                // Check for default/insecure values
                if (preg_match('/DB_PASS["\']?\s*,\s*["\']["\']/', $content)) {
                    $this->warnings[] = "Empty database password in: " . $this->relativePath($file);
                }

                if (preg_match('/DEBUG_MODE["\']?\s*,\s*true/i', $content)) {
                    $this->warnings[] = "Debug mode enabled in: " . $this->relativePath($file);
                }
            }
        }

        echo "   ✓ Validated configuration files\n";
    }

    private function testUploadDirectories() {
        echo "\n📂 TEST 15: Upload Directory Security\n";
        echo str_repeat('─', 60) . "\n";

        $uploadDirs = [
            $this->baseDir . '/public/uploads/',
            $this->baseDir . '/uploads/',
        ];

        foreach ($uploadDirs as $dir) {
            if (is_dir($dir)) {
                // Check for .htaccess
                $htaccess = $dir . '.htaccess';
                if (!file_exists($htaccess)) {
                    $this->warnings[] = "Missing .htaccess in upload directory: " . $this->relativePath($dir);
                }

                // Check for index.php
                $index = $dir . 'index.php';
                if (!file_exists($index) && !file_exists($dir . 'index.html')) {
                    $this->info[] = "Missing index file in upload directory: " . $this->relativePath($dir);
                }
            }
        }

        echo "   ✓ Checked upload directories\n";
    }

    private function testMissingDependencies() {
        echo "\n📦 TEST 16: Missing Dependencies\n";
        echo str_repeat('─', 60) . "\n";

        foreach ($this->phpFiles as $file) {
            $content = file_get_contents($file);
            $relPath = $this->relativePath($file);

            // Check for class usage
            if (preg_match_all('/new\s+([A-Z]\w+)/', $content, $matches)) {
                foreach (array_unique($matches[1]) as $class) {
                    // Check if class is defined or required
                    if (!class_exists($class, false)) {
                        $hasRequire = preg_match('/require.*' . $class . '\.php/i', $content);
                        if (!$hasRequire && !in_array($class, ['PDO', 'Exception', 'DateTime', 'stdClass'])) {
                            $this->info[] = "Class '$class' used but not required in: $relPath";
                        }
                    }
                }
            }
        }

        echo "   ✓ Checked dependencies\n";
    }

    private function testCircularDependencies() {
        echo "\n🔄 TEST 17: Circular Dependency Detection\n";
        echo str_repeat('─', 60) . "\n";

        $dependencies = [];

        foreach ($this->phpFiles as $file) {
            $content = file_get_contents($file);
            $relPath = $this->relativePath($file);

            // Extract require/include statements
            preg_match_all('/(require|include)(_once)?\s*[\'"]([^\'"]+)[\'"]/i', $content, $matches);

            if (!empty($matches[3])) {
                $dependencies[$relPath] = $matches[3];
            }
        }

        // Simple circular dependency check
        foreach ($dependencies as $file => $deps) {
            foreach ($deps as $dep) {
                if (isset($dependencies[$dep]) && in_array($file, $dependencies[$dep])) {
                    $this->warnings[] = "Circular dependency between '$file' and '$dep'";
                }
            }
        }

        echo "   ✓ Checked for circular dependencies\n";
    }

    private function testPerformanceIssues() {
        echo "\n⚡ TEST 18: Performance Anti-Patterns\n";
        echo str_repeat('─', 60) . "\n";

        foreach ($this->phpFiles as $file) {
            $content = file_get_contents($file);
            $relPath = $this->relativePath($file);

            // file_get_contents in loops
            if (preg_match('/(for|foreach|while)\s*\([^)]*\)\s*\{[^}]*file_get_contents/is', $content)) {
                $this->warnings[] = "file_get_contents() in loop in: $relPath";
            }

            // Multiple includes of same file
            if (preg_match_all('/(require|include)(_once)?\s*[\'"]([^\'"]+)[\'"]/i', $content, $matches)) {
                $includes = array_count_values($matches[3]);
                foreach ($includes as $inc => $count) {
                    if ($count > 2) {
                        $this->info[] = "File '$inc' included $count times in: $relPath";
                    }
                }
            }
        }

        echo "   ✓ Checked performance patterns\n";
    }

    private function testMemoryLeaks() {
        echo "\n💾 TEST 19: Memory Leak Detection\n";
        echo str_repeat('─', 60) . "\n";

        foreach ($this->phpFiles as $file) {
            $content = file_get_contents($file);
            $relPath = $this->relativePath($file);

            // Large arrays in loops
            if (preg_match('/(for|foreach|while)\s*\([^)]*\)\s*\{[^}]*(\[\]\s*=|\$\w+\s*=\s*array)/is', $content)) {
                if (stripos($content, 'unset') === false) {
                    $this->info[] = "Array building in loop without cleanup in: $relPath";
                }
            }
        }

        echo "   ✓ Checked for memory leaks\n";
    }

    private function testHardcodedCredentials() {
        echo "\n🔑 TEST 20: Hardcoded Credentials\n";
        echo str_repeat('─', 60) . "\n";

        foreach ($this->phpFiles as $file) {
            $content = file_get_contents($file);
            $relPath = $this->relativePath($file);

            // Look for password/api_key patterns
            if (preg_match('/(password|passwd|pwd)\s*=\s*[\'"][^\'"]{3,}[\'"](?!.*\$)/i', $content)) {
                $lines = $this->findMatchingLines($content, '/(password|passwd|pwd)\s*=\s*[\'"][^\'"]{3,}[\'"](?!.*\$)/i');
                foreach ($lines as $line => $match) {
                    if (!preg_match('/example|test|demo|placeholder/i', $match)) {
                        $this->errors[] = "CRITICAL: Hardcoded password in $relPath:$line";
                    }
                }
            }

            if (preg_match('/(api_key|api_secret|secret_key)\s*=\s*[\'"][^\'"]{10,}[\'"](?!.*\$)/i', $content)) {
                $this->errors[] = "CRITICAL: Hardcoded API key in: $relPath";
            }
        }

        echo "   ✓ Scanned for hardcoded credentials\n";
    }

    private function testDebugCode() {
        echo "\n🐛 TEST 21: Debug Code Detection\n";
        echo str_repeat('─', 60) . "\n";

        foreach ($this->phpFiles as $file) {
            $content = file_get_contents($file);
            $relPath = $this->relativePath($file);

            // var_dump, print_r in production code
            if (preg_match_all('/\b(var_dump|print_r|var_export)\s*\(/i', $content, $matches)) {
                $this->warnings[] = count($matches[0]) . " debug function(s) in: $relPath";
            }

            // console.log in PHP (copy-paste error)
            if (preg_match('/console\.log/i', $content)) {
                $this->warnings[] = "JavaScript console.log in PHP file: $relPath";
            }

            // TODO/FIXME comments
            if (preg_match_all('/(TODO|FIXME|XXX|HACK)/i', $content, $matches)) {
                $this->info[] = count($matches[0]) . " TODO/FIXME comment(s) in: $relPath";
            }
        }

        echo "   ✓ Detected debug code\n";
    }

    private function testDeprecatedFunctions() {
        echo "\n⚠️  TEST 22: Deprecated Function Usage\n";
        echo str_repeat('─', 60) . "\n";

        $deprecated = [
            'mysql_connect', 'mysql_query', 'mysql_fetch_array',
            'ereg', 'eregi', 'split',
            'mcrypt_encrypt', 'mcrypt_decrypt',
            'create_function'
        ];

        foreach ($this->phpFiles as $file) {
            $content = file_get_contents($file);
            $relPath = $this->relativePath($file);

            foreach ($deprecated as $func) {
                if (preg_match('/\b' . $func . '\s*\(/i', $content)) {
                    $this->errors[] = "Deprecated function '$func' in: $relPath";
                }
            }
        }

        echo "   ✓ Checked deprecated functions\n";
    }

    private function findMatchingLines($content, $pattern) {
        $lines = [];
        $contentLines = explode("\n", $content);

        foreach ($contentLines as $num => $line) {
            if (preg_match($pattern, $line)) {
                $lines[$num + 1] = $line;
            }
        }

        return $lines;
    }

    private function relativePath($path) {
        return str_replace($this->baseDir . '/', '', $path);
    }

    private function printHeader() {
        echo "\n";
        echo "╔════════════════════════════════════════════════════════════════╗\n";
        echo "║  MINALIA ULTRA DEEP TEST - İĞNE DELİĞİ SEVİYESİ              ║\n";
        echo "║  En Küçük Hatayı Bile Yakalayan Kapsamlı Test                 ║\n";
        echo "╚════════════════════════════════════════════════════════════════╝\n";
        echo "\n";
        echo "📁 Base Directory: {$this->baseDir}\n";
        echo "📄 PHP Files: " . count($this->phpFiles) . "\n";
        echo "📊 SQL Files: " . count($this->sqlFiles) . "\n";
        echo "\n";
    }

    private function printSummary() {
        echo "\n";
        echo str_repeat('═', 70) . "\n";
        echo "📊 ULTRA DEEP TEST SUMMARY\n";
        echo str_repeat('═', 70) . "\n";
        echo "\n";

        echo "Total Tests Run: {$this->totalTests}\n";
        echo "Critical Errors: " . count($this->errors) . "\n";
        echo "Warnings: " . count($this->warnings) . "\n";
        echo "Info/Suggestions: " . count($this->info) . "\n";
        echo "\n";

        if (!empty($this->errors)) {
            echo "❌ CRITICAL ERRORS:\n";
            echo str_repeat('─', 70) . "\n";
            foreach (array_slice($this->errors, 0, 50) as $error) {
                echo "❌ $error\n";
            }
            if (count($this->errors) > 50) {
                echo "... and " . (count($this->errors) - 50) . " more errors\n";
            }
            echo "\n";
        }

        if (!empty($this->warnings)) {
            echo "⚠️  WARNINGS:\n";
            echo str_repeat('─', 70) . "\n";
            foreach (array_slice($this->warnings, 0, 30) as $warning) {
                echo "⚠️  $warning\n";
            }
            if (count($this->warnings) > 30) {
                echo "... and " . (count($this->warnings) - 30) . " more warnings\n";
            }
            echo "\n";
        }

        if (!empty($this->info)) {
            echo "ℹ️  SUGGESTIONS & INFO:\n";
            echo str_repeat('─', 70) . "\n";
            foreach (array_slice($this->info, 0, 20) as $info) {
                echo "ℹ️  $info\n";
            }
            if (count($this->info) > 20) {
                echo "... and " . (count($this->info) - 20) . " more suggestions\n";
            }
            echo "\n";
        }

        // Overall status
        if (empty($this->errors)) {
            if (empty($this->warnings)) {
                echo "✅ ✅ ✅ SİSTEM MÜKEMMEL - HİÇBİR HATA YOK! ✅ ✅ ✅\n";
            } else {
                echo "✅ SİSTEM HATASIZ - Sadece uyarılar var (kritik değil)\n";
            }
        } else {
            echo "❌ SİSTEM DİKKAT GEREKTİRİYOR - Kritik hatalar bulundu!\n";
        }

        echo "\n";
    }
}

// Run the test
$test = new UltraDeepTest();
$test->runAllTests();
