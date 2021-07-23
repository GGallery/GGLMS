ALTER TABLE `#__gg_unit`
    ADD COLUMN `is_bookable` tinyint(1) UNSIGNED DEFAULT '0',
    ADD COLUMN `bookable_a_gruppi` TEXT DEFAULT NULL,
    ADD COLUMN `posti_disponibili` INT(10) UNSIGNED DEFAULT '0',
    ADD COLUMN `modalita` varchar(200) DEFAULT NULL COMMENT 'Specifica il tipo di evento, es. webinar',
    ADD COLUMN `sede` varchar(200) DEFAULT NULL COMMENT 'La sede di organizzazione',
    ADD COLUMN `obbligatorio` tinyint(1) NOT NULL DEFAULT 0;
