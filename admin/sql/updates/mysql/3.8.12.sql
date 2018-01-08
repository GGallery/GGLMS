ALTER TABLE `#__gg_unit`
ADD COLUMN `data_inizio` date NULL AFTER `is_corso`,
ADD COLUMN `data_fine` date NULL AFTER `data_inizio`;

CREATE TABLE `#__gg_csv_report`  (
  `id_chiamata` int(255) NOT NULL,
  `id_utente` int(255) NOT NULL,
  `nome` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `cognome` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `fields` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `stato` int(255) NULL DEFAULT NULL,
  `hainiziato` date NULL DEFAULT NULL,
  `hacompletato` date NULL DEFAULT NULL,
  `alert` int(255) NULL DEFAULT NULL
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=utf8;
