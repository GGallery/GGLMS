CREATE TABLE `#__gg_zoom` (
  `id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `token`  text NOT NULL ,
  `data_registrazione`  datetime NOT NULL ,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `#__gg_zoom_events` (
  `id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `id_evento`  bigint(20) UNSIGNED NOT NULL,
  `tipo_evento`  varchar(50) NOT NULL ,
  `label_evento`  varchar(200) NOT NULL,
  `response`  text NOT NULL ,
  `data_registrazione`  datetime NOT NULL ,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `#__gg_report` ADD COLUMN `data_primo_accesso` datetime NULL;

ALTER TABLE `#__gg_unit` ADD COLUMN `on_sale` tinyint(1) UNSIGNED DEFAULT '0',
ADD COLUMN `disponibile_dal` date DEFAULT NULL,
ADD COLUMN `disponibile_al` date DEFAULT NULL,
ADD COLUMN `prezzo` decimal(6,2) DEFAULT NULL,
ADD COLUMN `sc_a_data` date DEFAULT NULL,
ADD COLUMN `sc_valore_data` decimal(6,2) DEFAULT NULL,
ADD COLUMN `sc_a_data_gruppi` text,
ADD COLUMN `sc_valore_data_gruppi` decimal(6,2) DEFAULT NULL,
ADD COLUMN `sc_a_gruppi` text,
ADD COLUMN `sc_valore_gruppi` decimal(6,2) DEFAULT NULL,
ADD COLUMN `sc_da_data` date DEFAULT NULL,
ADD COLUMN `sc_custom_cb` varchar(255) NULL,
ADD COLUMN `sc_semaforo_custom_cb` varchar(255) NULL,
ADD COLUMN `sc_valore_custom_cb` decimal(6,2) NULL;

