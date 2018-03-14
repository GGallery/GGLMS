CREATE TABLE `#__gg_view_carige_learning_batch` (
`id_corso`  int(10) NOT NULL ,
`id_user`  int(10) NOT NULL ,
`data_primo_accesso`  date NULL ,
`data_ultimo_accesso`  date NULL ,
`data_completamento_edizione`  date NULL ,
`percentuale_completamento`  float(5,2) NULL ,
`timestamp`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id_corso`, `id_user`)
)
;

