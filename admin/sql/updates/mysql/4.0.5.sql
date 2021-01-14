ALTER TABLE `#__gg_coupon` ADD `tipologia_coupon` VARCHAR(100) NULL;
ALTER TABLE `#__gg_coupon` ADD `template` varchar(255) DEFAULT NULL COMMENT 'Integrazione per migrazione da vecchio GGLMS';
ALTER TABLE `#__gg_unit` ADD `attestato` varchar(255) DEFAULT NULL COMMENT 'Integrazione per migrazione da vecchio GGLMS';
ALTER TABLE `#__gg_contenuti` ADD `path_pdf` varchar(255) DEFAULT NULL COMMENT 'Integrazione per migrazione da vecchio GGLMS';
ALTER TABLE `#__gg_error_log` CHANGE `messaggio` `messaggio` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

