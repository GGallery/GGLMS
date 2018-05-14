CREATE TABLE `#__gg_report_view_permessi` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`id_utente`  int(11) NOT NULL ,
`id_corsi`  text NULL ,
PRIMARY KEY (`id`, `id_utente`)
)
;
CREATE TABLE `#__gg_report_view_permessi_gruppi` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`id_utente`  int(11) NOT NULL ,
`id_gruppi`  text NULL ,
PRIMARY KEY (`id`, `id_utente`)
)
;