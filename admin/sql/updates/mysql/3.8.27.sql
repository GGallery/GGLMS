
-- ----------------------------
-- Table structure for `un_gg_view_stato_user_corso`
-- ----------------------------
DROP TABLE IF EXISTS `un_gg_view_stato_user_corso`;
CREATE TABLE `un_gg_view_stato_user_corso` (
`id_anagrafica`  int(10) NOT NULL ,
`id_corso`  int(10) NOT NULL ,
`stato`  int(10) NOT NULL ,
`timestamp`  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP ,
PRIMARY KEY (`id_anagrafica`, `id_corso`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci

;

-- ----------------------------
-- Table structure for `un_gg_view_stato_user_unita`
-- ----------------------------
DROP TABLE IF EXISTS `un_gg_view_stato_user_unita`;
CREATE TABLE `un_gg_view_stato_user_unita` (
`id_anagrafica`  int(10) NOT NULL DEFAULT 0 ,
`id_unita`  int(10) NOT NULL DEFAULT 0 ,
`id_corso`  int(10) NOT NULL DEFAULT 0 ,
`stato`  int(10) NULL DEFAULT NULL ,
`timestamp`  timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP ,
PRIMARY KEY (`id_anagrafica`, `id_unita`, `id_corso`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci

;
