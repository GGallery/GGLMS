CREATE TABLE IF NOT EXISTS `#__gg_check_coupon_xml` (
    `id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `codice_corso` VARCHAR(200) DEFAULT NULL COMMENT 'Il corso di riferimento per la creazione del coupon',
    `codice_fiscale` VARCHAR(200) DEFAULT NULL COMMENT 'Il riferimento al codice fiscale iscritto',
    `coupon` TINYINT(1) DEFAULT NULL COMMENT 'Il riferimento al coupon creato',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


