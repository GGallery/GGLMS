ALTER TABLE `#__gg_unit`
    ADD COLUMN `usa_voucher` tinyint(1) UNSIGNED DEFAULT '0';

ALTER TABLE `#__gg_event_voucher`
    ADD COLUMN `unit_id` int(11) DEFAULT NULL AFTER `group_id`,
    ADD COLUMN `sc_valore` decimal(6,2) DEFAULT NULL AFTER `unit_id`;
