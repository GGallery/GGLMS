ALTER TABLE `#__gg_unit`
    ADD COLUMN `is_bookable` tinyint(1) UNSIGNED DEFAULT '0',
    ADD COLUMN `bookable_a_gruppi` TEXT DEFAULT NULL,
    ADD COLUMN `posti_disponibili` INT(10) UNSIGNED DEFAULT '0';
