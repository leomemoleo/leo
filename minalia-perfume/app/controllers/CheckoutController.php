<?php
/**
 * Checkout Controller
 * MINALIA Parfüm E-Ticaret Platformu
 */

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Coupon.php';
require_once __DIR__ . '/../helpers/PaymentGateway.php';

class CheckoutController extends BaseController {
    private $db;
    private $productModel;
    private $couponModel;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->productModel = new Product();
        $this->couponModel = new Coupon();
    }

    /**
     * Show checkout page
     */
    public function index() {
        $this->requireAuth();

        // Get cart from session/localStorage (for now, we'll use POST data)
        $cartData = json_decode($_POST['cart_data'] ?? '[]', true);

        if (empty($cartData)) {
            setFlashMessage('Sepetiniz boş.', 'error');
            redirect('/');
            return;
        }

        // Calculate totals
        $subtotal = 0;
        $items = [];

        foreach ($cartData as $item) {
            $product = $this->productModel->find($item['product_id']);

            if ($product && $product['stock_quantity'] >= $item['quantity']) {
                $price = $product['discount_price'] ?: $product['price'];
                $items[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'total' => $price * $item['quantity']
                ];
                $subtotal += $price * $item['quantity'];
            }
        }

        // Get user addresses
        $addressSql = "SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC";
        $addressStmt = $this->db->prepare($addressSql);
        $addressStmt->execute([getCurrentUserId()]);
        $addresses = $addressStmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('checkout/index', [
            'title' => 'Ödeme',
            'items' => $items,
            'subtotal' => $subtotal,
            'shipping' => 29.99, // Fixed shipping cost
            'total' => $subtotal + 29.99,
            'addresses' => $addresses
        ]);
    }

    /**
     * Process checkout
     */
    public function process() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/checkout');
            return;
        }

        try {
            // Validate input
            $address_id = (int)($_POST['address_id'] ?? 0);
            $payment_method = $_POST['payment_method'] ?? 'credit_card';
            $notes = sanitize($_POST['notes'] ?? '');
            $coupon_code = sanitize($_POST['coupon_code'] ?? '');

            // Get cart items
            $cartData = json_decode($_POST['cart_data'] ?? '[]', true);

            if (empty($cartData)) {
                throw new Exception('Sepetiniz boş.');
            }

            // Calculate totals
            $subtotal = 0;
            $orderItems = [];

            foreach ($cartData as $item) {
                $product = $this->productModel->find($item['product_id']);

                if (!$product || $product['stock_quantity'] < $item['quantity']) {
                    throw new Exception('Ürün stokta yok: ' . $product['name']);
                }

                $price = $product['discount_price'] ?: $product['price'];
                $orderItems[] = [
                    'product_id' => $product['id'],
                    'quantity' => $item['quantity'],
                    'price' => $price
                ];
                $subtotal += $price * $item['quantity'];
            }

            // Apply coupon
            $discount = 0;
            if ($coupon_code) {
                $couponResult = $this->couponModel->validateCoupon($coupon_code, $subtotal, getCurrentUserId());
                if ($couponResult['valid']) {
                    $discount = $couponResult['discount'];
                }
            }

            $shipping = $subtotal >= 500 ? 0 : 29.99;
            $total = $subtotal - $discount + $shipping;

            // Get address
            $addressSql = "SELECT * FROM addresses WHERE id = ? AND user_id = ?";
            $addressStmt = $this->db->prepare($addressSql);
            $addressStmt->execute([$address_id, getCurrentUserId()]);
            $address = $addressStmt->fetch(PDO::FETCH_ASSOC);

            if (!$address) {
                throw new Exception('Geçersiz teslimat adresi.');
            }

            // Create order
            $order_number = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));

            $orderSql = "INSERT INTO orders (
                user_id, order_number, subtotal, discount_amount, shipping_cost, total_amount,
                status, payment_status, payment_method, shipping_address, notes, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, 'pending', 'pending', ?, ?, ?, NOW())";

            $orderStmt = $this->db->prepare($orderSql);
            $orderStmt->execute([
                getCurrentUserId(),
                $order_number,
                $subtotal,
                $discount,
                $shipping,
                $total,
                $payment_method,
                json_encode($address),
                $notes
            ]);

            $orderId = $this->db->lastInsertId();

            // Insert order items
            foreach ($orderItems as $item) {
                $itemSql = "INSERT INTO order_items (order_id, product_id, quantity, price, created_at)
                            VALUES (?, ?, ?, ?, NOW())";
                $itemStmt = $this->db->prepare($itemSql);
                $itemStmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);

                // Decrease stock
                $this->productModel->decreaseStock($item['product_id'], $item['quantity']);
            }

            // Record coupon usage
            if ($coupon_code && $discount > 0) {
                $usageSql = "INSERT INTO coupon_usage (coupon_id, user_id, order_id, discount_amount, used_at)
                             SELECT id, ?, ?, ?, NOW() FROM coupons WHERE code = ?";
                $usageStmt = $this->db->prepare($usageSql);
                $usageStmt->execute([getCurrentUserId(), $orderId, $discount, $coupon_code]);
            }

            // Process payment (simplified - normally would integrate with payment gateway)
            if ($payment_method === 'credit_card') {
                // Redirect to payment gateway
                $paymentGateway = PaymentFactory::create(PAYMENT_PROVIDER);
                $paymentResult = $paymentGateway->createPayment([
                    'order_id' => $orderId,
                    'amount' => $total,
                    'user_id' => getCurrentUserId(),
                    'callback_url' => BASE_URL . '/checkout/callback'
                ]);

                if ($paymentResult['success']) {
                    redirect($paymentResult['payment_url']);
                    return;
                }
            }

            // For other payment methods, redirect to success page
            setFlashMessage('Siparişiniz başarıyla oluşturuldu!', 'success');
            redirect('/orders/' . $orderId);

        } catch (Exception $e) {
            setFlashMessage('Hata: ' . $e->getMessage(), 'error');
            redirect('/checkout');
        }
    }

    /**
     * Payment callback
     */
    public function callback() {
        // Handle payment gateway callback
        $paymentGateway = PaymentFactory::create(PAYMENT_PROVIDER);
        $result = $paymentGateway->verifyPayment($_POST);

        if ($result['success']) {
            // Update order status
            $orderSql = "UPDATE orders SET payment_status = 'completed', status = 'processing' WHERE id = ?";
            $orderStmt = $this->db->prepare($orderSql);
            $orderStmt->execute([$result['order_id']]);

            setFlashMessage('Ödemeniz başarıyla alındı!', 'success');
            redirect('/orders/' . $result['order_id']);
        } else {
            setFlashMessage('Ödeme başarısız. Lütfen tekrar deneyin.', 'error');
            redirect('/checkout');
        }
    }
}
