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
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

