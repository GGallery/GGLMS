ALTER TABLE `#__gg_unit` ADD COLUMN `codice`  varchar(255) NULL,
    ADD COLUMN `codice_alfanumerico`  varchar(255) NULL,
    ADD COLUMN `tipologia_corso`  tinyint(1) NOT NULL DEFAULT 6;

