ALTER TABLE `#__gg_contenuti`
ADD COLUMN `gruppo_attestato` TEXT DEFAULT NULL;

ALTER TABLE `#__gg_unit`
ADD COLUMN `attestato_personalizzato` tinyint(1) NOT NULL DEFAULT 0;
