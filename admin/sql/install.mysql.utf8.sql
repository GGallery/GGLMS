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
INSERT INTO `#__gg_configs` VALUES ('8', 'id_gruppo_venditori', '54');
INSERT INTO `#__gg_configs` VALUES ('9', 'id_gruppo_piattaforme', '31');
INSERT INTO `#__gg_configs` VALUES ('10', 'id_gruppo_tutor_piattaforma', '61');
INSERT INTO `#__gg_configs` VALUES ('11', 'id_gruppo_tutor_aziendale', '55');
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
INSERT INTO `#__gg_configs` VALUES ('30', 'larghezza_box_unita', 'size-25');
INSERT INTO `#__gg_configs` VALUES ('31', 'larghezza_box_contenuti', 'size-25');
INSERT INTO `#__gg_configs` VALUES ('32', 'testo_invito_scaricare_attestato', '<h2>Congratulazioni!</h2>\r\n<p>Ora puoi scaricare l\'attestato del corso cliccando sull\'icona qui a fianco.</p>');
INSERT INTO `#__gg_configs` VALUES ('33', 'check_coupon_attestato', '0');
INSERT INTO `#__gg_configs` VALUES ('34', 'testo_attestato_disabilitato', '<h2>Attestato disabilitato!</h2>\r\n<p>Il download di questo attestato è disabilitato, rivolgiti al tuo tutor aziendale.</p>');
INSERT INTO `#__gg_configs` VALUES ('35', 'data_sync', '2019-11-19 17:57:23');
INSERT INTO `#__gg_configs` VALUES ('36', 'alert_lista_corsi', '');
INSERT INTO `#__gg_configs` VALUES ('37', 'alert_days_before', '7');
INSERT INTO `#__gg_configs` VALUES ('38', 'alert_mail_object', 'Avviso scadenza corso Carige Learning');
INSERT INTO `#__gg_configs` VALUES ('39', 'alert_mail_text', 'Buongiorno, inviamo questa mail come promemoria per la scadenza prossima del corso');
INSERT INTO `#__gg_configs` VALUES ('40', 'campi_csv', 'user_id,firstname,lastname,cb_codicefiscale,cb_datadinascita,cb_luogodinascita,cb_provinciadinascita,cb_indirizzodiresidenza,cb_provdiresidenza,cb_cap,cb_telefono');
INSERT INTO `#__gg_configs` VALUES ('41', 'log_utente', '0');
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
INSERT INTO `#__gg_configs` VALUES ('55', 'titolo_pagina_rinnova_coupon', 'Inserisci qui il codice coupon da rinnnovare');
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
-- ----------------------------
-- Table structure for `#__gg_contenuti`
-- ----------------------------
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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

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
INSERT INTO `#__gg_contenuti_tipology` VALUES ('9', 'webinar', 'Webinar', '3', '1');

-- ----------------------------
-- Table structure for `#__gg_coupon`
-- ----------------------------
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
  PRIMARY KEY (`coupon`(20))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__gg_csv_report
-- ----------------------------

-- ----------------------------
-- Table structure for `#__gg_error_log`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_error_log`;
CREATE TABLE `#__gg_error_log` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `messaggio` varchar(250) DEFAULT NULL,
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
) ENGINE=MyISAM AUTO_INCREMENT=431 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__gg_files_map
-- ----------------------------

-- ----------------------------
-- Table structure for `#__gg_log`
-- ----------------------------
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
  `id_user` int(10) DEFAULT NULL,
  `nome` varchar(50) DEFAULT NULL,
  `cognome` varchar(50) DEFAULT NULL,
  `fields` longtext,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  PRIMARY KEY (`id`),
  FULLTEXT KEY `titolo` (`titolo`,`descrizione`)
) ENGINE=MyISAM AUTO_INCREMENT=247 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__gg_unit
-- ----------------------------
INSERT INTO `#__gg_unit` VALUES ('1', 'Corsi', 'corsi', '', '0', '0', '1', '0', 'Accesso libero', '1', '0', '2019-11-14', '2019-11-14', null);

-- ----------------------------
-- Table structure for `#__gg_unit_map`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_unit_map`;
CREATE TABLE `#__gg_unit_map` (
  `idcontenuto` int(11) unsigned NOT NULL,
  `idunita` int(11) unsigned NOT NULL,
  `ordinamento` int(11) DEFAULT '99',
  PRIMARY KEY (`idcontenuto`,`idunita`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
  `welcome` varchar(255) DEFAULT NULL,
  `corsi_visbili_catalogo` varchar(100) DEFAULT NULL,
  `testo_intro_homepage` varchar(50) DEFAULT NULL,
  `attestati_custom` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `mail_from_default` tinyint(1) unsigned zerofill DEFAULT '0',
  `info_pagamento` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


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
CREATE TABLE `cis19_gg_coupon_dispenser_log` (
  `email` varchar(255) NOT NULL,
  `id_dispenser` int(11) NOT NULL,
  `coupon` varchar(255) NOT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`email`,`id_dispenser`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


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
	`ru`.`id_user` AS `id_user`,
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
