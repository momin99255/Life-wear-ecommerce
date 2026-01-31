-- seed_products.sql
-- Purpose: Update product image URLs to better match product names and INSERT any missing products.
-- This script creates a backup of the current images for affected products before making changes.

START TRANSACTION;

-- Backup table (created only if missing)
CREATE TABLE IF NOT EXISTS product_image_backup (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    name VARCHAR(100),
    old_image VARCHAR(255),
    backed_up_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert backups for the products we will update (by name)
INSERT INTO product_image_backup (product_id, name, old_image)
SELECT id, name, image FROM products WHERE name IN (
  'White Shirt','Striped Shirt','Formal White Shirt','Linen Casual Shirt',
  'Blue T-Shirt','Graphic Tee','V-Neck T-Shirt','Polo Shirt',
  'Traditional Punjabi','Embroidered Punjabi','Silk Punjabi','Casual Punjabi','Festive Punjabi',
  'Premium Watch','Leather Belt','Leather Wallet','Cufflinks','Sunglasses',
  'Red Saree','Floral Saree','Silk Designer Saree','Party Wear Saree','Chiffon Saree',
  'Black Top','Blue Bottom','Casual Crop Top','Silk Blouse','Summer Tank Top',
  'Black Jacket','Navy Blazer','Puffer Jacket','Leather Jacket',
  'Grey Hoodie','Zip Hoodie','Graphic Hoodie','Fleece Hoodie',
  'Casual Chambray Shirt','Long Sleeve Tee',
  'Canvas Backpack','Beaded Necklace','Handbag','Charm Bracelet','Cufflinks'
);

-- UPDATE statements (set image to keyword-based Unsplash URLs)
UPDATE products SET image = 'https://source.unsplash.com/400x400/?white-shirt' WHERE name = 'White Shirt';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?striped-shirt' WHERE name = 'Striped Shirt';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?formal-shirt' WHERE name = 'Formal White Shirt';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?linen-shirt' WHERE name = 'Linen Casual Shirt';

UPDATE products SET image = 'https://source.unsplash.com/400x400/?blue-t-shirt' WHERE name = 'Blue T-Shirt';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?graphic-t-shirt' WHERE name = 'Graphic Tee';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?v-neck-tshirt' WHERE name = 'V-Neck T-Shirt';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?polo-shirt' WHERE name = 'Polo Shirt';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?long-sleeve-t-shirt' WHERE name = 'Long Sleeve Tee';

UPDATE products SET image = 'https://source.unsplash.com/400x400/?kurta' WHERE name = 'Traditional Punjabi';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?embroidered-kurta' WHERE name = 'Embroidered Punjabi';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?silk-kurta' WHERE name = 'Silk Punjabi';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?casual-kurta' WHERE name = 'Casual Punjabi';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?festive-kurta' WHERE name = 'Festive Punjabi';

UPDATE products SET image = 'https://source.unsplash.com/400x400/?watch' WHERE name = 'Premium Watch';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?gold-jewelry' WHERE name = 'Gold Jewelry';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?leather-belt' WHERE name = 'Leather Belt';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?sunglasses' WHERE name = 'Sunglasses';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?leather-wallet' WHERE name = 'Leather Wallet';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?cufflinks' WHERE name = 'Cufflinks';

UPDATE products SET image = 'https://source.unsplash.com/400x400/?red-saree' WHERE name = 'Red Saree';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?floral-saree' WHERE name = 'Floral Saree';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?silk-saree' WHERE name = 'Silk Designer Saree';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?party-saree' WHERE name = 'Party Wear Saree';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?chiffon-saree' WHERE name = 'Chiffon Saree';

UPDATE products SET image = 'https://source.unsplash.com/400x400/?black-top' WHERE name = 'Black Top';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?blue-bottom' WHERE name = 'Blue Bottom';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?crop-top' WHERE name = 'Casual Crop Top';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?silk-blouse' WHERE name = 'Silk Blouse';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?tank-top' WHERE name = 'Summer Tank Top';

UPDATE products SET image = 'https://source.unsplash.com/400x400/?black-jacket' WHERE name = 'Black Jacket';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?navy-blazer' WHERE name = 'Navy Blazer';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?puffer-jacket' WHERE name = 'Puffer Jacket';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?leather-jacket' WHERE name = 'Leather Jacket';

UPDATE products SET image = 'https://source.unsplash.com/400x400/?grey-hoodie' WHERE name = 'Grey Hoodie';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?zip-hoodie' WHERE name = 'Zip Hoodie';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?graphic-hoodie' WHERE name = 'Graphic Hoodie';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?fleece-hoodie' WHERE name = 'Fleece Hoodie';

UPDATE products SET image = 'https://source.unsplash.com/400x400/?chambray-shirt' WHERE name = 'Casual Chambray Shirt';

UPDATE products SET image = 'https://source.unsplash.com/400x400/?canvas-backpack' WHERE name = 'Canvas Backpack';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?necklace' WHERE name = 'Beaded Necklace';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?handbag' WHERE name = 'Handbag';
UPDATE products SET image = 'https://source.unsplash.com/400x400/?bracelet' WHERE name = 'Charm Bracelet';

-- Insert missing products if they don't exist (example entries)
INSERT INTO products (name, category, subcategory, price, description, image)
SELECT 'Casual Chambray Shirt', 'men', 'shirt', 1600, 'Casual chambray shirt with soft texture', 'https://source.unsplash.com/400x400/?casual-shirt'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Casual Chambray Shirt');

INSERT INTO products (name, category, subcategory, price, description, image)
SELECT 'Long Sleeve Tee', 'men', 'tshirt', 900, 'Comfortable long sleeve tee', 'https://source.unsplash.com/400x400/?long-sleeve-t-shirt'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Long Sleeve Tee');

COMMIT;

-- End of seed_products.sql
