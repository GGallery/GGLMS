CREATE TABLE `#__gg_event_voucher` (
                                       `id` int(10) NOT NULL AUTO_INCREMENT,
                                       `code` text NOT NULL,
                                       `user_id` int(11) DEFAULT NULL,
                                       `group_id` int(11) DEFAULT NULL,
                                       `date` datetime DEFAULT NULL,
                                       PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
