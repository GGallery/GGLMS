CREATE TABLE `#__gg_cod_votazioni_users` (
  `id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_user` varchar(100) NOT NULL,
  `codice` TINYINT(1) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`,`id_user`, `codice`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `#__gg_votazioni_candidati` (
      `id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `id_user` int(11) unsigned NOT NULL,
      `codice` varchar(50) NOT NULL,
      `id_candidato` int(11) unsigned NOT NULL,
      `dettagli` varchar(100) DEFAULT NULL,
      `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`,`id_user`,`codice`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
