<?php
// Fatura API

require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

$db = new Database();
$conn = $db->connect();

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Fatura Listele veya Detay
            if (isset($_GET['id'])) {
                $id = intval($_GET['id']);

                // Fatura bilgilerini al
                $stmt = $conn->prepare("
                    SELECT i.*, c.customer_name, c.tax_office, c.tax_number, c.address, c.city
                    FROM invoices i
                    LEFT JOIN customers c ON i.customer_id = c.id
                    WHERE i.id = ?
                ");
                $stmt->execute([$id]);
                $invoice = $stmt->fetch();

                if (!$invoice) {
                    sendError('Fatura bulunamadı', 404);
                }

                // Fatura kalemlerini al
                $stmt = $conn->prepare("
                    SELECT * FROM invoice_items
                    WHERE invoice_id = ?
                    ORDER BY line_number ASC
                ");
                $stmt->execute([$id]);
                $invoice['items'] = $stmt->fetchAll();

                // KDV özetini al
                $stmt = $conn->prepare("
                    SELECT * FROM invoice_tax_summary
                    WHERE invoice_id = ?
                    ORDER BY kdv_rate ASC
                ");
                $stmt->execute([$id]);
                $invoice['tax_summary'] = $stmt->fetchAll();

                sendSuccess($invoice);

            } else {
                // Fatura listesi
                $search = $_GET['search'] ?? '';
                $status = $_GET['status'] ?? '';
                $dateFrom = $_GET['date_from'] ?? '';
                $dateTo = $_GET['date_to'] ?? '';
                $customerId = intval($_GET['customer_id'] ?? 0);
                $limit = intval($_GET['limit'] ?? 50);
                $offset = intval($_GET['offset'] ?? 0);

                $query = "
                    SELECT i.*, c.customer_name
                    FROM invoices i
                    LEFT JOIN customers c ON i.customer_id = c.id
                    WHERE 1=1
                ";
                $params = [];

                if ($search) {
                    $query .= " AND (i.invoice_no LIKE ? OR c.customer_name LIKE ?)";
                    $searchParam = "%$search%";
                    $params[] = $searchParam;
                    $params[] = $searchParam;
                }

                if ($status) {
                    $query .= " AND i.invoice_status = ?";
                    $params[] = $status;
                }

                if ($dateFrom) {
                    $query .= " AND i.invoice_date >= ?";
                    $params[] = $dateFrom;
                }

                if ($dateTo) {
                    $query .= " AND i.invoice_date <= ?";
                    $params[] = $dateTo;
                }

                if ($customerId) {
                    $query .= " AND i.customer_id = ?";
                    $params[] = $customerId;
                }

                $query .= " ORDER BY i.invoice_date DESC, i.id DESC LIMIT ? OFFSET ?";
                $params[] = $limit;
                $params[] = $offset;

                $stmt = $conn->prepare($query);
                $stmt->execute($params);
                $invoices = $stmt->fetchAll();

                // Toplam sayı
                $countQuery = str_replace("SELECT i.*, c.customer_name", "SELECT COUNT(*) as total", explode("ORDER BY", $query)[0]);
                $countParams = array_slice($params, 0, -2); // Son 2 parametre (limit, offset) hariç

                $stmt = $conn->prepare($countQuery);
                $stmt->execute($countParams);
                $total = $stmt->fetch()['total'];

                sendSuccess([
                    'invoices' => $invoices,
                    'total' => $total,
                    'limit' => $limit,
                    'offset' => $offset
                ]);
            }
            break;

        case 'POST':
            // Yeni Fatura Oluştur
            $data = getPostData();

            if (empty($data['customer_id']) || empty($data['items']) || count($data['items']) == 0) {
                sendError('Müşteri ve en az bir ürün zorunludur');
            }

            $conn->beginTransaction();

            try {
                // Fatura numarası oluştur
                $invoiceNo = generateInvoiceNumber($db);
                $invoiceUUID = generateUUID();
                $ettn = generateETTN();

                // Tutarları hesapla
                $subtotal = 0;
                $totalKDV = 0;
                $taxBreakdown = [];

                foreach ($data['items'] as $item) {
                    $quantity = floatval($item['quantity']);
                    $unitPrice = floatval($item['unit_price']);
                    $discountRate = floatval($item['discount_rate'] ?? 0);
                    $kdvRate = floatval($item['kdv_rate']);

                    $lineTotal = $quantity * $unitPrice;
                    $discountAmount = $lineTotal * ($discountRate / 100);
                    $lineTotal -= $discountAmount;

                    $kdvAmount = $lineTotal * ($kdvRate / 100);

                    $subtotal += $lineTotal;
                    $totalKDV += $kdvAmount;

                    // KDV özeti için
                    if (!isset($taxBreakdown[$kdvRate])) {
                        $taxBreakdown[$kdvRate] = [
                            'taxable_amount' => 0,
                            'tax_amount' => 0
                        ];
                    }
                    $taxBreakdown[$kdvRate]['taxable_amount'] += $lineTotal;
                    $taxBreakdown[$kdvRate]['tax_amount'] += $kdvAmount;
                }

                // Genel iskonto
                $discountAmount = floatval($data['discount_amount'] ?? 0);
                $discountRate = floatval($data['discount_rate'] ?? 0);

                if ($discountRate > 0) {
                    $discountAmount = $subtotal * ($discountRate / 100);
                }

                $subtotal -= $discountAmount;

                // KDV'yi yeniden hesapla (iskonto sonrası)
                if ($discountAmount > 0) {
                    $totalKDV = 0;
                    foreach ($taxBreakdown as $rate => &$breakdown) {
                        $ratio = $breakdown['taxable_amount'] / ($subtotal + $discountAmount);
                        $breakdown['taxable_amount'] = $subtotal * $ratio;
                        $breakdown['tax_amount'] = $breakdown['taxable_amount'] * ($rate / 100);
                        $totalKDV += $breakdown['tax_amount'];
                    }
                }

                $totalAmount = $subtotal + $totalKDV;

                // Fatura kaydı
                $stmt = $conn->prepare("
                    INSERT INTO invoices (
                        invoice_no, invoice_uuid, invoice_type, invoice_scenario,
                        customer_id, invoice_date, invoice_time, currency_code,
                        exchange_rate, subtotal, total_kdv, discount_amount,
                        discount_rate, total_amount, ettn, profile_id,
                        invoice_status, notes
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");

                $stmt->execute([
                    $invoiceNo,
                    $invoiceUUID,
                    $data['invoice_type'] ?? 'sales',
                    $data['invoice_scenario'] ?? 'basic',
                    intval($data['customer_id']),
                    $data['invoice_date'] ?? date('Y-m-d'),
                    $data['invoice_time'] ?? date('H:i:s'),
                    $data['currency_code'] ?? 'TRY',
                    floatval($data['exchange_rate'] ?? 1.0),
                    $subtotal,
                    $totalKDV,
                    $discountAmount,
                    $discountRate,
                    $totalAmount,
                    $ettn,
                    $data['profile_id'] ?? 'TICARIFATURA',
                    $data['invoice_status'] ?? 'draft',
                    sanitize($data['notes'] ?? '')
                ]);

                $invoiceId = $db->lastInsertId();

                // Fatura kalemleri
                $lineNumber = 1;
                foreach ($data['items'] as $item) {
                    $quantity = floatval($item['quantity']);
                    $unitPrice = floatval($item['unit_price']);
                    $discountRate = floatval($item['discount_rate'] ?? 0);
                    $kdvRate = floatval($item['kdv_rate']);

                    $lineTotal = $quantity * $unitPrice;
                    $itemDiscountAmount = $lineTotal * ($discountRate / 100);
                    $lineTotal -= $itemDiscountAmount;

                    $kdvAmount = $lineTotal * ($kdvRate / 100);
                    $lineTotalWithKDV = $lineTotal + $kdvAmount;

                    $stmt = $conn->prepare("
                        INSERT INTO invoice_items (
                            invoice_id, product_id, line_number, product_code,
                            product_name, description, quantity, unit, unit_price,
                            discount_rate, discount_amount, kdv_rate, kdv_amount,
                            line_total, line_total_with_kdv
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");

                    $stmt->execute([
                        $invoiceId,
                        isset($item['product_id']) ? intval($item['product_id']) : null,
                        $lineNumber++,
                        sanitize($item['product_code'] ?? ''),
                        sanitize($item['product_name']),
                        sanitize($item['description'] ?? ''),
                        $quantity,
                        sanitize($item['unit'] ?? 'Adet'),
                        $unitPrice,
                        $discountRate,
                        $itemDiscountAmount,
                        $kdvRate,
                        $kdvAmount,
                        $lineTotal,
                        $lineTotalWithKDV
                    ]);
                }

                // KDV özeti
                foreach ($taxBreakdown as $rate => $breakdown) {
                    $stmt = $conn->prepare("
                        INSERT INTO invoice_tax_summary (invoice_id, kdv_rate, taxable_amount, tax_amount)
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $invoiceId,
                        $rate,
                        $breakdown['taxable_amount'],
                        $breakdown['tax_amount']
                    ]);
                }

                $conn->commit();

                sendSuccess([
                    'id' => $invoiceId,
                    'invoice_no' => $invoiceNo,
                    'invoice_uuid' => $invoiceUUID,
                    'ettn' => $ettn
                ], 'Fatura başarıyla oluşturuldu');

            } catch (Exception $e) {
                $conn->rollBack();
                throw $e;
            }
            break;

        case 'PUT':
            // Fatura Güncelle
            $data = getPostData();

            if (empty($data['id'])) {
                sendError('Fatura ID zorunludur');
            }

            $invoiceId = intval($data['id']);

            // Fatura durumunu kontrol et
            $stmt = $conn->prepare("SELECT invoice_status FROM invoices WHERE id = ?");
            $stmt->execute([$invoiceId]);
            $invoice = $stmt->fetch();

            if (!$invoice) {
                sendError('Fatura bulunamadı', 404);
            }

            if ($invoice['invoice_status'] !== 'draft') {
                sendError('Sadece taslak faturalar düzenlenebilir');
            }

            // Güncelleme işlemi (POST ile benzer mantık)
            // Kısalık için basit güncelleme
            $stmt = $conn->prepare("
                UPDATE invoices SET
                    notes = ?,
                    invoice_status = ?
                WHERE id = ?
            ");

            $stmt->execute([
                sanitize($data['notes'] ?? ''),
                $data['invoice_status'] ?? 'draft',
                $invoiceId
            ]);

            sendSuccess([], 'Fatura başarıyla güncellendi');
            break;

        case 'DELETE':
            // Fatura Sil (Sadece taslaklar silinebilir)
            $id = intval($_GET['id'] ?? 0);

            if (!$id) {
                sendError('Fatura ID zorunludur');
            }

            $stmt = $conn->prepare("SELECT invoice_status FROM invoices WHERE id = ?");
            $stmt->execute([$id]);
            $invoice = $stmt->fetch();

            if (!$invoice) {
                sendError('Fatura bulunamadı', 404);
            }

            if ($invoice['invoice_status'] !== 'draft') {
                sendError('Sadece taslak faturalar silinebilir');
            }

            $conn->beginTransaction();

            try {
                // Önce kalemler ve KDV özeti silinir (CASCADE ile otomatik olur)
                $stmt = $conn->prepare("DELETE FROM invoices WHERE id = ?");
                $stmt->execute([$id]);

                $conn->commit();

                sendSuccess([], 'Fatura başarıyla silindi');

            } catch (Exception $e) {
                $conn->rollBack();
                throw $e;
            }
            break;

        default:
            sendError('Geçersiz istek metodu', 405);
    }

} catch (Exception $e) {
    sendError($e->getMessage(), 500);
}
