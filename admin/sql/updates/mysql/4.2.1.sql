ALTER TABLE `#__gg_quote_iscrizioni`
ADD COLUMN `stato` TINYINT(1) DEFAULT 0;

CREATE TABLE `#__gg_registration_request` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `token` TEXT NOT NULL,
  `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
