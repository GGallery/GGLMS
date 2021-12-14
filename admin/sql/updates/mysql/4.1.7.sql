ALTER TABLE `#__gg_contenuti`
ADD COLUMN `id_evento` varchar(25) NULL,
ADD COLUMN `tipo_evento` tinyint(1) NOT NULL DEFAULT 0;
