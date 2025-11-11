<?php
/**
 * Stock Alert Cron Job
 * Runs daily to notify users when products are back in stock
 *
 * Crontab: 0 8 * * * php /path/to/cron/stock-alert-cron.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/EmailService.php';

echo "[" . date('Y-m-d H:i:s') . "] Starting stock alert check...\n";

try {
    $db = Database::getInstance()->getConnection();

    // Find products that are back in stock
    $sql = "SELECT DISTINCT sa.product_id, sa.user_id, sa.email,
            p.name as product_name, p.slug, p.stock_quantity
            FROM stock_alerts sa
            INNER JOIN products p ON sa.product_id = p.id
            WHERE sa.notified = 0
            AND p.stock_quantity > 0";

    $stmt = $db->query($sql);
    $alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sent = 0;
    foreach ($alerts as $alert) {
        // Send stock alert email
        $emailService = new EmailService();
        $result = $emailService->sendStockAlertEmail(
            $alert['email'],
            $alert['product_name'],
            $alert['slug']
        );

        if ($result) {
            // Mark as notified
            $updateSql = "UPDATE stock_alerts SET notified = 1, notified_at = NOW()
                         WHERE product_id = ? AND user_id = ?";
            $updateStmt = $db->prepare($updateSql);
            $updateStmt->execute([$alert['product_id'], $alert['user_id']]);

            $sent++;
            echo "Sent stock alert for: {$alert['product_name']} to {$alert['email']}\n";
        }
    }

    echo "Total alerts sent: $sent\n";
    echo "Completed successfully!\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    error_log("Stock alert cron error: " . $e->getMessage());
}
