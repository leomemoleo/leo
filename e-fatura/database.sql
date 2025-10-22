-- e-Fatura Sistemi Veritabanı
-- Türkiye e-Fatura Standartlarına Uyumlu

CREATE DATABASE IF NOT EXISTS efatura_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE efatura_db;

-- Şirket Bilgileri Tablosu
CREATE TABLE IF NOT EXISTS company (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    tax_office VARCHAR(100) NOT NULL,
    tax_number VARCHAR(20) NOT NULL UNIQUE,
    mersis_no VARCHAR(20),
    address TEXT NOT NULL,
    district VARCHAR(100),
    city VARCHAR(100) NOT NULL,
    postal_code VARCHAR(10),
    phone VARCHAR(20),
    email VARCHAR(100),
    website VARCHAR(150),
    logo_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Müşteri Bilgileri Tablosu
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_type ENUM('individual', 'corporate') DEFAULT 'individual',
    customer_name VARCHAR(255) NOT NULL,
    tax_office VARCHAR(100),
    tax_number VARCHAR(20),
    tc_no VARCHAR(11),
    address TEXT NOT NULL,
    district VARCHAR(100),
    city VARCHAR(100) NOT NULL,
    postal_code VARCHAR(10),
    phone VARCHAR(20),
    email VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_tax_number (tax_number),
    INDEX idx_tc_no (tc_no)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ürün/Hizmet Tablosu
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_code VARCHAR(50) UNIQUE,
    product_name VARCHAR(255) NOT NULL,
    description TEXT,
    unit VARCHAR(20) DEFAULT 'Adet',
    unit_price DECIMAL(15,2) NOT NULL,
    kdv_rate DECIMAL(5,2) NOT NULL DEFAULT 20.00,
    product_type ENUM('product', 'service') DEFAULT 'product',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_product_code (product_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Faturalar Tablosu
CREATE TABLE IF NOT EXISTS invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_no VARCHAR(50) UNIQUE NOT NULL,
    invoice_uuid VARCHAR(36) UNIQUE NOT NULL,
    invoice_type ENUM('sales', 'purchase', 'return') DEFAULT 'sales',
    invoice_scenario ENUM('basic', 'commercial', 'export', 'special') DEFAULT 'basic',
    customer_id INT NOT NULL,
    invoice_date DATE NOT NULL,
    invoice_time TIME NOT NULL,
    currency_code VARCHAR(3) DEFAULT 'TRY',
    exchange_rate DECIMAL(10,4) DEFAULT 1.0000,

    -- Tutar Bilgileri
    subtotal DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    total_kdv DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    discount_amount DECIMAL(15,2) DEFAULT 0.00,
    discount_rate DECIMAL(5,2) DEFAULT 0.00,
    total_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,

    -- e-Fatura Bilgileri
    ettn VARCHAR(36),
    profile_id VARCHAR(50) DEFAULT 'TICARIFATURA',
    invoice_status ENUM('draft', 'issued', 'sent', 'cancelled') DEFAULT 'draft',

    -- XML ve PDF
    xml_path VARCHAR(255),
    pdf_path VARCHAR(255),

    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT,
    INDEX idx_invoice_no (invoice_no),
    INDEX idx_invoice_date (invoice_date),
    INDEX idx_customer_id (customer_id),
    INDEX idx_invoice_status (invoice_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Fatura Kalemleri Tablosu
CREATE TABLE IF NOT EXISTS invoice_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    product_id INT,
    line_number INT NOT NULL,
    product_code VARCHAR(50),
    product_name VARCHAR(255) NOT NULL,
    description TEXT,
    quantity DECIMAL(15,3) NOT NULL DEFAULT 1.000,
    unit VARCHAR(20) DEFAULT 'Adet',
    unit_price DECIMAL(15,2) NOT NULL,
    discount_rate DECIMAL(5,2) DEFAULT 0.00,
    discount_amount DECIMAL(15,2) DEFAULT 0.00,
    kdv_rate DECIMAL(5,2) NOT NULL,
    kdv_amount DECIMAL(15,2) NOT NULL,
    line_total DECIMAL(15,2) NOT NULL,
    line_total_with_kdv DECIMAL(15,2) NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    INDEX idx_invoice_id (invoice_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- KDV Detay Tablosu (Fatura bazında KDV oranlarına göre toplam)
CREATE TABLE IF NOT EXISTS invoice_tax_summary (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    kdv_rate DECIMAL(5,2) NOT NULL,
    taxable_amount DECIMAL(15,2) NOT NULL,
    tax_amount DECIMAL(15,2) NOT NULL,

    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    INDEX idx_invoice_id (invoice_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Fatura Sıra Numarası Yönetimi
CREATE TABLE IF NOT EXISTS invoice_sequences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year INT NOT NULL,
    sequence_prefix VARCHAR(10) NOT NULL DEFAULT 'FTR',
    last_number INT NOT NULL DEFAULT 0,
    UNIQUE KEY unique_year_prefix (year, sequence_prefix)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sistem Ayarları
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Örnek Şirket Bilgisi
INSERT INTO company (company_name, tax_office, tax_number, address, city, phone, email)
VALUES (
    '42 DİL TERCÜME BÜROSU',
    'Ankara',
    '1234567890',
    'Örnek Mahalle Örnek Sokak No: 1',
    'Ankara',
    '+90 312 123 45 67',
    'info@42dil.com'
) ON DUPLICATE KEY UPDATE company_name = company_name;

-- Örnek Sistem Ayarları
INSERT INTO settings (setting_key, setting_value, description) VALUES
('invoice_prefix', 'FTR', 'Fatura numarası öneki'),
('default_kdv_rate', '20', 'Varsayılan KDV oranı'),
('currency', 'TRY', 'Varsayılan para birimi'),
('date_format', 'd.m.Y', 'Tarih formatı'),
('company_id', '1', 'Aktif şirket ID')
ON DUPLICATE KEY UPDATE setting_value = setting_value;

-- Örnek Ürünler
INSERT INTO products (product_code, product_name, description, unit, unit_price, kdv_rate, product_type) VALUES
('HZM001', 'Tercüme Hizmeti', 'Profesyonel tercüme hizmeti (sayfa)', 'Sayfa', 100.00, 20.00, 'service'),
('HZM002', 'Yeminli Tercüme', 'Yeminli tercüman hizmeti (sayfa)', 'Sayfa', 150.00, 20.00, 'service'),
('HZM003', 'Noter Tasdik', 'Noter tasdik işlemi', 'Adet', 50.00, 20.00, 'service'),
('HZM004', 'Apostil İşlemi', 'Apostil işlemi', 'Adet', 200.00, 20.00, 'service')
ON DUPLICATE KEY UPDATE product_name = product_name;

-- Örnek Müşteri
INSERT INTO customers (customer_type, customer_name, tax_office, tax_number, address, city, phone, email) VALUES
('corporate', 'Örnek Müşteri A.Ş.', 'Çankaya', '9876543210', 'Kızılay Mah. Atatürk Bulvarı No: 100', 'Ankara', '+90 312 987 65 43', 'info@ornek.com'),
('individual', 'Ahmet Yılmaz', NULL, NULL, 'Çankaya Mah. 123. Sokak No: 45', 'Ankara', '+90 532 123 45 67', 'ahmet@example.com')
ON DUPLICATE KEY UPDATE customer_name = customer_name;
