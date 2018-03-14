CREATE TABLE `#__gg_view_stato_user_corso` (
`id_anagrafica`  int(10) NOT NULL ,
`id_corso`  int(10) NOT NULL ,
`stato`  int(10) NOT NULL ,
`data_inizio`  date NULL,
`data_fine`  date NULL,
`timestamp`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id_anagrafica`, `id_corso`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci

;

CREATE TABLE `#__gg_view_stato_user_unita` (
`id_anagrafica`  int(10) NOT NULL DEFAULT 0 ,
`id_unita`  int(10) NOT NULL DEFAULT 0 ,
`id_corso`  int(10) NOT NULL DEFAULT 0 ,
`stato`  int(10) NULL DEFAULT NULL ,
`data_inizio`  date NULL,
`data_fine`  date NULL,
`timestamp`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id_anagrafica`, `id_unita`, `id_corso`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci

;