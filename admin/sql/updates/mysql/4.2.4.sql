ALTER TABLE `#__gg_vendita_sconti_particolari`
ADD COLUMN `sc_eta_valore` DECIMAL(6,2) NULL DEFAULT NULL COMMENT 'Attiva sconto per eta' AFTER `sc_data_valore`,
ADD COLUMN `rif_eta` INT NULL DEFAULT NULL AFTER `sc_eta_valore`;

ALTER TABLE `#__gg_vendita_sconti_particolari`
ADD COLUMN `sc_associazione_valore` DECIMAL(6,2) NULL DEFAULT NULL COMMENT 'Attiva sconto per affiliazione associazione fuori dal periodo di sconto' AFTER `sc_valore`,
ADD COLUMN `sc_data_associazione_valore` DECIMAL(6,2) NULL DEFAULT NULL COMMENT 'Attiva sconto per affiliazione associazione nel periodo di sconto' AFTER `sc_data_valore`;

