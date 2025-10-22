<?php
// Yardımcı Fonksiyonlar

// JSON Response Gönder
function sendJSON($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// Hata Mesajı Gönder
function sendError($message, $status = 400) {
    sendJSON([
        'success' => false,
        'error' => $message
    ], $status);
}

// Başarı Mesajı Gönder
function sendSuccess($data = [], $message = 'İşlem başarılı') {
    sendJSON([
        'success' => true,
        'message' => $message,
        'data' => $data
    ], 200);
}

// Veri Temizleme
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Tarih Formatla (Türkçe)
function formatDate($date, $format = 'd.m.Y') {
    return date($format, strtotime($date));
}

// Para Formatla (Türk Lirası)
function formatMoney($amount, $currency = 'TRY') {
    $formatted = number_format($amount, 2, ',', '.');

    $symbols = [
        'TRY' => '₺',
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£'
    ];

    $symbol = $symbols[$currency] ?? $currency;

    return $formatted . ' ' . $symbol;
}

// UUID v4 Oluştur
function generateUUID() {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

// ETTN Oluştur (e-Fatura Takip Numarası)
function generateETTN() {
    return strtoupper(generateUUID());
}

// Fatura Numarası Oluştur
function generateInvoiceNumber($db, $prefix = 'FTR', $year = null) {
    try {
        if ($year === null) {
            $year = date('Y');
        }

        $conn = $db->connect();

        // Transaction başlat
        $conn->beginTransaction();

        // Mevcut sırayı kontrol et
        $stmt = $conn->prepare("
            SELECT last_number
            FROM invoice_sequences
            WHERE year = ? AND sequence_prefix = ?
            FOR UPDATE
        ");
        $stmt->execute([$year, $prefix]);
        $row = $stmt->fetch();

        if ($row) {
            $nextNumber = $row['last_number'] + 1;

            // Güncelle
            $stmt = $conn->prepare("
                UPDATE invoice_sequences
                SET last_number = ?
                WHERE year = ? AND sequence_prefix = ?
            ");
            $stmt->execute([$nextNumber, $year, $prefix]);
        } else {
            $nextNumber = 1;

            // Yeni kayıt oluştur
            $stmt = $conn->prepare("
                INSERT INTO invoice_sequences (year, sequence_prefix, last_number)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$year, $prefix, $nextNumber]);
        }

        $conn->commit();

        // Fatura numarasını formatla: FTR2024000001
        $invoiceNo = $prefix . $year . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        return $invoiceNo;

    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        throw new Exception("Fatura numarası oluşturulamadı: " . $e->getMessage());
    }
}

// Vergi Numarası Doğrula (Türkiye)
function validateTaxNumber($taxNumber) {
    // Vergi numarası 10 haneli olmalı
    if (!preg_match('/^\d{10}$/', $taxNumber)) {
        return false;
    }

    $digits = str_split($taxNumber);
    $sum = 0;

    for ($i = 0; $i < 9; $i++) {
        $temp = ($digits[$i] + (10 - ($i + 1))) % 10;
        if ($temp == 9) {
            $temp = 0;
        }
        $sum += (2 * $temp) % 11;
    }

    $checkDigit = (10 - ($sum % 10)) % 10;

    return $checkDigit == $digits[9];
}

// TC Kimlik No Doğrula
function validateTCNo($tcNo) {
    if (!preg_match('/^\d{11}$/', $tcNo)) {
        return false;
    }

    if ($tcNo[0] == '0') {
        return false;
    }

    $digits = str_split($tcNo);

    $odd = $digits[0] + $digits[2] + $digits[4] + $digits[6] + $digits[8];
    $even = $digits[1] + $digits[3] + $digits[5] + $digits[7];

    $sum1 = ($odd * 7 - $even) % 10;
    if ($sum1 != $digits[9]) {
        return false;
    }

    $sum2 = array_sum(array_slice($digits, 0, 10)) % 10;
    if ($sum2 != $digits[10]) {
        return false;
    }

    return true;
}

// POST Verisi Al
function getPostData() {
    $data = json_decode(file_get_contents('php://input'), true);
    return $data ?? $_POST;
}

// Sayıyı Yazıya Çevir (Türkçe)
function numberToWords($number) {
    $ones = ['', 'bir', 'iki', 'üç', 'dört', 'beş', 'altı', 'yedi', 'sekiz', 'dokuz'];
    $tens = ['', 'on', 'yirmi', 'otuz', 'kırk', 'elli', 'altmış', 'yetmiş', 'seksen', 'doksan'];
    $hundreds = ['', 'yüz', 'ikiyüz', 'üçyüz', 'dörtyüz', 'beşyüz', 'altıyüz', 'yediyüz', 'sekizyüz', 'dokuzyüz'];

    $number = floatval($number);
    $integer = floor($number);
    $decimal = round(($number - $integer) * 100);

    if ($integer == 0) {
        $result = 'sıfır';
    } else if ($integer < 0) {
        return 'Negatif sayılar desteklenmiyor';
    } else {
        $result = convertIntegerToWords($integer, $ones, $tens, $hundreds);
    }

    $result .= ' Türk Lirası';

    if ($decimal > 0) {
        $result .= ' ' . convertIntegerToWords($decimal, $ones, $tens, $hundreds) . ' Kuruş';
    }

    return ucfirst(trim($result));
}

function convertIntegerToWords($number, $ones, $tens, $hundreds) {
    if ($number >= 1000000) {
        $millions = floor($number / 1000000);
        $result = ($millions == 1 ? '' : convertIntegerToWords($millions, $ones, $tens, $hundreds)) . ' milyon';
        $remainder = $number % 1000000;
        if ($remainder > 0) {
            $result .= ' ' . convertIntegerToWords($remainder, $ones, $tens, $hundreds);
        }
        return $result;
    }

    if ($number >= 1000) {
        $thousands = floor($number / 1000);
        $result = ($thousands == 1 ? '' : convertIntegerToWords($thousands, $ones, $tens, $hundreds)) . ' bin';
        $remainder = $number % 1000;
        if ($remainder > 0) {
            $result .= ' ' . convertIntegerToWords($remainder, $ones, $tens, $hundreds);
        }
        return $result;
    }

    $result = '';

    if ($number >= 100) {
        $h = floor($number / 100);
        $result .= $hundreds[$h];
        $number %= 100;
    }

    if ($number >= 10) {
        $t = floor($number / 10);
        $result .= ($result ? ' ' : '') . $tens[$t];
        $number %= 10;
    }

    if ($number > 0) {
        $result .= ($result ? ' ' : '') . $ones[$number];
    }

    return trim($result);
}
