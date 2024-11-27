ALTER TABLE `#__gg_unit`
ADD COLUMN `ecm` tinyint(1) NOT NULL DEFAULT 0;

CREATE TABLE IF NOT EXISTS `#__gg_report_ecm` (
                                `id_corso` int(10) NOT NULL,
                                `id_event_booking` int(10) DEFAULT NULL,
                                `id_unita` int(10) NOT NULL,
                                `id_contenuto` int(10) NOT NULL,
                                `id_utente` int(10) NOT NULL,
                                `id_anagrafica` int(10) DEFAULT NULL,
                                `stato` int(10) DEFAULT NULL,
                                `data` date DEFAULT NULL,
                                `visualizzazioni` int(10) DEFAULT NULL,
                                `permanenza_tot` int(10) DEFAULT NULL,
                                `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                `data_extra` datetime NULL,
                                `data_primo_accesso` datetime NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
