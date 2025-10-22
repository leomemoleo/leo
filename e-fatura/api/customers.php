<?php
// Müşteri API

require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

$db = new Database();
$conn = $db->connect();

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Müşteri Listele veya Detay
            if (isset($_GET['id'])) {
                $id = intval($_GET['id']);
                $stmt = $conn->prepare("SELECT * FROM customers WHERE id = ?");
                $stmt->execute([$id]);
                $customer = $stmt->fetch();

                if ($customer) {
                    sendSuccess($customer);
                } else {
                    sendError('Müşteri bulunamadı', 404);
                }
            } else {
                // Tüm müşterileri listele
                $search = $_GET['search'] ?? '';
                $limit = intval($_GET['limit'] ?? 50);
                $offset = intval($_GET['offset'] ?? 0);

                $query = "SELECT * FROM customers WHERE 1=1";
                $params = [];

                if ($search) {
                    $query .= " AND (customer_name LIKE ? OR tax_number LIKE ? OR email LIKE ?)";
                    $searchParam = "%$search%";
                    $params = [$searchParam, $searchParam, $searchParam];
                }

                $query .= " ORDER BY customer_name ASC LIMIT ? OFFSET ?";
                $params[] = $limit;
                $params[] = $offset;

                $stmt = $conn->prepare($query);
                $stmt->execute($params);
                $customers = $stmt->fetchAll();

                // Toplam sayı
                $countQuery = "SELECT COUNT(*) as total FROM customers WHERE 1=1";
                if ($search) {
                    $countQuery .= " AND (customer_name LIKE ? OR tax_number LIKE ? OR email LIKE ?)";
                    $stmt = $conn->prepare($countQuery);
                    $stmt->execute([$searchParam, $searchParam, $searchParam]);
                } else {
                    $stmt = $conn->query($countQuery);
                }
                $total = $stmt->fetch()['total'];

                sendSuccess([
                    'customers' => $customers,
                    'total' => $total,
                    'limit' => $limit,
                    'offset' => $offset
                ]);
            }
            break;

        case 'POST':
            // Yeni Müşteri Ekle
            $data = getPostData();

            // Zorunlu alanları kontrol et
            if (empty($data['customer_name']) || empty($data['address']) || empty($data['city'])) {
                sendError('Müşteri adı, adres ve şehir zorunludur');
            }

            // Vergi numarası doğrulama (kurumsal müşteri ise)
            if ($data['customer_type'] === 'corporate' && !empty($data['tax_number'])) {
                if (!validateTaxNumber($data['tax_number'])) {
                    sendError('Geçersiz vergi numarası');
                }
            }

            // TC Kimlik No doğrulama (bireysel müşteri ise)
            if ($data['customer_type'] === 'individual' && !empty($data['tc_no'])) {
                if (!validateTCNo($data['tc_no'])) {
                    sendError('Geçersiz TC Kimlik Numarası');
                }
            }

            $stmt = $conn->prepare("
                INSERT INTO customers (
                    customer_type, customer_name, tax_office, tax_number, tc_no,
                    address, district, city, postal_code, phone, email, notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $data['customer_type'] ?? 'individual',
                sanitize($data['customer_name']),
                sanitize($data['tax_office'] ?? ''),
                sanitize($data['tax_number'] ?? ''),
                sanitize($data['tc_no'] ?? ''),
                sanitize($data['address']),
                sanitize($data['district'] ?? ''),
                sanitize($data['city']),
                sanitize($data['postal_code'] ?? ''),
                sanitize($data['phone'] ?? ''),
                sanitize($data['email'] ?? ''),
                sanitize($data['notes'] ?? '')
            ]);

            $customerId = $db->lastInsertId();

            sendSuccess(['id' => $customerId], 'Müşteri başarıyla eklendi');
            break;

        case 'PUT':
            // Müşteri Güncelle
            $data = getPostData();

            if (empty($data['id'])) {
                sendError('Müşteri ID zorunludur');
            }

            $stmt = $conn->prepare("
                UPDATE customers SET
                    customer_type = ?,
                    customer_name = ?,
                    tax_office = ?,
                    tax_number = ?,
                    tc_no = ?,
                    address = ?,
                    district = ?,
                    city = ?,
                    postal_code = ?,
                    phone = ?,
                    email = ?,
                    notes = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $data['customer_type'] ?? 'individual',
                sanitize($data['customer_name']),
                sanitize($data['tax_office'] ?? ''),
                sanitize($data['tax_number'] ?? ''),
                sanitize($data['tc_no'] ?? ''),
                sanitize($data['address']),
                sanitize($data['district'] ?? ''),
                sanitize($data['city']),
                sanitize($data['postal_code'] ?? ''),
                sanitize($data['phone'] ?? ''),
                sanitize($data['email'] ?? ''),
                sanitize($data['notes'] ?? ''),
                intval($data['id'])
            ]);

            sendSuccess([], 'Müşteri başarıyla güncellendi');
            break;

        case 'DELETE':
            // Müşteri Sil
            $id = intval($_GET['id'] ?? 0);

            if (!$id) {
                sendError('Müşteri ID zorunludur');
            }

            // Müşteriye ait fatura var mı kontrol et
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM invoices WHERE customer_id = ?");
            $stmt->execute([$id]);
            $count = $stmt->fetch()['count'];

            if ($count > 0) {
                sendError('Bu müşteriye ait faturalar bulunduğundan silinemez');
            }

            $stmt = $conn->prepare("DELETE FROM customers WHERE id = ?");
            $stmt->execute([$id]);

            sendSuccess([], 'Müşteri başarıyla silindi');
            break;

        default:
            sendError('Geçersiz istek metodu', 405);
    }

} catch (Exception $e) {
    sendError($e->getMessage(), 500);
}
