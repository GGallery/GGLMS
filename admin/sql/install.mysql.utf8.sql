/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50724
Source Host           : localhost:3306
Source Database       : gglms_base

Target Server Type    : MYSQL
Target Server Version : 50724
File Encoding         : 65001

Date: 2019-11-26 12:03:23
*/
SET sql_mode='';
SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `#__gg_box_details`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_box_details`;
CREATE TABLE `#__gg_box_details` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__gg_box_details
-- ----------------------------
INSERT INTO `#__gg_box_details` VALUES ('1', 'Corsi di formazione');
INSERT INTO `#__gg_box_details` VALUES ('2', 'Corsi di aggiornamento');
INSERT INTO `#__gg_box_details` VALUES ('3', 'Corsi Privacy e D.Lgs. 231/01');
INSERT INTO `#__gg_box_details` VALUES ('4', 'Corsi riservati ad aziende clienti');

-- ----------------------------
-- Table structure for `#__gg_box_unit_map`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_box_unit_map`;
CREATE TABLE `#__gg_box_unit_map` (
  `box` int(11) NOT NULL,
  `id_unita` int(11) NOT NULL,
  `order` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__gg_box_unit_map
-- ----------------------------

-- ----------------------------
-- Table structure for `#__gg_configs`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_configs`;
CREATE TABLE `#__gg_configs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config_key` varchar(100) DEFAULT NULL,
  `config_value` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__gg_configs
-- ----------------------------
INSERT INTO `#__gg_configs` VALUES ('1', 'integrazione', 'cb');
INSERT INTO `#__gg_configs` VALUES ('2', 'campo_event_booking_auto_abilitazione_coupon', 'value');
INSERT INTO `#__gg_configs` VALUES ('3', 'campo_community_builder_auto_abilitazione_coupon', '17');
INSERT INTO `#__gg_configs` VALUES ('4', 'verifica_cf', '0');
INSERT INTO `#__gg_configs` VALUES ('5', 'campo_event_booking_controllo_cf', 'value');
INSERT INTO `#__gg_configs` VALUES ('6', 'campo_community_builder_controllo_cf', 'cb_codicefiscale');
INSERT INTO `#__gg_configs` VALUES ('7', 'id_gruppo_corsi', '10');
/*INSERT INTO `#__gg_configs` VALUES ('8', 'id_gruppo_venditori', '54');*/
INSERT INTO `#__gg_configs` VALUES ('8', 'id_gruppo_venditori', '15');
/*INSERT INTO `#__gg_configs` VALUES ('9', 'id_gruppo_piattaforme', '31');*/
INSERT INTO `#__gg_configs` VALUES ('9', 'id_gruppo_piattaforme', '12');
/*INSERT INTO `#__gg_configs` VALUES ('10', 'id_gruppo_tutor_piattaforma', '61');*/
INSERT INTO `#__gg_configs` VALUES ('10', 'id_gruppo_tutor_piattaforma', '14');
/*INSERT INTO `#__gg_configs` VALUES ('11', 'id_gruppo_tutor_aziendale', '55');*/
INSERT INTO `#__gg_configs` VALUES ('11', 'id_gruppo_tutor_aziendale', '13');
INSERT INTO `#__gg_configs` VALUES ('12', 'campo_community_builder_nome', 'cb_nome');
INSERT INTO `#__gg_configs` VALUES ('13', 'campo_community_builder_cognome', 'cb_cognome');
INSERT INTO `#__gg_configs` VALUES ('14', 'campo_event_booking_nome', 'value');
INSERT INTO `#__gg_configs` VALUES ('15', 'campo_event_booking_cognome', 'value');
INSERT INTO `#__gg_configs` VALUES ('16', 'ulteriori_attestati', '1');
INSERT INTO `#__gg_configs` VALUES ('17', 'abilita_breadcrumbs', '1');
INSERT INTO `#__gg_configs` VALUES ('18', 'visualizza_ultimo', '1');
INSERT INTO `#__gg_configs` VALUES ('19', 'visualizza_primo_item', '1');
INSERT INTO `#__gg_configs` VALUES ('20', 'customizza_primo_item', '0');
INSERT INTO `#__gg_configs` VALUES ('21', 'customizza_testo_primo_item', 'Corsi');
INSERT INTO `#__gg_configs` VALUES ('22', 'customizza_link_primo_item', 'index.php?option=com_gglms');
INSERT INTO `#__gg_configs` VALUES ('23', 'visualizza_solo_mieicorsi', '1');
INSERT INTO `#__gg_configs` VALUES ('24', 'nomenclatura_unita', 'Raccolte');
INSERT INTO `#__gg_configs` VALUES ('25', 'titolo_unita_visibile', '0');
INSERT INTO `#__gg_configs` VALUES ('26', 'nomenclatura_moduli', 'Elementi');
INSERT INTO `#__gg_configs` VALUES ('27', 'visibilita_durata', '1,2');
INSERT INTO `#__gg_configs` VALUES ('28', 'visibilita_durata_unita', '0');
INSERT INTO `#__gg_configs` VALUES ('29', 'filtro_date_corsi', '0');
-- INSERT INTO `#__gg_configs` VALUES ('30', 'larghezza_box_unita', 'size-25');
-- INSERT INTO `#__gg_configs` VALUES ('31', 'larghezza_box_contenuti', 'size-25');
INSERT INTO `#__gg_configs` VALUES ('32', 'testo_invito_scaricare_attestato', '<h2>Congratulazioni!</h2>\r\n<p>Ora puoi scaricare l\'attestato del corso cliccando sull\'icona qui a fianco.</p>');
INSERT INTO `#__gg_configs` VALUES ('33', 'check_coupon_attestato', '0');
INSERT INTO `#__gg_configs` VALUES ('34', 'testo_attestato_disabilitato', '<h2>Attestato disabilitato!</h2>\r\n<p>Il download di questo attestato è disabilitato, rivolgiti al tuo tutor aziendale.</p>');
INSERT INTO `#__gg_configs` VALUES ('35', 'data_sync', '2019-11-19 17:57:23');
INSERT INTO `#__gg_configs` VALUES ('36', 'alert_lista_corsi', '');
INSERT INTO `#__gg_configs` VALUES ('37', 'alert_days_before', '7');
INSERT INTO `#__gg_configs` VALUES ('38', 'alert_mail_object', 'Avviso scadenza corso Carige Learning');
INSERT INTO `#__gg_configs` VALUES ('39', 'alert_mail_text', 'Buongiorno, inviamo questa mail come promemoria per la scadenza prossima del corso');
INSERT INTO `#__gg_configs` VALUES ('40', 'campi_csv', 'user_id,firstname,lastname,cb_codicefiscale,cb_datadinascita,cb_luogodinascita,cb_provinciadinascita,cb_indirizzodiresidenza,cb_provdiresidenza,cb_cap,cb_telefono');
/*INSERT INTO `#__gg_configs` VALUES ('41', 'log_utente', '0');*/
INSERT INTO `#__gg_configs` VALUES ('41', 'log_utente', '1');
INSERT INTO `#__gg_configs` VALUES ('42', 'colonne_somme_tempi', '0');
INSERT INTO `#__gg_configs` VALUES ('43', 'testo_invito_scaricare_pdfsingolo', '<h3>Scarica il file PDF cliccando sull\'icona</h3>');
INSERT INTO `#__gg_configs` VALUES ('44', 'url_redirect_on_access_deny', '');
INSERT INTO `#__gg_configs` VALUES ('45', 'titolo_pagina_coupon', 'Inserisci qui il tuo codice coupon');
INSERT INTO `#__gg_configs` VALUES ('46', 'messaggio_inserimento_success', 'Inserimento effettuato con successo. Vai all\'area I MIEI CORSI per accedere. Ai sucessivi accessi, potrai trovare i corsi attivati direttamente in quella pagina.');
INSERT INTO `#__gg_configs` VALUES ('47', 'messaggio_inserimento_wrong', 'Il codice non è valido o è scaduto. Verifica con i tuoi referenti formazione o sicurezza..');
INSERT INTO `#__gg_configs` VALUES ('48', 'messaggio_inserimento_pending', 'Il codice è in attesa di abilitazione. (COD. 03)');
INSERT INTO `#__gg_configs` VALUES ('49', 'messaggio_inserimento_duplicate', 'Hai già inserito un coupon per questo corso. Verifica con i tuoi referenti formazione o sicurezza.');
INSERT INTO `#__gg_configs` VALUES ('50', 'messaggio_inserimento_tutor', 'Sembra che tu sia loggato come tutor aziendale. Le credenziali aziendali NON devono essere utilizzate per inserire il coupon o per seguire i corsi, ma solo per effettuare il monitoraggio degli utenti. Per utilizzare il coupon effettua l\'accesso con un utente standard.');
INSERT INTO `#__gg_configs` VALUES ('51', 'mail_coupon_acitve', '0');
INSERT INTO `#__gg_configs` VALUES ('52', 'specifica_durata_coupon', '0');
INSERT INTO `#__gg_configs` VALUES ('53', 'durata_standard_coupon', '60');
INSERT INTO `#__gg_configs` VALUES ('54', 'genera_forum', '1');
INSERT INTO `#__gg_configs` VALUES ('55', 'titolo_pagina_rinnova_coupon', 'Inserisci qui il codice coupon da rinnovare');
INSERT INTO `#__gg_configs` VALUES ('56', 'descrizione_pagina_rinnova_coupon', 'Inserisci il codice coupon da rinnovare. La durata dell\'iscrizione al corso è di 60 giorni a partire dal momento del rinnovo.');
INSERT INTO `#__gg_configs` VALUES ('57', 'messaggio_rinnovo_nouser', 'Il coupon non è stato ancora inserito da nessun utente, per ulteriori informazioni contatta il supporto tecnico');
INSERT INTO `#__gg_configs` VALUES ('58', 'messaggio_rinnovo_notutor', 'Ops sembra che tu non sia loggato come utente tutor di piattaforma. Non è possibilie effettuare il rinnovo se non sei un tutor di piattaforma');
INSERT INTO `#__gg_configs` VALUES ('59', 'messaggio_rinnovo_wrong_società', 'Ops sembra che il coupon che vuoi rinnovare  sia stato emesso per una società che non afferisce alle tue piattaforme. ');
INSERT INTO `#__gg_configs` VALUES ('60', 'messaggio_rinnovo_not_expired', 'Ops sembra che il coupon che vuoi rinnovare non sia ancora scaduto.');
INSERT INTO `#__gg_configs` VALUES ('61', 'messaggio_rinnovo_success', 'Il coupon è stato rinnovao con successo. Scadrà  nuovamente tra 60 giorni a partire da oggi.');
INSERT INTO `#__gg_configs` VALUES ('62', 'mail_riferimento_specifica', '1');
INSERT INTO `#__gg_configs` VALUES ('63', 'mail_richiesta_tecnica', 'mail_richiesta_tecnica@ggallery.it');
INSERT INTO `#__gg_configs` VALUES ('64', 'mail_richiesta_didattica', 'mail_richiesta_didattica@ggallery.it');
INSERT INTO `#__gg_configs` VALUES ('65', 'campicustom_report', '');
INSERT INTO `#__gg_configs` VALUES ('66', 'mail_debug', 'luca.gallo@gallerygroup.it');
INSERT INTO `#__gg_configs` VALUES ('67', 'xml_ip_dest', '31.14.141.98');
INSERT INTO `#__gg_configs` VALUES ('68', 'xml_ip_dest', '21');
INSERT INTO `#__gg_configs` VALUES ('69', 'xml_read_dir_dest', 'R2k');
INSERT INTO `#__gg_configs` VALUES ('70', 'xml_write_dir_dest', 'GGallery');
INSERT INTO `#__gg_configs` VALUES ('71', 'visualizza_link_semplice', '0');
INSERT INTO `#__gg_configs` VALUES ('72', 'attiva_blocco_video_focus', '0');
INSERT INTO `#__gg_configs` VALUES ('73', 'accesso_corsi_tutoraz', '0');
INSERT INTO `#__gg_configs` VALUES ('74', 'disabilita_mouse', '0');
INSERT INTO `#__gg_configs` VALUES ('75', 'abilita_gruppo_custom', '0');
-- ----------------------------
-- Table structure for `#__gg_contenuti`
-- ----------------------------
-- da MyISAM a InnoDB
DROP TABLE IF EXISTS `#__gg_contenuti`;
CREATE TABLE `#__gg_contenuti` (
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

-- ----------------------------
-- Records of #__gg_contenuti
-- ----------------------------

-- ----------------------------
-- Table structure for `#__gg_contenuti_acl_deprecated`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_contenuti_acl_deprecated`;
CREATE TABLE `#__gg_contenuti_acl_deprecated` (
  `id_contenuto` int(10) NOT NULL DEFAULT '0',
  `id_group` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_contenuto`,`id_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__gg_contenuti_acl_deprecated
-- ----------------------------

-- ----------------------------
-- Table structure for `#__gg_contenuti_tipology`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_contenuti_tipology`;
CREATE TABLE `#__gg_contenuti_tipology` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipologia` varchar(50) DEFAULT NULL,
  `descrizione` varchar(50) DEFAULT NULL,
  `ordinamento` int(10) DEFAULT NULL,
  `pubblicato` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__gg_contenuti_tipology
-- ----------------------------
INSERT INTO `#__gg_contenuti_tipology` VALUES ('1', 'videoslide', 'Contenuti', '1', '1');
INSERT INTO `#__gg_contenuti_tipology` VALUES ('2', 'solovideo', 'Contenuto Video', '2', '0');
INSERT INTO `#__gg_contenuti_tipology` VALUES ('3', 'allegati', 'Allegati', '3', '0');
INSERT INTO `#__gg_contenuti_tipology` VALUES ('4', 'scorm', 'Scorm', '4', '1');
INSERT INTO `#__gg_contenuti_tipology` VALUES ('5', 'attestato', 'Attestato', '5', '1');
INSERT INTO `#__gg_contenuti_tipology` VALUES ('6', 'testuale', 'Testuale', '6', '1');
INSERT INTO `#__gg_contenuti_tipology` VALUES ('7', 'quizdeluxe', 'Quiz Deluxe', '7', '1');
INSERT INTO `#__gg_contenuti_tipology` VALUES ('8', 'singlesignon', 'sso', '8', '1');
INSERT INTO `#__gg_contenuti_tipology` VALUES ('9', 'pdfsingolo', 'Singolo PDF', '9', '1');
INSERT INTO `#__gg_contenuti_tipology` VALUES ('10', 'webinar', 'Webinar', '3', '1');

-- ----------------------------
-- Table structure for `#__gg_coupon`
-- ----------------------------
-- la lunghezza della primary key coupon incrementata alla lunghezza della colonna 20 -> 100
-- da MyISAM a InnoDB
DROP TABLE IF EXISTS `#__gg_coupon`;
CREATE TABLE `#__gg_coupon` (
  `coupon` varchar(100) NOT NULL DEFAULT '',
  `corsi_abilitati` varchar(255) DEFAULT NULL,
  `id_utente` int(11) DEFAULT NULL,
  `gruppo` varchar(255) DEFAULT NULL,
  `creation_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `abilitato` tinyint(4) DEFAULT '0',
  `id_iscrizione` varchar(33) DEFAULT NULL,
  `data_utilizzo` datetime DEFAULT NULL,
  `data_abilitazione` datetime DEFAULT NULL,
  `data_scadenza` datetime DEFAULT NULL,
  `durata` int(3) unsigned DEFAULT '60',
  `attestato` tinyint(1) unsigned DEFAULT NULL,
  `trial` tinyint(1) unsigned DEFAULT '0',
  `id_societa` int(11) DEFAULT NULL,
  `id_gruppi` varchar(11) DEFAULT NULL,
  `stampatracciato` int(1) DEFAULT NULL,
  `venditore` varchar(255) DEFAULT NULL,
  `tipologia_coupon` VARCHAR(100) NULL,
  `template` varchar(255) DEFAULT NULL COMMENT 'Integrazione per migrazione da vecchio GGLMS',
  `ref_skill` varchar(200) DEFAULT NULL COMMENT 'Integrazione per riferimento corsi Skillab',
  PRIMARY KEY (`coupon`(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__gg_coupon
-- ----------------------------

-- ----------------------------
-- Table structure for `#__gg_csv_report`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_csv_report`;
CREATE TABLE `#__gg_csv_report` (
  `id_chiamata` int(255) NOT NULL,
  `id_utente` int(255) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `cognome` varchar(255) DEFAULT NULL,
  `fields` longtext,
  `email` varchar(255) DEFAULT NULL,
  `stato` int(255) DEFAULT NULL,
  `hainiziato` date DEFAULT NULL,
  `hacompletato` date DEFAULT NULL,
  `tempo_lavorativo` time DEFAULT NULL,
  `tempo_straordinario` time DEFAULT NULL,
  `alert` int(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__gg_csv_report
-- ----------------------------

-- ----------------------------
-- Table structure for `#__gg_error_log`
-- ----------------------------
-- colonne modificate:
-- `messaggio` varchar(250) DEFAULT NULL,
--
DROP TABLE IF EXISTS `#__gg_error_log`;
CREATE TABLE `#__gg_error_log` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `messaggio` TEXT DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__gg_error_log
-- ----------------------------

-- ----------------------------
-- Table structure for `#__gg_files`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_files`;
CREATE TABLE `#__gg_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` int(4) NOT NULL DEFAULT '1',
  `date` date DEFAULT NULL,
  `filename` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__gg_files
-- ----------------------------

-- ----------------------------
-- Table structure for `#__gg_files_map`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_files_map`;
CREATE TABLE `#__gg_files_map` (
  `idlink` int(11) NOT NULL AUTO_INCREMENT,
  `idcontenuto` int(11) unsigned NOT NULL,
  `idfile` int(11) unsigned NOT NULL,
  `ordinamento` int(11) DEFAULT NULL,
  `data` date DEFAULT NULL,
  PRIMARY KEY (`idlink`)
) ENGINE=InnoDB AUTO_INCREMENT=431 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__gg_files_map
-- ----------------------------

-- ----------------------------
-- Table structure for `#__gg_log`
-- ----------------------------
-- da MyISAM a InnoDB
DROP TABLE IF EXISTS `#__gg_log`;
CREATE TABLE `#__gg_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_utente` int(10) DEFAULT NULL,
  `id_contenuto` int(10) DEFAULT NULL,
  `data_accesso` datetime DEFAULT NULL,
  `supporto` tinytext,
  `ip_address` varchar(20) DEFAULT NULL,
  `uniqid` varchar(30) DEFAULT NULL,
  `permanenza` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__gg_log
-- ----------------------------

-- ----------------------------
-- Table structure for `#__gg_piattaforma_corso_map`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_piattaforma_corso_map`;
CREATE TABLE `#__gg_piattaforma_corso_map` (
  `id_gruppo_piattaforma` int(11) NOT NULL,
  `id_unita` int(11) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__gg_piattaforma_corso_map
-- ----------------------------

-- ----------------------------
-- Table structure for `#__gg_report`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_report`;
CREATE TABLE `#__gg_report` (
  `id_corso` int(10) NOT NULL,
  `id_event_booking` int(10) DEFAULT NULL,
  `id_unita` int(10) NOT NULL,
  `id_contenuto` int(10) NOT NULL,
  `id_utente` int(10) NOT NULL,
  `id_anagrafica` int(10) DEFAULT NULL,
  `stato` int(10) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `visualizzazioni` int(10) DEFAULT NULL,
  `permanenza_tot` int(10) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `data_extra` datetime NULL,
  `data_primo_accesso` datetime NULL,
  PRIMARY KEY (`id_contenuto`,`id_utente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__gg_report
-- ----------------------------

-- ----------------------------
-- Table structure for `#__gg_report_users`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_report_users`;
CREATE TABLE `#__gg_report_users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_event_booking` int(10) DEFAULT NULL,
  --`id_user` int(10) DEFAULT NULL,
  `id_user` int(10) NOT NULL,
  `nome` varchar(50) DEFAULT NULL,
  `cognome` varchar(50) DEFAULT NULL,
  `fields` longtext,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`,`id_user`),
  UNIQUE KEY `unico` (`id_event_booking`,`id_user`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__gg_report_users
-- ----------------------------

-- ----------------------------
-- Table structure for `#__gg_report_view_permessi`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_report_view_permessi`;
CREATE TABLE `#__gg_report_view_permessi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_utente` int(11) NOT NULL,
  `id_corsi` text,
  PRIMARY KEY (`id`,`id_utente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__gg_report_view_permessi
-- ----------------------------

-- ----------------------------
-- Table structure for `#__gg_report_view_permessi_gruppi`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_report_view_permessi_gruppi`;
CREATE TABLE `#__gg_report_view_permessi_gruppi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_utente` int(11) NOT NULL,
  `id_gruppi` text,
  PRIMARY KEY (`id`,`id_utente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__gg_report_view_permessi_gruppi
-- ----------------------------

-- ----------------------------
-- Table structure for `#__gg_scormvars`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_scormvars`;
CREATE TABLE `#__gg_scormvars` (
  `scoid` int(10) unsigned NOT NULL DEFAULT '0',
  `userid` int(11) NOT NULL DEFAULT '0',
  `varName` varchar(255) NOT NULL DEFAULT '',
  `varValue` text,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`scoid`,`userid`,`varName`),
  KEY `SCOInstanceID` (`scoid`),
  KEY `varName` (`varName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of #__gg_scormvars
-- ----------------------------

-- ----------------------------
-- Table structure for `#__gg_unit`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_unit`;
CREATE TABLE `#__gg_unit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `titolo` varchar(255) NOT NULL DEFAULT '1',
  `alias` varchar(255) DEFAULT NULL,
  `descrizione` mediumtext,
  `unitapadre` int(10) DEFAULT '0',
  `id_event_booking` int(10) DEFAULT NULL,
  `pubblicato` int(11) NOT NULL DEFAULT '0',
  `ordinamento` int(10) DEFAULT NULL,
  `accesso` text,
  `is_corso` int(10) DEFAULT NULL,
  `id_contenuto_completamento` int(10) DEFAULT NULL,
  `data_inizio` date DEFAULT NULL,
  `data_fine` date DEFAULT NULL,
  `prefisso_coupon` varchar(10) DEFAULT NULL,
  `mobile` int(10) DEFAULT '0',
  `attestato` varchar(255) DEFAULT NULL COMMENT 'Integrazione per migrazione da vecchio GGLMS',
  `on_sale` tinyint(1) UNSIGNED DEFAULT '0',
  `disponibile_dal` date DEFAULT NULL,
  `disponibile_al` date DEFAULT NULL,
  `prezzo` decimal(6,2) DEFAULT NULL,
  `sc_a_data` date DEFAULT NULL,
  `sc_valore_data` decimal(6,2) DEFAULT NULL,
  `sc_a_data_gruppi` text DEFAULT NULL,
  `sc_valore_data_gruppi` decimal(6,2) DEFAULT NULL,
  `sc_a_gruppi` text DEFAULT NULL,
  `sc_valore_gruppi` decimal(6,2) DEFAULT NULL,
  `sc_da_data` date DEFAULT NULL,
  `sc_custom_cb` varchar(255) NULL,
  `sc_semaforo_custom_cb` varchar(255) NULL,
  `sc_valore_custom_cb` decimal(6,2) NULL,
  `usa_coupon`  tinyint(1) NOT NULL DEFAULT 1,
  `codice`  varchar(255) NULL,
  `codice_alfanumerico`  varchar(255) NULL,
  `tipologia_corso`  tinyint(1) NOT NULL DEFAULT 6,
  `sconti_particolari` tinyint(1) UNSIGNED DEFAULT '0',
  `buy_voucher` tinyint(1) UNSIGNED DEFAULT '0',
  `riduzione_webinar` tinyint(1) UNSIGNED DEFAULT '0',
  `sc_webinar_perc` decimal(6,2) DEFAULT NULL COMMENT 'Lo sconto percentuale per acquisto in modalita webinar',
  `disabilita_aquisto_presenza` tinyint(1) UNSIGNED DEFAULT '0' COMMENT 'Vendita - Disabilita acquisto eventi in presenza',
  `prezzo_webinar_fisso` tinyint(1) NOT NULL DEFAULT 0,
  `id_gruppi_custom` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `titolo` (`titolo`,`descrizione`)
) ENGINE=InnoDB AUTO_INCREMENT=247 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__gg_unit
-- ----------------------------
INSERT INTO `#__gg_unit` VALUES (
                                '1',
                                'Corsi',
                                'corsi',
                                '',
                                '0',
                                '0',
                                '1',
                                '0',
                                'Accesso libero',
                                '1',
                                '0',
                                '2019-11-14',
                                '2019-11-14',
                                null,
                                0,
                                null,
                                0,
                                null,
                                null,
                                null,
                                null,
                                null,
                                null,
                                null,
                                null,
                                null,
                                null,
                                null,
                                null,
                                null,
                                1,
                                null,
                                null,
                                6,
                                0,
                                0,
                                null,
								0,
								1,
								0,
                                null);

-- ----------------------------
-- Table structure for `#__gg_unit_map`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_unit_map`;
CREATE TABLE `#__gg_unit_map` (
  `idcontenuto` int(11) unsigned NOT NULL,
  `idunita` int(11) unsigned NOT NULL,
  `ordinamento` int(11) DEFAULT '99',
  PRIMARY KEY (`idcontenuto`,`idunita`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__gg_unit_map
-- ----------------------------

-- ----------------------------
-- Table structure for `#__gg_usergroup_map`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_usergroup_map`;
CREATE TABLE `#__gg_usergroup_map` (
  `idunita` int(11) unsigned NOT NULL,
  `idgruppo` int(11) unsigned NOT NULL,
  PRIMARY KEY (`idunita`,`idgruppo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__gg_usergroup_map
-- ----------------------------

-- ----------------------------
-- Table structure for `#__gg_view_carige_learning_batch_deprecated`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_view_carige_learning_batch_deprecated`;
CREATE TABLE `#__gg_view_carige_learning_batch_deprecated` (
  `id_corso` int(10) NOT NULL,
  `id_user` int(10) NOT NULL,
  `data_primo_accesso` date DEFAULT NULL,
  `data_ultimo_accesso` date DEFAULT NULL,
  `data_completamento_edizione` date DEFAULT NULL,
  `percentuale_completamento` float(5,2) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_corso`,`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__gg_view_carige_learning_batch_deprecated
-- ----------------------------

-- ----------------------------
-- Table structure for `#__gg_view_stato_user_corso`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_view_stato_user_corso`;
CREATE TABLE `#__gg_view_stato_user_corso` (
  `id_anagrafica` int(10) NOT NULL,
  `id_corso` int(10) NOT NULL,
  `stato` int(10) NOT NULL,
  `data_inizio` date DEFAULT NULL,
  `data_fine` date DEFAULT NULL,
  `data_inizio_extra` datetime NULL,
  `data_fine_extra` datetime NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_anagrafica`,`id_corso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__gg_view_stato_user_corso
-- ----------------------------

-- ----------------------------
-- Table structure for `#__gg_view_stato_user_unita`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_view_stato_user_unita`;
CREATE TABLE `#__gg_view_stato_user_unita` (
  `id_anagrafica` int(10) NOT NULL DEFAULT '0',
  `id_unita` int(10) NOT NULL DEFAULT '0',
  `id_corso` int(10) NOT NULL DEFAULT '0',
  `stato` int(10) DEFAULT NULL,
  `data_inizio` date DEFAULT NULL,
  `data_fine` date DEFAULT NULL,
  `data_inizio_extra` datetime NULL,
  `data_fine_extra` datetime NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_anagrafica`,`id_unita`,`id_corso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__gg_view_stato_user_unita
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_mail_log`;
CREATE TABLE `#__gg_mail_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template` varchar(255) DEFAULT NULL,
  `sender` varchar(255) DEFAULT NULL,
  `recipient` varchar(255) DEFAULT NULL,
  `cc` varchar(255) DEFAULT NULL,
  `id_gruppo_corso` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ----------------------------
-- Table structure for `#__usergroups_details`
-- ----------------------------
DROP TABLE IF EXISTS `#__usergroups_details`;
CREATE TABLE `#__usergroups_details` (
  `group_id` int(11) NOT NULL,
  `is_default` tinyint(1) unsigned DEFAULT '0',
  `name` varchar(63) NOT NULL,
  `dg` varchar(127) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `nomi_tutor` varchar(255) DEFAULT NULL,
  `email_tutor` varchar(255) NOT NULL,
  `email_riferimento` varchar(255) DEFAULT NULL,
  `link_ecommerce` varchar(255) DEFAULT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `attivo` tinyint(4) DEFAULT NULL,
  `dominio` varchar(255) DEFAULT NULL,
  `patrocinio` varchar(255) DEFAULT NULL,
  `footer` mediumtext,
  `final_test` varchar(255) DEFAULT NULL,
  `welcome` TEXT DEFAULT NULL,
  `corsi_visbili_catalogo` varchar(100) DEFAULT NULL,
  `testo_intro_homepage` varchar(50) DEFAULT NULL,
  `attestati_custom` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `mail_from_default` tinyint(1) unsigned zerofill DEFAULT '0',
  `info_pagamento` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `#__gg_prezzi`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_prezzi`;
CREATE TABLE `#__gg_prezzi` (
  `id_corso` int(11) NOT NULL,
  `p1` int(255) NOT NULL,
  `p2` int(255) DEFAULT NULL,
  `p3` int(255) DEFAULT NULL,
  `p4` int(255) DEFAULT NULL,
  `sconto_associati` float NOT NULL,
  PRIMARY KEY (`id_corso`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- ----------------------------
-- Table structure for `#_gg_prezzi_range`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_prezzi_range`;
CREATE TABLE `#__gg_prezzi_range` (
  `id_corso` int(11) NOT NULL,
  `range1` int(11) NOT NULL,
  `range2` int(11) DEFAULT NULL,
  `range3` int(11) DEFAULT NULL,
  `range4` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_corso`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- ----------------------------
-- Table structure for `#__gg_coupon_dispenser`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_coupon_dispenser`;
CREATE TABLE `#__gg_coupon_dispenser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titolo` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `descrizione` text,
  `id_iscrizione` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;



-- ----------------------------
-- Table structure for `#__gg_coupon_dispenser_log`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_coupon_dispenser_log`;
CREATE TABLE `#__gg_coupon_dispenser_log` (
  `email` varchar(255) NOT NULL,
  `id_dispenser` int(11) NOT NULL,
  `coupon` varchar(255) NOT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`email`,`id_dispenser`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for `#__gg_zoom`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_zoom`;
CREATE TABLE IF NOT EXISTS `#__gg_zoom` (
  `id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `token`  text NOT NULL ,
  `data_registrazione`  datetime NOT NULL ,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `#__gg_zoom_events`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_zoom_events`;
CREATE TABLE IF NOT EXISTS `#__gg_zoom_events` (
  `id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `id_evento`  varchar(100) NOT NULL,
  `tipo_evento`  varchar(50) NOT NULL ,
  `label_evento`  varchar(200) NOT NULL,
  `response`  text NOT NULL ,
  `data_registrazione`  datetime NOT NULL ,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `#__gg_check_coupon_xml`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_check_coupon_xml`;
CREATE TABLE IF NOT EXISTS `#__gg_check_coupon_xml` (
    `id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `codice_corso` VARCHAR(200) DEFAULT NULL COMMENT 'Il corso di riferimento per la creazione del coupon',
    `codice_fiscale` VARCHAR(200) DEFAULT NULL COMMENT 'Il riferimento al codice fiscale iscritto',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `#__gg_vendita_sconti_particolari`
-- ----------------------------

-- tabella per impostare scontistiche articolate sulla vendita eventi
DROP TABLE IF EXISTS `#__gg_vendita_sconti_particolari`;
CREATE TABLE `#__gg_vendita_sconti_particolari` (
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
	`prezzo_webinar` decimal(6,2) NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `#__gg_quote_iscrizioni`
-- ----------------------------

DROP TABLE IF EXISTS `#__gg_quote_iscrizioni`;
CREATE TABLE IF NOT EXISTS `#__gg_quote_iscrizioni`
(
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
	`user_id` INT(11) UNSIGNED NOT NULL ,
	`anno` INT(4) NOT NULL ,
	`tipo_quota` VARCHAR(20) NOT NULL ,
	`tipo_pagamento` VARCHAR(50) NULL,
	`data_pagamento` DATETIME NULL,
	`totale` DECIMAL(6,2) NULL,
	`dettagli_transazione` TEXT NULL,
  `gruppo_corso` INT(11) UNSIGNED DEFAULT 0,
	`stato` TINYINT(1) DEFAULT 0,
	PRIMARY KEY (`id`), INDEX (`user_id`)
) ENGINE = InnoDB;

-- ----------------------------
-- Table structure for `#__gg_anagrafica_centri`
-- ----------------------------

DROP TABLE IF EXISTS `#__gg_anagrafica_centri`;
CREATE TABLE `#__gg_anagrafica_centri` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `centro` varchar(100) DEFAULT NULL,
  `indirizzo` varchar(100) DEFAULT NULL,
  `telefono_responsabile` longtext,
  `telefono_servizio` longtext,
  `fax` longtext,
  `email` VARCHAR(100) DEFAULT NULL,
  `responsabile` VARCHAR(100) DEFAULT NULL,
  `ruolo` VARCHAR(100) DEFAULT NULL,
  `latitudine` varchar(100) DEFAULT NULL,
  `longitudine` varchar(100) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `citta` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `#__gg_registration_request`
-- ----------------------------

DROP TABLE IF EXISTS `#__gg_registration_request`;
CREATE TABLE `#__gg_registration_request` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `token` TEXT NOT NULL,
  `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `#__gg_quote_voucher`
-- ----------------------------

DROP TABLE IF EXISTS `#__gg_quote_voucher`;
CREATE TABLE `#__gg_quote_voucher` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NULL,
  `course_id` INT(11) NULL,
  `code` TEXT NOT NULL,
  `buy_subscription` tinyint(1) UNSIGNED DEFAULT '1',
  `buy_course` tinyint(1) UNSIGNED DEFAULT '0',
  `date` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `#__gg_cod_votazioni_users`
-- ----------------------------

DROP TABLE IF EXISTS `#__gg_cod_votazioni_users`;
CREATE TABLE `#__gg_cod_votazioni_users` (
  `id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_user` varchar(100) NOT NULL,
  `codice` varchar(100) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`,`id_user`, `codice`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `#__gg_quote_voucher`
-- ----------------------------

DROP TABLE IF EXISTS `#__gg_quote_voucher`;
CREATE TABLE `#__gg_quote_voucher` (
      `id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `id_user` varchar(100) NOT NULL,
      `id_candidato` varchar(100) NOT NULL,
      `codice` varchar(100) NOT NULL,
      `dettagli` varchar(100) DEFAULT NULL,
      `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`,`id_user`, `codice`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- colonne custom di #__comprofiler
-- auto popolamento dei campi di Community Builder
-- ----------------------------
ALTER TABLE `#__comprofiler`
	ADD COLUMN `cb_cognome` text,
	ADD COLUMN `cb_codicefiscale` text,
	ADD COLUMN `cb_datadinascita` date DEFAULT NULL,
	ADD COLUMN `cb_luogodinascita` text,
	ADD COLUMN `cb_provinciadinascita` text,
	ADD COLUMN `cb_indirizzodiresidenza` text,
	ADD COLUMN `cb_provdiresidenza` text,
	ADD COLUMN `cb_cap` text,
	ADD COLUMN `cb_telefono` text,
	ADD COLUMN `cb_nome` text,
	ADD COLUMN `cb_citta` text,
	ADD COLUMN `cb_professionedisciplina` text,
	ADD COLUMN `cb_ordine` text,
	ADD COLUMN `cb_numeroiscrizione` text,
	ADD COLUMN `cb_reclutamento` text,
	ADD COLUMN `cb_ateco` text,
	ADD COLUMN `cb_codicereclutamento` text,
	ADD COLUMN `cb_professione` text,
	ADD COLUMN `cb_profiloprofessionale` text,
	ADD COLUMN `cb_settore` text,
	ADD COLUMN `cb_societa` text,
	ADD COLUMN `cb_rischio` text,
	ADD COLUMN `cb_privacy` tinyint(3),
	ADD COLUMN `cb_regione` text,
	ADD COLUMN `cb_dataiscrizione` text,
	ADD COLUMN `cb_azienda` text,
	ADD COLUMN `cb_dipartimento` text,
	ADD COLUMN `cb_reparto` text,
	ADD COLUMN `cb_laureain` text,
	ADD COLUMN `cb_laureanno` text,
	ADD COLUMN `cb_qualifica` text,
	ADD COLUMN `cb_newsletter` tinyint(3),
	ADD COLUMN `cb_ultimoannoinregola` text,
	ADD COLUMN `cb_accessonutritiononline` tinyint(3),
	ADD COLUMN `cb_codicenutritiononline` text,
	ADD COLUMN `cb_titolo` text,
	ADD COLUMN `cb_altraemail` text;

INSERT INTO `#__comprofiler_fields` (`fieldid`, `name`, `tablecolumns`, `table`, `title`, `description`, `type`, `maxlength`, `size`, `required`, `tabid`, `ordering`, `cols`, `rows`, `value`, `default`, `published`, `registration`, `edit`, `profile`, `readonly`, `searchable`, `calculated`, `sys`, `pluginid`, `cssclass`, `params`) VALUES
(55, 'cb_cognome', 'cb_cognome', '#__comprofiler', 'Cognome', '', 'text', 0, 0, 1, 11, 5, NULL, NULL, NULL, '', 1, 1, 1, 1, 0, 1, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\",\"fieldPlaceholder\":\"\",\"fieldMinLength\":\"0\",\"fieldValidateExpression\":\"\",\"pregexp\":\"\\/^.*$\\/\",\"pregexperror\":\"Not a valid input\",\"fieldValidateForbiddenList_register\":\"http:,https:,mailto:,\\/\\/.[url],<a,<\\/a>,&#\",\"fieldValidateForbiddenList_edit\":\"\"}'),
(56, 'cb_codicefiscale', 'cb_codicefiscale', '#__comprofiler', 'Codice fiscale', '', 'text', 16, 0, 1, 11, 6, NULL, NULL, NULL, '', 1, 1, 1, 1, 0, 1, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\",\"fieldPlaceholder\":\"\",\"fieldMinLength\":\"16\",\"fieldValidateExpression\":\"\",\"pregexp\":\"\\/^.*$\\/\",\"pregexperror\":\"Not a valid input\",\"fieldValidateForbiddenList_register\":\"http:,https:,mailto:,\\/\\/.[url],<a,<\\/a>,&#\",\"fieldValidateForbiddenList_edit\":\"\"}'),
(57, 'cb_datadinascita', 'cb_datadinascita', '#__comprofiler', 'Data di nascita', '', 'date', NULL, 0, 1, 11, 7, NULL, NULL, NULL, '', 1, 1, 1, 1, 0, 1, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\",\"field_edit_format\":\"0\",\"custom_date_edit_format\":\"Y-m-d\",\"field_display_by\":\"0\",\"custom_date_format\":\"Y-m-d\",\"field_search_by\":\"1\",\"custom_date_search_format\":\"Y-m-d\",\"duration_title\":\"\",\"calendar_type\":\"2\",\"year_min\":\"-80\",\"year_max\":\"0\",\"age_min\":\"\",\"age_max\":\"\"}'),
(59, 'cb_luogodinascita', 'cb_luogodinascita', '#__comprofiler', 'Luogo di nascita', '', 'text', 0, 0, 1, 11, 8, NULL, NULL, NULL, '', 1, 1, 1, 1, 0, 0, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\",\"fieldPlaceholder\":\"\",\"fieldMinLength\":\"0\",\"fieldValidateExpression\":\"\",\"pregexp\":\"\\/^.*$\\/\",\"pregexperror\":\"Not a valid input\",\"fieldValidateForbiddenList_register\":\"http:,https:,mailto:,\\/\\/.[url],<a,<\\/a>,&#\",\"fieldValidateForbiddenList_edit\":\"\"}'),
(60, 'cb_provinciadinascita', 'cb_provinciadinascita', '#__comprofiler', 'Provincia di nascita', '', 'select', NULL, 0, 1, 11, 9, NULL, NULL, NULL, '', 1, 1, 1, 1, 0, 0, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\"}'),
(61, 'cb_indirizzodiresidenza', 'cb_indirizzodiresidenza', '#__comprofiler', 'Indirizzo di residenza', '', 'text', 0, 0, 1, 11, 10, NULL, NULL, NULL, '', 1, 1, 1, 1, 0, 0, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\",\"fieldPlaceholder\":\"\",\"fieldMinLength\":\"0\",\"fieldValidateExpression\":\"\",\"pregexp\":\"\\/^.*$\\/\",\"pregexperror\":\"Not a valid input\",\"fieldValidateForbiddenList_register\":\"http:,https:,mailto:,\\/\\/.[url],<a,<\\/a>,&#\",\"fieldValidateForbiddenList_edit\":\"\"}'),
(62, 'cb_provdiresidenza', 'cb_provdiresidenza', '#__comprofiler', 'Provincia di residenza', '', 'select', NULL, 0, 1, 11, 13, NULL, NULL, NULL, '', 1, 1, 1, 1, 0, 0, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\"}'),
(63, 'cb_cap', 'cb_cap', '#__comprofiler', 'CAP', '', 'text', 5, 0, 1, 11, 12, NULL, NULL, NULL, '', 1, 1, 1, 1, 0, 0, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\",\"fieldPlaceholder\":\"\",\"fieldMinLength\":\"0\",\"fieldValidateExpression\":\"\",\"pregexp\":\"\\/^.*$\\/\",\"pregexperror\":\"Not a valid input\",\"fieldValidateForbiddenList_register\":\"http:,https:,mailto:,\\/\\/.[url],<a,<\\/a>,&#\",\"fieldValidateForbiddenList_edit\":\"\"}'),
(64, 'cb_telefono', 'cb_telefono', '#__comprofiler', 'Recapito telefonico', '', 'text', 0, 0, 0, 11, 14, NULL, NULL, NULL, '', 1, 1, 1, 1, 0, 0, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\",\"fieldPlaceholder\":\"\",\"fieldMinLength\":\"0\",\"fieldValidateExpression\":\"\",\"pregexp\":\"\\/^.*$\\/\",\"pregexperror\":\"Not a valid input\",\"fieldValidateForbiddenList_register\":\"http:,https:,mailto:,\\/\\/.[url],<a,<\\/a>,&#\",\"fieldValidateForbiddenList_edit\":\"\"}'),
(67, 'cb_nome', 'cb_nome', '#__comprofiler', 'Nome', '', 'text', 0, 0, 1, 11, 4, NULL, NULL, NULL, '', 1, 1, 1, 1, 0, 0, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\",\"fieldPlaceholder\":\"\",\"fieldMinLength\":\"0\",\"fieldValidateExpression\":\"\",\"pregexp\":\"\\/^.*$\\/\",\"pregexperror\":\"Not a valid input\",\"fieldValidateForbiddenList_register\":\"http:,https:,mailto:,\\/\\/.[url],<a,<\\/a>,&#\",\"fieldValidateForbiddenList_edit\":\"\"}'),
(69, 'cb_citta', 'cb_citta', '#__comprofiler', 'Città', '', 'text', 0, 0, 1, 11, 11, NULL, NULL, NULL, '', 1, 1, 1, 1, 0, 0, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\",\"fieldPlaceholder\":\"\",\"fieldMinLength\":\"0\",\"fieldValidateExpression\":\"\",\"pregexp\":\"\\/^.*$\\/\",\"pregexperror\":\"Not a valid input\",\"fieldValidateForbiddenList_register\":\"http:,https:,mailto:,\\/\\/.[url],<a,<\\/a>,&#\",\"fieldValidateForbiddenList_edit\":\"\"}'),
(70, 'cb_professionedisciplina', 'cb_professionedisciplina', '#__comprofiler', 'Professione/Disciplina', '', 'select', NULL, 0, 1, 11, 15, NULL, NULL, NULL, '', 1, 1, 1, 1, 0, 0, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\"}'),
(71, 'cb_ordine', 'cb_ordine', '#__comprofiler', 'Ordine di', '', 'select', NULL, 0, 1, 11, 16, NULL, NULL, NULL, '', 1, 1, 1, 1, 0, 0, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\"}'),
(72, 'cb_numeroiscrizione', 'cb_numeroiscrizione', '#__comprofiler', 'Numero iscrizione all\'albo', '', 'text', 0, 0, 1, 11, 17, NULL, NULL, NULL, '', 1, 1, 1, 1, 0, 0, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\",\"fieldPlaceholder\":\"\",\"fieldMinLength\":\"0\",\"fieldValidateExpression\":\"\",\"pregexp\":\"\\/^.*$\\/\",\"pregexperror\":\"Not a valid input\",\"fieldValidateForbiddenList_register\":\"http:,https:,mailto:,\\/\\/.[url],<a,<\\/a>,&#\",\"fieldValidateForbiddenList_edit\":\"\"}'),
(73, 'cb_reclutamento', 'cb_reclutamento', '#__comprofiler', 'Reclutamento sponsor', '', 'select', NULL, 0, 1, 11, 18, NULL, NULL, NULL, '', 1, 1, 1, 1, 0, 0, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\"}'),
(74, 'cb_ateco', 'cb_ateco', '#__comprofiler', 'ateco', '', 'text', 0, 0, 0, 11, 21, NULL, NULL, NULL, '', 0, 1, 1, 1, 0, 0, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\",\"fieldPlaceholder\":\"\",\"fieldMinLength\":\"0\",\"fieldValidateExpression\":\"\",\"pregexp\":\"\\/^.*$\\/\",\"pregexperror\":\"Not a valid input\",\"fieldValidateForbiddenList_register\":\"http:,https:,mailto:,\\/\\/.[url],<a,<\\/a>,&#\",\"fieldValidateForbiddenList_edit\":\"\"}'),
(75, 'cb_codicereclutamento', 'cb_codicereclutamento', '#__comprofiler', 'Codice reclutamento', '', 'text', 0, 0, 0, 11, 19, NULL, NULL, NULL, '', 1, 1, 1, 1, 0, 0, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\",\"fieldPlaceholder\":\"\",\"fieldMinLength\":\"0\",\"fieldValidateExpression\":\"\",\"pregexp\":\"\\/^.*$\\/\",\"pregexperror\":\"Not a valid input\",\"fieldValidateForbiddenList_register\":\"http:,https:,mailto:,\\/\\/.[url],<a,<\\/a>,&#\",\"fieldValidateForbiddenList_edit\":\"\"}'),
(76, 'cb_professione', 'cb_professione', '#__comprofiler', 'Professione', '', 'text', 0, 0, 1, 11, 22, NULL, NULL, NULL, '', 0, 1, 1, 1, 0, 0, 0, 0, 1, NULL, 'fieldMinLength=0\nfieldValidateExpression=\npregexp=/^.*$/\nfieldValidateInBrowser=1\npregexperror=Not a valid input\nfieldValidateForbiddenList_register=http:,https:,mailto:,//.[url],<a,</a>,&#\nfieldValidateForbiddenList_edit='),
(77, 'cb_profiloprofessionale', 'cb_profiloprofessionale', '#__comprofiler', 'Profilo professionale', 'Descrizioni dei contenuti \"tipici\" delle attività di specifiche categorie di lavoratori.', 'text', 0, 0, 1, 11, 23, NULL, NULL, NULL, '', 1, 1, 1, 1, 0, 0, 0, 0, 1, NULL, 'fieldMinLength=0\nfieldValidateExpression=\npregexp=/^.*$/\nfieldValidateInBrowser=1\npregexperror=Not a valid input\nfieldValidateForbiddenList_register=http:,https:,mailto:,//.[url],<a,</a>,&#\nfieldValidateForbiddenList_edit='),
(78, 'cb_settore', 'cb_settore', '#__comprofiler', 'Settore', '', 'text', 0, 0, 1, 11, 24, NULL, NULL, NULL, '', 1, 0, 1, 1, 0, 0, 0, 0, 1, NULL, 'fieldMinLength=0\nfieldValidateExpression=\npregexp=/^.*$/\nfieldValidateInBrowser=1\npregexperror=Not a valid input\nfieldValidateForbiddenList_register=http:,https:,mailto:,//.[url],<a,</a>,&#\nfieldValidateForbiddenList_edit='),
(79, 'cb_societa', 'cb_societa', '#__comprofiler', 'Società', '', 'text', 0, 0, 1, 11, 25, NULL, NULL, NULL, '', 1, 1, 1, 1, 0, 0, 0, 0, 1, NULL, 'fieldMinLength=0\nfieldValidateExpression=\npregexp=/^.*$/\nfieldValidateInBrowser=1\npregexperror=Not a valid input\nfieldValidateForbiddenList_register=http:,https:,mailto:,//.[url],<a,</a>,&#\nfieldValidateForbiddenList_edit='),
(80, 'cb_rischio', 'cb_rischio', '#__comprofiler', 'Rischio', '', 'text', 0, 0, 0, 20, 26, NULL, NULL, NULL, '', 0, 0, 1, 0, 0, 0, 0, 0, 1, NULL, 'fieldMinLength=0\nfieldValidateExpression=\npregexp=/^.*$/\nfieldValidateInBrowser=1\npregexperror=Not a valid input\nfieldValidateForbiddenList_register=http:,https:,mailto:,//.[url],<a,</a>,&#\nfieldValidateForbiddenList_edit='),
(81, 'cb_privacy', 'cb_privacy', '#__comprofiler', 'Ho letto l\'<a href=\"index.php?option=com_content&view=article&id=75\" target=\"_blank\">informativa privacy</a> e do il consenso al trattamento dei miei dati', '', 'checkbox', 0, 0, 1, 11, 27, NULL, NULL, NULL, '', 1, 1, 1, 1, 0, 0, 0, 0, 1, NULL, 'fieldMinLength=0\r\nfieldValidateExpression=\r\npregexp=/^.*$/\r\nfieldValidateInBrowser=1\r\npregexperror=Not a valid input\r\nfieldValidateForbiddenList_register=http:,https:,mailto:,//.[url],<a,</a>,&#\r\nfieldValidateForbiddenList_edit='),
(82, 'cb_regione', 'cb_regione', '#__comprofiler', 'Regione', '', 'select', NULL, 0, 1, 11, 28, NULL, NULL, NULL, '', 1, 1, 1, 1, 0, 0, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\"}'),
(83, 'cb_dataiscrizione', 'cb_dataiscrizione', '#__comprofiler', 'Data iscrizione', '', 'text', 0, 0, 0, 11, 29, NULL, NULL, NULL, '', 0, 1, 1, 1, 0, 0, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\",\"fieldPlaceholder\":\"\",\"fieldMinLength\":\"0\",\"fieldValidateExpression\":\"\",\"pregexp\":\"\\/^.*$\\/\",\"pregexperror\":\"Not a valid input\",\"fieldValidateForbiddenList_register\":\"http:,https:,mailto:,\\/\\/.[url],<a,<\\/a>,&#\",\"fieldValidateForbiddenList_edit\":\"\"}'),
(84, 'cb_azienda', 'cb_azienda', '#__comprofiler', 'Azienda', '', 'text', 0, 0, 0, 11, 30, NULL, NULL, NULL, '', 0, 1, 1, 1, 0, 0, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\",\"fieldPlaceholder\":\"\",\"fieldMinLength\":\"0\",\"fieldValidateExpression\":\"\",\"pregexp\":\"\\/^.*$\\/\",\"pregexperror\":\"Not a valid input\",\"fieldValidateForbiddenList_register\":\"http:,https:,mailto:,\\/\\/.[url],<a,<\\/a>,&#\",\"fieldValidateForbiddenList_edit\":\"\"}'),
(85, 'cb_dipartimento', 'cb_dipartimento', '#__comprofiler', 'Dipartimento', '', 'text', 0, 0, 0, 11, 30, NULL, NULL, NULL, '', 0, 1, 1, 1, 0, 0, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\",\"fieldPlaceholder\":\"\",\"fieldMinLength\":\"0\",\"fieldValidateExpression\":\"\",\"pregexp\":\"\\/^.*$\\/\",\"pregexperror\":\"Not a valid input\",\"fieldValidateForbiddenList_register\":\"http:,https:,mailto:,\\/\\/.[url],<a,<\\/a>,&#\",\"fieldValidateForbiddenList_edit\":\"\"}'),
(86, 'cb_reparto', 'cb_reparto', '#__comprofiler', 'Reparto', '', 'text', 0, 0, 0, 11, 31, NULL, NULL, NULL, '', 0, 1, 1, 1, 0, 0, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\",\"fieldPlaceholder\":\"\",\"fieldMinLength\":\"0\",\"fieldValidateExpression\":\"\",\"pregexp\":\"\\/^.*$\\/\",\"pregexperror\":\"Not a valid input\",\"fieldValidateForbiddenList_register\":\"http:,https:,mailto:,\\/\\/.[url],<a,<\\/a>,&#\",\"fieldValidateForbiddenList_edit\":\"\"}'),
(87, 'cb_laureain', 'cb_laureain', '#__comprofiler', 'Laurea in', '', 'select', NULL, 0, 1, 11, 32, NULL, NULL, NULL, '', 1, 1, 1, 1, 0, 0, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\"}'),
(88, 'cb_laureanno', 'cb_laureanno', '#__comprofiler', 'Anno laurea', '', 'text', 0, 0, 0, 11, 33, NULL, NULL, NULL, '', 0, 1, 1, 1, 0, 0, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\",\"fieldPlaceholder\":\"\",\"fieldMinLength\":\"0\",\"fieldValidateExpression\":\"\",\"pregexp\":\"\\/^.*$\\/\",\"pregexperror\":\"Not a valid input\",\"fieldValidateForbiddenList_register\":\"http:,https:,mailto:,\\/\\/.[url],<a,<\\/a>,&#\",\"fieldValidateForbiddenList_edit\":\"\"}'),
(89, 'cb_qualifica', 'cb_qualifica', '#__comprofiler', 'Qualifica', '', 'select', NULL, 0, 1, 11, 34, NULL, NULL, NULL, '', 1, 1, 1, 1, 0, 0, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\"}'),
(90, 'cb_newsletter', 'cb_newsletter', '#__comprofiler', 'Riceve le newsletter', '', 'checkbox', 0, 0, 1, 11, 35, NULL, NULL, NULL, '', 1, 1, 1, 1, 0, 0, 0, 0, 1, NULL, 'fieldMinLength=0\r\nfieldValidateExpression=\r\npregexp=/^.*$/\r\nfieldValidateInBrowser=1\r\npregexperror=Not a valid input\r\nfieldValidateForbiddenList_register=http:,https:,mailto:,//.[url],<a,</a>,&#\r\nfieldValidateForbiddenList_edit='),
(91, 'cb_ultimoannoinregola', 'cb_ultimoannoinregola', '#__comprofiler', 'Ultimo anno in regola', '', 'text', 0, 0, 0, 11, 36, NULL, NULL, NULL, '', 0, 1, 1, 1, 0, 0, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\",\"fieldPlaceholder\":\"\",\"fieldMinLength\":\"0\",\"fieldValidateExpression\":\"\",\"pregexp\":\"\\/^.*$\\/\",\"pregexperror\":\"Not a valid input\",\"fieldValidateForbiddenList_register\":\"http:,https:,mailto:,\\/\\/.[url],<a,<\\/a>,&#\",\"fieldValidateForbiddenList_edit\":\"\"}'),
(92, 'cb_accessonutritiononline', 'cb_accessonutritiononline', '#__comprofiler', 'Accesso Nutrition Online', '', 'checkbox', 0, 0, 1, 11, 37, NULL, NULL, NULL, '', 1, 1, 1, 1, 0, 0, 0, 0, 1, NULL, 'fieldMinLength=0\r\nfieldValidateExpression=\r\npregexp=/^.*$/\r\nfieldValidateInBrowser=1\r\npregexperror=Not a valid input\r\nfieldValidateForbiddenList_register=http:,https:,mailto:,//.[url],<a,</a>,&#\r\nfieldValidateForbiddenList_edit='),
(93, 'cb_codicenutritiononline', 'cb_codicenutritiononline', '#__comprofiler', 'Codice Nutrition Online', '', 'text', 0, 0, 0, 11, 36, NULL, NULL, NULL, '', 0, 1, 1, 1, 0, 0, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\",\"fieldPlaceholder\":\"\",\"fieldMinLength\":\"0\",\"fieldValidateExpression\":\"\",\"pregexp\":\"\\/^.*$\\/\",\"pregexperror\":\"Not a valid input\",\"fieldValidateForbiddenList_register\":\"http:,https:,mailto:,\\/\\/.[url],<a,<\\/a>,&#\",\"fieldValidateForbiddenList_edit\":\"\"}'),
(94, 'cb_titolo', 'cb_titolo', '#__comprofiler', 'Titolo', '', 'select', NULL, 0, 1, 11, 37, NULL, NULL, NULL, '', 1, 1, 1, 1, 0, 0, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\"}'),
(95, 'cb_altraemail', 'cb_altraemail', '#__comprofiler', 'Altra Email', '', 'text', 0, 0, 0, 11, 38, NULL, NULL, NULL, '', 0, 1, 1, 1, 0, 0, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldLayoutInputDesc\":\"1\",\"fieldPlaceholder\":\"\",\"fieldMinLength\":\"0\",\"fieldValidateExpression\":\"\",\"pregexp\":\"\\/^.*$\\/\",\"pregexperror\":\"Not a valid input\",\"fieldValidateForbiddenList_register\":\"http:,https:,mailto:,\\/\\/.[url],<a,<\\/a>,&#\",\"fieldValidateForbiddenList_edit\":\"\"}');
INSERT INTO `#__comprofiler_field_values` (`fieldid`, `fieldtitle`, `fieldlabel`, `fieldgroup`, `ordering`, `sys`) VALUES
	(60, 'AG', '', 0, 1, 0),
	(60, 'AL', '', 0, 2, 0),
	(60, 'AN', '', 0, 3, 0),
	(60, 'AO', '', 0, 4, 0),
	(60, 'AP', '', 0, 5, 0),
	(60, 'AQ', '', 0, 6, 0),
	(60, 'AR', '', 0, 7, 0),
	(60, 'AT', '', 0, 8, 0),
	(60, 'AV', '', 0, 9, 0),
	(60, 'BA', '', 0, 10, 0),
	(60, 'BG', '', 0, 11, 0),
	(60, 'BI', '', 0, 12, 0),
	(60, 'BL', '', 0, 13, 0),
	(60, 'BN', '', 0, 14, 0),
	(60, 'BO', '', 0, 15, 0),
	(60, 'BR', '', 0, 16, 0),
	(60, 'BS', '', 0, 17, 0),
	(60, 'BT', '', 0, 18, 0),
	(60, 'BZ', '', 0, 19, 0),
	(60, 'CA', '', 0, 20, 0),
	(60, 'CB', '', 0, 21, 0),
	(60, 'CE', '', 0, 22, 0),
	(60, 'CH', '', 0, 23, 0),
	(60, 'CI', '', 0, 24, 0),
	(60, 'CL', '', 0, 25, 0),
	(60, 'CN', '', 0, 26, 0),
	(60, 'CO', '', 0, 27, 0),
	(60, 'CR', '', 0, 28, 0),
	(60, 'CS', '', 0, 29, 0),
	(60, 'CT', '', 0, 30, 0),
	(60, 'CZ', '', 0, 31, 0),
	(60, 'EN', '', 0, 32, 0),
	(60, 'FC', '', 0, 33, 0),
	(60, 'FE', '', 0, 34, 0),
	(60, 'FG', '', 0, 35, 0),
	(60, 'FI', '', 0, 36, 0),
	(60, 'FM', '', 0, 37, 0),
	(60, 'FR', '', 0, 38, 0),
	(60, 'GE', '', 0, 39, 0),
	(60, 'GO', '', 0, 40, 0),
	(60, 'GR', '', 0, 41, 0),
	(60, 'IM', '', 0, 42, 0),
	(60, 'IS', '', 0, 43, 0),
	(60, 'KR', '', 0, 44, 0),
	(60, 'LC', '', 0, 45, 0),
	(60, 'LE', '', 0, 46, 0),
	(60, 'LI', '', 0, 47, 0),
	(60, 'LO', '', 0, 48, 0),
	(60, 'LT', '', 0, 49, 0),
	(60, 'LU', '', 0, 50, 0),
	(60, 'MB', '', 0, 51, 0),
	(60, 'MC', '', 0, 52, 0),
	(60, 'ME', '', 0, 53, 0),
	(60, 'MI', '', 0, 54, 0),
	(60, 'MN', '', 0, 55, 0),
	(60, 'MO', '', 0, 56, 0),
	(60, 'MS', '', 0, 57, 0),
	(60, 'MT', '', 0, 58, 0),
	(60, 'NA', '', 0, 59, 0),
	(60, 'NO', '', 0, 60, 0),
	(60, 'NU', '', 0, 61, 0),
	(60, 'OG', '', 0, 62, 0),
	(60, 'OR', '', 0, 63, 0),
	(60, 'OT', '', 0, 64, 0),
	(60, 'PA', '', 0, 65, 0),
	(60, 'PC', '', 0, 66, 0),
	(60, 'PD', '', 0, 67, 0),
	(60, 'PE', '', 0, 68, 0),
	(60, 'PG', '', 0, 69, 0),
	(60, 'PI', '', 0, 70, 0),
	(60, 'PN', '', 0, 71, 0),
	(60, 'PO', '', 0, 72, 0),
	(60, 'PR', '', 0, 73, 0),
	(60, 'PT', '', 0, 74, 0),
	(60, 'PU', '', 0, 75, 0),
	(60, 'PV', '', 0, 76, 0),
	(60, 'PZ', '', 0, 77, 0),
	(60, 'RA', '', 0, 78, 0),
	(60, 'RC', '', 0, 79, 0),
	(60, 'RE', '', 0, 80, 0),
	(60, 'RG', '', 0, 81, 0),
	(60, 'RI', '', 0, 82, 0),
	(60, 'RN', '', 0, 83, 0),
	(60, 'RO', '', 0, 84, 0),
	(60, 'Roma', '', 0, 85, 0),
	(60, 'SA', '', 0, 86, 0),
	(60, 'SI', '', 0, 87, 0),
	(60, 'SO', '', 0, 88, 0),
	(60, 'SP', '', 0, 89, 0),
	(60, 'SR', '', 0, 90, 0),
	(60, 'SS', '', 0, 91, 0),
	(60, 'SV', '', 0, 92, 0),
	(60, 'TA', '', 0, 93, 0),
	(60, 'TE', '', 0, 94, 0),
	(60, 'TN', '', 0, 95, 0),
	(60, 'TO', '', 0, 96, 0),
	(60, 'TP', '', 0, 97, 0),
	(60, 'TR', '', 0, 98, 0),
	(60, 'TS', '', 0, 99, 0),
	(60, 'TV', '', 0, 100, 0),
	(60, 'UD', '', 0, 101, 0),
	(60, 'VA', '', 0, 102, 0),
	(60, 'VB', '', 0, 103, 0),
	(60, 'VC', '', 0, 104, 0),
	(60, 'VE', '', 0, 105, 0),
	(60, 'VI', '', 0, 106, 0),
	(60, 'VR', '', 0, 107, 0),
	(60, 'VS', '', 0, 108, 0),
	(60, 'VT', '', 0, 109, 0),
	(60, 'VV', '', 0, 110, 0),
	(60, 'Stato Estero', '', 0, 111, 0),
	(62, 'AG', '', 0, 1, 0),
	(62, 'AL', '', 0, 2, 0),
	(62, 'AN', '', 0, 3, 0),
	(62, 'AO', '', 0, 4, 0),
	(62, 'AP', '', 0, 5, 0),
	(62, 'AQ', '', 0, 6, 0),
	(62, 'AR', '', 0, 7, 0),
	(62, 'AT', '', 0, 8, 0),
	(62, 'AV', '', 0, 9, 0),
	(62, 'BA', '', 0, 10, 0),
	(62, 'BG', '', 0, 11, 0),
	(62, 'BL', '', 0, 13, 0),
	(62, 'BN', '', 0, 14, 0),
	(62, 'BO', '', 0, 15, 0),
	(62, 'BR', '', 0, 16, 0),
	(62, 'BS', '', 0, 17, 0),
	(62, 'BT', '', 0, 18, 0),
	(62, 'BZ', '', 0, 19, 0),
	(62, 'CA', '', 0, 20, 0),
	(62, 'BI', '', 0, 12, 0),
	(62, 'CB', '', 0, 21, 0),
	(62, 'CE', '', 0, 22, 0),
	(62, 'CH', '', 0, 23, 0),
	(62, 'CI', '', 0, 24, 0),
	(62, 'CL', '', 0, 25, 0),
	(62, 'CN', '', 0, 26, 0),
	(62, 'CO', '', 0, 27, 0),
	(62, 'CR', '', 0, 28, 0),
	(62, 'CS', '', 0, 29, 0),
	(62, 'CT', '', 0, 30, 0),
	(62, 'CZ', '', 0, 31, 0),
	(62, 'EN', '', 0, 32, 0),
	(62, 'FC', '', 0, 33, 0),
	(62, 'FE', '', 0, 34, 0),
	(62, 'FG', '', 0, 35, 0),
	(62, 'FI', '', 0, 36, 0),
	(62, 'FM', '', 0, 37, 0),
	(62, 'FR', '', 0, 38, 0),
	(62, 'GE', '', 0, 39, 0),
	(62, 'GO', '', 0, 40, 0),
	(62, 'GR', '', 0, 41, 0),
	(62, 'IM', '', 0, 42, 0),
	(62, 'IS', '', 0, 43, 0),
	(62, 'KR', '', 0, 44, 0),
	(62, 'LC', '', 0, 45, 0),
	(62, 'LE', '', 0, 46, 0),
	(62, 'LI', '', 0, 47, 0),
	(62, 'LO', '', 0, 48, 0),
	(62, 'LT', '', 0, 49, 0),
	(62, 'LU', '', 0, 50, 0),
	(62, 'MB', '', 0, 51, 0),
	(62, 'MC', '', 0, 52, 0),
	(62, 'ME', '', 0, 53, 0),
	(62, 'MI', '', 0, 54, 0),
	(62, 'MN', '', 0, 55, 0),
	(62, 'MO', '', 0, 56, 0),
	(62, 'MS', '', 0, 57, 0),
	(62, 'MT', '', 0, 58, 0),
	(62, 'NA', '', 0, 59, 0),
	(62, 'NO', '', 0, 60, 0),
	(62, 'NU', '', 0, 61, 0),
	(62, 'OG', '', 0, 62, 0),
	(62, 'OR', '', 0, 63, 0),
	(62, 'OT', '', 0, 64, 0),
	(62, 'PA', '', 0, 65, 0),
	(62, 'PC', '', 0, 66, 0),
	(62, 'PD', '', 0, 67, 0),
	(62, 'PE', '', 0, 68, 0),
	(62, 'PG', '', 0, 69, 0),
	(62, 'PI', '', 0, 70, 0),
	(62, 'PN', '', 0, 71, 0),
	(62, 'PO', '', 0, 72, 0),
	(62, 'PR', '', 0, 73, 0),
	(62, 'PT', '', 0, 74, 0),
	(62, 'PU', '', 0, 75, 0),
	(62, 'PV', '', 0, 76, 0),
	(62, 'PZ', '', 0, 77, 0),
	(62, 'RA', '', 0, 78, 0),
	(62, 'RC', '', 0, 79, 0),
	(62, 'RE', '', 0, 80, 0),
	(62, 'RG', '', 0, 81, 0),
	(62, 'RI', '', 0, 82, 0),
	(62, 'RN', '', 0, 83, 0),
	(62, 'RO', '', 0, 84, 0),
	(62, 'Roma', '', 0, 85, 0),
	(62, 'SA', '', 0, 86, 0),
	(62, 'SI', '', 0, 87, 0),
	(62, 'SO', '', 0, 88, 0),
	(62, 'SP', '', 0, 89, 0),
	(62, 'SR', '', 0, 90, 0),
	(62, 'SS', '', 0, 91, 0),
	(62, 'SV', '', 0, 92, 0),
	(62, 'TA', '', 0, 93, 0),
	(62, 'TE', '', 0, 94, 0),
	(62, 'TN', '', 0, 95, 0),
	(62, 'TO', '', 0, 96, 0),
	(62, 'TP', '', 0, 97, 0),
	(62, 'TR', '', 0, 98, 0),
	(62, 'TS', '', 0, 99, 0),
	(62, 'TV', '', 0, 100, 0),
	(62, 'UD', '', 0, 101, 0),
	(62, 'VA', '', 0, 102, 0),
	(62, 'VB', '', 0, 103, 0),
	(62, 'VC', '', 0, 104, 0),
	(62, 'VE', '', 0, 105, 0),
	(62, 'VI', '', 0, 106, 0),
	(62, 'VR', '', 0, 107, 0),
	(62, 'VS', '', 0, 108, 0),
	(62, 'VT', '', 0, 109, 0),
	(62, 'VV', '', 0, 110, 0),
	(62, 'Stato Estero', '', 0, 111, 0),
	(71, 'Nessuna iscrizione', '', 0, 112, 0),
	(73, 'Partecipante non reclutato', 'Partecipante non reclutato', 0, 1, 0),
	(73, 'Partecipante reclutato', 'Partecipante reclutato', 0, 2, 0),
	(71, 'AG', '', 0, 1, 0),
	(71, 'AL', '', 0, 2, 0),
	(71, 'AN', '', 0, 3, 0),
	(71, 'AO', '', 0, 4, 0),
	(71, 'AP', '', 0, 5, 0),
	(71, 'AQ', '', 0, 6, 0),
	(71, 'AR', '', 0, 7, 0),
	(71, 'AT', '', 0, 8, 0),
	(71, 'AV', '', 0, 9, 0),
	(71, 'BA', '', 0, 10, 0),
	(71, 'BG', '', 0, 11, 0),
	(71, 'BI', '', 0, 12, 0),
	(71, 'BL', '', 0, 13, 0),
	(71, 'BN', '', 0, 14, 0),
	(71, 'BO', '', 0, 15, 0),
	(71, 'BR', '', 0, 16, 0),
	(71, 'BS', '', 0, 17, 0),
	(71, 'BT', '', 0, 18, 0),
	(71, 'BZ', '', 0, 19, 0),
	(71, 'CA', '', 0, 20, 0),
	(71, 'CB', '', 0, 21, 0),
	(71, 'CE', '', 0, 22, 0),
	(71, 'CH', '', 0, 23, 0),
	(71, 'CI', '', 0, 24, 0),
	(71, 'CL', '', 0, 25, 0),
	(71, 'CN', '', 0, 26, 0),
	(71, 'CO', '', 0, 27, 0),
	(71, 'CR', '', 0, 28, 0),
	(71, 'CS', '', 0, 29, 0),
	(71, 'CT', '', 0, 30, 0),
	(71, 'CZ', '', 0, 31, 0),
	(71, 'EN', '', 0, 32, 0),
	(71, 'FC', '', 0, 33, 0),
	(71, 'FE', '', 0, 34, 0),
	(71, 'FG', '', 0, 35, 0),
	(71, 'FI', '', 0, 36, 0),
	(71, 'FM', '', 0, 37, 0),
	(71, 'FR', '', 0, 38, 0),
	(71, 'GE', '', 0, 39, 0),
	(71, 'GO', '', 0, 40, 0),
	(71, 'GR', '', 0, 41, 0),
	(71, 'IM', '', 0, 42, 0),
	(71, 'IS', '', 0, 43, 0),
	(71, 'KR', '', 0, 44, 0),
	(71, 'LC', '', 0, 45, 0),
	(71, 'LE', '', 0, 46, 0),
	(71, 'LI', '', 0, 47, 0),
	(71, 'LO', '', 0, 48, 0),
	(71, 'LT', '', 0, 49, 0),
	(71, 'LU', '', 0, 50, 0),
	(71, 'MB', '', 0, 51, 0),
	(71, 'MC', '', 0, 52, 0),
	(71, 'ME', '', 0, 53, 0),
	(71, 'MI', '', 0, 54, 0),
	(71, 'MN', '', 0, 55, 0),
	(71, 'MO', '', 0, 56, 0),
	(71, 'MS', '', 0, 57, 0),
	(71, 'MT', '', 0, 58, 0),
	(71, 'NA', '', 0, 59, 0),
	(71, 'NO', '', 0, 60, 0),
	(71, 'NU', '', 0, 61, 0),
	(71, 'OG', '', 0, 62, 0),
	(71, 'OR', '', 0, 63, 0),
	(71, 'OT', '', 0, 64, 0),
	(71, 'PA', '', 0, 65, 0),
	(71, 'PC', '', 0, 66, 0),
	(71, 'PD', '', 0, 67, 0),
	(71, 'PE', '', 0, 68, 0),
	(71, 'PG', '', 0, 69, 0),
	(71, 'PI', '', 0, 70, 0),
	(71, 'PN', '', 0, 71, 0),
	(71, 'PO', '', 0, 72, 0),
	(71, 'PR', '', 0, 73, 0),
	(71, 'PT', '', 0, 74, 0),
	(71, 'PU', '', 0, 75, 0),
	(71, 'PV', '', 0, 76, 0),
	(71, 'PZ', '', 0, 77, 0),
	(71, 'RA', '', 0, 78, 0),
	(71, 'RC', '', 0, 79, 0),
	(71, 'RE', '', 0, 80, 0),
	(71, 'RG', '', 0, 81, 0),
	(71, 'RI', '', 0, 82, 0),
	(71, 'RN', '', 0, 83, 0),
	(71, 'RO', '', 0, 84, 0),
	(71, 'Roma', '', 0, 85, 0),
	(71, 'SA', '', 0, 86, 0),
	(71, 'SI', '', 0, 87, 0),
	(71, 'SO', '', 0, 88, 0),
	(71, 'SP', '', 0, 89, 0),
	(71, 'SR', '', 0, 90, 0),
	(71, 'SS', '', 0, 91, 0),
	(71, 'SV', '', 0, 92, 0),
	(71, 'TA', '', 0, 93, 0),
	(71, 'TE', '', 0, 94, 0),
	(71, 'TN', '', 0, 95, 0),
	(71, 'TO', '', 0, 96, 0),
	(71, 'TP', '', 0, 97, 0),
	(71, 'TR', '', 0, 98, 0),
	(71, 'TS', '', 0, 99, 0),
	(71, 'TV', '', 0, 100, 0),
	(71, 'UD', '', 0, 101, 0),
	(71, 'VA', '', 0, 102, 0),
	(71, 'VB', '', 0, 103, 0),
	(71, 'VC', '', 0, 104, 0),
	(71, 'VE', '', 0, 105, 0),
	(71, 'VI', '', 0, 106, 0),
	(71, 'VR', '', 0, 107, 0),
	(71, 'VS', '', 0, 108, 0),
	(71, 'VT', '', 0, 109, 0),
	(71, 'VV', '', 0, 110, 0),
	(71, 'Stato Estero', '', 0, 111, 0),
	(70, 'Assistente sanitario', '', 0, 0, 0),
	(70, 'Biologo/Anatomia patologica', '', 0, 0, 0),
	(70, 'Biologo/Biochimica clinica', '', 0, 0, 0),
	(70, 'Biologo/Igiene degli alimenti e della nutrizione', '', 0, 0, 0),
	(70, 'Biologo/Igiene, epidemiologia e sanità pubblica', '', 0, 0, 0),
	(70, 'Biologo/Laboratorio di genetica medica', '', 0, 0, 0),
	(70, 'Biologo/Medicina del lavoro e sicurezza degli ambienti di lavoro', '', 0, 0, 0),
	(70, 'Biologo/Medicina nucleare', '', 0, 0, 0),
	(70, 'Biologo/Medicina trasfusionale', '', 0, 0, 0),
	(70, 'Biologo/Microbiologia e virologia', '', 0, 0, 0),
	(70, 'Biologo/Patologia clinica (laboratorio di analisi chimico – cliniche e microbiologia)', '', 0, 0, 0),
	(70, 'Chimico/Biochimica clinica', '', 0, 0, 0),
	(70, 'Chimico/Igiene degli alimenti e della nutrizione', '', 0, 0, 0),
	(70, 'Chimico/Patologia clinica (laboratorio di analisi chimico – cliniche e microbiologia)', '', 0, 0, 0),
	(70, 'Dietista', '', 0, 0, 0),
	(70, 'Dietista/ Iscritto nell\'elenco speciale ad esaurimento', '', 0, 0, 0),
	(70, 'Educatore professionale', '', 0, 0, 0),
	(70, 'Educatore professionale/ Iscritto nell\'elenco speciale ad esaurimento', '', 0, 0, 0),
	(70, 'Farmacista/Farmacia territoriale', '', 0, 0, 0),
	(70, 'Farmacista/Farmacia pubblico del SSN', '', 0, 0, 0),
	(70, 'Farmacista/Farmacia di altro settore', '', 0, 0, 0),
	(70, 'Farmacista/Igiene, epidemiologia e sanità pubblica', '', 0, 0, 0),
	(70, 'Fisico/Fisica', '', 0, 0, 0),
	(70, 'Fisioterapista', '', 0, 0, 0),
	(70, 'Fisioterapista/ Iscritto nell\'elenco speciale ad esaurimento', '', 0, 0, 0),
	(70, 'Igienista dentale', '', 0, 0, 0),
	(70, 'Igienista dentale/ Iscritto nell\'elenco speciale ad esaurimento', '', 0, 0, 0),
	(70, 'Infermiere', '', 0, 0, 0),
	(70, 'Infermiere pediatrico', '', 0, 0, 0),
	(70, 'Logopedista', '', 0, 0, 0),
	(70, 'Logopedista/ Iscritto nell\'elenco speciale ad esaurimento', '', 0, 0, 0),
	(70, 'Massofisioterapista Iscritto all\'elenco speciale di cui allart. 5 del D.M. 9 agosto 2019', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Allergologia e immunologia clinica', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Anatomia patologica', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Anestesia e rianimazione', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Angiologia', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Audiologia e foniatria', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Biochimica clinica', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Cardiochirurgia', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Cardiologia', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Chirurgia generale', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Chirurgia maxillo-facciale', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Chirurgia pediatrica', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Chirurgia plastica e ricostruttiva', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Chirurgia toracica', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Chirurgia vascolare', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Continuità assistenziale', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Cure palliative', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Dermatologia e venereologia', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Direzione medica di presidio ospedaliero', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Ematologia', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Endocrinologia', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Epidemiologia', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Farmacologia e tossicologia clinica', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Gastroenterologia', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Genetica medica', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Geriatria', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Ginecologia e ostetricia', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Igiene degli alimenti e della nutrizione', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Igiene, epidemiologia e sanità pubblica', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Laboratorio di genetica medica', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Malattie dell"apparato respiratorio', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Malattie infettive', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Malattie metaboliche e diabetologia', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Medicina aeronautica e spaziale', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Medicina del lavoro e sicurezza degli ambienti di lavoro', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Medicina della comunità', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Medicina dello sport', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Medicina e chirurgia di accettazione e di urgenza', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Medicina fisica e riabilitazione', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Medicina generale (medici di famiglia)', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Medicina interna', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Medicina legale', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Medicina nucleare', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Medicina subacquea e iperbarica', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Medicina termale', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Medicina trasfusionale', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Microbiologia e virologia', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Nefrologia', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Neonatologia', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Neurochirurgia', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Neurofisiopatologia', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Neurologia', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Neuropsichiatria infantile', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Neuroradiologia', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Oftalmologia', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Oncologia', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Organizzazione dei servizi sanitari di base', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Ortopedia e traumatologia', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Otorinolaringoiatria', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Patologia clinica (laboratorio di analisi chimico-cliniche e microbiologia)', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Pediatria (pediatri di libera scelta)', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Psichiatria', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Psicoterapia', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Radiodiagnostica', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Radioterapia', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Reumatologia', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Scienza dell\'alimentazione e dietetica', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Urologia', '', 0, 0, 0),
	(70, 'Medico Chirurgo/Privo di specializzazione', '', 0, 0, 0),
	(70, 'Odontoiatra', '', 0, 0, 0),
	(70, 'Ortottista/Assistente di oftalmologia', '', 0, 0, 0),
	(70, 'Ortottista/assistente di oftalmologia/ Iscritto nellelenco speciale ad esaurimento', '', 0, 0, 0),
	(70, 'Ostetrica/o', '', 0, 0, 0),
	(70, 'Podologo', '', 0, 0, 0),
	(70, 'Podologo/ Iscritto nell\'elenco speciale ad esaurimento', '', 0, 0, 0),
	(70, 'Psicologo/Psicologia', '', 0, 0, 0),
	(70, 'Psicologo/Psicoterapia', '', 0, 0, 0),
	(70, 'Psicologo/ Igiene, epidemiologia e sanità pubblica', '', 0, 0, 0),
	(70, 'Tecnico audiometrista', '', 0, 0, 0),
	(70, 'Tecnico audiometrista/ Iscritto nell\'elenco speciale ad esaurimento', '', 0, 0, 0),
	(70, 'Tecnico audioprotesista', '', 0, 0, 0),
	(70, 'Tecnico audioprotesista/ Iscritto nell\'elenco speciale ad esaurimento', '', 0, 0, 0),
	(70, 'Tecnico della fisiopatologia cardiocircolatoria e perfusione cardiovascolare', '', 0, 0, 0),
	(70, 'Tecnico della fisiopatologia cardiocircolatoria e perfusione cardiovascolare/ Iscritto nell\'elenco speciale ad esaurimento', '', 0, 0, 0),
	(70, 'Tecnico della prevenzione nell\'ambiente e nei luoghi di lavoro', '', 0, 0, 0),
	(70, 'Tecnico della prevenzione nell\'ambiente e nei luoghi di lavoro/ Iscritto nellelenco speciale ad esaurimento', '', 0, 0, 0),
	(70, 'Tecnico della riabilitazione psichiatrica', '', 0, 0, 0),
	(70, 'Tecnico della riabilitazione psichiatrica/ Iscritto nell\'elenco speciale ad esaurimento', '', 0, 0, 0),
	(70, 'Tecnico di neurofisiopatologia', '', 0, 0, 0),
	(70, 'Tecnico di neurofisiopatologia/ Iscritto nell\'elenco speciale ad esaurimento', '', 0, 0, 0),
	(70, 'Tecnico ortopedico', '', 0, 0, 0),
	(70, 'Tecnico ortopedico/ Iscritto nell\'elenco speciale ad esaurimento', '', 0, 0, 0),
	(70, 'Tecnico sanitario laboratorio biomedico', '', 0, 0, 0),
	(70, 'Tecnico sanitario laboratorio biomedico/ Iscritto nell\'elenco speciale ad esaurimento', '', 0, 0, 0),
	(70, 'Tecnico sanitario di radiologia medica', '', 0, 0, 0),
	(70, 'Terapista della neuro e psicomotricità dell\'età evolutiva', '', 0, 0, 0),
	(70, 'Terapista della neuro e psicomotricità dell\'età evolutiva/ Iscritto nell\'elenco speciale ad esaurimento', '', 0, 0, 0),
	(70, 'Terapista occupazionale', '', 0, 0, 0),
	(70, 'Terapista occupazionale/ Iscritto nell\'elenco speciale ad esaurimento', '', 0, 0, 0),
	(70, 'Veterinario/Igiene degli allevamenti e delle produzioni zootecniche', '', 0, 0, 0),
	(70, 'Veterinario/Igiene, epidemiologia e sanità pubblica', '', 0, 0, 0),
	(70, 'Veterinario/Igiene, produzione, trasformazione, commercializzazione conservazione e trasporto alimenti di origine animale e derivati', '', 0, 0, 0),
	(70, 'Veterinario/Sanità animale', '', 0, 0, 0),
	(70, 'Altro / NON ECM', '', 0, 0, 0),
	(82, "Valle d\'Aosta", '', 0, 1, 0),
	(82, 'Piemonte', '', 0, 1, 0),
	(82, 'Liguria', '', 0, 1, 0),
	(82, 'Lombardia', '', 0, 1, 0),
	(82, 'Trentino Alto Adige', '', 0, 1, 0),
	(82, 'Veneto', '', 0, 1, 0),
	(82, 'Friuli Venezia Giulia', '', 0, 1, 0),
	(82, 'Emilia Romagna', '', 0, 1, 0),
	(82, 'Toscana', '', 0, 1, 0),
	(82, 'Umbria', '', 0, 1, 0),
	(82, 'Marche', '', 0, 1, 0),
	(82, 'Lazio', '', 0, 1, 0),
	(82, 'Abruzzo', '', 0, 1, 0),
	(82, 'Molise', '', 0, 1, 0),
	(82, 'Campania', '', 0, 1, 0),
	(82, 'Puglia', '', 0, 1, 0),
	(82, 'Basilicata', '', 0, 1, 0),
	(82, 'Calabria', '', 0, 1, 0),
	(82, 'Sicilia', '', 0, 1, 0),
	(82, 'Sardegna', '', 0, 1, 0),
	(87, 'Dietistica', '', 0, 1, 0),
	(87, 'Medicina e chirurgia', '', 0, 1, 0),
	(87, 'Infermieristica', '', 0, 1, 0),
	(87, 'Logopedista', '', 0, 1, 0),
	(87, 'Farmacia', '', 0, 1, 0),
	(87, 'Piscologia', '', 0, 1, 0),
	(87, 'Biologia', '', 0, 1, 0),
	(87, 'Altra disciplina', '', 0, 1, 0),
	(89, 'Altro', '', 0, 1, 0),
	(89, 'Direttore di Dipartimento', '', 0, 1, 0),
	(89, 'Direttore U.O. Complessa', '', 0, 1, 0),
	(89, 'Dirigente Medico', '', 0, 1, 0),
	(89, 'Infermiere', '', 0, 1, 0),
	(89, 'Responsabile U.O. Semplice', '', 0, 1, 0),
	(89, 'Studente', '', 0, 1, 0),
	(94, 'Dott.', '', 0, 1, 0),
	(94, 'Dott.ssa', '', 0, 1, 0),
	(94, 'Prof.', '', 0, 1, 0),
	(94, 'Prof.ssa', '', 0, 1, 0),
	(94, 'Sig.', '', 0, 1, 0),
	(94, 'Sig.ra', '', 0, 1, 0);
 -- gruppi utente
	-- 10
	INSERT INTO `#__usergroups` (`parent_id`, `lft`, `rgt`, `title`) VALUES (2, 11, 86, '_ACCESSO_CORSI');
	-- 11
	INSERT INTO `#__usergroups` (`parent_id`, `lft`, `rgt`, `title`) VALUES (2, 551, 552, 'ReportTutor');
	-- 12
	INSERT INTO `#__usergroups` (`parent_id`, `lft`, `rgt`, `title`) VALUES (2, 87, 540, '_PIATTAFORME');
	-- 13
	INSERT INTO `#__usergroups` (`parent_id`, `lft`, `rgt`, `title`) VALUES (2, 541, 542, '_TUTOR_AZ');
	-- 14
	INSERT INTO `#__usergroups` (`parent_id` ,`lft` ,`rgt` ,`title`) VALUES (2, 543, 544, '_TUTOR_P');
	-- 15
	INSERT INTO `#__usergroups` (`parent_id`, `lft`, `rgt`, `title`) VALUES (1, 2, 3, '_VENDITORE');
	-- 16
	INSERT INTO `#__usergroups` (`parent_id`, `lft`, `rgt`, `title`) VALUES (12, 545, 546, 'GruppoPiattaforma');
	-- 17
	INSERT INTO `#__usergroups` (`parent_id`, `lft`, `rgt`, `title`) VALUES (16, 547, 548, 'GGallery');
	-- 18
	INSERT INTO `#__usergroups` (`parent_id`, `lft`, `rgt`, `title`) VALUES (10, 549, 550, 'Corso');
	-- 19
	INSERT INTO `#__usergroups` (`parent_id`, `lft`, `rgt`, `title`) VALUES (16, 551, 552, '000000');

	-- livelli visualizzazione basati su gruppi utente
	INSERT INTO `#__viewlevels` (`title`, `ordering`, `rules`) VALUES ('tutor_report_nonusato', 0, '[11,8]');
	INSERT INTO `#__viewlevels` (`title`, `ordering`, `rules`) VALUES ('Venditori', 0, '[15]');
	INSERT INTO `#__viewlevels` (`title`, `ordering`, `rules`) VALUES ('Tutti i tutor e amministratori', 0, '[7,13,14,3,8]');
	INSERT INTO `#__viewlevels` (`title`, `ordering`, `rules`) VALUES ('solo_tutor_PIATTAFORMA', 0, '[14]');
	INSERT INTO `#__viewlevels` (`title`, `ordering`, `rules`) VALUES ('solo_tutor_AZIENDALE', 0, '[13]');
	INSERT INTO `#__viewlevels` (`title`, `ordering`, `rules`) VALUES ('Venditori_E_TutorP', 0, '[15,14]');

	-- user_groups_details
	-- rif id 16 GruppoPiattaforma
	INSERT INTO `#__usergroups_details` (
										`group_id`,
										`is_default`,
										`name`,
										`dg`,
										`logo`,
										`nomi_tutor`,
										`email_tutor`,
										`email_riferimento`,
										`link_ecommerce`,
										`telefono`,
										`attivo`,
										`dominio`,
										`patrocinio`,
										`footer`,
										`final_test`,
										`welcome`,
										`corsi_visbili_catalogo`,
										`testo_intro_homepage`,
										`attestati_custom`,
										`alias`
										)
										VALUES (
											16,
											1,
											'GruppoPiattaforma',
											'',
											'',
											'Tutor piattaforma',
											'francesca@ggallery.it',
											'francesca@ggallery.it',
											NULL,
											NULL,
											1,
											'ggallery.it',
											'',
											'',
											NULL,
											'',
											NULL,
											'',
											NULL,
											'GruppoPiattaforma'
											);

-- colonna per il calcolo dei report
ALTER TABLE `#__quiz_r_student_quiz`
    ADD COLUMN `timestamp`  timestamp NULL ON UPDATE CURRENT_TIMESTAMP AFTER `params`;

-- -------------------------------------------------
-- vista per summary report
-- ------------------------------------------------

Create or REPLACE VIEW #__view_report AS
SELECT
	`coupon`.`coupon` AS `coupon`,
	`coupon`.`creation_time` AS `data_creazione`,
	`coupon`.`data_utilizzo` AS `data_utilizzo`,
	`corso_map`.`idunita` AS `id_corso`,
	`corsi`.`title` AS `titolo_corso`,
	`soc`.`id` AS `id_azienda`,
	`soc`.`title` AS `azienda`,
	`soc`.`parent_id` AS `id_piattaforma`,
	`coupon`.`venditore` AS `venditore`,
	COALESCE (
		concat(
			`ru`.`nome`,
			' ',
			`ru`.`cognome`
		),
		''
	) AS `user_`,
	`ru`.`nome` AS `nome`,
	`ru`.`cognome` AS `cognome`,
	`ru`.`id_user` AS `id_user`,
	`comp`.`cb_codicefiscale` AS `cb_codicefiscale`,
	(
		CASE
		WHEN isnull(`ru`.`id_user`) THEN
			- (1)
		ELSE
			COALESCE (`vuc`.`stato`, 0)
		END
	) AS `stato`,
	`vuc`.`data_inizio` AS `data_inizio`,
	`vuc`.`data_fine` AS `data_fine`,
	`vuc`.`data_inizio_extra` AS `data_inizio_extra`,
	`vuc`.`data_fine_extra` AS `data_fine_extra`,
	(
		CASE
		WHEN (
			(
				`coupon`.`data_utilizzo` + INTERVAL `coupon`.`durata` DAY
			) < now()
		) THEN
			1
		ELSE
			0
		END
	) AS `scaduto`
FROM
	(
		(
			(
				(
					(
						(
							(
								`#__gg_coupon` `coupon`
								JOIN `#__usergroups` `corsi` ON (
									(
										`coupon`.`id_gruppi` = `corsi`.`id`
									)
								)
							)
							JOIN `#__usergroups` `soc` ON (
								(
									`soc`.`id` = `coupon`.`id_societa`
								)
							)
						)
						JOIN `#__gg_usergroup_map` `corso_map` ON (
							(
								`corso_map`.`idgruppo` = `corsi`.`id`
							)
						)
					)
					LEFT JOIN `#__users` `u` ON (
						(
							`coupon`.`id_utente` = `u`.`id`
						)
					)
				)
				LEFT JOIN `#__gg_report_users` `ru` ON ((`ru`.`id_user` = `u`.`id`))
			)
			LEFT JOIN `#__gg_view_stato_user_corso` `vuc` ON (
				(
					(
						`vuc`.`id_corso` = `corso_map`.`idunita`
					)
					AND (
						`vuc`.`id_anagrafica` = `ru`.`id`
					)
				)
			)
		)
		LEFT JOIN `#__comprofiler` `comp` ON (
			(
				`comp`.`user_id` = `ru`.`id_user`
			)
		)
	)
