<?php
// Ürün/Hizmet API

require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

$db = new Database();
$conn = $db->connect();

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Ürün Listele veya Detay
            if (isset($_GET['id'])) {
                $id = intval($_GET['id']);
                $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
                $stmt->execute([$id]);
                $product = $stmt->fetch();

                if ($product) {
                    sendSuccess($product);
                } else {
                    sendError('Ürün bulunamadı', 404);
                }
            } else {
                // Tüm ürünleri listele
                $search = $_GET['search'] ?? '';
                $active = isset($_GET['active']) ? intval($_GET['active']) : null;
                $type = $_GET['type'] ?? null;

                $query = "SELECT * FROM products WHERE 1=1";
                $params = [];

                if ($search) {
                    $query .= " AND (product_name LIKE ? OR product_code LIKE ? OR description LIKE ?)";
                    $searchParam = "%$search%";
                    $params = [$searchParam, $searchParam, $searchParam];
                }

                if ($active !== null) {
                    $query .= " AND is_active = ?";
                    $params[] = $active;
                }

                if ($type) {
                    $query .= " AND product_type = ?";
                    $params[] = $type;
                }

                $query .= " ORDER BY product_name ASC";

                $stmt = $conn->prepare($query);
                $stmt->execute($params);
                $products = $stmt->fetchAll();

                sendSuccess($products);
            }
            break;

        case 'POST':
            // Yeni Ürün Ekle
            $data = getPostData();

            if (empty($data['product_name']) || empty($data['unit_price'])) {
                sendError('Ürün adı ve birim fiyat zorunludur');
            }

            // Ürün kodu varsa, benzersiz olmalı
            if (!empty($data['product_code'])) {
                $stmt = $conn->prepare("SELECT COUNT(*) as count FROM products WHERE product_code = ?");
                $stmt->execute([sanitize($data['product_code'])]);
                if ($stmt->fetch()['count'] > 0) {
                    sendError('Bu ürün kodu zaten kullanılıyor');
                }
            }

            $stmt = $conn->prepare("
                INSERT INTO products (
                    product_code, product_name, description, unit, unit_price,
                    kdv_rate, product_type, is_active
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                sanitize($data['product_code'] ?? ''),
                sanitize($data['product_name']),
                sanitize($data['description'] ?? ''),
                sanitize($data['unit'] ?? 'Adet'),
                floatval($data['unit_price']),
                floatval($data['kdv_rate'] ?? 20),
                $data['product_type'] ?? 'product',
                intval($data['is_active'] ?? 1)
            ]);

            $productId = $db->lastInsertId();

            sendSuccess(['id' => $productId], 'Ürün başarıyla eklendi');
            break;

        case 'PUT':
            // Ürün Güncelle
            $data = getPostData();

            if (empty($data['id'])) {
                sendError('Ürün ID zorunludur');
            }

            $stmt = $conn->prepare("
                UPDATE products SET
                    product_code = ?,
                    product_name = ?,
                    description = ?,
                    unit = ?,
                    unit_price = ?,
                    kdv_rate = ?,
                    product_type = ?,
                    is_active = ?
                WHERE id = ?
            ");

            $stmt->execute([
                sanitize($data['product_code'] ?? ''),
                sanitize($data['product_name']),
                sanitize($data['description'] ?? ''),
                sanitize($data['unit'] ?? 'Adet'),
                floatval($data['unit_price']),
                floatval($data['kdv_rate'] ?? 20),
                $data['product_type'] ?? 'product',
                intval($data['is_active'] ?? 1),
                intval($data['id'])
            ]);

            sendSuccess([], 'Ürün başarıyla güncellendi');
            break;

        case 'DELETE':
            // Ürün Sil
            $id = intval($_GET['id'] ?? 0);

            if (!$id) {
                sendError('Ürün ID zorunludur');
            }

            // Soft delete (is_active = 0)
            $stmt = $conn->prepare("UPDATE products SET is_active = 0 WHERE id = ?");
            $stmt->execute([$id]);

            sendSuccess([], 'Ürün başarıyla silindi');
            break;

        default:
            sendError('Geçersiz istek metodu', 405);
    }

} catch (Exception $e) {
    sendError($e->getMessage(), 500);
}
