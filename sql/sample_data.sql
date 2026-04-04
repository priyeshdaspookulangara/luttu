-- Sample Data for ShopPV

-- 1. Insert Categories
INSERT INTO categories (name, custom_attributes) VALUES
('Spicy Snacks', '["Spice Level", "Weight", "Main Ingredient"]'),
('Sweet Treats', '["Sugar Content", "Flavor", "Weight"]'),
('Crunchy Bites', '["Texture", "Oil Used", "Weight"]'),
('Salty Delights', '["Salt Type", "Weight"]');

-- 2. Insert Products (Assuming IDs 1-4 for categories)
INSERT INTO products (category_id, name, description, price, list_price, margin_amount, pv_value, brand, manufacturer, supplier, attributes) VALUES
(1, 'Fiery Chilli Crisps', 'Our OG. Recklessly spicy, dangerously addictive crisps made with Guntur chillies.', 49.00, 60.00, 15.00, 1.50, 'Luttu', 'Luttu Kitchens', 'Direct Farmer Sourcing', '{"Spice Level": "Fiery Hot", "Weight": "100g", "Main Ingredient": "Rice Flour"}'),
(2, 'Caramel Popcorn', 'Gourmet popcorn coated in rich, buttery sea-salt caramel.', 65.00, 85.00, 20.00, 2.00, 'Luttu', 'Luttu Kitchens', 'Karnataka Corn Farms', '{"Sugar Content": "Medium", "Flavor": "Caramel", "Weight": "150g"}'),
(3, 'Cheesy Puff Rings', 'Air-puffed corn rings with a double layer of aged cheddar cheese powder.', 55.00, 75.00, 18.00, 1.80, 'Luttu', 'Luttu Kitchens', 'Dairy Co-op', '{"Texture": "Extra Crunchy", "Oil Used": "Sunflower Oil", "Weight": "120g"}'),
(4, 'Truffle Chips XO', 'Premium potato chips infused with black truffle oil and sea salt.', 99.00, 150.00, 35.00, 3.50, 'Luttu', 'Premium Line', 'Global Imports', '{"Salt Type": "Sea Salt", "Weight": "80g"}'),
(1, 'Lemon Pepper Stix', 'Tangy lemon meets bold black pepper in these crunchy corn stix.', 45.00, 55.00, 12.00, 1.20, 'Luttu', 'Luttu Kitchens', 'Local Spices', '{"Spice Level": "Mild", "Weight": "100g", "Main Ingredient": "Corn"}');

-- 3. Insert Product Images (Assuming IDs 1-5 for products)
INSERT INTO product_images (product_id, image_path, is_primary) VALUES
(1, 'https://images.unsplash.com/photo-1621852004158-f3bc188aec74?auto=format&fit=crop&q=80&w=800', 1),
(2, 'https://images.unsplash.com/photo-1578849278619-e734c1facac6?auto=format&fit=crop&q=80&w=800', 1),
(3, 'https://images.unsplash.com/photo-1599599810765-bfb1a3148bfc?auto=format&fit=crop&q=80&w=800', 1),
(4, 'https://images.unsplash.com/photo-1566478989037-e92383833545?auto=format&fit=crop&q=80&w=800', 1),
(5, 'https://images.unsplash.com/photo-1628557044797-f21a177c37ec?auto=format&fit=crop&q=80&w=800', 1);

-- 4. Set Initial PV Conversion Settings
INSERT INTO pv_settings (cash_per_pv, min_withdrawal, effective_from) VALUES
(10.00, 100.00, CURRENT_DATE);

-- 5. Insert a Sample Customer (password: customer123)
-- Hash generated via password_hash('customer123', PASSWORD_DEFAULT)
INSERT INTO users (username, password, email, role) VALUES
('johndoe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'john@example.com', 'user');
