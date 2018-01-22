ALTER TABLE `un_gg_unit_map_copy`
DROP COLUMN `data`,
DROP COLUMN `idlink`;

ALTER TABLE `un_gg_unit_map_copy`
ADD PRIMARY KEY (`idcontenuto`);