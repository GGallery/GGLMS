ALTER TABLE `#__gg_unit`
    ADD COLUMN `is_bookable` tinyint(1) UNSIGNED DEFAULT '0',
    ADD COLUMN `bookable_a_gruppi` TEXT DEFAULT NULL,
    ADD COLUMN `posti_disponibili` INT(10) UNSIGNED DEFAULT '0',
    ADD COLUMN `modalita` INT(10) UNSIGNED DEFAULT NULL COMMENT 'Specifica il tipo di evento, es. webinar',
    ADD COLUMN `sede` varchar(200) DEFAULT NULL COMMENT 'La sede di organizzazione',
    ADD COLUMN `obbligatorio` tinyint(1) NOT NULL DEFAULT 0;
    ADD COLUMN `sconti_particolari` tinyint(1) UNSIGNED DEFAULT '0',
    ADD COLUMN `riduzione_webinar` tinyint(1) UNSIGNED DEFAULT '0',
    ADD COLUMN `sc_webinar_perc` decimal(6,2) DEFAULT NULL COMMENT 'Lo sconto percentuale per acquisto in modalita webinar';

CREATE TABLE IF NOT EXISTS `#__gg_categorie_evento` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
    `titolo` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE = InnoDB;

INSERT INTO `#__gg_categorie_evento` (titolo) VALUES ('Webinar sincrono'),
                                                     ('In presenza'),
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
                                                     ('FAD asincrona');
