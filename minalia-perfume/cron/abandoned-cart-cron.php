<?php
/**
 * Abandoned Cart Email Cron Job
 * Runs every hour to send emails for abandoned carts
 *
 * Crontab: 0 * * * * php /path/to/cron/abandoned-cart-cron.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/EmailService.php';

echo "[" . date('Y-m-d H:i:s') . "] Starting abandoned cart check...\n";

try {
    $tracker = new AbandonedCartTracker();
    $result = $tracker->trackAbandonedCarts();

    echo "Processed: {$result['processed']} carts\n";
    echo "Emails sent: {$result['sent']}\n";
    echo "Completed successfully!\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    error_log("Abandoned cart cron error: " . $e->getMessage());
}
