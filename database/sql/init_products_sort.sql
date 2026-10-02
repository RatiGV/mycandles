UPDATE `products` p JOIN (SELECT `id`, ROW_NUMBER() OVER (ORDER BY `created_at` DESC, `id` DESC) AS rn FROM `products`) t ON t.`id` = p.`id` SET p.`sort` = t.rn;
