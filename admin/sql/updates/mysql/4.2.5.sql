DROP TABLE IF EXISTS `#__gg_votazioni_candidati` ;

CREATE TABLE IF NOT EXISTS `#__gg_votazioni_candidati` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `id_user` varchar(100) NOT NULL,
      `id_candidato` varchar(100) NOT NULL,
      `codice` varchar(100) NOT NULL,
      `dettagli` varchar(100) DEFAULT NULL,
      `tipo_votazione` varchar(100) NOT NULL COMMENT '1: presidente, 2: consiglieri',
      `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__gg_votazioni_lista_candidati` (
      `id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `nome` varchar(200) NOT NULL,
      `cognome` varchar(200) NOT NULL,
      `tipo` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1: presidente, 2: consiglieri',
      `ruolo` varchar(200) DEFAULT NULL,
      `note` TEXT DEFAULT NULL,
      `immagine` TEXT DEFAULT NULL,
      PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;