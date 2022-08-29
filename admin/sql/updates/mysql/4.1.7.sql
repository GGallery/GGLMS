ALTER TABLE `#__gg_contenuti`
ADD COLUMN `id_evento` varchar(25) NULL,
ADD COLUMN `tipo_zoom` tinyint(1) NOT NULL DEFAULT 0;
