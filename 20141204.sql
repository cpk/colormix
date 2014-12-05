ALTER TABLE `product` ADD `supplier` TINYINT NOT NULL DEFAULT '1' AFTER `recipe`;
ALTER TABLE `order` ADD `supplier` TINYINT NOT NULL DEFAULT '1' AFTER `date`;
ALTER TABLE `color` ADD `supplier` TINYINT NOT NULL DEFAULT '1' AFTER `id_measurement`;