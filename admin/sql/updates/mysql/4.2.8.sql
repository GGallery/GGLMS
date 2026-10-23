ALTER TABLE `#__gg_quote_voucher`
    ADD COLUMN `sc_percentuale` INT NULL AFTER `sc_valore`,
    ADD COLUMN `durata` INT DEFAULT 0 AFTER `sc_percentuale`,
    ADD COLUMN `data_emissione` DATE NULL AFTER `durata`;