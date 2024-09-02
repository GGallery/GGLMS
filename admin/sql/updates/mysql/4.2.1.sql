CREATE TABLE IF NOT EXISTS `#__gg_quote_iscrizioni`
(
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
	`user_id` INT(11) UNSIGNED NOT NULL ,
	`anno` INT(4) NOT NULL ,
	`tipo_quota` VARCHAR(20) NOT NULL ,
	`tipo_pagamento` VARCHAR(50) NULL,
	`data_pagamento` DATETIME NULL,
	`totale` DECIMAL(6,2) NULL,
	`dettagli_transazione` TEXT NULL,
  `gruppo_corso` INT(11) UNSIGNED DEFAULT 0,
	`stato` TINYINT(1) DEFAULT 0,
	PRIMARY KEY (`id`), INDEX (`user_id`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `#__gg_registration_request` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `token` TEXT NOT NULL,
  `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
