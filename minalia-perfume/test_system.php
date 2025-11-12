<?php
/**
 * MINALIA System Deep Tester
 * Finds ALL errors in the system
 */

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  MINALIA DEEP SYSTEM TEST - ZERO TOLERANCE                ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

$errors = [];
$warnings = [];
$tested = 0;

// TEST 1: Check all require/include paths
echo "🔍 TEST 1: Checking all require/include statements...\n";
$phpFiles = glob('{*.php,*/*.php,*/*/*.php,*/*/*/*.php,*/*/*/*/*.php}', GLOB_BRACE);
$phpFiles = array_filter($phpFiles, function($f) {
    return strpos($f, 'vendor/') === false && strpos($f, 'test_system.php') === false;
});

foreach ($phpFiles as $file) {
    $content = file_get_contents($file);
    preg_match_all('/(?:require|include)(?:_once)?\s*[\'"]([^\'"]+)[\'"]/', $content, $matches);
    
    foreach ($matches[1] as $includedPath) {
        $tested++;
        // Resolve relative paths
        $basePath = dirname($file);
        $fullPath = realpath($basePath . '/' . $includedPath);
        
        if (!$fullPath || !file_exists($fullPath)) {
            $errors[] = "❌ $file: Cannot find included file: $includedPath";
        }
    }
}
echo "   ✓ Tested $tested include/require statements\n\n";

// TEST 2: Check all function calls have definitions
echo "🔍 TEST 2: Checking undefined functions...\n";
$definedFunctions = get_defined_functions()['user'];
$calledFunctions = [];

foreach ($phpFiles as $file) {
    $content = file_get_contents($file);
    // Find function calls
    preg_match_all('/\b([a-zA-Z_][a-zA-Z0-9_]*)\s*\(/', $content, $matches);
    foreach ($matches[1] as $func) {
        if (!in_array($func, ['if', 'foreach', 'while', 'echo', 'print', 'isset', 'empty', 'array', 'define'])) {
            $calledFunctions[] = $func;
        }
    }
}

$commonFunctions = ['redirect', 'sanitize', 'getSetting', 'getDB', 'setFlashMessage', 'getFlashMessage', 'requireAdminAuth'];
foreach (array_unique($calledFunctions) as $func) {
    if (!function_exists($func) && !in_array($func, $commonFunctions)) {
        $warnings[] = "⚠️  Function may not exist: $func()";
    }
}
echo "   ✓ Checked function calls\n\n";

// TEST 3: Check view files exist
echo "🔍 TEST 3: Checking view file references...\n";
foreach ($phpFiles as $file) {
    $content = file_get_contents($file);
    // Find render() calls
    preg_match_all('/render\s*\(\s*[\'"]([^\'"]+)[\'"]/', $content, $matches);
    foreach ($matches[1] as $viewPath) {
        $tested++;
        $possiblePaths = [
            "admin/views/$viewPath.php",
            "views/$viewPath.php",
            "public/views/$viewPath.php"
        ];
        
        $found = false;
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $errors[] = "❌ View not found: $viewPath.php (referenced in $file)";
        }
    }
}
echo "   ✓ Tested view references\n\n";

// TEST 4: Check all routes point to existing controllers
echo "🔍 TEST 4: Checking route definitions...\n";
$indexFile = file_get_contents('admin/index.php');
preg_match_all('/[\'"]([^\'"]+)[\'"]\s*=>\s*[\'"]([^@]+)@([^\'"]+)[\'"]/', $indexFile, $routeMatches);

for ($i = 0; $i < count($routeMatches[2]); $i++) {
    $controller = $routeMatches[2][$i];
    $method = $routeMatches[3][$i];
    $route = $routeMatches[1][$i];
    $tested++;
    
    $controllerFile = "admin/controllers/$controller.php";
    if (!file_exists($controllerFile)) {
        $errors[] = "❌ Route '$route': Controller not found: $controllerFile";
    } else {
        $controllerContent = file_get_contents($controllerFile);
        if (strpos($controllerContent, "function $method") === false) {
            $errors[] = "❌ Route '$route': Method $method() not found in $controller";
        }
    }
}
echo "   ✓ Tested " . count($routeMatches[2]) . " routes\n\n";

// TEST 5: Check database table references in SQL
echo "🔍 TEST 5: Checking SQL table references...\n";
$schemaContent = file_get_contents('database/complete_schema.sql');
preg_match_all('/CREATE TABLE (?:IF NOT EXISTS )?\`?([a-zA-Z_]+)\`?/', $schemaContent, $tableMatches);
$definedTables = $tableMatches[1];

foreach (glob('admin/controllers/*.php') as $controller) {
    $content = file_get_contents($controller);
    preg_match_all('/FROM\s+([a-zA-Z_]+)/', $content, $fromMatches);
    preg_match_all('/INTO\s+([a-zA-Z_]+)/', $content, $intoMatches);
    preg_match_all('/UPDATE\s+([a-zA-Z_]+)/', $content, $updateMatches);
    
    $usedTables = array_merge($fromMatches[1], $intoMatches[1], $updateMatches[1]);
    
    foreach ($usedTables as $table) {
        if (!in_array($table, $definedTables) && !in_array($table, ['CURRENT_DATE', 'NOW'])) {
            $warnings[] = "⚠️  Table '$table' used in " . basename($controller) . " but not defined in schema";
        }
    }
}
echo "   ✓ Found " . count($definedTables) . " tables in schema\n\n";

// TEST 6: Check SESSION constants
echo "🔍 TEST 6: Checking SESSION constant usage...\n";
$sessionConstants = ['SESSION_ADMIN_ID', 'SESSION_ADMIN_USERNAME', 'SESSION_ADMIN_EMAIL', 'SESSION_USER_ID', 'SESSION_USER_EMAIL'];

foreach ($phpFiles as $file) {
    $content = file_get_contents($file);
    // Check for raw session keys instead of constants
    if (preg_match('/\$_SESSION\s*\[\s*[\'"](?!SESSION_)[a-z_]+[\'"]\s*\]/', $content)) {
        $warnings[] = "⚠️  $file may use raw session keys instead of constants";
    }
}
echo "   ✓ Checked session usage\n\n";

// SUMMARY
echo "═══════════════════════════════════════════════════════════\n";
echo "📊 TEST SUMMARY:\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "Total Tests: $tested\n";
echo "Errors: " . count($errors) . "\n";
echo "Warnings: " . count($warnings) . "\n\n";

if (count($errors) > 0) {
    echo "❌ ERRORS FOUND:\n";
    echo "─────────────────────────────────────────────────────────\n";
    foreach (array_slice($errors, 0, 20) as $error) {
        echo "$error\n";
    }
    if (count($errors) > 20) {
        echo "... and " . (count($errors) - 20) . " more errors\n";
    }
    echo "\n";
}

if (count($warnings) > 0) {
    echo "⚠️  WARNINGS:\n";
    echo "─────────────────────────────────────────────────────────\n";
    foreach (array_slice($warnings, 0, 10) as $warning) {
        echo "$warning\n";
    }
    if (count($warnings) > 10) {
        echo "... and " . (count($warnings) - 10) . " more warnings\n";
    }
    echo "\n";
}

if (count($errors) === 0 && count($warnings) === 0) {
    echo "✅ ALL TESTS PASSED! SYSTEM IS CLEAN!\n";
} else {
    echo "System needs attention!\n";
}

echo "\n";
