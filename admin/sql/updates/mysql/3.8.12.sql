ALTER TABLE `#__gg_unit`
ADD COLUMN `data_inizio` date NULL AFTER `is_corso`,
ADD COLUMN `data_fine` date NULL AFTER `data_inizio`;