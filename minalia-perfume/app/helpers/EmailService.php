<?php
/**
 * Email Service with PHPMailer
 * MINALIA Parfüm E-Ticaret Platformu
 */

class EmailService {
    private $mailer;
    private $templates;

    public function __construct() {
        // Note: In production, install PHPMailer via Composer
        // composer require phpmailer/phpmailer

        // For now, using native mail() function
        $this->templates = __DIR__ . '/../views/emails/';
    }

    /**
     * Send abandoned cart email
     */
    public function sendAbandonedCartEmail($userEmail, $userName, $cartItems, $cartTotal) {
        $subject = "Sepetinizde ürünler bekliyor! 🛍️";

        $data = [
            'user_name' => $userName,
            'cart_items' => $cartItems,
            'cart_total' => formatPrice($cartTotal),
            'cart_url' => BASE_URL . '/cart',
            'discount_code' => 'SEPET10' // 10% discount for abandoned carts
        ];

        $body = $this->renderTemplate('abandoned-cart', $data);

        return $this->send($userEmail, $subject, $body);
    }

    /**
     * Send order confirmation email
     */
    public function sendOrderConfirmation($userEmail, $orderData) {
        $subject = "Siparişiniz Alındı! Sipariş #" . $orderData['order_number'];

        $data = [
            'order' => $orderData,
            'order_url' => BASE_URL . '/account/orders/' . $orderData['order_number']
        ];

        $body = $this->renderTemplate('order-confirmation', $data);

        return $this->send($userEmail, $subject, $body);
    }

    /**
     * Send welcome email
     */
    public function sendWelcomeEmail($userEmail, $userName) {
        $subject = "MINALIA'ya Hoş Geldiniz! 🌟";

        $data = [
            'user_name' => $userName,
            'welcome_discount' => 'HOSGELDIN15',
            'explore_url' => BASE_URL . '/products'
        ];

        $body = $this->renderTemplate('welcome', $data);

        return $this->send($userEmail, $subject, $body);
    }

    /**
     * Send newsletter
     */
    public function sendNewsletter($subscribers, $subject, $content) {
        $successCount = 0;

        foreach ($subscribers as $subscriber) {
            $data = [
                'content' => $content,
                'unsubscribe_url' => BASE_URL . '/newsletter/unsubscribe/' . $subscriber['token']
            ];

            $body = $this->renderTemplate('newsletter', $data);

            if ($this->send($subscriber['email'], $subject, $body)) {
                $successCount++;
            }
        }

        return $successCount;
    }

    /**
     * Send password reset email
     */
    public function sendPasswordReset($userEmail, $resetToken) {
        $subject = "Şifre Sıfırlama Talebi";

        $data = [
            'reset_url' => BASE_URL . '/reset-password/' . $resetToken,
            'expire_time' => '1 saat'
        ];

        $body = $this->renderTemplate('password-reset', $data);

        return $this->send($userEmail, $subject, $body);
    }

    /**
     * Send stock alert email
     */
    public function sendStockAlert($userEmail, $productName, $productUrl) {
        $subject = "$productName - Stokta!";

        $data = [
            'product_name' => $productName,
            'product_url' => $productUrl
        ];

        $body = $this->renderTemplate('stock-alert', $data);

        return $this->send($userEmail, $subject, $body);
    }

    /**
     * Send birthday discount
     */
    public function sendBirthdayDiscount($userEmail, $userName) {
        $subject = "🎉 Doğum Günün Kutlu Olsun! Hediyemiz Var!";

        $data = [
            'user_name' => $userName,
            'discount_code' => 'DOGUMGUNU' . date('Y'),
            'discount_amount' => '20%'
        ];

        $body = $this->renderTemplate('birthday-discount', $data);

        return $this->send($userEmail, $subject, $body);
    }

    /**
     * Render email template
     */
    private function renderTemplate($template, $data) {
        $templateFile = $this->templates . $template . '.php';

        if (!file_exists($templateFile)) {
            return $this->getDefaultTemplate($template, $data);
        }

        extract($data);
        ob_start();
        include $templateFile;
        return ob_get_clean();
    }

    /**
     * Get default email template
     */
    private function getDefaultTemplate($type, $data) {
        $baseTemplate = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #7A8B5C; color: white; padding: 30px; text-align: center; }
        .content { background: #f8f5f0; padding: 30px; }
        .footer { background: #1A1A1A; color: white; padding: 20px; text-align: center; font-size: 12px; }
        .button { display: inline-block; padding: 12px 30px; background: #D4AF37; color: #1A1A1A; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .product-item { border-bottom: 1px solid #ddd; padding: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>MINALIA</h1>
            <p>Lüks Parfüm Deneyimi</p>
        </div>
        <div class="content">
            {{CONTENT}}
        </div>
        <div class="footer">
            <p>&copy; ' . date('Y') . ' MINALIA Parfüm. Tüm hakları saklıdır.</p>
            <p>İstanbul, Türkiye | info@minalia.com</p>
        </div>
    </div>
</body>
</html>';

        $content = '';

        switch ($type) {
            case 'abandoned-cart':
                $content = '
                    <h2>Merhaba ' . $data['user_name'] . ',</h2>
                    <p>Sepetinizde harika ürünler var! Alışverişinizi tamamlamak ister misiniz?</p>
                    <p><strong>Toplam: ' . $data['cart_total'] . '</strong></p>
                    <p>Size özel <strong>' . $data['discount_code'] . '</strong> kupon koduyla %10 indirim!</p>
                    <p><a href="' . $data['cart_url'] . '" class="button">Sepetime Git</a></p>
                ';
                break;

            case 'order-confirmation':
                $content = '
                    <h2>Siparişiniz Alındı!</h2>
                    <p>Sipariş Numarası: <strong>' . $data['order']['order_number'] . '</strong></p>
                    <p>Toplam: <strong>' . formatPrice($data['order']['total']) . '</strong></p>
                    <p><a href="' . $data['order_url'] . '" class="button">Siparişimi İzle</a></p>
                ';
                break;

            case 'welcome':
                $content = '
                    <h2>Hoş Geldiniz ' . $data['user_name'] . '!</h2>
                    <p>MINALIA ailesine katıldığınız için çok mutluyuz.</p>
                    <p>Size özel <strong>' . $data['welcome_discount'] . '</strong> koduyla %15 indirim!</p>
                    <p><a href="' . $data['explore_url'] . '" class="button">Alışverişe Başla</a></p>
                ';
                break;
        }

        return str_replace('{{CONTENT}}', $content, $baseTemplate);
    }

    /**
     * Send email
     */
    private function send($to, $subject, $body) {
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . SMTP_FROM_NAME . ' <' . SMTP_FROM_EMAIL . '>',
            'Reply-To: ' . SMTP_FROM_EMAIL,
            'X-Mailer: PHP/' . phpversion()
        ];

        // In production, use PHPMailer or a service like SendGrid
        // For now, using PHP's mail() function
        $success = mail($to, $subject, $body, implode("\r\n", $headers));

        // Log email
        $this->logEmail($to, $subject, $success);

        return $success;
    }

    /**
     * Log email activity
     */
    private function logEmail($to, $subject, $success) {
        $logFile = LOG_PATH . '/emails.log';
        $timestamp = date('Y-m-d H:i:s');
        $status = $success ? 'SUCCESS' : 'FAILED';
        $logMessage = "[$timestamp] [$status] To: $to | Subject: $subject\n";

        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
}

/**
 * Abandoned Cart Tracker
 */
class AbandonedCartTracker {
    private $db;
    private $emailService;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->emailService = new EmailService();
    }

    /**
     * Track cart abandonment
     */
    public function trackAbandonedCarts() {
        // Find carts abandoned for 1 hour
        $sql = "SELECT
                    u.id as user_id,
                    u.email,
                    u.first_name,
                    COUNT(ci.id) as item_count,
                    SUM(p.price * ci.quantity) as cart_total
                FROM users u
                INNER JOIN cart_items ci ON u.id = ci.user_id
                INNER JOIN products p ON ci.product_id = p.id
                WHERE ci.updated_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)
                AND NOT EXISTS (
                    SELECT 1 FROM abandoned_cart_emails ace
                    WHERE ace.user_id = u.id
                    AND ace.sent_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
                )
                GROUP BY u.id
                HAVING item_count > 0";

        $stmt = $this->db->query($sql);
        $abandonedCarts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sentCount = 0;

        foreach ($abandonedCarts as $cart) {
            // Get cart items
            $items = $this->getCartItems($cart['user_id']);

            // Send email
            $success = $this->emailService->sendAbandonedCartEmail(
                $cart['email'],
                $cart['first_name'],
                $items,
                $cart['cart_total']
            );

            if ($success) {
                $this->logAbandonedCartEmail($cart['user_id']);
                $sentCount++;
            }
        }

        return $sentCount;
    }

    /**
     * Get cart items for user
     */
    private function getCartItems($userId) {
        $sql = "SELECT p.*, ci.quantity
                FROM cart_items ci
                INNER JOIN products p ON ci.product_id = p.id
                WHERE ci.user_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Log abandoned cart email
     */
    private function logAbandonedCartEmail($userId) {
        $sql = "INSERT INTO abandoned_cart_emails (user_id, sent_at) VALUES (?, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId]);
    }
}
