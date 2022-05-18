ALTER TABLE `#__gg_contenuti`
ADD COLUMN `id_evento` varchar(25) NULL,
ADD COLUMN `tipo_evento` tinyint(1) NOT NULL DEFAULT 0,
ADD COLUMN`data_evento` date DEFAULT NULL ,
ADD COLUMN `id_utente_zoom` int(10) NOT NULL;
