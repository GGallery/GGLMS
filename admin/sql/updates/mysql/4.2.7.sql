ALTER TABLE `#__usergroups_details`
    MODIFY COLUMN name VARCHAR(255) NOT NULL;

DROP TABLE IF EXISTS `#__usergroups_details_firme`;
CREATE TABLE `#__usergroup_details_firme` (
                             `id`  int(10) NOT NULL AUTO_INCREMENT ,
                             `usergroup_id`  int(10) NULL DEFAULT NULL ,
                             `dg`  VARCHAR(255) NULL DEFAULT NULL ,
                             `dg_firma`  VARCHAR(255) NULL DEFAULT NULL ,
                             `data_da`  DATE NULL DEFAULT NULL ,
                             `data_a`  DATE NULL DEFAULT NULL ,
                             PRIMARY KEY (`id`)
)
    ENGINE=InnoDB
    DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;
