DROP TABLE IF EXISTS `#__gg_log`;
CREATE TABLE `#__gg_log` (
`id`  int(10) NOT NULL AUTO_INCREMENT ,
`id_utente`  int(10) NULL DEFAULT NULL ,
`id_contenuto`  int(10) NULL DEFAULT NULL ,
`data_accesso`  datetime NULL DEFAULT NULL ,
`supporto`  tinytext CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`ip_address`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`uniqid`  varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`permanenza`  int(11) NULL DEFAULT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;

-- ----------------------------
-- Table of `#__gg_error_log`
-- ----------------------------
DROP TABLE IF EXISTS `#__gg_error_log`;
CREATE TABLE `#__gg_error_log` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `messaggio` varchar(250) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8;
