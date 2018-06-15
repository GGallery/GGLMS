ALTER TABLE `#__gg_contenuti`
ADD COLUMN `attestato_path`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `path`;
update #__gg_contenuti set attestato_path=path;
