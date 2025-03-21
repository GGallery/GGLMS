ALTER TABLE `#__gg_unit`
    ADD COLUMN `buy_voucher` tinyint(1) UNSIGNED DEFAULT '0' AFTER `sconti_particolari`;

ALTER TABLE `#__gg_quote_voucher`
    ADD COLUMN `course_id` INT(11) NULL AFTER `user_id`,
    ADD COLUMN `buy_subscription` tinyint(1) UNSIGNED DEFAULT '1' AFTER `code`,
    ADD COLUMN `buy_course` tinyint(1) UNSIGNED DEFAULT '0' AFTER `subscription`;
    