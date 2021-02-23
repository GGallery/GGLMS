CREATE TABLE `#__gg_zoom` (
  `id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `token`  text NOT NULL ,
  `data_registrazione`  datetime NOT NULL ,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `#__gg_zoom_events` (
  `id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `id_evento`  bigint(20) UNSIGNED NOT NULL,
  `tipo_evento`  varchar(50) NOT NULL ,
  `label_evento`  varchar(200) NOT NULL,
  `response`  text NOT NULL ,
  `data_registrazione`  datetime NOT NULL ,
  PRIMARY KEY (`id`)
)  ENGINE=InnoDB DEFAULT CHARSET=utf8;
