ALTER TABLE `#__gg_unit`
DROP COLUMN `tipologia`,
ADD COLUMN `is_corso`  int(10) NULL AFTER `unitapadre`,
ADD COLUMN `id_contenuto_completamento`  int(10) NULL AFTER `id_event_booking`;

