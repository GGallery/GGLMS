ALTER TABLE `#__gg_quote_voucher`
    ADD COLUMN `sc_percentuale` TINYINT NULL AFTER `sc_valore`,
    ADD COLUMN `durata` TINYINT DEFAULT 0 AFTER `sc_percentuale`,
    ADD COLUMN `data_emissione` DATE NULL AFTER `durata`;