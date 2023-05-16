CREATE TABLE `#__gg_report_view_permessi` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`id_utente`  int(11) NOT NULL ,
`id_corsi`  text NULL ,
PRIMARY KEY (`id`, `id_utente`)
)
;
CREATE TABLE `#__gg_report_view_permessi_gruppi` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`id_utente`  int(11) NOT NULL ,
`id_gruppi`  text NULL ,
PRIMARY KEY (`id`, `id_utente`)
)
;

CREATE TABLE IF NOT EXISTS `#__gg_contenuti` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `titolo` text NOT NULL,
  `alias` text,
  `pubblicato` tinyint(1) NOT NULL DEFAULT '1',
  `durata` int(10) unsigned NOT NULL DEFAULT '0',
  `descrizione` longtext,
  `access` int(10) unsigned DEFAULT '0',
  `meta_tag` varchar(255) DEFAULT '-',
  `abstract` mediumtext,
  `datapubblicazione` date DEFAULT NULL,
  `slide` tinyint(1) NOT NULL DEFAULT '0',
  `path` varchar(255) DEFAULT NULL,
  `id_quizdeluxe` int(4) DEFAULT NULL,
  `tipologia` int(4) NOT NULL DEFAULT '1',
  `files` varchar(255) DEFAULT NULL COMMENT 'NON CANCELLARE SERVE PER IL MAP',
  `mod_track` int(255) DEFAULT '1',
  `prerequisiti` varchar(255) DEFAULT NULL,
  `id_completed_data` int(10) DEFAULT NULL,
  `attestato_path` varchar(255) DEFAULT NULL,
  `orientamento` varchar(1) DEFAULT NULL,
  `path_pdf` varchar(255) DEFAULT NULL COMMENT 'Integrazione per migrazione da vecchio GGLMS',
  `id_evento` varchar(25) NULL COMMENT 'aggiunta per le chiamate api zoom',
  `tipo_zoom` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'aggiunta per distinguere webinar da meeting',
  `url_streaming_azure` TEXT DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
