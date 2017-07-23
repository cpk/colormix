ALTER TABLE `order_item` ADD `item_count` SMALLINT NOT NULL DEFAULT '1' AFTER `price_sale`;

DROP VIEW `view_order`;
CREATE VIEW `view_order` AS
    SELECT 
        `o`.`id` AS `id`,
        `o`.`supplier` AS `supplier`,
        `o`.`date` AS `date`,
        `c`.`name` AS `name`,
        `u`.`givenname` AS `givenname`,
        `u`.`surname` AS `surname`,
        ((COALESCE((SELECT 
                        SUM((`x`.`quantity_kg` * `x`.`price` * `z`.`item_count`))
                    FROM
                        (`order_item` `z`
                        JOIN (`order_subitem` `x`
                        JOIN `color` `y` ON (((`y`.`id` = `x`.`id_color`)
                            AND ((`y`.`color_type` = 2)
                            OR (`y`.`color_type` = 3))))))
                    WHERE
                        ((`x`.`id_order` = `o`.`id`)
                            AND (`z`.`id_order` = `x`.`id_order`)
                            AND (`x`.`id_product` = `z`.`id_product`))),
                0) + COALESCE((SELECT 
                        SUM(((`x`.`quantity_kg` * `x`.`price`) * `z`.`quantity` * `z`.`item_count`))
                    FROM
                        (`order_item` `z`
                        JOIN (`order_subitem` `x`
                        JOIN `color` `y` ON (((`y`.`id` = `x`.`id_color`)
                            AND (`y`.`color_type` = 1)))))
                    WHERE
                        ((`x`.`id_order` = `o`.`id`)
                            AND (`z`.`id_order` = `x`.`id_order`)
                            AND (`x`.`id_product` = `z`.`id_product`))),
                0)) + COALESCE((SELECT 
                        COALESCE(SUM((`z`.`quantity` * `z`.`price`  * `z`.`item_count`)), 0)
                    FROM
                        (`order_item` `z`
                        JOIN `product` `p`)
                    WHERE
                        ((`p`.`recipe` = 0)
                            AND (`p`.`id` = `z`.`id_product`)
                            AND (`o`.`id` = `z`.`id_order`))),
                0)) AS `spolu_nakup`,
        (COALESCE((SELECT 
                        ROUND(COALESCE(SUM((`x`.`quantity_kg` * `z`.`price_sale` * `z`.`item_count`)),
                                            0),
                                    2)
                    FROM
                        ((`order_item` `z`
                        LEFT JOIN `order_subitem` `x` ON (((`x`.`id_product` = `z`.`id_product`)
                            AND (`z`.`id_order` = `x`.`id_order`))))
                        JOIN `color` `y` ON (((`y`.`id` = `x`.`id_color`)
                            AND (`y`.`color_type` = 2))))
                    WHERE
                        (`o`.`id` = `z`.`id_order`)),
                0) + COALESCE((SELECT 
                        ROUND(COALESCE(SUM((`z`.`quantity` * `z`.`price_sale` * `z`.`item_count`)),
                                            0),
                                    2)
                    FROM
                        `order_item` `z`
                    WHERE
                        (`z`.`id_order` = `o`.`id`)),
                0)) AS `spolu_predaj`
    FROM
        ((((`order` `o`
        JOIN `customer` `c` ON ((`o`.`id_customer` = `c`.`id`)))
        LEFT JOIN `user` `u` ON ((`o`.`id_user` = `u`.`id_user`)))
        LEFT JOIN `order_item` `i` ON ((`i`.`id_order` = `o`.`id`)))
        LEFT JOIN `order_subitem` `si` ON (((`si`.`id_product` = `i`.`id_product`)
            AND (`si`.`id_order` = `i`.`id_order`))))
    GROUP BY `o`.`id`
