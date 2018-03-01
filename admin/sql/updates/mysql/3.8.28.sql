ALTER TABLE `#__gg_view_stato_user_unita`
ADD COLUMN `data_inizio`  date NULL AFTER `stato`,
ADD COLUMN `data_fine`  date NULL AFTER `data_inizio`;

ALTER TABLE `#__gg_view_stato_user_corso`
ADD COLUMN `data_inizio`  date NULL AFTER `stato`,
ADD COLUMN `data_fine`  date NULL AFTER `data_inizio`;