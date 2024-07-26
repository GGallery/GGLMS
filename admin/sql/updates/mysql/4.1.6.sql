CREATE TABLE IF NOT EXISTS `#__gg_report_queue` (
  `id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `user_id` int(11) NOT NULL,
  `report_dal`  date NOT NULL ,
  `report_al`  date NOT NULL ,
  `stato` text NOT NULL DEFAULT 'to-do',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;