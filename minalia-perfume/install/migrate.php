<?php
/**
 * Database Migration Runner
 * MINALIA Parfüm E-Ticaret Platformu
 *
 * Usage: php install/migrate.php
 *
 * This script runs all pending database migrations.
 * It tracks which migrations have been executed using a migrations table.
 */

// Prevent web access - CLI only
if (php_sapi_name() !== 'cli') {
    die('This script can only be run from command line.');
}

// Load configuration
$configFile = __DIR__ . '/../config.php';
if (!file_exists($configFile)) {
    die("ERROR: config.php not found. Please install the application first.\n");
}

require_once $configFile;

// Connect to database
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connected to database: " . DB_NAME . "\n\n";
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage() . "\n");
}

// Create migrations tracking table if it doesn't exist
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS schema_migrations (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) UNIQUE NOT NULL,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_migration (migration)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Migrations tracking table ready\n\n";
} catch (PDOException $e) {
    die("❌ Failed to create migrations table: " . $e->getMessage() . "\n");
}

// Get list of executed migrations
$stmt = $pdo->query("SELECT migration FROM schema_migrations ORDER BY migration");
$executedMigrations = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo "📊 Already executed: " . count($executedMigrations) . " migrations\n\n";

// Scan migrations directory
$migrationsDir = __DIR__ . '/migrations';
if (!is_dir($migrationsDir)) {
    die("❌ Migrations directory not found: $migrationsDir\n");
}

$migrationFiles = glob($migrationsDir . '/*.sql');
sort($migrationFiles); // Execute in order

if (empty($migrationFiles)) {
    echo "ℹ️  No migration files found in: $migrationsDir\n";
    exit(0);
}

echo "📁 Found " . count($migrationFiles) . " migration files\n\n";

// Execute pending migrations
$executedCount = 0;
$skippedCount = 0;
$failedCount = 0;

foreach ($migrationFiles as $file) {
    $filename = basename($file);

    // Skip if already executed
    if (in_array($filename, $executedMigrations)) {
        echo "⏭️  SKIP: $filename (already executed)\n";
        $skippedCount++;
        continue;
    }

    echo "🔄 RUNNING: $filename\n";

    try {
        // Read migration file
        $sql = file_get_contents($file);

        if (empty(trim($sql))) {
            echo "   ⚠️  Empty migration file, skipping\n\n";
            $skippedCount++;
            continue;
        }

        // Begin transaction
        $pdo->beginTransaction();

        // Remove comments for statement splitting
        $sql = preg_replace('/--.*?$/m', '', $sql);

        // Execute all statements in the migration
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        $statementCount = 0;

        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $pdo->exec($statement);
                $statementCount++;
            }
        }

        // Record migration as executed
        $stmt = $pdo->prepare("INSERT INTO schema_migrations (migration) VALUES (?)");
        $stmt->execute([$filename]);

        // Commit transaction
        $pdo->commit();

        echo "   ✅ SUCCESS: $statementCount statements executed\n\n";
        $executedCount++;

    } catch (Exception $e) {
        // Rollback on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        echo "   ❌ FAILED: " . $e->getMessage() . "\n\n";
        $failedCount++;

        // Stop on first error (to maintain consistency)
        echo "❌ Migration stopped due to error.\n";
        echo "   Fix the error and run migrate.php again.\n";
        exit(1);
    }
}

// Summary
echo "─────────────────────────────────────────\n";
echo "📊 MIGRATION SUMMARY:\n";
echo "   ✅ Executed: $executedCount\n";
echo "   ⏭️  Skipped:  $skippedCount\n";
echo "   ❌ Failed:   $failedCount\n";
echo "─────────────────────────────────────────\n";

if ($executedCount > 0) {
    echo "\n🎉 Database migrations completed successfully!\n";
} elseif ($skippedCount > 0 && $executedCount === 0) {
    echo "\nℹ️  Database is already up to date.\n";
} else {
    echo "\nℹ️  No migrations executed.\n";
}

exit(0);
