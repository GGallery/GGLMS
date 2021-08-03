ALTER TABLE `#__gg_unit`
    ADD COLUMN `sconti_particolari` tinyint(1) UNSIGNED DEFAULT '0',
    ADD COLUMN `riduzione_webinar` tinyint(1) UNSIGNED DEFAULT '0',
    ADD COLUMN `sc_webinar_perc` decimal(6,2) DEFAULT NULL COMMENT 'Lo sconto percentuale per acquisto in modalita webinar';

CREATE TABLE IF NOT EXISTS `#__gg_vendita_sconti_particolari` (
    `id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_unita`  bigint(20) UNSIGNED NOT NULL,
    `rif_campo_nome` VARCHAR(200) DEFAULT NULL COMMENT 'Nome della colonna del campo custom di integrazione es CB',
    `rif_campo_valore`  TEXT DEFAULT NULL COMMENT 'Riferimento ai valori da controllare di rif_campo_nome - valori separati da virgola, es. Medico, Farmacista...',
    `socio` tinyint(1) UNSIGNED DEFAULT '0' COMMENT '1 = Si tratta di un socio',
    `sc_valore` decimal(6,2) DEFAULT NULL COMMENT 'Lo sconto attivo di default se non impostato da data a data',
    `da_data` date DEFAULT NULL,
    `a_data` date DEFAULT NULL,
    `sc_data_valore` decimal(6,2) DEFAULT NULL COMMENT 'Lo sconto attivo da data a data',
    `priorita` int(11) DEFAULT '0' COMMENT 'La priorita del peso degli sconti',
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


