ALTER TABLE `#__gg_vendita_sconti_particolari`
ADD COLUMN `sc_eta_valore` DECIMAL(6,2) NULL DEFAULT NULL COMMENT 'Attiva sconto per eta' AFTER `sc_data_valore`,
ADD COLUMN `rif_eta` INT NULL DEFAULT NULL AFTER `sc_eta_valore`;

