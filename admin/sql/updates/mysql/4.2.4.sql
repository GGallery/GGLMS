CREATE TABLE `#__gg_cod_votazioni_users` (
  `id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_user` varchar(100) NOT NULL,
  `codice` varchar(100) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`,`id_user`, `codice`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `#__gg_votazioni_candidati` (
      `id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `id_user` varchar(100) NOT NULL,
      `id_candidato` varchar(100) NOT NULL,
      `codice` varchar(100) NOT NULL,
      `dettagli` varchar(100) DEFAULT NULL,
      `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`,`id_user`, `codice`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
