ALTER TABLE `#__gg_contenuti`
ADD COLUMN `data_evento` date DEFAULT NULL,
ADD COLUMN `id_utente_zoom` int(10) NOT NULL;



CREATE TABLE `#__gg_zoom_log`  (
  `id_utente` int(255) NOT NULL DEFAULT '0',
  `codice_fiscale` varchar (255) NOT NULL DEFAULT '0',
  `id_contenuto` int(10) NOT NULL DEFAULT '0',
  `data_accesso` datetime NULL,
  `durata` int(255) NOT NULL,
  PRIMARY KEY (`id_utente`,`codice_fiscale`,`id_contenuto`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8;

CREATE TABLE `#__gg_zoom_users`  (
   `id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_user_zoom` varchar (255) NOT NULL,
  `email` varchar (255) NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8;

CREATE TABLE `#__gg_zoom_riferimento`  (
  `id_evento` varchar(25) NULL,
  `uuid_evento` varchar (255) NOT NULL,
  `id_contenuto` int(10) NULL DEFAULT NULL,
  `data_evento` date DEFAULT NULL,
  PRIMARY KEY (`uuid_evento`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8;

CREATE TABLE `#__gg_zoom_codice_fiscale`  (
  `id_utente` int(255) NOT NULL DEFAULT '0',
  `codice_fiscale` varchar (255) NOT NULL DEFAULT '0',
  `id_zoom_user` varchar (255) NOT NULL DEFAULT '0',
  `data_accesso` datetime NULL,
  `durata` int(255) NOT NULL,
  PRIMARY KEY (`id_utente`,`codice_fiscale`,`id_zoom_user`,`durata`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8;
