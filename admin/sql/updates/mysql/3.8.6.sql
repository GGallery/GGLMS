DROP TABLE IF EXISTS `#__gg_usergroup_map`;
CREATE TABLE `#__gg_usergroup_map` (
  `idunita` int(11) unsigned NOT NULL,
  `idgruppo` int(11) unsigned NOT NULL,
  PRIMARY KEY (`idunita`,`idgruppo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


