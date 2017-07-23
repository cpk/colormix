ALTER TABLE `order_subitem` 
ADD COLUMN `id_order_item` INT NULL AFTER `id_order`;


UPDATE order_subitem si
left join order_item i on si.id_order = i.id_order AND si.id_product = i.id_product
SET id_order_item = i.id;


ALTER TABLE `order_subitem` 
DROP COLUMN `id_order`,
DROP COLUMN `id_product`,
DROP INDEX `id_order` ;


ALTER TABLE `order_subitem` 
CHANGE COLUMN `id_order_item` `id_order_item` INT(11) NOT NULL ;


ALTER TABLE `order_subitem` 
ADD INDEX `id_order_item_idx` (`id_order_item` ASC);


ALTER TABLE `order_item` 
ADD COLUMN `order_subitem_color_cost` DECIMAL(8,4) NULL AFTER `item_count`;

ALTER TABLE `order_item` 
ADD COLUMN `order_subitem_package_cost` DECIMAL(8,4) NULL AFTER `order_subitem_color_cost`;


DROP VIEW `view_order`;
CREATE VIEW `view_order` AS
SELECT 
    `o`.`id` AS `id`,
    `o`.`supplier` AS `supplier`,
    `o`.`date` AS `date`,
    `c`.`name` AS `name`,
    `u`.`givenname` AS `givenname`,
    `u`.`surname` AS `surname`,
	 (
     SUM(
		IFNULL(`i`.`quantity` * `i`.`price` * `i`.`item_count`,0) +
        IFNULL(`i`.`quantity` * `i`.`order_subitem_color_cost` * `i`.`item_count`,0) +
        IFNULL(`i`.`order_subitem_package_cost` * `i`.`item_count`,0)
	  )
     ) as `spolu_nakup`,
     (
     SUM(
		IFNULL(`i`.`quantity` * `i`.`price_sale` * `i`.`item_count`,0)
	  )
     ) as `spolu_predaj`
    
FROM
    `order` `o`
    JOIN `customer` `c` ON `o`.`id_customer` = `c`.`id`
    JOIN `user` `u` ON `o`.`id_user` = `u`.`id_user`
    LEFT JOIN `order_item` `i` ON `i`.`id_order` = `o`.`id`

GROUP BY o.id
ORDER BY `o`.`id`  DESC
