ALTER TABLE `#__gg_contenuti`
ADD COLUMN `attestato_path` varchar(255) DEFAULT NULL;
update #__gg_contenuti set attestato_path=path;
