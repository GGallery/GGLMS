ALTER TABLE `#__gg_unit_map_copy`
DROP COLUMN `data`,
DROP COLUMN `idlink`;

ALTER TABLE `#__gg_unit_map_copy`
ADD PRIMARY KEY (`idcontenuto`);

ALTER TABLE `#__gg_contenuti`
MODIFY COLUMN `mod_track`  int(255) NULL DEFAULT 0 AFTER `files`;

ALTER TABLE `un_gg_unit_map`
MODIFY COLUMN `ordinamento` int(11) NULL DEFAULT 0 AFTER `idunita`;