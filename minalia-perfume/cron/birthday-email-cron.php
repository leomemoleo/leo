<?php
/**
 * Birthday Email Cron Job
 * Runs daily at 09:00 to send birthday emails with special discounts
 *
 * Crontab: 0 9 * * * php /path/to/cron/birthday-email-cron.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/EmailService.php';

echo "[" . date('Y-m-d H:i:s') . "] Starting birthday email check...\n";

try {
    $db = Database::getInstance()->getConnection();

    // Find users with birthdays today
    $sql = "SELECT * FROM users
            WHERE DAY(birth_date) = DAY(CURDATE())
            AND MONTH(birth_date) = MONTH(CURDATE())
            AND is_active = 1
            AND email IS NOT NULL";

    $stmt = $db->query($sql);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sent = 0;
    foreach ($users as $user) {
        // Send birthday email
        $emailService = new EmailService();
        $result = $emailService->sendBirthdayEmail($user['id'], $user['email'], $user['name']);

        if ($result) {
            $sent++;

            // Award birthday bonus points
            $pointsSql = "INSERT INTO loyalty_points (user_id, points, type, description, created_at)
                         VALUES (?, 100, 'birthday', 'Doğum günü hediyesi', NOW())";
            $pointsStmt = $db->prepare($pointsSql);
            $pointsStmt->execute([$user['id']]);

            echo "Sent birthday email to: {$user['email']}\n";
        }
    }

    echo "Total emails sent: $sent\n";
    echo "Completed successfully!\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    error_log("Birthday email cron error: " . $e->getMessage());
}
