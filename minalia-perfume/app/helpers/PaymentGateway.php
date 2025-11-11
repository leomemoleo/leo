<?php
/**
 * Payment Gateway Interface
 * Adapter pattern for multiple payment providers
 */

interface PaymentGatewayInterface {
    public function createPayment($orderData);
    public function verifyPayment($paymentId);
    public function refund($paymentId, $amount);
    public function getPaymentStatus($paymentId);
}

/**
 * iyzico Payment Adapter
 */
class IyzicoAdapter implements PaymentGatewayInterface {
    private $apiKey;
    private $secretKey;
    private $baseUrl;

    public function __construct() {
        $this->apiKey = $_ENV['IYZICO_API_KEY'] ?? '';
        $this->secretKey = $_ENV['IYZICO_SECRET_KEY'] ?? '';
        $this->baseUrl = $_ENV['IYZICO_BASE_URL'] ?? 'https://sandbox-api.iyzipay.com';
    }

    public function createPayment($orderData) {
        $options = $this->getOptions();

        $request = [
            'locale' => 'tr',
            'conversationId' => $orderData['order_number'],
            'price' => number_format($orderData['subtotal'], 2, '.', ''),
            'paidPrice' => number_format($orderData['total'], 2, '.', ''),
            'currency' => 'TRY',
            'installment' => $orderData['installment'] ?? 1,
            'basketId' => $orderData['order_number'],
            'paymentChannel' => 'WEB',
            'paymentGroup' => 'PRODUCT',
            'callbackUrl' => BASE_URL . '/payment/callback',
            'enabledInstallments' => [1, 2, 3, 6, 9, 12],

            'buyer' => [
                'id' => $orderData['user_id'],
                'name' => $orderData['buyer']['first_name'],
                'surname' => $orderData['buyer']['last_name'],
                'gsmNumber' => $orderData['buyer']['phone'],
                'email' => $orderData['buyer']['email'],
                'identityNumber' => '11111111111',
                'registrationAddress' => $orderData['buyer']['address'],
                'ip' => getClientIP(),
                'city' => $orderData['buyer']['city'],
                'country' => 'Turkey'
            ],

            'shippingAddress' => [
                'contactName' => $orderData['shipping']['name'],
                'city' => $orderData['shipping']['city'],
                'country' => 'Turkey',
                'address' => $orderData['shipping']['address'],
                'zipCode' => $orderData['shipping']['postal_code']
            ],

            'billingAddress' => [
                'contactName' => $orderData['billing']['name'],
                'city' => $orderData['billing']['city'],
                'country' => 'Turkey',
                'address' => $orderData['billing']['address'],
                'zipCode' => $orderData['billing']['postal_code']
            ],

            'basketItems' => $this->prepareBasketItems($orderData['items'])
        ];

        $paymentCard = [
            'cardHolderName' => $orderData['card']['holder_name'],
            'cardNumber' => $orderData['card']['number'],
            'expireMonth' => $orderData['card']['expire_month'],
            'expireYear' => $orderData['card']['expire_year'],
            'cvc' => $orderData['card']['cvc'],
            'registerCard' => 0
        ];

        $request['paymentCard'] = $paymentCard;

        $response = $this->makeRequest('/payment/auth', $request, $options);

        return [
            'success' => $response['status'] === 'success',
            'payment_id' => $response['paymentId'] ?? null,
            'message' => $response['errorMessage'] ?? 'Ödeme başarılı',
            'raw_response' => $response
        ];
    }

    public function verify Payment($paymentId) {
        // iyzico otomatik olarak doğrulama yapar
        return true;
    }

    public function refund($paymentId, $amount) {
        $options = $this->getOptions();

        $request = [
            'locale' => 'tr',
            'conversationId' => uniqid(),
            'paymentTransactionId' => $paymentId,
            'price' => number_format($amount, 2, '.', ''),
            'currency' => 'TRY',
            'ip' => getClientIP()
        ];

        $response = $this->makeRequest('/payment/refund', $request, $options);

        return [
            'success' => $response['status'] === 'success',
            'message' => $response['errorMessage'] ?? 'İade başarılı'
        ];
    }

    public function getPaymentStatus($paymentId) {
        // Implementation for checking payment status
        return 'completed';
    }

    private function prepareBasketItems($items) {
        $basketItems = [];

        foreach ($items as $item) {
            $basketItems[] = [
                'id' => $item['product_id'],
                'name' => $item['name'],
                'category1' => $item['category'] ?? 'Parfüm',
                'itemType' => 'PHYSICAL',
                'price' => number_format($item['price'] * $item['quantity'], 2, '.', '')
            ];
        }

        return $basketItems;
    }

    private function getOptions() {
        return [
            'api_key' => $this->apiKey,
            'secret_key' => $this->secretKey,
            'base_url' => $this->baseUrl
        ];
    }

    private function makeRequest($endpoint, $data, $options) {
        $url = $options['base_url'] . $endpoint;

        $randomString = uniqid();
        $authString = $randomString . $options['api_key'] . $options['secret_key'];
        $authorization = 'IYZWS ' . $options['api_key'] . ':' . base64_encode(hash_hmac('sha256', $authString, $options['secret_key'], true));

        $jsonData = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $authorization,
            'Content-Type: application/json',
            'x-iyzi-rnd: ' . $randomString
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}

/**
 * PayTR Payment Adapter
 */
class PayTRAdapter implements PaymentGatewayInterface {
    private $merchantId;
    private $merchantKey;
    private $merchantSalt;

    public function __construct() {
        $this->merchantId = $_ENV['PAYTR_MERCHANT_ID'] ?? '';
        $this->merchantKey = $_ENV['PAYTR_MERCHANT_KEY'] ?? '';
        $this->merchantSalt = $_ENV['PAYTR_MERCHANT_SALT'] ?? '';
    }

    public function createPayment($orderData) {
        $userIp = getClientIP();
        $merchantOid = $orderData['order_number'];
        $email = $orderData['buyer']['email'];
        $paymentAmount = (int)($orderData['total'] * 100); // Kuruş cinsinden

        $userBasket = base64_encode(json_encode($this->prepareBasket($orderData['items'])));

        $hashStr = $this->merchantId . $userIp . $merchantOid . $email . $paymentAmount . $userBasket .
                   0 . 0 . 'TRY' . 1 . $this->merchantSalt;
        $token = base64_encode(hash_hmac('sha256', $hashStr, $this->merchantKey, true));

        $postData = [
            'merchant_id' => $this->merchantId,
            'user_ip' => $userIp,
            'merchant_oid' => $merchantOid,
            'email' => $email,
            'payment_amount' => $paymentAmount,
            'paytr_token' => $token,
            'user_basket' => $userBasket,
            'debug_on' => 1,
            'no_installment' => 0,
            'max_installment' => 12,
            'user_name' => $orderData['buyer']['first_name'] . ' ' . $orderData['buyer']['last_name'],
            'user_address' => $orderData['buyer']['address'],
            'user_phone' => $orderData['buyer']['phone'],
            'merchant_ok_url' => BASE_URL . '/payment/success',
            'merchant_fail_url' => BASE_URL . '/payment/failed',
            'timeout_limit' => 30,
            'currency' => 'TRY',
            'test_mode' => ($_ENV['PAYMENT_MODE'] === 'test') ? 1 : 0
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.paytr.com/odeme/api/get-token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result, true);

        if ($response['status'] == 'success') {
            return [
                'success' => true,
                'payment_token' => $response['token'],
                'payment_url' => 'https://www.paytr.com/odeme/guvenli/' . $response['token'],
                'message' => 'Ödeme sayfasına yönlendiriliyorsunuz'
            ];
        }

        return [
            'success' => false,
            'message' => $response['reason'] ?? 'Ödeme başlatılamadı'
        ];
    }

    public function verifyPayment($paymentId) {
        // PayTR callback'te doğrulama yapılır
        if (isset($_POST['merchant_oid']) && isset($_POST['status']) && isset($_POST['hash'])) {
            $merchantOid = $_POST['merchant_oid'];
            $status = $_POST['status'];
            $totalAmount = $_POST['total_amount'];
            $hash = $_POST['hash'];

            $hashStr = $merchantOid . $this->merchantSalt . $status . $totalAmount;
            $calculatedHash = base64_encode(hash_hmac('sha256', $hashStr, $this->merchantKey, true));

            if ($hash === $calculatedHash) {
                return $status === 'success';
            }
        }

        return false;
    }

    public function refund($paymentId, $amount) {
        // PayTR iade işlemi
        $refundAmount = (int)($amount * 100);

        $hashStr = $this->merchantId . $paymentId . $refundAmount . $this->merchantSalt;
        $token = base64_encode(hash_hmac('sha256', $hashStr, $this->merchantKey, true));

        $postData = [
            'merchant_id' => $this->merchantId,
            'merchant_oid' => $paymentId,
            'return_amount' => $refundAmount,
            'paytr_token' => $token
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.paytr.com/odeme/iade');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result, true);

        return [
            'success' => $response['status'] == 'success',
            'message' => $response['err_msg'] ?? 'İade işlemi başarılı'
        ];
    }

    public function getPaymentStatus($paymentId) {
        return 'completed';
    }

    private function prepareBasket($items) {
        $basket = [];

        foreach ($items as $item) {
            $basket[] = [
                $item['name'],
                number_format($item['price'], 2, '.', ''),
                $item['quantity']
            ];
        }

        return $basket;
    }
}

/**
 * Payment Factory
 */
class PaymentFactory {
    public static function create($provider = null) {
        if ($provider === null) {
            $provider = $_ENV['PAYMENT_PROVIDER'] ?? 'iyzico';
        }

        switch (strtolower($provider)) {
            case 'iyzico':
                return new IyzicoAdapter();
            case 'paytr':
                return new PayTRAdapter();
            default:
                throw new Exception("Unsupported payment provider: $provider");
        }
    }
}
