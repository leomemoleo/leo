-- MINALIA Parfüm - Demo Verileri
-- Test için örnek kategoriler, markalar ve ürünler

USE minalia_perfume;

-- Kategoriler
INSERT INTO categories (name, slug, description, sort_order, is_active) VALUES
('Erkek Parfüm', 'erkek-parfum', 'Erkekler için özel parfümler', 1, 1),
('Kadın Parfüm', 'kadin-parfum', 'Kadınlar için özel parfümler', 2, 1),
('Unisex Parfüm', 'unisex-parfum', 'Herkes için parfümler', 3, 1),
('Niş Parfüm', 'nis-parfum', 'Özel ve niş parfümler', 4, 1);

-- Markalar
INSERT INTO brands (name, slug, description, country, is_featured, sort_order) VALUES
('Chanel', 'chanel', 'Fransız lüks marka', 'Fransa', 1, 1),
('Dior', 'dior', 'Prestijli parfüm markası', 'Fransa', 1, 2),
('Tom Ford', 'tom-ford', 'Modern lüks parfümler', 'ABD', 1, 3),
('Creed', 'creed', 'Niş parfüm üreticisi', 'İngiltere', 1, 4),
('Jo Malone', 'jo-malone', 'İngiliz parfüm markası', 'İngiltere', 1, 5);

-- Ürünler
INSERT INTO products (sku, name, slug, brand_id, category_id, description, short_description, price, sale_price, stock_quantity, fragrance_notes, gender, launch_year, perfumer, is_featured, is_new, is_bestseller, rating, review_count) VALUES
-- Chanel Ürünleri
('CHANEL-001', 'Chanel No. 5 Eau de Parfum', 'chanel-no-5-eau-de-parfum', 1, 2, 'Dünyanın en ünlü parfümü. Zarif ve sofistike bir koku.', 'Klasik ve zamansız kadın parfümü', 3500.00, 2999.00, 15, '{"ust": ["Neroli", "Ylang-Ylang"], "orta": ["Jasmine", "Rose"], "alt": ["Vetiver", "Sandalwood"]}', 'women', 1921, 'Ernest Beaux', 1, 0, 1, 4.8, 156),

('CHANEL-002', 'Bleu de Chanel Eau de Toilette', 'bleu-de-chanel-eau-de-toilette', 1, 1, 'Modern ve güçlü erkek parfümü. Taze ve odunsu notalar.', 'Erkeksi ve karizmatik koku', 2800.00, NULL, 22, '{"ust": ["Bergamot", "Limon"], "orta": ["Zencefil", "Jasmine"], "alt": ["Cedar", "Sandalwood"]}', 'men', 2010, 'Jacques Polge', 1, 1, 1, 4.7, 203),

-- Dior Ürünleri
('DIOR-001', 'Dior Sauvage Eau de Parfum', 'dior-sauvage-eau-de-parfum', 2, 1, 'Vahşi ve özgür ruh. Erkeksi ve güçlü bir parfüm.', 'En çok satan erkek parfümü', 3200.00, 2799.00, 18, '{"ust": ["Bergamot", "Pepper"], "orta": ["Lavender", "Geranium"], "alt": ["Amberwood", "Patchouli"]}', 'men', 2015, 'François Demachy', 1, 0, 1, 4.9, 421),

('DIOR-002', 'Miss Dior Blooming Bouquet', 'miss-dior-blooming-bouquet', 2, 2, 'Taze ve romantik çiçek buketi. Genç ve zarif.', 'Bahar kokulu kadın parfümü', 2950.00, NULL, 12, '{"ust": ["Mandalina", "Bergamot"], "orta": ["Gül", "Şakayık"], "alt": ["Beyaz Misk"]}', 'women', 2014, 'François Demachy', 1, 1, 0, 4.6, 187),

-- Tom Ford Ürünleri
('TOMFORD-001', 'Tom Ford Oud Wood', 'tom-ford-oud-wood', 3, 3, 'Egzotik oud ve baharat karışımı. Lüks ve zengin.', 'Niş oud parfümü', 8500.00, 7999.00, 8, '{"ust": ["Kırmızı Biber", "Kardamon"], "orta": ["Oud", "Gülağacı"], "alt": ["Amber", "Vetiver"]}', 'unisex', 2007, 'Richard Herpin', 1, 0, 0, 4.8, 92),

('TOMFORD-002', 'Tom Ford Black Orchid', 'tom-ford-black-orchid', 3, 2, 'Karanlık ve gizemli. Çikolata ve orkide notaları.', 'Gizemli kadın parfümü', 7200.00, NULL, 10, '{"ust": ["Trüf", "Bergamot"], "orta": ["Siyah Orkide", "Yasemin"], "alt": ["Patchouli", "Vanilya"]}', 'women', 2006, 'David Apel', 1, 0, 1, 4.7, 256),

-- Creed Ürünleri
('CREED-001', 'Creed Aventus', 'creed-aventus', 4, 1, 'Güç ve başarının kokusu. Meyvemsi ve odunsu.', 'Efsanevi niş erkek parfümü', 12500.00, 11999.00, 5, '{"ust": ["Ananas", "Bergamot", "Elma"], "orta": ["Gül", "Yasemin"], "alt": ["Birch", "Misk"]}', 'men', 2010, 'Olivier Creed', 1, 0, 1, 4.9, 523),

('CREED-002', 'Creed Silver Mountain Water', 'creed-silver-mountain-water', 4, 3, 'Berrak göl suyu ve dağ esintisi. Ferahlatıcı.', 'Taze unisex parfüm', 9800.00, NULL, 7, '{"ust": ["Bergamot", "Mandalina"], "orta": ["Yeşil Çay"], "alt": ["Misk", "Sandalwood"]}', 'unisex', 1995, 'Olivier Creed', 1, 1, 0, 4.6, 134),

-- Jo Malone Ürünleri
('JOMALONE-001', 'Jo Malone Wood Sage & Sea Salt', 'jo-malone-wood-sage-sea-salt', 5, 3, 'Sahil esintisi ve tuzlu hava. Doğal ve ferahlatıcı.', 'En popüler Jo Malone kokusu', 4500.00, 3999.00, 14, '{"ust": ["Deniz Tuzu"], "orta": ["Adaçayı"], "alt": ["Ambergris"]}', 'unisex', 2014, 'Christine Nagel', 1, 1, 1, 4.7, 298),

('JOMALONE-002', 'Jo Malone English Pear & Freesia', 'jo-malone-english-pear-freesia', 5, 2, 'Tatlı armut ve frezya çiçeği. Zarif ve sofistike.', 'Meyveli kadın parfümü', 4200.00, NULL, 16, '{"ust": ["Armut"], "orta": ["Frezya"], "alt": ["Patchouli", "Amber"]}', 'women', 2010, 'Christine Nagel', 0, 1, 0, 4.5, 176);

-- Ürün varyantları (boyutlar)
INSERT INTO product_variants (product_id, size, price, stock_quantity, sku_suffix) VALUES
-- Chanel No. 5
(1, '50ml', 2999.00, 8, '50ML'),
(1, '100ml', 4500.00, 7, '100ML'),
-- Bleu de Chanel
(2, '50ml', 2800.00, 12, '50ML'),
(2, '100ml', 3800.00, 10, '100ML'),
-- Sauvage
(3, '60ml', 2799.00, 10, '60ML'),
(3, '100ml', 3999.00, 8, '100ML'),
-- Aventus
(7, '50ml', 11999.00, 3, '50ML'),
(7, '100ml', 18500.00, 2, '100ML');

-- Ürün görselleri (placeholder)
INSERT INTO product_images (product_id, image_url, alt_text, is_primary, sort_order) VALUES
(1, '/images/products/chanel-no-5.jpg', 'Chanel No. 5', 1, 0),
(2, '/images/products/bleu-de-chanel.jpg', 'Bleu de Chanel', 1, 0),
(3, '/images/products/dior-sauvage.jpg', 'Dior Sauvage', 1, 0),
(4, '/images/products/miss-dior.jpg', 'Miss Dior', 1, 0),
(5, '/images/products/oud-wood.jpg', 'Tom Ford Oud Wood', 1, 0),
(6, '/images/products/black-orchid.jpg', 'Tom Ford Black Orchid', 1, 0),
(7, '/images/products/aventus.jpg', 'Creed Aventus', 1, 0),
(8, '/images/products/silver-mountain.jpg', 'Creed Silver Mountain', 1, 0),
(9, '/images/products/wood-sage.jpg', 'Jo Malone Wood Sage', 1, 0),
(10, '/images/products/english-pear.jpg', 'Jo Malone English Pear', 1, 0);

-- Test kullanıcısı (şifre: test123)
INSERT INTO users (email, password, first_name, last_name, phone, gender, newsletter_subscribed, is_active) VALUES
('test@minalia.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test', 'Kullanıcı', '+90 555 123 4567', 'male', 1, 1);

-- Örnek yorumlar
INSERT INTO reviews (product_id, user_id, rating, title, comment, is_verified_purchase, is_approved) VALUES
(3, 1, 5, 'Harika bir parfüm!', 'Dior Sauvage gerçekten mükemmel. Hem gündüz hem gece kullanabiliyorum. Kalıcılığı çok iyi.', 1, 1),
(7, 1, 5, 'Pahalı ama değer', 'Creed Aventus fiyatı yüksek ama gerçekten hakkını veriyor. Benzersiz bir koku.', 1, 1),
(1, 1, 5, 'Klasik ve zarif', 'Chanel No. 5 hiç eskimeyen bir klasik. Annem de kullanıyor, ben de kullanıyorum.', 1, 1);

-- Newsletter aboneleri
INSERT INTO newsletter_subscribers (email, name, is_active) VALUES
('subscriber1@example.com', 'Abone 1', 1),
('subscriber2@example.com', 'Abone 2', 1);

-- Ayarlar
UPDATE settings SET setting_value = 'MINALIA Parfüm' WHERE setting_key = 'site_name';
UPDATE settings SET setting_value = 'Lüks Parfüm Deneyimi' WHERE setting_key = 'site_tagline';
