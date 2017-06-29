/*
Navicat MySQL Data Transfer

Source Server         : AXACollege_DEV
Source Server Version : 50555
Source Host           : 31.14.141.98:3306
Source Database       : axa_college_dev

Target Server Type    : MYSQL
Target Server Version : 50555
File Encoding         : 65001

Date: 2017-06-29 18:13:35
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `un_gg_configs`
-- ----------------------------

CREATE TABLE IF NOT EXISTS `un_gg_configs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config_key` varchar(100) DEFAULT NULL,
  `config_value` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of un_gg_configs
-- ----------------------------
INSERT INTO `un_gg_configs` VALUES ('1', 'integrazione', null);
INSERT INTO `un_gg_configs` VALUES ('2', 'campo_event_booking_auto_abilitazione_coupon', '');
INSERT INTO `un_gg_configs` VALUES ('3', 'verifica_cf', '0');
INSERT INTO `un_gg_configs` VALUES ('4', 'campo_event_booking_controllo_cf', '');
INSERT INTO `un_gg_configs` VALUES ('5', 'abilita_breadcrumbs', '1');
INSERT INTO `un_gg_configs` VALUES ('6', 'visualizza_ultimo', '1');
INSERT INTO `un_gg_configs` VALUES ('7', 'visualizza_primo_item', '1');
INSERT INTO `un_gg_configs` VALUES ('8', 'customizza_primo_item', '0');
INSERT INTO `un_gg_configs` VALUES ('9', 'customizza_testo_primo_item', 'Corsi');
INSERT INTO `un_gg_configs` VALUES ('10', 'customizza_link_primo_item', 'index.php?option=com_gglms');
INSERT INTO `un_gg_configs` VALUES ('11', 'visualizza_solo_mieicorsi', '1');
INSERT INTO `un_gg_configs` VALUES ('12', 'nomenclatura_unita', 'Raccolte');
INSERT INTO `un_gg_configs` VALUES ('13', 'titolo_unita_visibile', '0');
INSERT INTO `un_gg_configs` VALUES ('14', 'nomenclatura_moduli', 'Elementi');
INSERT INTO `un_gg_configs` VALUES ('15', 'visibilita_durata', '1,2');
INSERT INTO `un_gg_configs` VALUES ('16', 'larghezza_box_unita', 'size-30');
INSERT INTO `un_gg_configs` VALUES ('17', 'larghezza_box_contenuti', 'size-30');

-- ----------------------------
-- Table structure for `un_gg_contenuti`
-- ----------------------------

CREATE TABLE IF NOT EXISTS `un_gg_contenuti` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `titolo` text NOT NULL,
  `alias` text,
  `pubblicato` tinyint(1) NOT NULL DEFAULT '1',
  `durata` int(10) unsigned NOT NULL DEFAULT '0',
  `descrizione` varchar(200) DEFAULT '-',
  `access` int(10) unsigned DEFAULT '0',
  `meta_tag` varchar(255) DEFAULT '-',
  `abstract` mediumtext,
  `datapubblicazione` date DEFAULT NULL,
  `slide` tinyint(1) NOT NULL DEFAULT '0',
  `path` varchar(255) DEFAULT NULL,
  `categoria` varchar(255) DEFAULT NULL COMMENT 'NON CANCELLARE SERVER PER IL MAP',
  `tipologia` int(4) NOT NULL DEFAULT '1',
  `files` varchar(255) DEFAULT NULL COMMENT 'NON CANCELLARE SERVE PER IL MAP',
  `mod_track` int(255) DEFAULT '1',
  `prerequisiti` varchar(255) DEFAULT NULL,
  `parametri` varchar(255) DEFAULT NULL,
  `acl` varchar(255) DEFAULT NULL,
  `livello` int(4) NOT NULL DEFAULT '1',
  `area` int(4) NOT NULL DEFAULT '1',
  `prodotto` varchar(255) DEFAULT NULL,
  `formato` varchar(255) DEFAULT NULL,
  `id_completed_data` int(10) DEFAULT NULL,
  `id_quizdeluxe` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `titolo` (`titolo`,`abstract`,`descrizione`,`meta_tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of un_gg_contenuti
-- ----------------------------

-- ----------------------------
-- Table structure for `un_gg_contenuti_tipology`
-- ----------------------------

CREATE TABLE IF NOT EXISTS `un_gg_contenuti_tipology` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipologia` varchar(50) DEFAULT NULL,
  `descrizione` varchar(50) DEFAULT NULL,
  `ordinamento` int(10) DEFAULT NULL,
  `pubblicato` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of un_gg_contenuti_tipology
-- ----------------------------
INSERT INTO `un_gg_contenuti_tipology` VALUES ('1', 'videoslide', 'Contenuti', '1', '1');
INSERT INTO `un_gg_contenuti_tipology` VALUES ('2', 'solovideo', 'Contenuto Video', '2', '1');
INSERT INTO `un_gg_contenuti_tipology` VALUES ('3', 'allegati', 'Allegati', '3', '1');
INSERT INTO `un_gg_contenuti_tipology` VALUES ('4', 'scorm', 'Test', '4', '1');
INSERT INTO `un_gg_contenuti_tipology` VALUES ('5', 'attestato', 'Attestato', '5', '1');
INSERT INTO `un_gg_contenuti_tipology` VALUES ('6', 'testuale', 'Testuale / HTML', '6', '1');
INSERT INTO `un_gg_contenuti_tipology` VALUES ('7', 'quizdeluxe', 'Quiz Deluxe', '7', '1');
INSERT INTO `un_gg_contenuti_tipology` VALUES ('8', 'sso', 'SingleSingOn', '8', '1');

-- ----------------------------
-- Table structure for `un_gg_coupon`
-- ----------------------------

CREATE TABLE IF NOT EXISTS `un_gg_coupon` (
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
  PRIMARY KEY (`coupon`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of un_gg_coupon
-- ----------------------------

-- ----------------------------
-- Table structure for `un_gg_files`
-- ----------------------------

CREATE TABLE IF NOT EXISTS `un_gg_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` int(4) NOT NULL DEFAULT '1',
  `date` date DEFAULT NULL,
  `filename` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of un_gg_files
-- ----------------------------

-- ----------------------------
-- Table structure for `un_gg_files_map`
-- ----------------------------

CREATE TABLE IF NOT EXISTS `un_gg_files_map` (
  `idlink` int(11) NOT NULL AUTO_INCREMENT,
  `idcontenuto` int(11) unsigned NOT NULL,
  `idfile` int(11) unsigned NOT NULL,
  `ordinamento` int(11) DEFAULT NULL,
  `data` date DEFAULT NULL,
  PRIMARY KEY (`idlink`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of un_gg_files_map
-- ----------------------------

-- ----------------------------
-- Table structure for `un_gg_log`
-- ----------------------------

CREATE TABLE IF NOT EXISTS `un_gg_log` (
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
-- Records of un_gg_log
-- ----------------------------

-- ----------------------------
-- Table structure for `un_gg_scormvars`
-- ----------------------------

CREATE TABLE IF NOT EXISTS `un_gg_scormvars` (
  `scoid` int(10) unsigned NOT NULL DEFAULT '0',
  `userid` int(11) NOT NULL DEFAULT '0',
  `varName` varchar(255) NOT NULL DEFAULT '',
  `varValue` text,
  PRIMARY KEY (`scoid`,`userid`,`varName`),
  KEY `SCOInstanceID` (`scoid`),
  KEY `varName` (`varName`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of un_gg_scormvars
-- ----------------------------

-- ----------------------------
-- Table structure for `un_gg_unit`
-- ----------------------------

CREATE TABLE IF NOT EXISTS `un_gg_unit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `titolo` varchar(255) NOT NULL DEFAULT '1',
  `alias` varchar(255) DEFAULT NULL,
  `descrizione` mediumtext,
  `unitapadre` int(10) DEFAULT '0',
  `is_corso` int(10) DEFAULT NULL,
  `id_event_booking` int(10) DEFAULT NULL,
  `id_contenuto_completamento` int(10) DEFAULT NULL,
  `ordinamento` int(10) DEFAULT NULL,
  `accesso` text,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `titolo` (`titolo`,`descrizione`)
) ENGINE=MyISAM AUTO_INCREMENT=113 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of un_gg_unit
-- ----------------------------
INSERT INTO `un_gg_unit` VALUES ('1', 'Corsi', 'corsi', '', '0', '1', null, '0', '0', 'Accesso libero');

-- ----------------------------
-- Table structure for `un_gg_unit_map`
-- ----------------------------

CREATE TABLE IF NOT EXISTS `un_gg_unit_map` (
  `idlink` int(11) NOT NULL AUTO_INCREMENT,
  `idcontenuto` int(11) unsigned NOT NULL,
  `idunita` int(11) unsigned NOT NULL,
  `ordinamento` int(11) DEFAULT '99',
  `data` date DEFAULT NULL,
  PRIMARY KEY (`idlink`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of un_gg_unit_map
-- ----------------------------
DROP TRIGGER IF EXISTS `Protection`;
DELIMITER ;;
CREATE TRIGGER `Protection` BEFORE UPDATE ON `un_gg_unit` FOR EACH ROW BEGIN
           IF OLD.id = 1 THEN
               SET NEW.unitapadre = 0,  NEW.id = 1,  NEW.ordinamento = 0;
           END IF;
       END
;;
DELIMITER ;
