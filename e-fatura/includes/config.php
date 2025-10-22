<?php
// e-Fatura Sistemi Yapılandırma Dosyası
// Türkiye e-Fatura Standartlarına Uyumlu

// Hata Raporlama
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Zaman Dilimi
date_default_timezone_set('Europe/Istanbul');

// Veritabanı Yapılandırması
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'efatura_db');

// Uygulama Ayarları
define('APP_NAME', 'e-Fatura Sistemi');
define('APP_VERSION', '1.0.0');
define('BASE_URL', '/e-fatura/');

// Dosya Yolları
define('EXPORT_PATH', __DIR__ . '/../exports/');
define('PDF_PATH', EXPORT_PATH . 'pdf/');
define('XML_PATH', EXPORT_PATH . 'xml/');

// Türkiye KDV Oranları (2024)
define('KDV_RATES', [
    '0' => 0,
    '1' => 1,
    '10' => 10,
    '20' => 20
]);

// Para Birimleri
define('CURRENCIES', [
    'TRY' => 'Türk Lirası',
    'USD' => 'Amerikan Doları',
    'EUR' => 'Euro',
    'GBP' => 'İngiliz Sterlini'
]);

// Birimler
define('UNITS', [
    'Adet' => 'Adet',
    'Kg' => 'Kilogram',
    'Gr' => 'Gram',
    'Lt' => 'Litre',
    'M' => 'Metre',
    'M2' => 'Metrekare',
    'M3' => 'Metreküp',
    'Koli' => 'Koli',
    'Paket' => 'Paket',
    'Kutu' => 'Kutu',
    'Saat' => 'Saat',
    'Gün' => 'Gün',
    'Ay' => 'Ay',
    'Yıl' => 'Yıl',
    'Sayfa' => 'Sayfa'
]);

// Fatura Senaryoları (e-Fatura Standartları)
define('INVOICE_SCENARIOS', [
    'basic' => 'Temel Fatura',
    'commercial' => 'Ticari Fatura',
    'export' => 'İhracat Faturası',
    'special' => 'Özel Matrah Faturası'
]);

// Güvenlik
define('SECRET_KEY', 'your-secret-key-here-change-in-production');

// CORS Ayarları
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
