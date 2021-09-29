ALTER TABLE `#__gg_unit`
    ADD COLUMN `is_bookable` tinyint(1) UNSIGNED DEFAULT '0',
    ADD COLUMN `bookable_a_gruppi` TEXT DEFAULT NULL,
    ADD COLUMN `posti_disponibili` INT(10) UNSIGNED DEFAULT '0',
    ADD COLUMN `modalita` INT(10) UNSIGNED DEFAULT NULL COMMENT 'Specifica il tipo di evento, es. webinar',
    ADD COLUMN `sede` varchar(200) DEFAULT NULL COMMENT 'La sede di organizzazione',
    ADD COLUMN `obbligatorio` tinyint(1) NOT NULL DEFAULT 0,
    ADD COLUMN `orario` VARCHAR(100) DEFAULT NULL COMMENT 'Riferimento orario del corso';

CREATE TABLE IF NOT EXISTS `#__gg_categorie_evento` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
    `titolo` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE = InnoDB;

INSERT INTO `#__gg_categorie_evento` (titolo) VALUES ('Webinar sincrono'),
                                                     ('In presenza'),
                                                     ('FAD asincrona');
