ALTER TABLE `products`
    ADD COLUMN `top_product` TINYINT(1) NOT NULL DEFAULT 0 AFTER `new_product`;
