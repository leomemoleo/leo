<?php
/**
 * SMS Gateway Interface
 * Enterprise SMS notification system for Turkey
 * Supports: Netgsm, İletimerkezi
 */

interface SmsGatewayInterface {
    public function sendSms($phone, $message);
    public function sendBulkSms($phones, $message);
    public function getBalance();
    public function getDeliveryReport($messageId);
}

/**
 * Netgsm SMS Adapter
 * Turkey's most popular SMS gateway
 * API Documentation: https://www.netgsm.com.tr/dokuman
 */
class NetgsmAdapter implements SmsGatewayInterface {
    private $username;
    private $password;
    private $header;
    private $apiUrl = 'https://api.netgsm.com.tr/sms/send/get';

    public function __construct() {
        $this->username = $_ENV['NETGSM_USERNAME'] ?? '';
        $this->password = $_ENV['NETGSM_PASSWORD'] ?? '';
        $this->header = $_ENV['NETGSM_HEADER'] ?? 'MINALIA';
    }

    /**
     * Send single SMS
     */
    public function sendSms($phone, $message) {
        $phone = $this->formatPhone($phone);

        $params = [
            'usercode' => $this->username,
            'password' => $this->password,
            'gsmno' => $phone,
            'message' => $message,
            'msgheader' => $this->header,
            'dil' => 'TR'
        ];

        $response = $this->makeRequest($this->apiUrl, $params);

        return $this->parseResponse($response);
    }

    /**
     * Send bulk SMS to multiple numbers
     */
    public function sendBulkSms($phones, $message) {
        $results = [];

        foreach ($phones as $phone) {
            $results[] = $this->sendSms($phone, $message);
        }

        return [
            'success' => !in_array(false, array_column($results, 'success')),
            'results' => $results
        ];
    }

    /**
     * Get account balance (credits)
     */
    public function getBalance() {
        $url = 'https://api.netgsm.com.tr/balance/list/get';

        $params = [
            'usercode' => $this->username,
            'password' => $this->password
        ];

        $response = $this->makeRequest($url, $params);

        // Response format: "XX / YY" (Credits / Amount)
        if (preg_match('/(\d+)\s*\/\s*(\d+)/', $response, $matches)) {
            return [
                'success' => true,
                'credits' => (int)$matches[1],
                'amount' => (float)$matches[2]
            ];
        }

        return ['success' => false, 'message' => 'Balance check failed'];
    }

    /**
     * Get delivery report for sent message
     */
    public function getDeliveryReport($messageId) {
        $url = 'https://api.netgsm.com.tr/sms/report';

        $params = [
            'usercode' => $this->username,
            'password' => $this->password,
            'bulkid' => $messageId
        ];

        $response = $this->makeRequest($url, $params);

        return [
            'success' => strpos($response, '0') === 0,
            'status' => $this->getDeliveryStatus($response),
            'raw_response' => $response
        ];
    }

    /**
     * Format Turkish phone number
     * Supports: 05XXXXXXXXX, 5XXXXXXXXX, +905XXXXXXXXX
     */
    private function formatPhone($phone) {
        // Remove non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Remove leading country code (90)
        if (strlen($phone) === 12 && substr($phone, 0, 2) === '90') {
            $phone = substr($phone, 2);
        }

        // Add leading 0 if missing
        if (strlen($phone) === 10 && substr($phone, 0, 1) !== '0') {
            $phone = '0' . $phone;
        }

        // Validate format
        if (!preg_match('/^0[5][0-9]{9}$/', $phone)) {
            throw new Exception("Invalid Turkish mobile number: {$phone}");
        }

        return $phone;
    }

    /**
     * Parse Netgsm API response
     */
    private function parseResponse($response) {
        // Netgsm response codes:
        // 00 or 01 = Success (returns message ID)
        // 20 = Message contains XML error
        // 30 = Invalid username/password
        // 40 = Header not found
        // 50 = Incorrect gsm number
        // 51 = Line not registered
        // 70 = Insufficient credits
        // 85 = Unauthorized message content

        if (is_numeric($response) && (int)$response > 0) {
            return [
                'success' => true,
                'message_id' => $response,
                'message' => 'SMS sent successfully'
            ];
        }

        $errors = [
            '20' => 'Message format error',
            '30' => 'Invalid credentials',
            '40' => 'SMS header not found',
            '50' => 'Invalid phone number',
            '51' => 'Line not registered',
            '70' => 'Insufficient credits',
            '85' => 'Unauthorized message content'
        ];

        return [
            'success' => false,
            'message' => $errors[$response] ?? "SMS send failed: {$response}"
        ];
    }

    /**
     * Get delivery status description
     */
    private function getDeliveryStatus($code) {
        $statuses = [
            '0' => 'Delivered',
            '1' => 'Pending',
            '2' => 'Failed',
            '3' => 'Waiting',
            '11' => 'Not delivered to operator',
            '12' => 'Message rejected',
            '13' => 'Timeout'
        ];

        return $statuses[$code] ?? 'Unknown';
    }

    /**
     * Make HTTP request
     */
    private function makeRequest($url, $params) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'User-Agent: MINALIA/1.0'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("HTTP Error: {$httpCode}");
        }

        return trim($response);
    }
}

/**
 * İletimerkezi SMS Adapter
 * Popular Turkish SMS gateway
 * API Documentation: https://www.iletimerkezi.com/api
 */
class IletimerkeziAdapter implements SmsGatewayInterface {
    private $apiKey;
    private $apiHash;
    private $sender;
    private $apiUrl = 'https://api.iletimerkezi.com/v1';

    public function __construct() {
        $this->apiKey = $_ENV['ILETIMERKEZI_API_KEY'] ?? '';
        $this->apiHash = $_ENV['ILETIMERKEZI_API_HASH'] ?? '';
        $this->sender = $_ENV['ILETIMERKEZI_SENDER'] ?? 'MINALIA';
    }

    /**
     * Send single SMS
     */
    public function sendSms($phone, $message) {
        $phone = $this->formatPhone($phone);

        $xml = $this->buildSmsXml([$phone], $message);

        $response = $this->makeRequest('/send-sms/get/', $xml);

        return $this->parseResponse($response);
    }

    /**
     * Send bulk SMS
     */
    public function sendBulkSms($phones, $message) {
        $phones = array_map([$this, 'formatPhone'], $phones);

        $xml = $this->buildSmsXml($phones, $message);

        $response = $this->makeRequest('/send-sms/get/', $xml);

        return $this->parseResponse($response);
    }

    /**
     * Get account balance
     */
    public function getBalance() {
        $xml = "<?xml version='1.0' encoding='UTF-8'?>
        <request>
            <authentication>
                <key>{$this->apiKey}</key>
                <hash>{$this->apiHash}</hash>
            </authentication>
        </request>";

        $response = $this->makeRequest('/get-balance/get/', $xml);

        $data = simplexml_load_string($response);

        if ($data && isset($data->balance)) {
            return [
                'success' => true,
                'credits' => (float)$data->balance->amount,
                'sms' => (int)$data->balance->sms
            ];
        }

        return ['success' => false, 'message' => 'Balance check failed'];
    }

    /**
     * Get delivery report
     */
    public function getDeliveryReport($messageId) {
        $xml = "<?xml version='1.0' encoding='UTF-8'?>
        <request>
            <authentication>
                <key>{$this->apiKey}</key>
                <hash>{$this->apiHash}</hash>
            </authentication>
            <order>
                <id>{$messageId}</id>
            </order>
        </request>";

        $response = $this->makeRequest('/get-report/get/', $xml);

        $data = simplexml_load_string($response);

        if ($data && isset($data->order)) {
            return [
                'success' => true,
                'status' => (string)$data->order->status,
                'delivered' => (int)$data->order->delivered ?? 0,
                'total' => (int)$data->order->total ?? 0
            ];
        }

        return ['success' => false, 'message' => 'Report not found'];
    }

    /**
     * Build SMS XML request
     */
    private function buildSmsXml($phones, $message) {
        $phonesXml = '';
        foreach ($phones as $phone) {
            $phonesXml .= "<number>{$phone}</number>\n";
        }

        $message = htmlspecialchars($message, ENT_XML1, 'UTF-8');

        return "<?xml version='1.0' encoding='UTF-8'?>
        <request>
            <authentication>
                <key>{$this->apiKey}</key>
                <hash>{$this->apiHash}</hash>
            </authentication>
            <order>
                <sender>{$this->sender}</sender>
                <sendDateTime></sendDateTime>
                <iys>1</iys>
                <iysList>BIREYSEL</iysList>
                <message>
                    <text>{$message}</text>
                    <receipents>
                        {$phonesXml}
                    </receipents>
                </message>
            </order>
        </request>";
    }

    /**
     * Format phone number
     */
    private function formatPhone($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) === 12 && substr($phone, 0, 2) === '90') {
            $phone = substr($phone, 2);
        }

        if (strlen($phone) === 10 && substr($phone, 0, 1) !== '0') {
            $phone = '0' . $phone;
        }

        if (!preg_match('/^0[5][0-9]{9}$/', $phone)) {
            throw new Exception("Invalid Turkish mobile number: {$phone}");
        }

        return $phone;
    }

    /**
     * Parse XML response
     */
    private function parseResponse($response) {
        $data = simplexml_load_string($response);

        if ($data && isset($data->order)) {
            $orderId = (string)$data->order->id;

            if ($orderId) {
                return [
                    'success' => true,
                    'message_id' => $orderId,
                    'message' => 'SMS sent successfully'
                ];
            }
        }

        if ($data && isset($data->status)) {
            $code = (string)$data->status->code;
            $message = (string)$data->status->message;

            return [
                'success' => false,
                'message' => "{$message} (Code: {$code})"
            ];
        }

        return [
            'success' => false,
            'message' => 'SMS send failed'
        ];
    }

    /**
     * Make HTTP request
     */
    private function makeRequest($endpoint, $xml) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'data=' . urlencode($xml));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'User-Agent: MINALIA/1.0'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("HTTP Error: {$httpCode}");
        }

        return $response;
    }
}

/**
 * SMS Factory - Create SMS Gateway instance
 */
class SmsFactory {
    public static function create($provider = null) {
        if ($provider === null) {
            $provider = $_ENV['SMS_PROVIDER'] ?? 'netgsm';
        }

        switch (strtolower($provider)) {
            case 'netgsm':
                return new NetgsmAdapter();
            case 'iletimerkezi':
                return new IletimerkeziAdapter();
            default:
                throw new Exception("Unsupported SMS provider: {$provider}");
        }
    }
}

/**
 * SMS Helper Class - Easy SMS sending
 */
class SmsHelper {
    private static $gateway = null;

    /**
     * Get SMS gateway instance
     */
    private static function getGateway() {
        if (self::$gateway === null) {
            self::$gateway = SmsFactory::create();
        }
        return self::$gateway;
    }

    /**
     * Send SMS notification
     */
    public static function send($phone, $message) {
        try {
            $gateway = self::getGateway();
            $result = $gateway->sendSms($phone, $message);

            // Log SMS
            self::logSms($phone, $message, $result);

            return $result;

        } catch (Exception $e) {
            error_log("SMS Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Send bulk SMS
     */
    public static function sendBulk($phones, $message) {
        try {
            $gateway = self::getGateway();
            return $gateway->sendBulkSms($phones, $message);

        } catch (Exception $e) {
            error_log("Bulk SMS Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Send order notification SMS
     */
    public static function sendOrderNotification($phone, $orderNumber, $status) {
        $messages = [
            'pending' => "MINALIA: Siparişiniz #{$orderNumber} alındı. Teşekkür ederiz!",
            'processing' => "MINALIA: Siparişiniz #{$orderNumber} hazırlanıyor.",
            'shipped' => "MINALIA: Siparişiniz #{$orderNumber} kargoya verildi. Takip kodu SMS ile gönderilecektir.",
            'delivered' => "MINALIA: Siparişiniz #{$orderNumber} teslim edildi. Afiyet olsun!",
            'cancelled' => "MINALIA: Siparişiniz #{$orderNumber} iptal edildi. Bilgi için: {$_ENV['SITE_PHONE']}"
        ];

        $message = $messages[$status] ?? "MINALIA: Sipariş #{$orderNumber} durumu: {$status}";

        return self::send($phone, $message);
    }

    /**
     * Send verification code SMS
     */
    public static function sendVerificationCode($phone, $code) {
        $message = "MINALIA: Doğrulama kodunuz: {$code}. Bu kodu kimseyle paylaşmayın.";
        return self::send($phone, $message);
    }

    /**
     * Send password reset SMS
     */
    public static function sendPasswordReset($phone, $code) {
        $message = "MINALIA: Şifre sıfırlama kodunuz: {$code}. Geçerlilik: 15 dakika.";
        return self::send($phone, $message);
    }

    /**
     * Send shipping notification
     */
    public static function sendShippingNotification($phone, $orderNumber, $trackingCode, $courier) {
        $message = "MINALIA: Sipariş #{$orderNumber} kargoya verildi. Kargo: {$courier}, Takip: {$trackingCode}";
        return self::send($phone, $message);
    }

    /**
     * Log SMS to database
     */
    private static function logSms($phone, $message, $result) {
        global $db;

        if (!$db) return;

        try {
            $stmt = $db->prepare("
                INSERT INTO sms_logs
                (phone, message, status, message_id, provider, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");

            $stmt->execute([
                $phone,
                $message,
                $result['success'] ? 'sent' : 'failed',
                $result['message_id'] ?? null,
                $_ENV['SMS_PROVIDER'] ?? 'netgsm'
            ]);

        } catch (Exception $e) {
            error_log("SMS Log Error: " . $e->getMessage());
        }
    }

    /**
     * Get SMS balance
     */
    public static function getBalance() {
        try {
            $gateway = self::getGateway();
            return $gateway->getBalance();
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
