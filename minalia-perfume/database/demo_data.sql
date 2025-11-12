-- ========================================
-- MINALIA Parfüm - Demo Data
-- Realistic Product, Brand & Category Data
-- ========================================

-- Clear existing demo data (optional - comment out if you want to keep existing data)
-- DELETE FROM order_items WHERE order_id IN (SELECT id FROM orders);
-- DELETE FROM orders;
-- DELETE FROM reviews;
-- DELETE FROM products;
-- DELETE FROM brands;
-- DELETE FROM categories WHERE id > 4;

-- ========================================
-- BRANDS (Lüks Parfüm Markaları)
-- ========================================

INSERT INTO brands (name, slug, description, logo, is_featured, sort_order, created_at) VALUES
('Chanel', 'chanel', 'Fransız lüks moda ve güzellik markası. 1910 yılında Coco Chanel tarafından kuruldu.', NULL, 1, 1, NOW()),
('Dior', 'dior', 'Christian Dior\'un kurduğu Fransız lüks moda ve koku markası.', NULL, 1, 2, NOW()),
('Tom Ford', 'tom-ford', 'Amerikan lüks moda tasarımcısı ve markası. Modern ve seksi kokular.', NULL, 1, 3, NOW()),
('Creed', 'creed', '1760 yılından beri el yapımı lüks parfümler üreten İngiliz markası.', NULL, 1, 4, NOW()),
('Jo Malone', 'jo-malone', 'Britanya kökenli lüks niş parfüm markası. Zarif ve sofistike kokular.', NULL, 1, 5, NOW()),
('Yves Saint Laurent', 'yves-saint-laurent', 'İkonik Fransız moda ve güzellik markası.', NULL, 1, 6, NOW()),
('Armani', 'armani', 'İtalyan lüks moda ve koku evi Giorgio Armani.', NULL, 1, 7, NOW()),
('Gucci', 'gucci', 'İtalyan lüks moda ve aksesuar markası.', NULL, 1, 8, NOW()),
('Versace', 'versace', 'İtalyan lüks moda ve koku markası Gianni Versace.', NULL, 0, 9, NOW()),
('Burberry', 'burberry', 'İngiliz lüks moda markası, klasik ve zarif kokular.', NULL, 0, 10, NOW()),
('Paco Rabanne', 'paco-rabanne', 'İspanyol moda tasarımcısı ve parfüm markası.', NULL, 0, 11, NOW()),
('Calvin Klein', 'calvin-klein', 'Amerikan moda markası, modern ve minimal kokular.', NULL, 0, 12, NOW()),
('Hermès', 'hermes', 'Fransız lüks moda ve deri ürünleri markası.', NULL, 1, 13, NOW()),
('Bvlgari', 'bvlgari', 'İtalyan lüks mücevher ve parfüm markası.', NULL, 0, 14, NOW()),
('Dolce & Gabbana', 'dolce-gabbana', 'İtalyan lüks moda dünyasının iki büyük ismi.', NULL, 0, 15, NOW());

-- ========================================
-- PRODUCTS (Top 20 Bestsellers)
-- ========================================

-- Chanel Products
INSERT INTO products (name, slug, description, price, stock_quantity, category_id, brand_id, gender, size, notes, launch_year, is_featured, is_bestseller, is_new, rating, review_count, created_at) VALUES
('Chanel No. 5 Eau de Parfum', 'chanel-no-5-edp', 'Dünyanın en ünlü parfümü. Aldehitli çiçek buketi, kadınlığın sembolü.', 3500.00, 45, 2, 1, 'kadın', '100ml', 'Aldehydes, Ylang-Ylang, Neroli, Jasmine, Rose, Sandalwood, Vanilla', 1921, 1, 1, 0, 4.8, 342, NOW()),
('Bleu de Chanel Eau de Toilette', 'bleu-de-chanel-edt', 'Erkek zarafetini ve özgürlüğünü yansıtan modern koku.', 2800.00, 38, 1, 1, 'erkek', '100ml', 'Grapefruit, Lemon, Mint, Pink Pepper, Vetiver, Cedar, Incense', 2010, 1, 1, 0, 4.7, 289, NOW()),
('Chanel Chance Eau Tendre', 'chanel-chance-eau-tendre', 'Genç ve tazeleyici çiçeksi-meyve notaları ile kadın parfümü.', 3200.00, 52, 2, 1, 'kadın', '100ml', 'Grapefruit, Quince, Jasmine, White Musk, Rose', 2010, 1, 0, 0, 4.6, 178, NOW()),

-- Dior Products
('Dior Sauvage Eau de Toilette', 'dior-sauvage-edt', 'En çok satan erkek parfümü. Vahşi ve çekici koku.', 2700.00, 65, 1, 2, 'erkek', '100ml', 'Bergamot, Pepper, Lavender, Pink Pepper, Vetiver, Patchouli, Cedar', 2015, 1, 1, 0, 4.9, 512, NOW()),
('Miss Dior Blooming Bouquet', 'miss-dior-blooming-bouquet', 'Taze ve romantik pembe şakayık notaları ile kadın parfümü.', 3100.00, 48, 2, 2, 'kadın', '100ml', 'Mandarin, Peony, Rose, White Musk', 2014, 1, 1, 0, 4.7, 298, NOW()),
('J\'adore Dior Eau de Parfum', 'jadore-dior-edp', 'Lüks çiçek buketi, kadınlığın ve zarafetin kokusu.', 3600.00, 42, 2, 2, 'kadın', '100ml', 'Ylang-Ylang, Rose, Jasmine Sambac, Tuberose', 1999, 1, 1, 0, 4.8, 445, NOW()),

-- Tom Ford Products
('Tom Ford Oud Wood', 'tom-ford-oud-wood', 'Doğu\'nun en nadide malzemesi oud ile zenginleştirilmiş unisex parfüm.', 6500.00, 22, 3, 3, 'unisex', '100ml', 'Oud, Rosewood, Cardamom, Sandalwood, Vetiver, Tonka Bean, Amber', 2007, 1, 1, 0, 4.9, 267, NOW()),
('Tom Ford Black Orchid', 'tom-ford-black-orchid', 'Siyah orkide ile süslenmiş lüks ve gizemli unisex koku.', 5800.00, 31, 3, 3, 'unisex', '100ml', 'Black Truffle, Ylang-Ylang, Bergamot, Black Orchid, Lotus, Patchouli, Vanilla', 2006, 1, 1, 0, 4.8, 334, NOW()),
('Tom Ford Tobacco Vanille', 'tom-ford-tobacco-vanille', 'Tütün ve vanilya ile baharatlaştırılmış zengin unisex parfüm.', 7200.00, 18, 3, 3, 'unisex', '100ml', 'Tobacco Leaf, Vanilla, Ginger, Tonka Bean, Cacao, Dried Fruits', 2007, 1, 0, 1, 4.9, 289, NOW()),

-- Creed Products
('Creed Aventus', 'creed-aventus', 'Modern efsane. Başarı, güç ve güzelliğin kokusu.', 8500.00, 35, 1, 4, 'erkek', '100ml', 'Pineapple, Bergamot, Apple, Birch, Patchouli, Jasmine, Musk, Oakmoss, Ambergris, Vanilla', 2010, 1, 1, 0, 5.0, 678, NOW()),
('Creed Silver Mountain Water', 'creed-silver-mountain-water', 'İsviçre dağlarından esinlenen ferah ve temiz unisex koku.', 7800.00, 28, 3, 4, 'unisex', '100ml', 'Bergamot, Mandarin, Green Tea, Black Currant, Sandalwood, Musk', 1995, 1, 1, 0, 4.8, 245, NOW()),

-- Jo Malone Products
('Jo Malone Wood Sage & Sea Salt', 'jo-malone-wood-sage-sea-salt', 'İngiliz sahillerinden esinlenen taze ve doğal unisex koku.', 4200.00, 40, 3, 5, 'unisex', '100ml', 'Ambrette Seeds, Sea Salt, Sage, Red Algae, Grapefruit', 2014, 1, 1, 0, 4.6, 289, NOW()),
('Jo Malone English Pear & Freesia', 'jo-malone-english-pear-freesia', 'Tatlı armut ve çiçek notaları ile zarif unisex parfüm.', 3900.00, 36, 3, 5, 'unisex', '100ml', 'Pear, Freesia, Rose, Patchouli, Amber, Rhuburb', 2010, 1, 1, 0, 4.7, 312, NOW()),

-- YSL Products
('YSL Black Opium', 'ysl-black-opium', 'Kahve ve vanilya ile bağımlılık yapan kadın parfümü.', 3300.00, 56, 2, 6, 'kadın', '90ml', 'Pink Pepper, Orange Blossom, Coffee, Vanilla, Patchouli, Cedar', 2014, 1, 1, 0, 4.8, 423, NOW()),
('YSL Y Eau de Toilette', 'ysl-y-edt', 'Modern ve dinamik erkekler için ferahlatıcı koku.', 2600.00, 48, 1, 6, 'erkek', '100ml', 'Apple, Ginger, Bergamot, Sage, Cedarwood, Vetiver, Olibanum', 2017, 1, 1, 0, 4.6, 256, NOW()),
('YSL Libre', 'ysl-libre', 'Özgür kadının parfümü. Lavanta ve portakal çiçeği.', 3600.00, 42, 2, 6, 'kadın', '90ml', 'Lavender, Mandarin, Black Currant, Orange Blossom, Jasmine, Musk, Vanilla', 2019, 1, 1, 1, 4.7, 289, NOW()),

-- Armani Products
('Armani Code', 'armani-code', 'Gizemli ve çekici erkek parfümü. Baştan çıkarıcı koku.', 2700.00, 44, 1, 7, 'erkek', '110ml', 'Lemon, Bergamot, Anise, Olive Blossom, Guaiac Wood, Leather, Tonka Bean, Tobacco', 2004, 1, 1, 0, 4.6, 312, NOW()),
('Acqua di Giò Profumo', 'acqua-di-gio-profumo', 'Deniz ferahlığı ve odunsu notalar ile erkek parfümü.', 3200.00, 50, 1, 7, 'erkek', '125ml', 'Bergamot, Marine Notes, Geranium, Sage, Rosemary, Patchouli, Incense', 2015, 1, 1, 0, 4.8, 456, NOW()),

-- Other Bestsellers
('Versace Eros', 'versace-eros', 'Tutku ve güç simgesi erkek parfümü.', 2600.00, 54, 1, 9, 'erkek', '100ml', 'Mint, Lemon, Apple, Tonka Bean, Ambroxan, Geranium, Vanilla', 2012, 1, 1, 0, 4.6, 389, NOW()),
('Paco Rabanne 1 Million', 'paco-rabanne-1-million', 'Altın külçe şeklindeki şişede lüks erkek parfümü.', 2500.00, 60, 1, 11, 'erkek', '100ml', 'Blood Mandarin, Grapefruit, Mint, Cinnamon, Rose, Blond Leather, Amber, Patchouli', 2008, 1, 1, 0, 4.7, 512, NOW());

-- ========================================
-- REVIEWS (Sample Reviews)
-- ========================================

INSERT INTO reviews (product_id, user_id, rating, title, comment, is_approved, created_at) VALUES
(1, 1, 5, 'Klasikleşmiş bir parfüm', 'Chanel No. 5 gerçekten efsane. Kalıcılığı mükemmel, kokusuysa tarif edilemez bir zarafet.', 1, DATE_SUB(NOW(), INTERVAL 15 DAY)),
(2, 1, 5, 'En sevdiğim erkek parfümü', 'Bleu de Chanel tam istediğim gibi. Hem günlük hem özel günler için ideal.', 1, DATE_SUB(NOW(), INTERVAL 12 DAY)),
(4, 1, 5, 'Herkese tavsiye ederim', 'Dior Sauvage alırken biraz tereddüt ettim ama aldığım en iyi karar oldu. Çok beğeniliyor.', 1, DATE_SUB(NOW(), INTERVAL 8 DAY)),
(7, 1, 5, 'Oud Wood harika', 'Tom Ford Oud Wood lüksün ta kendisi. Oud notaları çok kaliteli.', 1, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(10, 1, 5, 'Creed Aventus efsane', 'Aventus gerçekten efsane bir parfüm. Kalıcılığı inanılmaz.', 1, DATE_SUB(NOW(), INTERVAL 3 DAY));

-- ========================================
-- ANALYTICS DATA
-- ========================================

INSERT INTO popular_searches (search_term, search_count, is_trending, last_searched_at) VALUES
('chanel', 156, 1, NOW()),
('dior sauvage', 142, 1, NOW()),
('tom ford', 98, 0, NOW()),
('creed aventus', 87, 0, NOW()),
('erkek parfüm', 234, 1, NOW()),
('kadın parfüm', 198, 1, NOW());

-- Summary
SELECT 
    (SELECT COUNT(*) FROM brands) as brands_count,
    (SELECT COUNT(*) FROM products) as products_count,
    (SELECT COUNT(*) FROM reviews) as reviews_count;
