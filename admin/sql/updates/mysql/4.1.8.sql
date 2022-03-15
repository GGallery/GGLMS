ALTER TABLE `#__gg_vendita_sconti_particolari`
ADD COLUMN `prezzo_webinar` decimal(6,2) NULL;

ALTER TABLE `#__gg_unit`
ADD COLUMN `prezzo_webinar_fisso` tinyint(1) NOT NULL DEFAULT 0;

