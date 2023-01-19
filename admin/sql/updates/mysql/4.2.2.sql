CREATE TABLE `#__gg_quote_voucher` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NULL,
  `code` TEXT NOT NULL,
  `date` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
